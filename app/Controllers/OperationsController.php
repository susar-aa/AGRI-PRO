<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use Core\Session;
use Core\Helper;
use App\Models\ProductModel;

class OperationsController extends Controller {

    public function plantation(): void {
        Auth::requirePermission('machinery.view');

        $db = Database::getInstance();

        // 1. Fetch statistics
        $activeCount = (int)$db->query("SELECT COUNT(*) FROM plantation_projects WHERE status = 'ACTIVE'")->fetchColumn();
        $completedCount = (int)$db->query("SELECT COUNT(*) FROM plantation_projects WHERE status = 'COMPLETED'")->fetchColumn();
        
        // Sum posted expenses linked to Plantation cost center (ID = 4) or linked directly to any project
        $totalExpenses = (float)$db->query("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM expenses 
            WHERE source_module = 'PLANTATION' AND status = 'posted'
        ")->fetchColumn();
        
        $totalHarvest = 0; // Placeholder for Stage 1

        // 2. Fetch projects lists
        $activeProjects = $db->query("SELECT * FROM plantation_projects WHERE status = 'ACTIVE' ORDER BY start_date DESC")->fetchAll();
        $completedProjects = $db->query("SELECT * FROM plantation_projects WHERE status = 'COMPLETED' ORDER BY start_date DESC")->fetchAll();

        // Enrich projects with crops list and project-specific total expenses
        foreach ($activeProjects as &$p) {
            $p['crops'] = $db->query("
                SELECT ppc.*, prod.name_en AS product_name 
                FROM plantation_project_crops ppc 
                JOIN products prod ON ppc.product_id = prod.id 
                WHERE ppc.project_id = {$p['id']}
            ")->fetchAll();

            $p['total_expenses'] = (float)$db->query("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE project_id = {$p['id']} AND status = 'posted'
            ")->fetchColumn();
        }
        unset($p);

        foreach ($completedProjects as &$p) {
            $p['crops'] = $db->query("
                SELECT ppc.*, prod.name_en AS product_name 
                FROM plantation_project_crops ppc 
                JOIN products prod ON ppc.product_id = prod.id 
                WHERE ppc.project_id = {$p['id']}
            ")->fetchAll();

            $p['total_expenses'] = (float)$db->query("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE project_id = {$p['id']} AND status = 'posted'
            ")->fetchColumn();
        }
        unset($p);

        // Fetch products for grow modal
        $productModel = new ProductModel();
        $products = $productModel->getAll(['is_active' => 1]);

        $this->render('operations/plantation', [
            'pageTitle' => 'Plantation Operations Control Panel',
            'activeNav' => 'ops_plantation',
            'stats' => [
                'active_projects' => $activeCount,
                'completed_projects' => $completedCount,
                'total_expenses' => $totalExpenses,
                'total_harvest' => $totalHarvest
            ],
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
            'products' => $products
        ]);
    }

    public function storePlantationProject(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $db = Database::getInstance();

        $projectName = trim($_POST['project_name'] ?? '');
        $location = 'N/A'; // Hidden from form
        $startDate = trim($_POST['start_date'] ?? date('Y-m-d'));
        $expectedHarvestDate = null;
        $description = null;

        if (empty($projectName)) {
            Session::setFlash('error', 'Project name is required.');
            Helper::redirect('operations/plantation');
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO plantation_projects (project_name, location, start_date, expected_harvest_date, description, status) 
                VALUES (:name, :location, :start_date, :harvest_date, :desc, 'ACTIVE')
            ");
            $stmt->execute([
                'name' => $projectName,
                'location' => $location,
                'start_date' => $startDate,
                'harvest_date' => $expectedHarvestDate,
                'desc' => $description
            ]);
            $projectId = (int)$db->lastInsertId();

            // Store grow crops / plants
            if (!empty($_POST['crops']) && is_array($_POST['crops'])) {
                $cropStmt = $db->prepare("
                    INSERT INTO plantation_project_crops (project_id, product_id, planned_quantity, unit, notes) 
                    VALUES (:project_id, :product_id, :planned_qty, :unit, :notes)
                ");
                foreach ($_POST['crops'] as $c) {
                    if (empty($c['product_id'])) continue;
                    $cropStmt->execute([
                        'project_id' => $projectId,
                        'product_id' => (int)$c['product_id'],
                        'planned_qty' => (float)$c['planned_quantity'],
                        'unit' => trim($c['unit'] ?? 'Units'),
                        'notes' => trim($c['notes'] ?? '')
                    ]);
                }
            }

            $db->commit();
            Session::setFlash('success', 'Plantation project started successfully.');
        } catch (\Exception $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to start project: ' . $e->getMessage());
        }

        Helper::redirect('operations/plantation');
    }

    public function viewPlantationProject(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("SELECT * FROM plantation_projects WHERE id = {$id}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Plantation project not found.');
            Helper::redirect('operations/plantation');
        }

        // Fetch crops
        $crops = $db->query("
            SELECT ppc.*, prod.name_en AS product_name, prod.product_code 
            FROM plantation_project_crops ppc 
            JOIN products prod ON ppc.product_id = prod.id 
            WHERE ppc.project_id = {$id}
        ")->fetchAll();

        // Calculate total from posted expenses linked to project
        $totalExpenses = (float)$db->query("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM expenses 
            WHERE project_id = {$id} AND status = 'posted'
        ")->fetchColumn();

        // Fetch harvesting stats
        $totalHarvest = (float)$db->query("
            SELECT COALESCE(SUM(quantity), 0.00) 
            FROM plantation_harvests 
            WHERE project_id = {$id}
        ")->fetchColumn();

        $harvestRecordsCount = (int)$db->query("
            SELECT COUNT(*) 
            FROM plantation_harvests 
            WHERE project_id = {$id}
        ")->fetchColumn();

        $totalTransferred = (float)$db->query("
            SELECT COALESCE(SUM(pht.quantity), 0.00) 
            FROM plantation_harvest_transfers pht
            JOIN plantation_harvests ph ON pht.harvest_id = ph.id
            WHERE ph.project_id = {$id}
        ")->fetchColumn();

        $this->render('operations/plantation_dashboard', [
            'pageTitle' => 'Plantation Dashboard - ' . $project['project_name'],
            'activeNav' => 'ops_plantation',
            'project' => $project,
            'crops' => $crops,
            'totalExpenses' => $totalExpenses,
            'totalHarvest' => $totalHarvest,
            'harvestRecordsCount' => $harvestRecordsCount,
            'totalTransferred' => $totalTransferred
        ]);
    }

    public function plantationExpenses(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("SELECT * FROM plantation_projects WHERE id = {$id}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Plantation project not found.');
            Helper::redirect('operations/plantation');
        }

        // Fetch expenses linked to project
        $expenses = $db->query("
            SELECT e.*, c.name AS category_name 
            FROM expenses e 
            LEFT JOIN expense_categories c ON e.expense_category_id = c.id 
            WHERE e.project_id = {$id} AND e.source_module = 'PLANTATION'
            ORDER BY e.expense_date DESC, e.id DESC
        ")->fetchAll();

        // Load data needed for the recording modal
        $expenseModel = new \App\Models\Expense();
        $categories = $expenseModel->getAllCategories();
        $costCenters = (new \App\Models\CostCenter())->getActive();
        $cashAccounts = $expenseModel->getCashAccounts();
        $bankAccounts = $expenseModel->getBankAccounts();
        
        $suppliers = $db->query("SELECT id, party_code AS supplier_code, name AS name_en FROM parties WHERE party_type IN ('SUPPLIER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();
        $staffMembers = $db->query("SELECT id, name AS name_en FROM parties WHERE party_type = 'EMPLOYEE' AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('operations/plantation_expenses', [
            'pageTitle' => 'Plantation Expenses',
            'activeNav' => 'plantation',
            'project' => $project,
            'expenses' => $expenses,
            'categories' => $categories,
            'costCenters' => $costCenters,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'suppliers' => $suppliers,
            'staffMembers' => $staffMembers
        ]);
    }

    public function plantationCrops(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("SELECT * FROM plantation_projects WHERE id = {$id}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Plantation project not found.');
            Helper::redirect('operations/plantation');
        }

        // Fetch crops
        $crops = $db->query("
            SELECT ppc.*, prod.name_en AS product_name, prod.product_code 
            FROM plantation_project_crops ppc 
            JOIN products prod ON ppc.product_id = prod.id 
            WHERE ppc.project_id = {$id}
        ")->fetchAll();

        // Fetch active products list for adding new crops
        $productModel = new ProductModel();
        $products = $productModel->getAll(['is_active' => 1]);

        $this->render('operations/plantation_crops', [
            'pageTitle' => 'Plantation Crops - ' . $project['project_name'],
            'activeNav' => 'ops_plantation',
            'project' => $project,
            'crops' => $crops,
            'products' => $products
        ]);
    }

    public function addPlantationCrop(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $projectId = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
        $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $unit = trim($_POST['unit'] ?? 'Units');
        $notes = trim($_POST['notes'] ?? '');

        if (!$projectId || !$productId) {
            Session::setFlash('error', 'Project ID and Crop Selection are required.');
            Helper::redirect('operations/plantation');
        }

        $db = Database::getInstance();
        try {
            $db->prepare("
                INSERT INTO plantation_project_crops (project_id, product_id, planned_quantity, unit, notes) 
                VALUES (:project_id, :product_id, 0.00, :unit, :notes)
            ")->execute([
                'project_id' => $projectId,
                'product_id' => $productId,
                'unit' => $unit,
                'notes' => $notes
            ]);
            Session::setFlash('success', 'Crop added to project successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to add crop: ' . $e->getMessage());
        }

        Helper::redirect("operations/plantation/crops?id={$projectId}");
    }

    public function deletePlantationCrop(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $projectId = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;

        if (!$id || !$projectId) {
            Session::setFlash('error', 'Invalid parameters.');
            Helper::redirect('operations/plantation');
        }

        $db = Database::getInstance();
        try {
            $db->prepare("DELETE FROM plantation_project_crops WHERE id = :id")->execute(['id' => $id]);
            Session::setFlash('success', 'Crop removed from project successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to remove crop: ' . $e->getMessage());
        }

        Helper::redirect("operations/plantation/crops?id={$projectId}");
    }

    public function updatePlantationCrop(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $projectId = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
        $unit = trim($_POST['unit'] ?? 'Units');
        $notes = trim($_POST['notes'] ?? '');

        if (!$id || !$projectId) {
            Session::setFlash('error', 'Invalid parameters.');
            Helper::redirect('operations/plantation');
        }

        $db = Database::getInstance();
        try {
            $db->prepare("
                UPDATE plantation_project_crops 
                SET unit = :unit, notes = :notes 
                WHERE id = :id
            ")->execute([
                'unit' => $unit,
                'notes' => $notes,
                'id' => $id
            ]);
            Session::setFlash('success', 'Crop details updated successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update crop: ' . $e->getMessage());
        }

        Helper::redirect("operations/plantation/crops?id={$projectId}");
    }

    public function updatePlantationProjectStatus(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = $_POST['status'] ?? 'ACTIVE';

        if (!in_array($status, ['ACTIVE', 'COMPLETED', 'CANCELLED'])) {
            Session::setFlash('error', 'Invalid status.');
            Helper::redirect('operations/plantation');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE plantation_projects SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);

        Session::setFlash('success', 'Project status updated successfully.');
        Helper::redirect("operations/plantation/view?id={$id}");
    }

    public function plantationHarvesting(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("SELECT * FROM plantation_projects WHERE id = {$id}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Plantation project not found.');
            Helper::redirect('operations/plantation');
        }

        // Fetch crops associated with this project
        $crops = $db->query("
            SELECT ppc.*, prod.name_en AS product_name, prod.product_code 
            FROM plantation_project_crops ppc 
            JOIN products prod ON ppc.product_id = prod.id 
            WHERE ppc.project_id = {$id}
        ")->fetchAll();

        // Fetch harvest logs for this project
        $harvests = $db->query("
            SELECT ph.*, prod.name_en AS product_name, prod.product_code 
            FROM plantation_harvests ph 
            JOIN products prod ON ph.product_id = prod.id 
            WHERE ph.project_id = {$id}
            ORDER BY ph.harvest_date DESC, ph.id DESC
        ")->fetchAll();

        // Calculate summary stats
        $totalQuantity = 0.00;
        $harvestsCount = count($harvests);
        $lastHarvestDate = '-';

        if ($harvestsCount > 0) {
            $lastHarvestDate = $harvests[0]['harvest_date'];
            foreach ($harvests as $h) {
                $totalQuantity += (float)$h['quantity'];
            }
        }

        // Group harvests by crop product_id to show total harvested per crop
        $cropHarvestTotals = [];
        foreach ($crops as $c) {
            $pid = (int)$c['product_id'];
            $cropHarvestTotals[$pid] = 0.00;
        }
        foreach ($harvests as $h) {
            $pid = (int)$h['product_id'];
            if (isset($cropHarvestTotals[$pid])) {
                $cropHarvestTotals[$pid] += (float)$h['quantity'];
            }
        }

        $this->render('operations/plantation_harvesting', [
            'pageTitle' => 'Yield Harvesting - ' . $project['project_name'],
            'activeNav' => 'ops_plantation',
            'project' => $project,
            'crops' => $crops,
            'harvests' => $harvests,
            'stats' => [
                'total_quantity' => $totalQuantity,
                'harvests_count' => $harvestsCount,
                'last_harvest_date' => $lastHarvestDate
            ],
            'cropHarvestTotals' => $cropHarvestTotals
        ]);
    }

    public function storePlantationHarvest(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $projectId = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
        $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $harvestDate = $_POST['harvest_date'] ?? date('Y-m-d');
        $quantity = (float)($_POST['quantity'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'Units');
        $qualityGrade = trim($_POST['quality_grade'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$projectId || !$productId || $quantity <= 0) {
            Session::setFlash('error', 'Project ID, Crop selection, and positive quantity are required.');
            if ($projectId) {
                Helper::redirect("operations/plantation/harvesting?id={$projectId}");
            } else {
                Helper::redirect("operations/plantation");
            }
        }

        $db = Database::getInstance();
        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            // Insert harvest record
            $stmt = $db->prepare("
                INSERT INTO plantation_harvests (project_id, product_id, harvest_date, quantity, unit, quality_grade, notes, created_by)
                VALUES (:project_id, :product_id, :harvest_date, :quantity, :unit, :quality_grade, :notes, :created_by)
            ");
            $userId = Auth::id() ?? 1;
            $stmt->execute([
                'project_id' => $projectId,
                'product_id' => $productId,
                'harvest_date' => $harvestDate,
                'quantity' => $quantity,
                'unit' => $unit,
                'quality_grade' => !empty($qualityGrade) ? $qualityGrade : null,
                'notes' => !empty($notes) ? $notes : null,
                'created_by' => $userId
            ]);
            $harvestId = (int)$db->lastInsertId();

            // Harvest is only recorded in the project; it is not yet transferred to marketplace inventory.

            if (!$inTransaction) {
                Database::commit();
            }

            Session::setFlash('success', 'Harvest successfully recorded and added to inventory balances.');

        } catch (\Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            Session::setFlash('error', 'Failed to record harvest: ' . $e->getMessage());
        }

        Helper::redirect("operations/plantation/harvesting?id={$projectId}");
    }

    public function plantationMarketplace(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("SELECT * FROM plantation_projects WHERE id = {$id}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Plantation project not found.');
            Helper::redirect('operations/plantation');
        }

        // Fetch harvests mapped to this project, including total quantity and already transferred quantity grouped by product
        $harvests = $db->query("
            SELECT prod.id AS product_id, prod.name_en AS product_name, prod.product_code,
                   prod.default_selling_price, prod.default_purchase_price, MAX(ph.unit) AS unit,
                   SUM(ph.quantity) AS quantity,
                   COALESCE(
                       (SELECT SUM(pht.quantity) 
                        FROM plantation_harvest_transfers pht
                        JOIN plantation_harvests ph2 ON pht.harvest_id = ph2.id
                        WHERE ph2.project_id = {$id} AND ph2.product_id = prod.id), 
                       0.00
                   ) AS transferred_quantity,
                   MAX(ph.harvest_date) AS harvest_date
            FROM plantation_harvests ph 
            JOIN products prod ON ph.product_id = prod.id 
            WHERE ph.project_id = {$id}
            GROUP BY prod.id, prod.name_en, prod.product_code, prod.default_selling_price, prod.default_purchase_price
            ORDER BY prod.product_code ASC
        ")->fetchAll();

        // Calculate summaries
        $totalHarvested = 0.00;
        $totalTransferred = 0.00;
        $remainingHarvest = 0.00;

        // Collect unique product ids in this project to get Marketplace on hand stock
        $productIds = [];
        foreach ($harvests as &$h) {
            $h['remaining_quantity'] = (float)$h['quantity'] - (float)$h['transferred_quantity'];
            $totalHarvested += (float)$h['quantity'];
            $totalTransferred += (float)$h['transferred_quantity'];
            $productIds[] = (int)$h['product_id'];
        }
        $remainingHarvest = $totalHarvested - $totalTransferred;

        $marketplaceQty = 0.00;
        if (!empty($productIds)) {
            $uniquePids = implode(',', array_unique($productIds));
            // Get sum of quantity on hand in Location ID = 1 (default warehouse) for these products
            $marketplaceQty = (float)$db->query("
                SELECT COALESCE(SUM(quantity_on_hand), 0.00) 
                FROM inventory_balances 
                WHERE location_id = 1 AND product_id IN ({$uniquePids})
            ")->fetchColumn();
        }

        // Calculate Cost Per Unit
        $totalExpenses = 0.00;

        $costPerUnit = $totalHarvested > 0 ? ($totalExpenses / $totalHarvested) : 0.00;

        // Fetch transfer history with costs and prices
        $transfers = $db->query("
            SELECT pht.*, prod.name_en AS product_name, prod.product_code, ph.harvest_date
            FROM plantation_harvest_transfers pht
            JOIN plantation_harvests ph ON pht.harvest_id = ph.id
            JOIN products prod ON ph.product_id = prod.id
            WHERE ph.project_id = {$id}
            ORDER BY pht.transfer_date DESC, pht.id DESC
        ")->fetchAll();

        $this->render('operations/plantation_marketplace', [
            'pageTitle' => 'Marketplace Transfers - ' . $project['project_name'],
            'activeNav' => 'ops_plantation',
            'project' => $project,
            'harvests' => $harvests,
            'transfers' => $transfers,
            'stats' => [
                'total_harvested' => $totalHarvested,
                'total_transferred' => $totalTransferred,
                'remaining_harvest' => $remainingHarvest,
                'marketplace_qty' => $marketplaceQty,
                'total_expenses' => $totalExpenses,
                'cost_per_unit' => $costPerUnit
            ]
        ]);
    }

    public function transferPlantationHarvest(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $projectId = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
        $productId = !empty($_POST['harvest_id']) ? (int)$_POST['harvest_id'] : 0;
        $quantity = (float)($_POST['quantity'] ?? 0);
        $transferDate = $_POST['transfer_date'] ?? date('Y-m-d');
        $costPrice = (float)($_POST['cost_price'] ?? 0);
        $sellingPrice = (float)($_POST['selling_price'] ?? 0);

        if (!$projectId || !$productId || $quantity <= 0) {
            Session::setFlash('error', 'Invalid parameters. Please specify a crop and positive transfer quantity.');
            if ($projectId) {
                Helper::redirect("operations/plantation/marketplace?id={$projectId}");
            } else {
                Helper::redirect("operations/plantation");
            }
        }

        $db = Database::getInstance();
        
        // Fetch all harvest records for this product and project
        $harvests = $db->query("
            SELECT ph.*,
                   COALESCE((SELECT SUM(quantity) FROM plantation_harvest_transfers WHERE harvest_id = ph.id), 0.00) AS transferred_quantity
            FROM plantation_harvests ph
            WHERE ph.project_id = {$projectId} AND ph.product_id = {$productId}
            ORDER BY ph.harvest_date ASC, ph.id ASC
        ")->fetchAll();

        if (empty($harvests)) {
            Session::setFlash('error', 'No harvests found for the selected crop.');
            Helper::redirect("operations/plantation/marketplace?id={$projectId}");
        }

        $totalRemaining = 0.00;
        foreach ($harvests as &$h) {
            $h['remaining'] = (float)$h['quantity'] - (float)$h['transferred_quantity'];
            $totalRemaining += $h['remaining'];
        }

        if (round($quantity, 4) > round($totalRemaining, 4)) {
            Session::setFlash('error', 'Cannot transfer more than the remaining quantity (' . number_format($totalRemaining, 2) . ').');
            Helper::redirect("operations/plantation/marketplace?id={$projectId}");
        }

        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }

        try {
            $remainingToTransfer = $quantity;
            $userId = Auth::id() ?? 1;
            $firstTransferId = null;

            // 1. Distribute quantity across harvest batches
            foreach ($harvests as $h) {
                if ($remainingToTransfer <= 0) {
                    break;
                }
                $avail = $h['remaining'];
                if ($avail <= 0) {
                    continue;
                }
                $transferQtyThisBatch = min($remainingToTransfer, $avail);

                $stmt = $db->prepare("
                    INSERT INTO plantation_harvest_transfers (harvest_id, transfer_date, quantity, cost_price_per_unit, selling_price_per_unit, created_by)
                    VALUES (:harvest_id, :transfer_date, :quantity, :cost_price, :selling_price, :created_by)
                ");
                $stmt->execute([
                    'harvest_id' => $h['id'],
                    'transfer_date' => $transferDate,
                    'quantity' => $transferQtyThisBatch,
                    'cost_price' => $costPrice,
                    'selling_price' => $sellingPrice,
                    'created_by' => $userId
                ]);

                $lastId = (int)$db->lastInsertId();
                if ($firstTransferId === null) {
                    $firstTransferId = $lastId;
                }

                $remainingToTransfer -= $transferQtyThisBatch;
            }

            // 2. Mark product as available in Marketplace and update default selling and purchase prices
            $db->prepare("
                UPDATE products 
                SET is_marketplace = 1, default_selling_price = :selling_price, default_purchase_price = :cost_price 
                WHERE id = :pid
            ")->execute([
                'selling_price' => $sellingPrice,
                'cost_price' => $costPrice,
                'pid' => $productId
            ]);

            // 3. Record physical stock IN using InventoryEngine at Location ID = 1
            \App\Services\InventoryEngine::recordStockIn(
                $productId,
                1,
                $quantity,
                $costPrice,
                'Transfer',
                'PLANTATION_HARVEST',
                $firstTransferId,
                'TRF-' . $firstTransferId
            );

            // 4. Overwrite average_cost and inventory_value in inventory_balances to match computed cost price exactly
            $db->prepare("
                UPDATE inventory_balances 
                SET average_cost = :cost_price, inventory_value = quantity_on_hand * :cost_price_val, updated_at = NOW()
                WHERE product_id = :pid AND location_id = 1
            ")->execute([
                'cost_price' => $costPrice,
                'cost_price_val' => $costPrice,
                'pid' => $productId
            ]);

            if (!$inTransaction) {
                Database::commit();
            }

            Session::setFlash('success', 'Harvest yield successfully transferred to Marketplace and product price updated.');

        } catch (\Exception $e) {
            if (!$inTransaction && Database::inTransaction()) {
                Database::rollBack();
            }
            Session::setFlash('error', 'Failed to transfer harvest: ' . $e->getMessage());
        }

        Helper::redirect("operations/plantation/marketplace?id={$projectId}");
    }

    public function machinery(): void {
        Auth::requirePermission('machinery.view');

        $db = Database::getInstance();

        // Fetch actual stats
        $available = (int)$db->query("SELECT COUNT(*) FROM machinery WHERE status = 'AVAILABLE'")->fetchColumn();
        $rented = (int)$db->query("SELECT COUNT(*) FROM machinery WHERE status = 'RENTED'")->fetchColumn();
        $activeRentals = (int)$db->query("SELECT COUNT(*) FROM machinery_rentals WHERE status = 'ACTIVE'")->fetchColumn();
        $completedRentals = (int)$db->query("SELECT COUNT(*) FROM machinery_rentals WHERE status = 'COMPLETED'")->fetchColumn();
        
        $revenue = (float)$db->query("
            SELECT COALESCE(SUM(inv.total), 0.00) 
            FROM machinery_rentals mr
            JOIN invoices inv ON mr.invoice_id = inv.id
            WHERE inv.status = 'POSTED'
        ")->fetchColumn();

        $this->render('operations/machinery', [
            'pageTitle' => 'Machinery Renting Operations Control Panel',
            'activeNav' => 'ops_machinery',
            'stats' => [
                'available' => $available,
                'rented' => $rented,
                'active_rentals' => $activeRentals,
                'completed_rentals' => $completedRentals,
                'revenue' => $revenue
            ]
        ]);
    }

    public function fruitPacking(): void {
        Auth::requirePermission('machinery.view');

        $this->render('operations/fruit-packing', [
            'pageTitle' => 'Fruit Packing Control Panel',
            'activeNav' => 'ops_fruit_packing'
        ]);
    }

    public function brickManufacturing(): void {
        Auth::requirePermission('machinery.view');

        $db = Database::getInstance();

        // 1. Fetch statistics
        $activeCount = (int)$db->query("SELECT COUNT(*) FROM brick_production_projects WHERE status = 'ACTIVE'")->fetchColumn();
        $completedCount = (int)$db->query("SELECT COUNT(*) FROM brick_production_projects WHERE status = 'COMPLETED'")->fetchColumn();
        
        // Sum posted expenses linked to Brick Manufacturing cost center (ID = 5) or linked directly to any brick project
        $totalExpenses = (float)$db->query("
            SELECT COALESCE(SUM(e.amount), 0.00) 
            FROM expenses e
            WHERE (e.cost_center_id = 5 OR e.project_id IN (SELECT id FROM brick_production_projects))
              AND e.status = 'posted'
        ")->fetchColumn();
        
        // Total bricks produced across all projects
        $totalProduced = (float)$db->query("
            SELECT COALESCE(SUM(quantity), 0.00) 
            FROM brick_production_records
        ")->fetchColumn();

        // 2. Fetch projects lists
        $activeProjects = $db->query("
            SELECT bpp.*, prod.name_en AS product_name, prod.product_code 
            FROM brick_production_projects bpp
            JOIN products prod ON bpp.product_id = prod.id
            WHERE bpp.status = 'ACTIVE' 
            ORDER BY bpp.start_date DESC
        ")->fetchAll();

        $completedProjects = $db->query("
            SELECT bpp.*, prod.name_en AS product_name, prod.product_code 
            FROM brick_production_projects bpp
            JOIN products prod ON bpp.product_id = prod.id
            WHERE bpp.status = 'COMPLETED' 
            ORDER BY bpp.start_date DESC
        ")->fetchAll();

        // Enrich projects with project-specific total expenses
        foreach ($activeProjects as &$p) {
            $p['total_expenses'] = (float)$db->query("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE project_id = {$p['id']} AND cost_center_id = 5 AND status = 'posted'
            ")->fetchColumn();
        }
        unset($p);

        foreach ($completedProjects as &$p) {
            $p['total_expenses'] = (float)$db->query("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE project_id = {$p['id']} AND cost_center_id = 5 AND status = 'posted'
            ")->fetchColumn();
        }
        unset($p);

        // Fetch products for dropdown
        $productModel = new ProductModel();
        $products = $productModel->getAll(['is_active' => 1]);

        $this->render('operations/brick-manufacturing', [
            'pageTitle' => 'Brick Manufacturing Operations',
            'activeNav' => 'ops_brick_manufacturing',
            'stats' => [
                'active_projects' => $activeCount,
                'completed_projects' => $completedCount,
                'total_expenses' => $totalExpenses,
                'total_produced' => $totalProduced
            ],
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
            'products' => $products
        ]);
    }

    public function storeBrickProject(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $db = Database::getInstance();

        $projectName = trim($_POST['project_name'] ?? '');
        $location = 'Yard';
        $startDate = trim($_POST['start_date'] ?? date('Y-m-d'));
        $expectedCompletionDate = null;
        $productId = (int)($_POST['product_id'] ?? 0);
        $plannedQuantity = 0.00;
        $unit = trim($_POST['unit'] ?? 'Pieces');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($projectName) || !$productId) {
            Session::setFlash('error', 'Project name and brick type are required.');
            Helper::redirect('operations/brick-manufacturing');
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO brick_production_projects (project_name, location, start_date, expected_completion_date, product_id, planned_quantity, unit, notes, status, created_by) 
                VALUES (:name, :location, :start_date, :completion_date, :product_id, :planned_qty, :unit, :notes, 'ACTIVE', :created_by)
            ");
            $stmt->execute([
                'name' => $projectName,
                'location' => $location,
                'start_date' => $startDate,
                'completion_date' => $expectedCompletionDate,
                'product_id' => $productId,
                'planned_qty' => $plannedQuantity,
                'unit' => $unit,
                'notes' => $notes,
                'created_by' => $_SESSION['user_id'] ?? 1
            ]);

            Session::setFlash('success', 'Production project started successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to start project: ' . $e->getMessage());
        }

        Helper::redirect('operations/brick-manufacturing');
    }

    public function viewBrickProject(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("
            SELECT bpp.*, prod.name_en AS product_name, prod.product_code 
            FROM brick_production_projects bpp
            JOIN products prod ON bpp.product_id = prod.id
            WHERE bpp.id = {$id}
        ")->fetch();

        if (!$project) {
            Session::setFlash('error', 'Brick Production project not found.');
            Helper::redirect('operations/brick-manufacturing');
        }

        // Calculate total from posted expenses linked to project and cost center = 5
        $totalExpenses = (float)$db->query("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM expenses 
            WHERE project_id = {$id} AND cost_center_id = 5 AND status = 'posted'
        ")->fetchColumn();

        // Fetch production stats
        $totalProduced = (float)$db->query("
            SELECT COALESCE(SUM(quantity), 0.00) 
            FROM brick_production_records 
            WHERE project_id = {$id}
        ")->fetchColumn();

        $productionRecordsCount = (int)$db->query("
            SELECT COUNT(*) 
            FROM brick_production_records 
            WHERE project_id = {$id}
        ")->fetchColumn();

        // Total transferred to marketplace
        $totalTransferred = (float)$db->query("
            SELECT COALESCE(SUM(quantity), 0.00) 
            FROM brick_transfers 
            WHERE project_id = {$id}
        ")->fetchColumn();

        $this->render('operations/brick_dashboard', [
            'pageTitle' => 'Brick Production Dashboard - ' . $project['project_name'],
            'activeNav' => 'ops_brick_manufacturing',
            'project' => $project,
            'totalExpenses' => $totalExpenses,
            'totalProduced' => $totalProduced,
            'productionRecordsCount' => $productionRecordsCount,
            'totalTransferred' => $totalTransferred
        ]);
    }

    public function updateBrickProjectStatus(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($id && in_array($status, ['ACTIVE', 'COMPLETED', 'CANCELLED'])) {
            $db = Database::getInstance();
            $db->prepare("UPDATE brick_production_projects SET status = :status WHERE id = :id")->execute([
                'status' => $status,
                'id' => $id
            ]);
            Session::setFlash('success', 'Project status updated successfully.');
        } else {
            Session::setFlash('error', 'Invalid request parameters.');
        }

        Helper::redirect('operations/brick-manufacturing/view?id=' . $id);
    }

    public function brickExpenses(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("SELECT * FROM brick_production_projects WHERE id = {$id}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Brick Production project not found.');
            Helper::redirect('operations/brick-manufacturing');
        }

        // Fetch posted expenses linked to this project (cost center 5)
        $expenses = $db->query("
            SELECT e.*, ec.name AS category_name, je.journal_number 
            FROM expenses e
            JOIN expense_categories ec ON e.expense_category_id = ec.id
            LEFT JOIN journal_entries je ON e.journal_entry_id = je.id
            WHERE e.project_id = {$id} AND e.cost_center_id = 5
            ORDER BY e.expense_date DESC, e.id DESC
        ")->fetchAll();

        // Calculate total
        $totalExpenses = 0.00;
        foreach ($expenses as $e) {
            if ($e['status'] === 'posted') {
                $totalExpenses += (float)$e['amount'];
            }
        }

        // Load data needed for the recording modal
        $expenseModel = new \App\Models\Expense();
        $categories = $expenseModel->getAllCategories();
        $costCenters = (new \App\Models\CostCenter())->getActive();
        $cashAccounts = $expenseModel->getCashAccounts();
        $bankAccounts = $expenseModel->getBankAccounts();
        $suppliers = $db->query("SELECT id, party_code AS supplier_code, name AS name_en FROM parties WHERE party_type IN ('SUPPLIER', 'BOTH') AND status = 'active' ORDER BY name ASC")->fetchAll();

        $this->render('operations/brick_expenses', [
            'pageTitle' => 'Brick Project Expenses - ' . $project['project_name'],
            'activeNav' => 'ops_brick_manufacturing',
            'project' => $project,
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'categories' => $categories,
            'costCenters' => $costCenters,
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'suppliers' => $suppliers
        ]);
    }

    public function brickProduction(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("
            SELECT bpp.*, prod.name_en AS product_name, prod.product_code 
            FROM brick_production_projects bpp
            JOIN products prod ON bpp.product_id = prod.id
            WHERE bpp.id = {$id}
        ")->fetch();

        if (!$project) {
            Session::setFlash('error', 'Brick Production project not found.');
            Helper::redirect('operations/brick-manufacturing');
        }

        // Fetch production records
        $productionRecords = $db->query("
            SELECT bpr.*, u.full_name AS creator_name
            FROM brick_production_records bpr
            LEFT JOIN users u ON bpr.created_by = u.id
            WHERE bpr.project_id = {$id}
            ORDER BY bpr.production_date DESC, bpr.id DESC
        ")->fetchAll();

        $totalProduced = 0.00;
        foreach ($productionRecords as $r) {
            $totalProduced += (float)$r['quantity'];
        }

        $this->render('operations/brick_production', [
            'pageTitle' => 'Brick Production Log - ' . $project['project_name'],
            'activeNav' => 'ops_brick_manufacturing',
            'project' => $project,
            'productionRecords' => $productionRecords,
            'totalProduced' => $totalProduced
        ]);
    }

    public function storeBrickProduction(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $db = Database::getInstance();

        $projectId = (int)($_POST['project_id'] ?? 0);
        $productionDate = trim($_POST['production_date'] ?? date('Y-m-d'));
        $quantity = (float)($_POST['quantity'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        $project = $db->query("SELECT * FROM brick_production_projects WHERE id = {$projectId}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Production project not found.');
            Helper::redirect('operations/brick-manufacturing');
        }

        if ($quantity <= 0) {
            Session::setFlash('error', 'Please enter a valid quantity.');
            Helper::redirect('operations/brick-manufacturing/production?id=' . $projectId);
        }

        try {
            $db->prepare("
                INSERT INTO brick_production_records (project_id, production_date, product_id, quantity, unit, notes, created_by)
                VALUES (:project_id, :production_date, :product_id, :quantity, :unit, :notes, :created_by)
            ")->execute([
                'project_id' => $projectId,
                'production_date' => $productionDate,
                'product_id' => $project['product_id'],
                'quantity' => $quantity,
                'unit' => $project['unit'],
                'notes' => $notes,
                'created_by' => $_SESSION['user_id'] ?? 1
            ]);

            Session::setFlash('success', 'Production batch logged successfully.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to record production: ' . $e->getMessage());
        }

        Helper::redirect('operations/brick-manufacturing/production?id=' . $projectId);
    }

    public function brickMarketplace(): void {
        Auth::requirePermission('machinery.view');

        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = Database::getInstance();

        $project = $db->query("
            SELECT bpp.*, prod.name_en AS product_name, prod.product_code, prod.default_selling_price 
            FROM brick_production_projects bpp
            JOIN products prod ON bpp.product_id = prod.id
            WHERE bpp.id = {$id}
        ")->fetch();

        if (!$project) {
            Session::setFlash('error', 'Brick Production project not found.');
            Helper::redirect('operations/brick-manufacturing');
        }

        // Calculate total posted expenses linked to project
        $totalExpenses = (float)$db->query("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM expenses 
            WHERE project_id = {$id} AND cost_center_id = 5 AND status = 'posted'
        ")->fetchColumn();

        // Calculate total quantity produced
        $totalProduced = (float)$db->query("
            SELECT COALESCE(SUM(quantity), 0.00) 
            FROM brick_production_records 
            WHERE project_id = {$id}
        ")->fetchColumn();

        // Cost per unit
        $costPerUnit = $totalProduced > 0 ? ($totalExpenses / $totalProduced) : 0.0000;

        // Fetch transfers
        $transfers = $db->query("
            SELECT bt.*, bpr.production_date, u.full_name AS creator_name
            FROM brick_transfers bt
            JOIN brick_production_records bpr ON bt.production_record_id = bpr.id
            LEFT JOIN users u ON bt.created_by = u.id
            WHERE bt.project_id = {$id}
            ORDER BY bt.transfer_date DESC, bt.id DESC
        ")->fetchAll();

        // Group transfers by production record to find remaining transferable quantities
        $productionRecords = $db->query("
            SELECT bpr.*,
                   (bpr.quantity - COALESCE((SELECT SUM(quantity) FROM brick_transfers WHERE production_record_id = bpr.id), 0.00)) AS remaining_quantity
            FROM brick_production_records bpr
            WHERE bpr.project_id = {$id}
            ORDER BY bpr.production_date ASC, bpr.id ASC
        ")->fetchAll();

        $totalTransferred = 0.00;
        $remainingQuantity = 0.00;
        foreach ($productionRecords as $pr) {
            $remainingQuantity += (float)$pr['remaining_quantity'];
            $totalTransferred += ((float)$pr['quantity'] - (float)$pr['remaining_quantity']);
        }

        // Get current selling price from inventory product catalog
        $currentSellingPrice = (float)$project['default_selling_price'];

        $this->render('operations/brick_marketplace', [
            'pageTitle' => 'Marketplace Transfer - ' . $project['project_name'],
            'activeNav' => 'ops_brick_manufacturing',
            'project' => $project,
            'totalExpenses' => $totalExpenses,
            'totalProduced' => $totalProduced,
            'costPerUnit' => $costPerUnit,
            'transfers' => $transfers,
            'productionRecords' => $productionRecords,
            'remainingQuantity' => $remainingQuantity,
            'totalTransferred' => $totalTransferred,
            'currentSellingPrice' => $currentSellingPrice
        ]);
    }

    public function transferBrickProduction(): void {
        Auth::requirePermission('machinery.view');
        $this->validateCsrf();

        $db = Database::getInstance();

        $projectId = (int)($_POST['project_id'] ?? 0);
        $transferDate = trim($_POST['transfer_date'] ?? date('Y-m-d'));
        $quantity = (float)($_POST['quantity'] ?? 0);
        $sellingPrice = (float)($_POST['selling_price'] ?? 0);

        $project = $db->query("SELECT * FROM brick_production_projects WHERE id = {$projectId}")->fetch();
        if (!$project) {
            Session::setFlash('error', 'Production project not found.');
            Helper::redirect('operations/brick-manufacturing');
        }

        if ($quantity <= 0 || $sellingPrice <= 0) {
            Session::setFlash('error', 'Please enter valid transfer quantity and selling price.');
            Helper::redirect('operations/brick-manufacturing/marketplace?id=' . $projectId);
        }

        // Compute cost per unit from posted expenses and total production
        $totalExpenses = (float)$db->query("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM expenses 
            WHERE project_id = {$projectId} AND cost_center_id = 5 AND status = 'posted'
        ")->fetchColumn();

        $totalProduced = (float)$db->query("
            SELECT COALESCE(SUM(quantity), 0.00) 
            FROM brick_production_records 
            WHERE project_id = {$projectId}
        ")->fetchColumn();

        $costPerUnit = $totalProduced > 0 ? ($totalExpenses / $totalProduced) : 0.0000;

        // Fetch production records with remaining quantities chronologically
        $productionRecords = $db->query("
            SELECT bpr.*,
                   (bpr.quantity - COALESCE((SELECT SUM(quantity) FROM brick_transfers WHERE production_record_id = bpr.id), 0.00)) AS remaining_quantity
            FROM brick_production_records bpr
            WHERE bpr.project_id = {$projectId}
            ORDER BY bpr.production_date ASC, bpr.id ASC
        ")->fetchAll();

        $totalAvailable = 0.00;
        foreach ($productionRecords as $pr) {
            $totalAvailable += (float)$pr['remaining_quantity'];
        }

        if ($quantity > $totalAvailable) {
            Session::setFlash('error', 'Requested transfer quantity exceeds remaining available quantity.');
            Helper::redirect('operations/brick-manufacturing/marketplace?id=' . $projectId);
        }

        try {
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) {
                $db->beginTransaction();
            }

            // 1. Distribute quantity chronologically and save transfers
            $remainingToTransfer = $quantity;
            $firstTransferId = null;

            $transferStmt = $db->prepare("
                INSERT INTO brick_transfers (project_id, production_record_id, transfer_date, quantity, cost_price_per_unit, selling_price_per_unit, created_by)
                VALUES (:project_id, :production_record_id, :transfer_date, :quantity, :cost, :selling, :created_by)
            ");

            foreach ($productionRecords as $pr) {
                if ($remainingToTransfer <= 0) break;
                $rem = (float)$pr['remaining_quantity'];
                if ($rem <= 0) continue;

                $take = min($remainingToTransfer, $rem);
                $transferStmt->execute([
                    'project_id' => $projectId,
                    'production_record_id' => $pr['id'],
                    'transfer_date' => $transferDate,
                    'quantity' => $take,
                    'cost' => $costPerUnit,
                    'selling' => $sellingPrice,
                    'created_by' => $_SESSION['user_id'] ?? 1
                ]);

                if (!$firstTransferId) {
                    $firstTransferId = (int)$db->lastInsertId();
                }

                $remainingToTransfer -= $take;
            }

            // 2. Enable product for marketplace and update catalog prices
            $db->prepare("
                UPDATE products 
                SET is_marketplace = 1, default_selling_price = :sell_price, default_purchase_price = :cost_price 
                WHERE id = :pid
            ")->execute([
                'sell_price' => $sellingPrice,
                'cost_price' => $costPerUnit,
                'pid' => $project['product_id']
            ]);

            // 3. Record physical stock IN using InventoryEngine at Location ID = 1
            \App\Services\InventoryEngine::recordStockIn(
                $project['product_id'],
                1,
                $quantity,
                $costPerUnit,
                'Transfer',
                'BRICK_PRODUCTION',
                $firstTransferId,
                'BRK-' . $firstTransferId
            );

            // 4. Overwrite average_cost and inventory_value in inventory_balances to match computed cost price exactly
            $db->prepare("
                UPDATE inventory_balances 
                SET average_cost = :cost_price, inventory_value = quantity_on_hand * :cost_price_val, updated_at = NOW()
                WHERE product_id = :pid AND location_id = 1
            ")->execute([
                'cost_price' => $costPerUnit,
                'cost_price_val' => $costPerUnit,
                'pid' => $project['product_id']
            ]);

            if (!$inTransaction) {
                $db->commit();
            }

            Session::setFlash('success', 'Production transferred to marketplace successfully.');
        } catch (\Exception $e) {
            if (!$inTransaction) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Failed to transfer production: ' . $e->getMessage());
        }

        Helper::redirect('operations/brick-manufacturing/marketplace?id=' . $projectId);
    }

    public function construction(): void {
        Auth::requirePermission('machinery.view');

        $this->render('operations/construction', [
            'pageTitle' => 'Construction Projects Control Panel',
            'activeNav' => 'ops_construction'
        ]);
    }

    public function grindingMill(): void {
        Auth::requirePermission('machinery.view');

        $this->render('operations/grinding-mill', [
            'pageTitle' => 'Grinding Mill Control Panel',
            'activeNav' => 'ops_grinding_mill'
        ]);
    }
}
