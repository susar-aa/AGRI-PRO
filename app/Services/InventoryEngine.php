<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use Exception;

class InventoryEngine {

    /**
     * Get current stock quantity on hand for a product at a specific location/warehouse.
     */
    public static function getStockOnHand(int $productId, int $locationId): float {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT quantity_on_hand FROM inventory_balances WHERE product_id = :pid AND location_id = :loc LIMIT 1");
        $stmt->execute(['pid' => $productId, 'loc' => $locationId]);
        return (float)($stmt->fetchColumn() ?: 0.0000);
    }

    /**
     * Get current average unit cost (AVCO) for a product at a specific location.
     * Falls back to default purchase price on the product master.
     */
    public static function getAverageCost(int $productId, int $locationId): float {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT average_cost FROM inventory_balances WHERE product_id = :pid AND location_id = :loc LIMIT 1");
        $stmt->execute(['pid' => $productId, 'loc' => $locationId]);
        $cost = $stmt->fetchColumn();

        if ($cost !== false && (float)$cost > 0) {
            return (float)$cost;
        }

        // Fallback to product default purchase price
        $stmt = $db->prepare("SELECT default_purchase_price FROM products WHERE id = :pid LIMIT 1");
        $stmt->execute(['pid' => $productId]);
        return (float)($stmt->fetchColumn() ?: 0.00);
    }

