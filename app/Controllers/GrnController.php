<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use Core\Session;
use Core\Helper;
use App\Services\InventoryEngine;
use Exception;

class GrnController extends Controller {

    public function index(): void {
        Auth::requirePermission('marketplace.products');

        $db = Database::getInstance();

        $grns = $db->query("
            SELECT sl.*, p.name_en AS product_name, p.product_code, loc.name AS location_name,
                   sup.name AS supplier_name, u.full_name AS user_name
            FROM stock_ledger sl
            LEFT JOIN products p ON sl.product_id = p.id
            LEFT JOIN inventory_locations loc ON sl.location_id = loc.id
            LEFT JOIN parties sup ON sl.source_transaction_id = sup.id AND sl.source_module = 'marketplace'
            LEFT JOIN users u ON sl.created_by = u.id
            WHERE sl.movement_type = 'GRN'
            ORDER BY sl.id DESC
            LIMIT 100
        ")->fetchAll();

        $this->render('grn/index', [
            'pageTitle' => 'Goods Receipt Notes (GRN)',
            'activeNav' => 'grn',
            'grns' => $grns
        ]);
    }

    public function create(): void {
        Auth::requirePermission('marketplace.products');

        $db = Database::getInstance();
        $products = $db->query("SELECT id, product_code, name_en, default_purchase_price FROM products WHERE is_active = 1 ORDER BY name_en ASC")->fetchAll();
        $warehouses = $db->query("SELECT id, name FROM inventory_locations WHERE is_active = 1 OR 1=1 ORDER BY name ASC")->fetchAll();
        $suppliers = $db->query("SELECT id, name, party_code FROM parties WHERE party_type IN ('SUPPLIER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('grn/create', [
            'pageTitle' => 'New Goods Receipt Note (GRN)',
            'activeNav' => 'grn',
            'products' => $products,
            'warehouses' => $warehouses,
            'suppliers' => $suppliers
        ]);
    }

    public function store(): void {
        Auth::requirePermission('marketplace.products');
        $this->validateCsrf();

        try {
            $locationId = (int)$_POST['location_id'];
            $refNumber = trim($_POST['reference_number'] ?? '');
            $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
            
            $productIds = $_POST['products'] ?? [];
            $quantities = $_POST['quantities'] ?? [];
            $unitCosts = $_POST['unit_costs'] ?? [];

            if (empty($productIds) || !is_array($productIds)) {
                throw new Exception("No products provided for GRN.");
            }

            if ($locationId <= 0) {
                throw new Exception("Invalid receiving warehouse.");
            }

            $db = Database::getInstance();
            $db->beginTransaction();

            $updatePriceStmt = $db->prepare("UPDATE products SET default_purchase_price = :cost WHERE id = :id");

            for ($i = 0; $i < count($productIds); $i++) {
                $pId = (int)($productIds[$i] ?? 0);
                $qty = (float)($quantities[$i] ?? 0);
                $cost = (float)($unitCosts[$i] ?? 0);

                if ($pId <= 0 || $qty <= 0 || $cost < 0) {
                    throw new Exception("Invalid product, quantity, or cost data at row " . ($i + 1) . ".");
                }

                // Record stock IN
                InventoryEngine::recordStockIn(
                    $pId,
                    $locationId,
                    $qty,
                    $cost,
                    'GRN',
                    'marketplace', // module
                    $supplierId ?? 0, // transaction/party id
                    $refNumber
                );

                // Update product's default purchase price
                $updatePriceStmt->execute([
                    'cost' => $cost,
                    'id' => $pId
                ]);
            }

            $db->commit();

            Session::setFlash('success', 'GRN successfully processed for ' . count($productIds) . ' item(s).');
            Helper::redirect('grn');
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', $e->getMessage());
            Helper::redirect('grn/create');
        }
    }
}
