<?php
$file = 'c:\xampp\htdocs\AGRI PRO\app\Controllers\InvoiceController.php';
$content = file_get_contents($file);

$editMethod = <<<PHP

    public function edit(): void {
        Auth::requirePermission('invoices.edit');

        \$id = !empty(\$_GET['id']) ? (int)\$_GET['id'] : 0;
        \$invoice = \$this->invoiceModel->getById(\$id);

        if (!\$invoice || \$invoice['status'] !== 'DRAFT') {
            Session::setFlash('error', 'Invoice cannot be edited (Not found or already posted).');
            Helper::redirect('modules/invoices');
        }

        \$db = \Core\Database::getInstance();
        \$customers = \$db->query("SELECT id, party_code, name, phone, address FROM parties WHERE party_type IN ('CUSTOMER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();
        \$members = \$db->query("SELECT id, member_number, full_name, phone, address, party_id FROM coop_members WHERE status = 'ACTIVE' ORDER BY full_name ASC")->fetchAll();
        \$staff = \$db->query("SELECT id, username, full_name, role_id, party_id FROM users WHERE status = 'active' ORDER BY full_name ASC")->fetchAll();
        \$warehouses = \$db->query("SELECT id, code, name FROM inventory_locations WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        \$defaultWarehouseId = \$warehouses[0]['id'] ?? 1;
        \$cashAccounts = \$db->query("SELECT id, name FROM cash_accounts WHERE status = 'active' ORDER BY name ASC")->fetchAll();
        \$bankAccounts = \$db->query("SELECT id, account_name, bank_name FROM bank_accounts WHERE status = 'active' ORDER BY account_name ASC")->fetchAll();
        \$products = \$db->query("SELECT p.id, p.sku, p.name_en, p.price, p.sales_unit_id, u.code AS unit_code FROM products p LEFT JOIN units_of_measure u ON p.sales_unit_id = u.id WHERE p.status = 'active' ORDER BY p.name_en ASC")->fetchAll();

        foreach (\$products as &\$p) {
            \$p['stocks'] = [];
            foreach (\$warehouses as \$wh) {
                \$p['stocks'][\$wh['id']] = \App\Services\InventoryEngine::getStockOnHand((int)\$p['id'], (int)\$wh['id']);
            }
        }

        \$services = \$db->query("SELECT s.id, s.service_code, s.service_name, s.unit, s.default_price, s.id AS service_id, s.description FROM services s WHERE s.is_active = 1 ORDER BY s.service_name ASC")->fetchAll();
        \$rentals = \$db->query("SELECT mr.*, m.machinery_name, m.machinery_code, pt.name AS customer_name FROM machinery_rentals mr JOIN machinery m ON mr.machinery_id = m.id JOIN parties pt ON mr.customer_id = pt.id WHERE mr.status = 'ACTIVE' AND (mr.invoice_id IS NULL OR mr.invoice_id = {\$id}) ORDER BY mr.id DESC")->fetchAll();
        \$machineryAssets = \$db->query("SELECT * FROM machinery WHERE status = 'AVAILABLE' OR 1=1 ORDER BY machinery_name ASC")->fetchAll();

        // Convert the invoice_items back into the format expected by the JS
        \$editItems = [];
        foreach (\$invoice['items'] as \$itm) {
            \$editItems[] = [
                'type' => \$itm['item_type'],
                'product_id' => \$itm['product_id'] ?? '',
                'product_name' => \$itm['product_name'] ?? '',
                'service_id' => \$itm['service_id'] ?? '',
                'service_name' => \$itm['service_name'] ?? '',
                'description' => \$itm['description'] ?? '',
                'unit' => \$itm['product_unit'] ?? \$itm['service_unit'] ?? 'Qty',
                'qty' => (float)\$itm['quantity'],
                'price' => (float)\$itm['unit_price'],
                'total' => (float)\$itm['total'],
                'service_job_id' => \$itm['service_job_id'] ?? '',
                'machinery_rental_id' => \$itm['machinery_rental_id'] ?? ''
            ];
        }

        \$this->render('invoices/edit', [
            'pageTitle' => 'Edit Invoice ' . \$invoice['invoice_number'],
            'activeNav' => 'invoices',
            'customers' => \$customers,
            'members' => \$members,
            'staff' => \$staff,
            'warehouses' => \$warehouses,
            'defaultWarehouseId' => \$defaultWarehouseId,
            'cashAccounts' => \$cashAccounts,
            'bankAccounts' => \$bankAccounts,
            'products' => \$products,
            'services' => \$services,
            'rentals' => \$rentals,
            'machineryAssets' => \$machineryAssets,
            'invoice' => \$invoice,
            'editItems' => \$editItems
        ]);
    }
PHP;

$content = str_replace("public function store(): void {", $editMethod . "\n\n    public function store(): void {", $content);
file_put_contents($file, $content);
echo "Edit method added.\n";
