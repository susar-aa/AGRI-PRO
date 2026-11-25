<?php
namespace App\Services;

use Core\Database;
use Core\Auth;
use App\Models\MemberModel;
use App\Models\Party;
use ZipArchive;
use SimpleXMLElement;
use Exception;

class MemberExcelService {

    /**
     * Define standardized column keys and display labels
     */
    public const COLUMNS = [
        'member_no'           => ['label' => 'Member No',           'type' => 'String', 'width' => 120, 'required' => false, 'hint' => 'Unique code e.g. AGC/25/001 (leave empty for auto-generation)'],
        'full_name'           => ['label' => 'Full Name',           'type' => 'String', 'width' => 200, 'required' => true,  'hint' => 'Full legal name of the member'],
        'nic'                 => ['label' => 'NIC / National ID',   'type' => 'String', 'width' => 130, 'required' => false, 'hint' => 'Sri Lankan NIC (e.g. 196136204872 or 643481090V)'],
        'dob'                 => ['label' => 'Date of Birth',       'type' => 'String', 'width' => 100, 'required' => false, 'hint' => 'YYYY-MM-DD or DD/MM/YYYY'],
        'gender'              => ['label' => 'Gender',              'type' => 'String', 'width' => 80,  'required' => false, 'hint' => 'Male, Female, or Other'],
        'phone'               => ['label' => 'Phone Number',        'type' => 'String', 'width' => 110, 'required' => false, 'hint' => 'e.g. 0712345678'],
        'whatsapp'            => ['label' => 'WhatsApp Number',     'type' => 'String', 'width' => 110, 'required' => false, 'hint' => 'e.g. 0712345678'],
        'email'               => ['label' => 'Email Address',       'type' => 'String', 'width' => 160, 'required' => false, 'hint' => 'Valid email address'],
        'occupation'          => ['label' => 'Occupation',          'type' => 'String', 'width' => 130, 'required' => false, 'hint' => 'e.g. Farmer, Teacher, Retired'],
        'agricultural_sector' => ['label' => 'Agricultural Sectors', 'type' => 'String', 'width' => 180, 'required' => false, 'hint' => 'Comma separated (e.g. Paddy, Coconut, Banana)'],
        'address'             => ['label' => 'Address',             'type' => 'String', 'width' => 220, 'required' => false, 'hint' => 'Permanent residential address'],
        'city'                => ['label' => 'City',                'type' => 'String', 'width' => 110, 'required' => false, 'hint' => 'City / Town e.g. Rambukkana'],
        'registration_date'   => ['label' => 'Registration Date',   'type' => 'String', 'width' => 110, 'required' => false, 'hint' => 'YYYY-MM-DD (defaults to today)'],
        'membership_type'     => ['label' => 'Membership Type',     'type' => 'String', 'width' => 110, 'required' => false, 'hint' => 'Ordinary, Life, Associate'],
        'status'              => ['label' => 'Status',              'type' => 'String', 'width' => 90,  'required' => false, 'hint' => 'ACTIVE, INACTIVE, SUSPENDED, RESIGNED'],
        'registration_fee'    => ['label' => 'Registration Fee',    'type' => 'Number', 'width' => 110, 'required' => false, 'hint' => 'Amount e.g. 500.00'],
        'shares_fee'          => ['label' => 'Shares Fee',          'type' => 'Number', 'width' => 110, 'required' => false, 'hint' => 'Amount e.g. 1000.00'],
        'payment_method'      => ['label' => 'Payment Method',      'type' => 'String', 'width' => 110, 'required' => false, 'hint' => 'Unpaid, Cash, Bank Transfer, Cheque'],
        'payment_status'      => ['label' => 'Payment Status',      'type' => 'String', 'width' => 100, 'required' => false, 'hint' => 'UNPAID, PAID'],
        'heir_name'           => ['label' => 'Heir Name',           'type' => 'String', 'width' => 160, 'required' => false, 'hint' => 'Nominated heir full name'],
        'heir_nic'            => ['label' => 'Heir NIC',            'type' => 'String', 'width' => 120, 'required' => false, 'hint' => 'Heir national identity number'],
        'heir_contact_number' => ['label' => 'Heir Contact Number', 'type' => 'String', 'width' => 120, 'required' => false, 'hint' => 'Heir phone number'],
        'heir_address'        => ['label' => 'Heir Address',        'type' => 'String', 'width' => 180, 'required' => false, 'hint' => 'Heir residential address'],
        'notes'               => ['label' => 'Notes',               'type' => 'String', 'width' => 180, 'required' => false, 'hint' => 'Additional remarks or references']
    ];

