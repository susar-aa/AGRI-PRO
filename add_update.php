<?php
$file = 'c:\xampp\htdocs\AGRI PRO\app\Controllers\InvoiceController.php';
$content = file_get_contents($file);

$updateMethod = <<<PHP

    public function update(): void {
        Auth::requirePermission('invoices.edit');
        \$this->validateCsrf();

        \$id = !empty(\$_POST['id']) ? (int)\$_POST['id'] : 0;
        if (!\$id) {
            Session::setFlash('error', 'Invoice ID is required for update.');
            Helper::redirect('modules/invoices');
        }

        \$db = \Core\Database::getInstance();
        
        \$invoice = \$this->invoiceModel->getById(\$id);
        if (!\$invoice || \$invoice['status'] !== 'DRAFT') {
            Session::setFlash('error', 'Cannot update this invoice. It may have already been posted.');
            Helper::redirect('modules/invoices');
        }

        // Get single warehouse automatically if none provided
        \$warehouseId = !empty(\$_POST['warehouse_id']) ? (int)\$_POST['warehouse_id'] : null;
        if (!\$warehouseId) {
            \$warehouseId = (int)\$db->query("SELECT id FROM inventory_locations WHERE code = 'LOC-MAIN' OR is_active = 1 LIMIT 1")->fetchColumn();
        }

        // Resolve Walk-in Customer ID
        \$customerIdInput = \$_POST['customer_id'] ?? '';
        \$customerId = 0;
        
        \$walkinCustomer = \$db->query("SELECT id FROM parties WHERE party_code = 'PTY-WALKIN'")->fetch();
        \$walkinId = \$walkinCustomer ? (int)\$walkinCustomer['id'] : 0;

        if (empty(\$customerIdInput)) {
            \$customerId = \$walkinId;
        } elseif (strpos(\$customerIdInput, 'M_') === 0) {
            \$memberId = (int)substr(\$customerIdInput, 2);
            \$member = \$db->query("SELECT * FROM coop_members WHERE id = " . \$memberId . " AND member_type IN ('MEMBER', 'DIRECTOR')")->fetch();
            if (!\$member) throw new \Exception("Selected member/director not found.");
            
            if (!empty(\$member['party_id'])) {
                \$customerId = (int)\$member['party_id'];
            } else {
                \$partyCode = 'CUST-' . strtoupper(substr(uniqid(), -6));
                \$stmt = \$db->prepare("INSERT INTO parties (party_code, party_type, name, phone, address, status) VALUES (:code, 'CUSTOMER', :name, :phone, :address, 'active')");
                \$stmt->execute(['code' => \$partyCode, 'name' => \$member['full_name'], 'phone' => \$member['phone'], 'address' => \$member['address']]);
                \$customerId = (int)\$db->lastInsertId();
                \$db->prepare("UPDATE coop_members SET party_id = :pid WHERE id = :mid")->execute(['pid' => \$customerId, 'mid' => \$memberId]);
            }
        } elseif (strpos(\$customerIdInput, 'U_') === 0) {
            \$userId = (int)substr(\$customerIdInput, 2);
            \$staff = \$db->query("SELECT * FROM users WHERE id = " . \$userId)->fetch();
            if (!\$staff) throw new \Exception("Selected staff not found.");
            
            if (!empty(\$staff['party_id'])) {
                \$customerId = (int)\$staff['party_id'];
            } else {
                \$partyCode = 'STF-' . strtoupper(substr(uniqid(), -6));
                \$stmt = \$db->prepare("INSERT INTO parties (party_code, party_type, name, phone, status) VALUES (:code, 'CUSTOMER', :name, :phone, 'active')");
                \$stmt->execute(['code' => \$partyCode, 'name' => \$staff['full_name'], 'phone' => \$staff['phone'] ?? '']);
                \$customerId = (int)\$db->lastInsertId();
                \$db->prepare("UPDATE users SET party_id = :pid WHERE id = :uid")->execute(['pid' => \$customerId, 'uid' => \$userId]);
            }
        } else {
            \$customerId = (int)\$customerIdInput;
        }

        \$invoiceDate = \$_POST['invoice_date'] ?? date('Y-m-d');
        \$dueDate = \$_POST['due_date'] ?? \$invoiceDate;
        \$reference = trim(\$_POST['reference'] ?? '');
        \$notes = trim(\$_POST['notes'] ?? '');
        \$paymentType = \$_POST['payment_type'] ?? 'CASH';
        
        \$cashAccountId = !empty(\$_POST['cash_account_id']) ? (int)\$_POST['cash_account_id'] : null;
        \$bankAccountId = !empty(\$_POST['bank_account_id']) ? (int)\$_POST['bank_account_id'] : null;
        \$chequeNo = trim(\$_POST['cheque_number'] ?? '');
        \$chequeBank = trim(\$_POST['cheque_bank'] ?? '');
        \$chequeDate = \$_POST['cheque_date'] ?? null;

        \$itemTypes = \$_POST['item_type'] ?? [];
        \$productIds = \$_POST['product_id'] ?? [];
        \$serviceIds = \$_POST['service_id'] ?? [];
        \$descriptions = \$_POST['item_description'] ?? [];
        \$quantities = \$_POST['quantity'] ?? [];
        \$unitPrices = \$_POST['unit_price'] ?? [];
        \$totals = \$_POST['line_total'] ?? [];
        \$serviceJobIds = \$_POST['service_job_id'] ?? [];
        \$machineryRentalIds = \$_POST['machinery_rental_id'] ?? [];

        \$subtotal = 0.00;
        \$taxAmount = 0.00;

        if (empty(\$itemTypes)) {
            Session::setFlash('error', 'Please add at least one item to the invoice.');
            Helper::redirect('modules/invoices/edit?id=' . \$id);
        }

        try {
            \$db->beginTransaction();

            // 1. Delete existing items
            \$db->prepare("DELETE FROM invoice_items WHERE invoice_id = :id")->execute(['id' => \$id]);

            // 2. Insert new items
            \$insertItemStmt = \$db->prepare("
                INSERT INTO invoice_items 
                (invoice_id, item_type, product_id, service_id, description, quantity, unit_price, total, tax_amount, service_job_id, machinery_rental_id)
                VALUES 
                (:inv_id, :type, :prod_id, :srv_id, :desc, :qty, :price, :tot, 0.00, :job_id, :rental_id)
            ");

            for (\$i = 0; \$i < count(\$itemTypes); \$i++) {
                \$type = \$itemTypes[\$i];
                \$pid = !empty(\$productIds[\$i]) ? (int)\$productIds[\$i] : null;
                \$sid = !empty(\$serviceIds[\$i]) ? (int)\$serviceIds[\$i] : null;
                \$desc = \$descriptions[\$i] ?? '';
                \$qty = (float)(\$quantities[\$i] ?? 0);
                \$price = (float)(\$unitPrices[\$i] ?? 0);
                \$tot = (float)(\$totals[\$i] ?? 0);
                \$sjid = !empty(\$serviceJobIds[\$i]) ? (int)\$serviceJobIds[\$i] : null;
                \$mrid = !empty(\$machineryRentalIds[\$i]) ? (int)\$machineryRentalIds[\$i] : null;

                if (\$tot <= 0) continue;

                \$insertItemStmt->execute([
                    'inv_id' => \$id,
                    'type' => \$type,
                    'prod_id' => \$pid,
                    'srv_id' => \$sid,
                    'desc' => \$desc,
                    'qty' => \$qty,
                    'price' => \$price,
                    'tot' => \$tot,
                    'job_id' => \$sjid,
                    'rental_id' => \$mrid
                ]);

                \$subtotal += \$tot;
            }

            \$discount = (float)(\$_POST['discount'] ?? 0);
            \$netTotal = \$subtotal - \$discount;
            if (\$netTotal < 0) \$netTotal = 0;

            // 3. Update invoice header
            \$stmt = \$db->prepare("
                UPDATE invoices SET 
                    customer_id = :customer_id, 
                    warehouse_id = :warehouse_id,
                    invoice_date = :invoice_date, 
                    due_date = :due_date, 
                    reference = :reference,
                    notes = :notes, 
                    payment_type = :payment_type, 
                    cash_account_id = :cash_id, 
                    bank_account_id = :bank_id,
                    subtotal = :subtotal, 
                    discount = :discount, 
                    tax_amount = :tax_amount, 
                    total = :total, 
                    updated_at = NOW()
                WHERE id = :id
            ");

            \$stmt->execute([
                'customer_id' => \$customerId,
                'warehouse_id' => \$warehouseId,
                'invoice_date' => \$invoiceDate,
                'due_date' => \$dueDate,
                'reference' => \$reference,
                'notes' => \$notes,
                'payment_type' => \$paymentType,
                'cash_id' => \$cashAccountId,
                'bank_id' => \$bankAccountId,
                'subtotal' => \$subtotal,
                'discount' => \$discount,
                'tax_amount' => \$taxAmount,
                'total' => \$netTotal,
                'id' => \$id
            ]);

            // Save cheque details to session if CHEQUE payment type
            if (\$paymentType === 'CHEQUE') {
                \$_SESSION['temp_cheque_' . \$id] = [
                    'cheque_number' => \$chequeNo,
                    'bank_name' => \$chequeBank,
                    'cheque_date' => \$chequeDate
                ];
            } else {
                unset(\$_SESSION['temp_cheque_' . \$id]);
            }

            \$db->commit();
            Session::setFlash('success', 'Invoice updated successfully.');
            Helper::redirect('modules/invoices/view?id=' . \$id);

        } catch (\Exception \$e) {
            \$db->rollBack();
            Session::setFlash('error', 'Error updating invoice: ' . \$e->getMessage());
            Helper::redirect('modules/invoices/edit?id=' . \$id);
        }
    }
PHP;

// Insert update method before store method
$content = str_replace("public function store(): void {", $updateMethod . "\n\n    public function store(): void {", $content);
file_put_contents($file, $content);
echo "Update method added.\n";
