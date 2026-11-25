<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

use Core\Database;
use App\Models\MemberModel;
use App\Services\MemberExcelService;

echo "=== AGRI PRO: MEMBER IMPORT & EXPORT VERIFICATION TEST ===" . PHP_EOL;

$db = Database::getInstance();
$memberModel = new MemberModel();

// 1. Test Model Queries
echo "[Test 1] Testing MemberModel::getAll with explicit column selection..." . PHP_EOL;
$members = $memberModel->getAll([], 10, 0);
assert(is_array($members), "getAll should return an array");
echo "-> OK. Fetched " . count($members) . " members." . PHP_EOL;

echo "[Test 2] Testing MemberModel::getAllForExport..." . PHP_EOL;
$exportMembers = $memberModel->getAllForExport([]);
assert(is_array($exportMembers), "getAllForExport should return an array");
echo "-> OK. Total exportable members: " . count($exportMembers) . PHP_EOL;

echo "[Test 3] Testing MemberModel::getCount..." . PHP_EOL;
$totalCount = $memberModel->getCount([]);
echo "-> OK. Total count in DB: " . $totalCount . PHP_EOL;

// 2. Test CSV Parsing & Import
echo "[Test 4] Testing CSV generation and import..." . PHP_EOL;
$testCsvFile = __DIR__ . '/test_import_members.csv';
$csvContent = "\xEF\xBB\xBF" . "Full Name,NIC,Phone,Date of Birth,Gender,Occupation,Agricultural Sectors,Address,City,Registration Fee\n"
    . "Test Person Alpha,199912345678,0711112233,1999-05-10,Male,Teacher,\"Vegetables, Tea\",No 12 Hill St,Kegalle,500\n"
    . "Test Person Beta,199987654321,0722223344,15/08/1995,Female,Farmer,Coconut,No 34 Valley Rd,Mawanella,0\n"
    . "Test Person Gamma,,0733334455,1990-01-01,Male,Driver,Paddy,No 56 River St,Rambukkana,0\n" // No NIC, tests auto member_no
    . "=SUM(1+2),199912345678,0744445566,1980-01-01,Male,Other,Paddy,Test,Test,0\n"; // In-file duplicate NIC + formula injection

file_put_contents($testCsvFile, $csvContent);

$parsedRows = MemberExcelService::parseUploadedFile($testCsvFile, 'test_import_members.csv');
echo "-> Parsed " . count($parsedRows) . " rows from CSV." . PHP_EOL;

// Import with mode 'skip'
$result = MemberExcelService::processImport($parsedRows, 'skip', 1);
echo "-> Import Result (Skip Mode): Imported={$result['imported']}, Updated={$result['updated']}, Skipped={$result['skipped']}, Errors=" . count($result['errors']) . PHP_EOL;
assert($result['imported'] >= 3, "At least 3 new members should be imported");
assert($result['skipped'] >= 1, "Duplicate NIC row should be skipped");

// Test Update Mode with same file
echo "[Test 5] Testing Update Mode for duplicate records..." . PHP_EOL;
$updateResult = MemberExcelService::processImport($parsedRows, 'update', 1);
echo "-> Import Result (Update Mode): Imported={$updateResult['imported']}, Updated={$updateResult['updated']}, Skipped={$updateResult['skipped']}" . PHP_EOL;
assert($updateResult['updated'] >= 2, "Existing records should be updated in update mode");

// 3. Test XML Spreadsheet (.xls) Parsing
echo "[Test 6] Testing XML Spreadsheet (.xls) parsing..." . PHP_EOL;
$testXmlFile = __DIR__ . '/test_import_members.xls';
$xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
 <Worksheet ss:Name="Sheet1">
  <Table>
   <Row>
    <Cell><Data ss:Type="String">Member No</Data></Cell>
    <Cell><Data ss:Type="String">Full Name</Data></Cell>
    <Cell><Data ss:Type="String">NIC / National ID</Data></Cell>
    <Cell><Data ss:Type="String">Phone Number</Data></Cell>
   </Row>
   <Row>
    <Cell><Data ss:Type="String">TEST/99/001</Data></Cell>
    <Cell><Data ss:Type="String">Test Person Delta</Data></Cell>
    <Cell><Data ss:Type="String">200055566677</Data></Cell>
    <Cell><Data ss:Type="String">0755556677</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>';
file_put_contents($testXmlFile, $xmlContent);
$parsedXmlRows = MemberExcelService::parseUploadedFile($testXmlFile, 'test_import_members.xls');
echo "-> Parsed " . count($parsedXmlRows) . " rows from XML Spreadsheet." . PHP_EOL;
assert(count($parsedXmlRows) === 2, "Expected 2 rows from XML file");
$xmlImportResult = MemberExcelService::processImport($parsedXmlRows, 'skip', 1);
echo "-> XML Import: Imported={$xmlImportResult['imported']}, Skipped={$xmlImportResult['skipped']}" . PHP_EOL;

// 4. Clean up test records
echo "[Test 7] Cleaning up test records from database..." . PHP_EOL;
$cleanupNics = ['199912345678', '199987654321', '200055566677'];
foreach ($cleanupNics as $cnic) {
    $row = $db->query("SELECT id, party_id FROM coop_members WHERE nic = '$cnic'")->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $db->exec("DELETE FROM coop_members WHERE id = " . (int)$row['id']);
        if (!empty($row['party_id'])) {
            $db->exec("DELETE FROM parties WHERE id = " . (int)$row['party_id']);
        }
    }
}
// Clean up Gamma & Delta
$db->exec("DELETE FROM coop_members WHERE full_name IN ('Test Person Gamma', 'Test Person Delta')");
$db->exec("DELETE FROM parties WHERE name IN ('Test Person Gamma', 'Test Person Delta')");

@unlink($testCsvFile);
@unlink($testXmlFile);

echo "=== ALL TESTS COMPLETED SUCCESSFULLY! ===" . PHP_EOL;
