<?php
namespace App\Models;

use Core\Model;

class ProductModel extends Model {

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, pc.name AS category_name, u.code AS unit_code
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id
            WHERE p.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT p.*, pc.name AS category_name, u.code AS unit_code
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (isset($filters['is_marketplace'])) {
            $sql .= " AND p.is_marketplace = :is_mkt";
            $params['is_mkt'] = (int)$filters['is_marketplace'];
        }
        if (isset($filters['is_active'])) {
            $sql .= " AND p.is_active = :is_act";
            $params['is_act'] = (int)$filters['is_active'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.product_code LIKE :search_code OR p.sku LIKE :search_sku OR p.name_en LIKE :search_name)";
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_sku'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY p.name_en ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getCount(array $filters = []): int {
        $sql = "
            SELECT COUNT(*)
            FROM products p
            WHERE 1=1
        ";
        $params = [];

        if (isset($filters['is_marketplace'])) {
            $sql .= " AND p.is_marketplace = :is_mkt";
            $params['is_mkt'] = (int)$filters['is_marketplace'];
        }
        if (isset($filters['is_active'])) {
            $sql .= " AND p.is_active = :is_act";
            $params['is_act'] = (int)$filters['is_active'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.product_code LIKE :search_code OR p.sku LIKE :search_sku OR p.name_en LIKE :search_name)";
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_sku'] = '%' . $filters['search'] . '%';
            $params['search_name'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function toggleMarketplace(int $productId, bool $isMarketplace): bool {
        $stmt = $this->db->prepare("UPDATE products SET is_marketplace = :is_mkt, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            'is_mkt' => $isMarketplace ? 1 : 0,
            'id' => $productId
        ]);
    }

    public function updateSourceInfo(int $productId, string $sourceModule, ?int $sourceTransactionId): bool {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET source_module = :src_mod, 
                source_transaction_id = :src_id, 
                updated_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute([
            'src_mod' => $sourceModule,
            'src_id' => $sourceTransactionId,
            'id' => $productId
        ]);
    }

    public function updatePrices(int $productId, float $purchasePrice, float $sellingPrice): bool {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET default_purchase_price = :purch, 
                default_selling_price = :sell, 
                updated_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute([
            'purch' => $purchasePrice,
            'sell' => $sellingPrice,
            'id' => $productId
        ]);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO products (
                sku, product_code, name_en, category_id, product_type, 
                base_unit_id, purchase_unit_id, sales_unit_id, 
                default_purchase_price, default_selling_price, 
                inventory_account_id, cogs_account_id, sales_revenue_account_id, 
                is_active, is_marketplace, source_module, created_by, created_at, updated_at
            ) VALUES (
                :sku, :product_code, :name_en, :category_id, :product_type,
                :base_unit_id, :purchase_unit_id, :sales_unit_id,
                :default_purchase_price, :default_selling_price,
                :inventory_account_id, :cogs_account_id, :sales_revenue_account_id,
                1, 1, :source_module, :created_by, NOW(), NOW()
            )
        ");
        $stmt->execute([
            'sku' => $data['sku'],
            'product_code' => $data['product_code'],
            'name_en' => $data['name_en'],
            'category_id' => $data['category_id'] ?? null,
            'product_type' => $data['product_type'] ?? 'TRADING',
            'base_unit_id' => (int)$data['base_unit_id'],
            'purchase_unit_id' => (int)$data['base_unit_id'],
            'sales_unit_id' => (int)$data['base_unit_id'],
            'default_purchase_price' => (float)$data['default_purchase_price'],
            'default_selling_price' => (float)$data['default_selling_price'],
            'inventory_account_id' => !empty($data['inventory_account_id']) ? (int)$data['inventory_account_id'] : 13,
            'cogs_account_id' => !empty($data['cogs_account_id']) ? (int)$data['cogs_account_id'] : 38,
            'sales_revenue_account_id' => !empty($data['sales_revenue_account_id']) ? (int)$data['sales_revenue_account_id'] : 30,
            'source_module' => $data['source_module'] ?? 'PURCHASE',
            'created_by' => $data['created_by'] ?? 1
        ]);
        return (int)$this->db->lastInsertId();
    }
}