    /**
     * Map common external column header variations to internal keys
     */
    public static function getHeaderMap(): array {
        return [
            'member_no'           => 'member_no',
            'member no'           => 'member_no',
            'member number'       => 'member_no',
            'membership_no'       => 'member_no',
            'membership no'       => 'member_no',
            'registration number' => 'member_no',
            'reg no'              => 'member_no',
            'reg_no'              => 'member_no',
            'member id'           => 'member_no',
            'code'                => 'member_no',

            'full_name'           => 'full_name',
            'full name'           => 'full_name',
            'name'                => 'full_name',
            'member name'         => 'full_name',
            'member_name'         => 'full_name',

            'nic'                 => 'nic',
            'nic / national id'   => 'nic',
            'national id'         => 'nic',
            'national_id'         => 'nic',
            'nic_no'              => 'nic',
            'nic no'              => 'nic',
            'identity'            => 'nic',

            'dob'                 => 'dob',
            'date of birth'       => 'dob',
            'birth date'          => 'dob',
            'birth_date'          => 'dob',
            'birthdate'           => 'dob',

            'gender'              => 'gender',
            'sex'                 => 'gender',

            'phone'               => 'phone',
            'phone number'        => 'phone',
            'contact'             => 'phone',
            'contact number'      => 'phone',
            'mobile'              => 'phone',
            'mobile number'       => 'phone',
            'telephone'           => 'phone',

            'whatsapp'            => 'whatsapp',
            'whatsapp number'     => 'whatsapp',
            'wa'                  => 'whatsapp',
            'whatsapp_no'         => 'whatsapp',

            'email'               => 'email',
            'email address'       => 'email',
            'e-mail'              => 'email',

            'occupation'          => 'occupation',
            'job'                 => 'occupation',
            'profession'          => 'occupation',

            'agricultural_sector' => 'agricultural_sector',
            'agricultural sectors'=> 'agricultural_sector',
            'agricultural sector' => 'agricultural_sector',
            'sectors'             => 'agricultural_sector',
            'sector'              => 'agricultural_sector',
            'crops'               => 'agricultural_sector',

            'address'             => 'address',
            'home address'        => 'address',
            'permanent address'   => 'address',

            'city'                => 'city',
            'town'                => 'city',

            'registration_date'   => 'registration_date',
            'registration date'   => 'registration_date',
            'reg date'            => 'registration_date',
            'joined date'         => 'registration_date',
            'date joined'         => 'registration_date',

            'membership_type'     => 'membership_type',
            'membership type'     => 'membership_type',
            'member type'         => 'membership_type',

            'status'              => 'status',
            'member status'       => 'status',

            'registration_fee'    => 'registration_fee',
            'registration fee'    => 'registration_fee',
            'reg fee'             => 'registration_fee',

            'shares_fee'          => 'shares_fee',
            'shares fee'          => 'shares_fee',
            'share fee'           => 'shares_fee',

            'payment_method'      => 'payment_method',
            'payment method'      => 'payment_method',

            'payment_status'      => 'payment_status',
            'payment status'      => 'payment_status',

            'heir_name'           => 'heir_name',
            'heir name'           => 'heir_name',
            'nominee name'        => 'heir_name',
            'nominee'             => 'heir_name',

            'heir_nic'            => 'heir_nic',
            'heir nic'            => 'heir_nic',
            'nominee nic'         => 'heir_nic',

            'heir_contact_number' => 'heir_contact_number',
            'heir contact number' => 'heir_contact_number',
            'heir contact'        => 'heir_contact_number',
            'heir phone'          => 'heir_contact_number',
            'nominee phone'       => 'heir_contact_number',

            'heir_address'        => 'heir_address',
            'heir address'        => 'heir_address',
            'nominee address'     => 'heir_address',

            'notes'               => 'notes',
            'note'                => 'notes',
            'remarks'             => 'notes',
            'comments'            => 'notes'
        ];
    }