    /**
     * Record a Stock OUT movement (AVCO based deduction).
     * Returns total Cost of Goods Sold (COGS).
     */
    public static function recordStockOut(
        int $productId,
        int $locationId,
        float $qty,
        string $movementType,
        string $sourceModule,
        int $sourceTransactionId,
        string $refNumber
    ): float {
        $db = Database::getInstance();

        if ($qty <= 0) {
            throw new Exception("Stock Out quantity must be positive.");
        }

        $currentQty = self::getStockOnHand($productId, $locationId);
        if ($currentQty < $qty) {
            throw new Exception("Insufficient stock. Requested: " . $qty . ", Available: " . $currentQty);
        }

        $avgCost = self::getAverageCost($productId, $locationId);
        $totalCost = round($qty * $avgCost, 2);

        $newQty = $currentQty - $qty;
        $newValue = round($newQty * $avgCost, 2);

        $createdBy = Auth::id() ?? 1;

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // 1. Update inventory balance
            $stmt = $db->prepare("
                UPDATE inventory_balances 
                SET quantity_on_hand = :new_qty,
                    inventory_value = :new_val,
                    updated_at = NOW()
                WHERE product_id = :pid AND location_id = :loc
            ");
            $stmt->execute([
                'new_qty' => $newQty,
                'new_val' => $newValue,
                'pid' => $productId,
                'loc' => $locationId
            ]);

            // 2. Add entry to stock ledger
            $stmt = $db->prepare("
                INSERT INTO stock_ledger 
                (product_id, location_id, movement_date, reference_number, movement_type, source_module, source_type, source_transaction_id, quantity_in, quantity_out, unit_cost, total_cost, balance_quantity, balance_value, created_by)
                VALUES 
                (:pid, :loc, CURDATE(), :ref, :mov_type, :src_mod, 'STOCK_OUT', :src_id, 0.00, :qty, :unit_cost, :total_cost, :bal_qty, :bal_val, :created_by)
            ");
            $stmt->execute([
                'pid' => $productId,
                'loc' => $locationId,
                'ref' => $refNumber,
                'mov_type' => $movementType,
                'src_mod' => $sourceModule,
                'src_id' => $sourceTransactionId,
                'qty' => $qty,
                'unit_cost' => $avgCost,
                'total_cost' => $totalCost,
                'bal_qty' => $newQty,
                'bal_val' => $newValue,
                'created_by' => $createdBy
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            return $totalCost;

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Record a Stock IN movement (updates Weighted Average Cost).
     */
    public static function recordStockIn(
        int $productId,
        int $locationId,
        float $qty,
        float $unitCost,
        string $movementType,
        string $sourceModule,
        int $sourceTransactionId,
        string $refNumber
    ): void {
        $db = Database::getInstance();

        if ($qty <= 0) {
            throw new Exception("Stock In quantity must be positive.");
        }

        $createdBy = Auth::id() ?? 1;

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Retrieve current balance row
            $stmt = $db->prepare("SELECT * FROM inventory_balances WHERE product_id = :pid AND location_id = :loc LIMIT 1");
            $stmt->execute(['pid' => $productId, 'loc' => $locationId]);
            $balance = $stmt->fetch();

            if ($balance) {
                $currentQty = (float)$balance['quantity_on_hand'];
                $currentVal = (float)$balance['inventory_value'];

                $newQty = $currentQty + $qty;
                $newVal = round($currentVal + ($qty * $unitCost), 2);
                $newAvgCost = $newQty > 0 ? round($newVal / $newQty, 4) : 0.00;

                $stmt = $db->prepare("
                    UPDATE inventory_balances 
                    SET quantity_on_hand = :new_qty,
                        average_cost = :new_avg,
                        inventory_value = :new_val,
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([
                    'new_qty' => $newQty,
                    'new_avg' => $newAvgCost,
                    'new_val' => $newVal,
                    'id' => (int)$balance['id']
                ]);
            } else {
                $newQty = $qty;
                $newVal = round($qty * $unitCost, 2);
                $newAvgCost = $unitCost;

                $stmt = $db->prepare("
                    INSERT INTO inventory_balances 
                    (product_id, location_id, quantity_on_hand, average_cost, inventory_value)
                    VALUES 
                    (:pid, :loc, :new_qty, :new_avg, :new_val)
                ");
                $stmt->execute([
                    'pid' => $productId,
                    'loc' => $locationId,
                    'new_qty' => $newQty,
                    'new_avg' => $newAvgCost,
                    'new_val' => $newVal
                ]);
            }

            // 2. Add entry to stock ledger
            $stmt = $db->prepare("
                INSERT INTO stock_ledger 
                (product_id, location_id, movement_date, reference_number, movement_type, source_module, source_type, source_transaction_id, quantity_in, quantity_out, unit_cost, total_cost, balance_quantity, balance_value, created_by)
                VALUES 
                (:pid, :loc, CURDATE(), :ref, :mov_type, :src_mod, 'STOCK_IN', :src_id, :qty, 0.00, :unit_cost, :total_cost, :bal_qty, :bal_val, :created_by)
            ");
            $stmt->execute([
                'pid' => $productId,
                'loc' => $locationId,
                'ref' => $refNumber,
                'mov_type' => $movementType,
                'src_mod' => $sourceModule,
                'src_id' => $sourceTransactionId,
                'qty' => $qty,
                'unit_cost' => $unitCost,
                'total_cost' => round($qty * $unitCost, 2),
                'bal_qty' => $newQty,
                'bal_val' => $newVal,
                'created_by' => $createdBy
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Reverse stock movements for a specific source transaction (e.g. on sale cancellation).
     */
    public static function reverseStockMovement(string $sourceModule, int $sourceTransactionId): void {
        $db = Database::getInstance();

        // Get original movements
        $stmt = $db->prepare("SELECT * FROM stock_ledger WHERE source_module = :mod AND source_transaction_id = :tx_id ORDER BY id ASC");
        $stmt->execute(['mod' => $sourceModule, 'tx_id' => $sourceTransactionId]);
        $movements = $stmt->fetchAll();

        if (empty($movements)) {
            return;
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            foreach ($movements as $m) {
                // If it was stock OUT (e.g. quantity_out > 0), we put it back (Stock IN)
                if ((float)$m['quantity_out'] > 0) {
                    self::recordStockIn(
                        (int)$m['product_id'],
                        (int)$m['location_id'],
                        (float)$m['quantity_out'],
                        (float)$m['unit_cost'],
                        'SALES_RETURN',
                        $sourceModule,
                        $sourceTransactionId,
                        'REV-' . $m['reference_number']
                    );
                }
                // If it was stock IN (quantity_in > 0), we take it out
                elseif ((float)$m['quantity_in'] > 0) {
                    self::recordStockOut(
                        (int)$m['product_id'],
                        (int)$m['location_id'],
                        (float)$m['quantity_in'],
                        'ADJUSTMENT_OUT',
                        $sourceModule,
                        $sourceTransactionId,
                        'REV-' . $m['reference_number']
                    );
                }
            }

            if (!$inTransaction) {
                Database::commit();
            }

        } catch (Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            throw $e;
        }
    }
}