    /**
     * Export members list to styled Excel XML Spreadsheet (.xls)
     */
    public static function exportExcel(array $members, array $company = [], string $filename = ''): void {
        if (empty($filename)) {
            $filename = 'Society_Members_Export_' . date('Ymd_His') . '.xls';
        }

        $companyName = htmlspecialchars($company['company_name'] ?? 'Agri Co-Op Society Limited', ENT_QUOTES, 'UTF-8');
        $exportDate = date('Y-m-d H:i:s');
        $totalCount = count($members);

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
        header('Pragma: public');

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author><?= $companyName; ?></Author>
  <Created><?= date('c'); ?></Created>
  <Company><?= $companyName; ?></Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" x:Family="Swiss" ss:Size="10" ss:Color="#1F2937"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="TitleStyle">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="14" ss:Color="#1B4332" ss:Bold="1"/>
  </Style>
  <Style ss:ID="MetaStyle">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#4B5563" ss:Italic="1"/>
  </Style>
  <Style ss:ID="HeaderStyle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#1B4332" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataString">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
  </Style>
  <Style ss:ID="DataStringAlt">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
   <Interior ss:Color="#F9FAFB" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
  </Style>
  <Style ss:ID="DataCenterAlt">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
   <Interior ss:Color="#F9FAFB" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataCurrency">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="DataCurrencyAlt">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
   <Interior ss:Color="#F9FAFB" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="StatusActive">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#15803D" ss:Bold="1"/>
   <Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="StatusInactive">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#B91C1C" ss:Bold="1"/>
   <Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Society Members">
  <Table ss:DefaultRowHeight="20">
<?php foreach (self::COLUMNS as $col): ?>
   <Column ss:Width="<?= (int)$col['width']; ?>"/>
<?php endforeach; ?>

   <!-- Title Section -->
   <Row ss:Height="26">
    <Cell ss:MergeAcross="<?= count(self::COLUMNS) - 1; ?>" ss:StyleID="TitleStyle">
     <Data ss:Type="String"><?= $companyName; ?> - Society Members Directory</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="<?= count(self::COLUMNS) - 1; ?>" ss:StyleID="MetaStyle">
     <Data ss:Type="String">Generated on <?= $exportDate; ?> | Total Members: <?= $totalCount; ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="8"/>

   <!-- Table Header -->
   <Row ss:Height="24">
<?php foreach (self::COLUMNS as $col): ?>
    <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String"><?= htmlspecialchars($col['label'], ENT_QUOTES, 'UTF-8'); ?></Data></Cell>
<?php endforeach; ?>
   </Row>

   <!-- Table Data Rows -->
<?php
$rowIndex = 0;
foreach ($members as $m):
    $rowIndex++;
    $isAlt = ($rowIndex % 2 === 0);
    $strStyle = $isAlt ? 'DataStringAlt' : 'DataString';
    $ctrStyle = $isAlt ? 'DataCenterAlt' : 'DataCenter';
    $curStyle = $isAlt ? 'DataCurrencyAlt' : 'DataCurrency';
    $status = strtoupper($m['status'] ?? 'ACTIVE');
    $statusStyle = ($status === 'ACTIVE') ? 'StatusActive' : (($status === 'INACTIVE') ? 'StatusInactive' : $ctrStyle);
?>
   <Row ss:Height="20">
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['member_no'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['full_name'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['nic'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['dob'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['gender'] ?? 'Male'); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['phone'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['whatsapp'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['email'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['occupation'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['agricultural_sector'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['address'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['city'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['registration_date'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['membership_type'] ?? 'Ordinary'); ?></Data></Cell>
    <Cell ss:StyleID="<?= $statusStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($status); ?></Data></Cell>
    <Cell ss:StyleID="<?= $curStyle; ?>"><Data ss:Type="Number"><?= (float)($m['registration_fee'] ?? 0); ?></Data></Cell>
    <Cell ss:StyleID="<?= $curStyle; ?>"><Data ss:Type="Number"><?= (float)($m['shares_fee'] ?? 0); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['payment_method'] ?? 'Unpaid'); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['payment_status'] ?? 'UNPAID'); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['heir_name'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['heir_nic'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $ctrStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['heir_contact_number'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['heir_address'] ?? ''); ?></Data></Cell>
    <Cell ss:StyleID="<?= $strStyle; ?>"><Data ss:Type="String"><?= self::sanitizeXml($m['notes'] ?? ''); ?></Data></Cell>
   </Row>
<?php endforeach; ?>
  </Table>
 </Worksheet>
</Workbook>
<?php
        exit;
    }

    /**
     * Download pre-configured Excel Import Template with instructions and sample rows
     */
    public static function downloadTemplate(): void {
        $filename = 'Society_Members_Import_Template.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, must-revalidate');
        header('Pragma: public');

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" x:Family="Swiss" ss:Size="10" ss:Color="#1F2937"/>
  </Style>
  <Style ss:ID="HeaderReq">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#1B4332" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="HeaderOpt">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#143426"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#2D6A4F" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="HintRow">
   <Alignment ss:Horizontal="Left" ss:Vertical="Top" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="8" ss:Color="#6B7280" ss:Italic="1"/>
   <Interior ss:Color="#F3F4F6" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="SampleData">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
  </Style>
  <Style ss:ID="SampleNumber">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="SampleCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1F2937"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Members Import">
  <Table ss:DefaultRowHeight="20">
<?php foreach (self::COLUMNS as $col): ?>
   <Column ss:Width="<?= (int)$col['width']; ?>"/>
<?php endforeach; ?>

   <!-- Header Row -->
   <Row ss:Height="24">
<?php foreach (self::COLUMNS as $key => $col): 
    $style = !empty($col['required']) ? 'HeaderReq' : 'HeaderOpt';
    $label = $col['label'] . (!empty($col['required']) ? ' *' : '');
?>
    <Cell ss:StyleID="<?= $style; ?>"><Data ss:Type="String"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></Data></Cell>
<?php endforeach; ?>
   </Row>

   <!-- Hints / Format Instructions Row -->
   <Row ss:Height="28">
<?php foreach (self::COLUMNS as $col): ?>
    <Cell ss:StyleID="HintRow"><Data ss:Type="String"><?= htmlspecialchars($col['hint'], ENT_QUOTES, 'UTF-8'); ?></Data></Cell>
<?php endforeach; ?>
   </Row>

   <!-- Sample Row 1 -->
   <Row ss:Height="20">
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">AGC/25/001</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Sunil Shantha Jayawardena</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">197514201840</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">1975-04-12</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">Male</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">0771234567</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">0771234567</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">sunil.j@gmail.com</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Farmer</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Paddy, Vegetable, Coconut</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">No. 45, Temple Road, Gangoda</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Rambukkana</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">2025-01-15</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">Ordinary</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">ACTIVE</Data></Cell>
    <Cell ss:StyleID="SampleNumber"><Data ss:Type="Number">500.00</Data></Cell>
    <Cell ss:StyleID="SampleNumber"><Data ss:Type="Number">1000.00</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">Cash</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">PAID</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Kusumawathi Jayawardena</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">785123456V</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">0719876543</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Same address</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Registered member</Data></Cell>
   </Row>

   <!-- Sample Row 2 (Showing Auto-ID and optional fields) -->
   <Row ss:Height="20">
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Anula Kumari Herath</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">826541230V</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">1982-08-20</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">Female</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">0786543210</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">0786543210</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Agriculturalist</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Tea, Banana</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Main Street</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String">Mawanella</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String"><?= date('Y-m-d'); ?></Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">Ordinary</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">ACTIVE</Data></Cell>
    <Cell ss:StyleID="SampleNumber"><Data ss:Type="Number">0.00</Data></Cell>
    <Cell ss:StyleID="SampleNumber"><Data ss:Type="Number">0.00</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">Unpaid</Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String">UNPAID</Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="SampleCenter"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="SampleData"><Data ss:Type="String"></Data></Cell>
   </Row>

  </Table>
 </Worksheet>
</Workbook>
<?php
        exit;
    }

    /**
     * Parse uploaded file (.xlsx, .xls, .csv) into structured raw rows
     */
    public static function parseUploadedFile(string $filePath, string $originalFilename): array {
        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

        if ($ext === 'xlsx') {
            return self::parseXlsx($filePath);
        } elseif ($ext === 'csv') {
            return self::parseCsv($filePath);
        } elseif ($ext === 'xls' || $ext === 'xml') {
            return self::parseXlsOrXml($filePath);
        } else {
            throw new Exception("Unsupported file format (.{$ext}). Please upload an .xlsx, .xls, or .csv file.");
        }
    }

    /**
     * Parse modern OpenXML (.xlsx) files using native PHP ZipArchive + SimpleXML
     */
    private static function parseXlsx(string $filePath): array {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Unable to open Excel (.xlsx) file archive.");
        }

        // 1. Extract shared strings table
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Extract first worksheet
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlContent === false) {
            // Try looking for any sheet
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('#^xl/worksheets/sheet\d+\.xml$#i', $name)) {
                    $sheetXmlContent = $zip->getFromName($name);
                    break;
                }
            }
        }
        $zip->close();

        if ($sheetXmlContent === false) {
            throw new Exception("No valid worksheet found inside the Excel file.");
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowNum = (int)$row['r'];
            $cells = [];

            foreach ($row->c as $c) {
                $ref = (string)$c['r']; // e.g. A1, B3
                $colLetters = preg_replace('/[0-9]/', '', $ref);
                $colIdx = self::colLetterToIndex($colLetters);

                $cellType = (string)$c['t'];
                $val = '';

                if ($cellType === 's') { // shared string
                    $sIndex = (int)$c->v;
                    $val = $sharedStrings[$sIndex] ?? '';
                } elseif ($cellType === 'inlineStr' && isset($c->is->t)) {
                    $val = (string)$c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string)$c->v;
                }

                $cells[$colIdx] = trim($val);
            }

            if (!empty($cells)) {
                // Fill any missing middle columns with empty string
                $maxCol = max(array_keys($cells));
                $fullRow = [];
                for ($col = 0; $col <= $maxCol; $col++) {
                    $fullRow[$col] = $cells[$col] ?? '';
                }
                $rows[$rowNum] = $fullRow;
            }
        }

        return array_values($rows);
    }

    /**
     * Parse CSV file with auto delimiter detection and UTF-8 handling
     */
    private static function parseCsv(string $filePath): array {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Unable to open CSV file.");
        }

        // Read first line to detect delimiter and check BOM
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        // Strip UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        if (substr($firstLine, 0, 3) === $bom) {
            $firstLine = substr($firstLine, 3);
        }

        // Delimiter detection
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        rewind($handle);
        // Skip BOM if present
        $headerCheck = fread($handle, 3);
        if ($headerCheck !== $bom) {
            rewind($handle);
        }

        $rows = [];
        while (($data = fgetcsv($handle, 10000, $bestDelimiter)) !== false) {
            // Trim all cell values
            $cleanRow = array_map(function($val) {
                $val = trim((string)$val);
                // Strip formula injection prefixes
                if (isset($val[0]) && in_array($val[0], ['=', '+', '-', '@'])) {
                    $val = ltrim($val, "=+-@'");
                }
                return $val;
            }, $data);

            // Ignore totally empty lines
            if (array_filter($cleanRow) !== []) {
                $rows[] = $cleanRow;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse XML Spreadsheet 2003 or HTML table (.xls / .xml)
     */
    private static function parseXlsOrXml(string $filePath): array {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new Exception("Unable to read file contents.");
        }

        // Check if XML Spreadsheet format
        if (stripos($content, 'urn:schemas-microsoft-com:office:spreadsheet') !== false || stripos($content, '<Workbook') !== false) {
            $dom = new \DOMDocument();
            if (@$dom->loadXML($content)) {
                $rows = [];
                $tableRows = $dom->getElementsByTagName('Row');
                foreach ($tableRows as $tr) {
                    $rowCells = [];
                    $colIdx = 0;
                    $cells = $tr->getElementsByTagName('Cell');
                    foreach ($cells as $cell) {
                        if ($cell->hasAttribute('ss:Index')) {
                            $colIdx = (int)$cell->getAttribute('ss:Index') - 1;
                        } elseif ($cell->hasAttribute('Index')) {
                            $colIdx = (int)$cell->getAttribute('Index') - 1;
                        }
                        $dataNodes = $cell->getElementsByTagName('Data');
                        $val = ($dataNodes->length > 0) ? $dataNodes->item(0)->textContent : $cell->textContent;
                        $rowCells[$colIdx] = trim($val);
                        $colIdx++;
                    }
                    if (!empty($rowCells) && array_filter($rowCells) !== []) {
                        $maxCol = max(array_keys($rowCells));
                        $fullRow = [];
                        for ($c = 0; $c <= $maxCol; $c++) {
                            $fullRow[$c] = $rowCells[$c] ?? '';
                        }
                        $rows[] = $fullRow;
                    }
                }
                if (!empty($rows)) {
                    return $rows;
                }
            }
        }

        // Fallback: Check if HTML table (.xls saved as HTML)
        if (stripos($content, '<table') !== false) {
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            $tableRows = $dom->getElementsByTagName('tr');
            $rows = [];
            foreach ($tableRows as $tr) {
                $cells = [];
                foreach ($tr->childNodes as $node) {
                    if (in_array(strtolower($node->nodeName), ['td', 'th'])) {
                        $cells[] = trim($node->textContent);
                    }
                }
                if (!empty($cells) && array_filter($cells) !== []) {
                    $rows[] = $cells;
                }
            }
            if (!empty($rows)) {
                return $rows;
            }
        }

        // Last fallback: Treat as CSV
        return self::parseCsv($filePath);
    }

    /**
     * Execute safe database import with full validation, batch duplicate checking, and transaction safety
     */
    public static function processImport(array $rawRows, string $duplicateMode = 'skip', int $userId = 1): array {
        if (count($rawRows) < 2) {
            throw new Exception("The uploaded file does not contain enough data rows to process.");
        }

        $headerMap = self::getHeaderMap();
        $headerRow = $rawRows[0];
        $columnMapping = [];

        // Identify header columns
        foreach ($headerRow as $colIdx => $headerText) {
            $cleanHeader = strtolower(trim(preg_replace('/[*:]/', '', (string)$headerText)));
            if (isset($headerMap[$cleanHeader])) {
                $columnMapping[$colIdx] = $headerMap[$cleanHeader];
            }
        }

        if (!in_array('full_name', $columnMapping, true)) {
            throw new Exception("Mandatory 'Full Name' column was not recognized. Please verify the header row format.");
        }

        $db = Database::getInstance();
        $memberModel = new MemberModel();

        // 1. Batch collect all NICs and Member Numbers from the file to check existing records in ONE query (Avoid N+1 queries)
        $fileNics = [];
        $fileMemberNos = [];
        $dataRows = [];

        for ($i = 1; $i < count($rawRows); $i++) {
            $row = $rawRows[$i];
            // Check if this row is an instruction hint row
            $isHint = false;
            foreach ($row as $cellVal) {
                if (stripos((string)$cellVal, 'leave empty for auto') !== false || stripos((string)$cellVal, 'sri lankan nic') !== false) {
                    $isHint = true;
                    break;
                }
            }
            if ($isHint) continue;

            $record = [];
            foreach ($columnMapping as $colIdx => $fieldKey) {
                $record[$fieldKey] = trim((string)($row[$colIdx] ?? ''));
            }

            // Skip empty rows
            if (empty(array_filter($record))) {
                continue;
            }

            $lineNo = $i + 1; // 1-based line number in spreadsheet
            $record['_line_no'] = $lineNo;

            if (!empty($record['nic'])) {
                $normNic = strtoupper(preg_replace('/[^0-9a-zA-Z]/', '', $record['nic']));
                $record['nic'] = $normNic;
                $fileNics[] = $normNic;
            }
            if (!empty($record['member_no'])) {
                $fileMemberNos[] = $record['member_no'];
            }

            $dataRows[] = $record;
        }

        if (empty($dataRows)) {
            throw new Exception("No valid member records found to import.");
        }

        // 2. Fetch existing DB records in batched queries
        $existingByNic = [];
        $existingByNo = [];

        $fileNics = array_values(array_unique(array_filter($fileNics)));
        if (!empty($fileNics)) {
            $placeholders = implode(',', array_fill(0, count($fileNics), '?'));
            $stmt = $db->prepare("
                SELECT id, member_no, nic, party_id, full_name 
                FROM coop_members 
                WHERE member_type = 'MEMBER' AND nic IN ($placeholders)
            ");
            $stmt->execute($fileNics);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $existingByNic[strtoupper($row['nic'])] = $row;
            }
        }

        $fileMemberNos = array_values(array_unique(array_filter($fileMemberNos)));
        if (!empty($fileMemberNos)) {
            $placeholders = implode(',', array_fill(0, count($fileMemberNos), '?'));
            $stmt = $db->prepare("
                SELECT id, member_no, nic, party_id, full_name 
                FROM coop_members 
                WHERE member_no IN ($placeholders)
            ");
            $stmt->execute($fileMemberNos);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $existingByNo[$row['member_no']] = $row;
            }
        }

        // 3. Prepare Insert/Update SQL statements once outside the loop (Rule 5: Avoid N+1 queries)
        $insertPartyStmt = $db->prepare("
            INSERT INTO parties (party_code, party_type, name, phone, address, city, nic_reg_no, created_by)
            VALUES (:code, 'MEMBER', :name, :phone, :address, :city, :nic, :by)
        ");

        $updatePartyStmt = $db->prepare("
            UPDATE parties 
            SET name = :name, phone = :phone, address = :address, city = :city, nic_reg_no = :nic
            WHERE id = :id
        ");

        $insertMemberStmt = $db->prepare("
            INSERT INTO coop_members 
            (member_type, member_no, party_id, full_name, nic, dob, gender, occupation, phone, email, whatsapp, 
             agricultural_sector, heir_name, heir_address, heir_nic, heir_contact_number, address, city, 
             registration_date, membership_type, status, registration_fee, shares_fee, payment_method, payment_status, notes)
            VALUES 
            ('MEMBER', :member_no, :party_id, :full_name, :nic, :dob, :gender, :occupation, :phone, :email, :whatsapp, 
             :agricultural_sector, :heir_name, :heir_address, :heir_nic, :heir_contact_number, :address, :city, 
             :registration_date, :membership_type, :status, :registration_fee, :shares_fee, :payment_method, :payment_status, :notes)
        ");

        $updateMemberStmt = $db->prepare("
            UPDATE coop_members 
            SET member_no = :member_no, full_name = :full_name, nic = :nic, dob = :dob, gender = :gender, 
                occupation = :occupation, phone = :phone, email = :email, whatsapp = :whatsapp, 
                agricultural_sector = :agricultural_sector, heir_name = :heir_name, heir_address = :heir_address, 
                heir_nic = :heir_nic, heir_contact_number = :heir_contact_number, address = :address, city = :city, 
                status = :status, registration_fee = :registration_fee, shares_fee = :shares_fee, 
                payment_method = :payment_method, payment_status = :payment_status, notes = :notes
            WHERE id = :id AND member_type = 'MEMBER'
        ");

        $insertSectorStmt = $db->prepare("INSERT IGNORE INTO agricultural_sectors (name) VALUES (:name)");

        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $skippedDetails = [];
        $seenFileNics = [];
        $seenFileNos = [];

        $db->beginTransaction();

        try {
            foreach ($dataRows as $row) {
                $lineNo = $row['_line_no'];
                $fullName = trim($row['full_name'] ?? '');

                if (empty($fullName)) {
                    $errors[] = "Row {$lineNo}: Full Name is required.";
                    continue;
                }

                $nic = !empty($row['nic']) ? $row['nic'] : null;
                $memberNo = !empty($row['member_no']) ? $row['member_no'] : '';

                // Check in-file duplicate NIC
                if ($nic !== null) {
                    if (isset($seenFileNics[$nic])) {
                        $skippedCount++;
                        $skippedDetails[] = [
                            'line' => $lineNo,
                            'name' => $fullName,
                            'reason' => "Duplicate NIC ({$nic}) already appeared earlier in this file on row " . $seenFileNics[$nic]
                        ];
                        continue;
                    }
                    $seenFileNics[$nic] = $lineNo;
                }

                // Check in-file duplicate Member No
                if (!empty($memberNo)) {
                    if (isset($seenFileNos[$memberNo])) {
                        $skippedCount++;
                        $skippedDetails[] = [
                            'line' => $lineNo,
                            'name' => $fullName,
                            'reason' => "Duplicate Member No ({$memberNo}) already appeared earlier in this file on row " . $seenFileNos[$memberNo]
                        ];
                        continue;
                    }
                    $seenFileNos[$memberNo] = $lineNo;
                }

                // Check if existing record exists in DB
                $existingRecord = null;
                if ($nic !== null && isset($existingByNic[$nic])) {
                    $existingRecord = $existingByNic[$nic];
                } elseif (!empty($memberNo) && isset($existingByNo[$memberNo])) {
                    $existingRecord = $existingByNo[$memberNo];
                }

                // Format fields
                $dob = self::parseDate($row['dob'] ?? '');
                $regDate = self::parseDate($row['registration_date'] ?? '') ?: date('Y-m-d');
                $gender = self::normalizeGender($row['gender'] ?? 'Male');
                $status = self::normalizeStatus($row['status'] ?? 'ACTIVE');
                $memType = !empty($row['membership_type']) ? $row['membership_type'] : 'Ordinary';
                $phone = preg_replace('/[^0-9+]/', '', $row['phone'] ?? '');
                $whatsapp = preg_replace('/[^0-9+]/', '', $row['whatsapp'] ?? '');
                $email = filter_var($row['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $row['email'] : null;
                $regFee = round((float)($row['registration_fee'] ?? 0), 2);
                $sharesFee = round((float)($row['shares_fee'] ?? 0), 2);
                $payMethod = self::normalizePaymentMethod($row['payment_method'] ?? 'Unpaid');
                $payStatus = self::normalizePaymentStatus($row['payment_status'] ?? 'UNPAID');

                // Process sectors
                $sectorsRaw = $row['agricultural_sector'] ?? '';
                $sectorList = array_filter(array_map('trim', explode(',', $sectorsRaw)));
                foreach ($sectorList as $secName) {
                    if (!empty($secName)) {
                        $insertSectorStmt->execute(['name' => $secName]);
                    }
                }
                $sectorsJoined = implode(', ', $sectorList);

                if ($existingRecord !== null) {
                    if ($duplicateMode === 'skip') {
                        $skippedCount++;
                        $skippedDetails[] = [
                            'line' => $lineNo,
                            'name' => $fullName,
                            'reason' => "Member already exists in system with matching NIC (" . ($nic ?? 'N/A') . ") or No (" . ($existingRecord['member_no'] ?? '') . ")"
                        ];
                        continue;
                    } elseif ($duplicateMode === 'update') {
                        // Update existing member record
                        $memberId = (int)$existingRecord['id'];
                        $finalMemberNo = !empty($memberNo) ? $memberNo : $existingRecord['member_no'];

                        $updateMemberStmt->execute([
                            'id'                  => $memberId,
                            'member_no'           => $finalMemberNo,
                            'full_name'           => $fullName,
                            'nic'                 => $nic,
                            'dob'                 => $dob,
                            'gender'              => $gender,
                            'occupation'          => $row['occupation'] ?? null,
                            'phone'               => $phone,
                            'email'               => $email,
                            'whatsapp'            => $whatsapp,
                            'agricultural_sector' => $sectorsJoined ?: null,
                            'heir_name'           => $row['heir_name'] ?? null,
                            'heir_address'        => $row['heir_address'] ?? null,
                            'heir_nic'            => $row['heir_nic'] ?? null,
                            'heir_contact_number' => $row['heir_contact_number'] ?? null,
                            'address'             => $row['address'] ?? '',
                            'city'                => $row['city'] ?? '',
                            'status'              => $status,
                            'registration_fee'    => $regFee,
                            'shares_fee'          => $sharesFee,
                            'payment_method'      => $payMethod,
                            'payment_status'      => $payStatus,
                            'notes'               => $row['notes'] ?? null
                        ]);

                        // Update party record
                        if (!empty($existingRecord['party_id'])) {
                            $updatePartyStmt->execute([
                                'id'      => (int)$existingRecord['party_id'],
                                'name'    => $fullName,
                                'phone'   => $phone,
                                'address' => $row['address'] ?? '',
                                'city'    => $row['city'] ?? '',
                                'nic'     => $nic ?? ''
                            ]);
                        }

                        $updatedCount++;
                        continue;
                    }
                }

                // New record: Generate member number if not provided
                if (empty($memberNo)) {
                    $memberNo = $memberModel->generateMembershipNumber();
                    // Increment locally to prevent colliding sequential generation within the same batch
                    $seenFileNos[$memberNo] = $lineNo;
                    $existingByNo[$memberNo] = ['id' => 0];
                }

                // 1. Insert party
                $insertPartyStmt->execute([
                    'code'    => $memberNo,
                    'name'    => $fullName,
                    'phone'   => $phone,
                    'address' => $row['address'] ?? '',
                    'city'    => $row['city'] ?? '',
                    'nic'     => $nic ?? '',
                    'by'      => $userId
                ]);
                $partyId = (int)$db->lastInsertId();

                // 2. Insert coop_member
                $insertMemberStmt->execute([
                    'member_no'           => $memberNo,
                    'party_id'            => $partyId,
                    'full_name'           => $fullName,
                    'nic'                 => $nic,
                    'dob'                 => $dob,
                    'gender'              => $gender,
                    'occupation'          => $row['occupation'] ?? null,
                    'phone'               => $phone,
                    'email'               => $email,
                    'whatsapp'            => $whatsapp,
                    'agricultural_sector' => $sectorsJoined ?: null,
                    'heir_name'           => $row['heir_name'] ?? null,
                    'heir_address'        => $row['heir_address'] ?? null,
                    'heir_nic'            => $row['heir_nic'] ?? null,
                    'heir_contact_number' => $row['heir_contact_number'] ?? null,
                    'address'             => $row['address'] ?? '',
                    'city'                => $row['city'] ?? '',
                    'registration_date'   => $regDate,
                    'membership_type'     => $memType,
                    'status'              => $status,
                    'registration_fee'    => $regFee,
                    'shares_fee'          => $sharesFee,
                    'payment_method'      => $payMethod,
                    'payment_status'      => $payStatus,
                    'notes'               => $row['notes'] ?? null
                ]);

                $newMemberId = (int)$db->lastInsertId();
                if ($nic !== null) {
                    $existingByNic[$nic] = ['id' => $newMemberId, 'member_no' => $memberNo, 'party_id' => $partyId];
                }
                $existingByNo[$memberNo] = ['id' => $newMemberId, 'member_no' => $memberNo, 'party_id' => $partyId];

                $importedCount++;
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception("Database transaction error during import: " . $e->getMessage());
        }

        return [
            'total_rows'      => count($dataRows),
            'imported'        => $importedCount,
            'updated'         => $updatedCount,
            'skipped'         => $skippedCount,
            'errors'          => $errors,
            'skipped_details' => $skippedDetails
        ];
    }

    /**
     * Helpers for validation, date parsing and sanitization
     */
    private static function parseDate(?string $str): ?string {
        if (empty($str)) return null;

        // If numeric (Excel serial date)
        if (is_numeric($str) && (float)$str > 1000) {
            $unixTime = ((float)$str - 25569) * 86400;
            return date('Y-m-d', (int)$unixTime);
        }

        // Standard string dates
        $str = trim($str);
        if (preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
        }
        if (preg_match('/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
        }

        $time = strtotime($str);
        return $time ? date('Y-m-d', $time) : null;
    }

    private static function normalizeGender(string $g): string {
        $g = ucfirst(strtolower(trim($g)));
        return in_array($g, ['Male', 'Female', 'Other'], true) ? $g : 'Male';
    }

    private static function normalizeStatus(string $s): string {
        $s = strtoupper(trim($s));
        return in_array($s, ['ACTIVE', 'INACTIVE', 'SUSPENDED', 'RESIGNED'], true) ? $s : 'ACTIVE';
    }

    private static function normalizePaymentMethod(string $p): string {
        $p = strtolower(trim($p));
        if (str_contains($p, 'cash')) return 'Cash';
        if (str_contains($p, 'bank')) return 'Bank Transfer';
        if (str_contains($p, 'cheque') || str_contains($p, 'check')) return 'Cheque';
        return 'Unpaid';
    }

    private static function normalizePaymentStatus(string $p): string {
        return strtoupper(trim($p)) === 'PAID' ? 'PAID' : 'UNPAID';
    }

    private static function sanitizeXml(?string $str): string {
        if ($str === null || $str === '') return '';
        // Prevent Excel formula injection when opened
        if (isset($str[0]) && in_array($str[0], ['=', '+', '-', '@'], true)) {
            $str = "'" . $str;
        }
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function colLetterToIndex(string $letters): int {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - 64);
        }
        return $idx - 1;
    }
}
