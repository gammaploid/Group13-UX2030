<?php
// generate_report.php
error_reporting(E_ERROR | E_PARSE);
ob_start();
require_once('tcpdf/tcpdf.php');
require_once('db_connection.php');

// Clear any existing output
ob_clean();

if (!isset($_POST['report_type'])) {
    header('Location: audit_reports.php');
    exit;
}

// Create PDF class with custom Header and Footer
class PDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo
        //$this->Image('path_to_logo.png', 10, 10, 30);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, 'SMD System Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('SMD System');
$pdf->SetAuthor('SMD Admin');
$pdf->SetTitle('SMD Report - ' . ucfirst($_POST['report_type']));

// Set header and footer fonts
$pdf->setHeaderFont(Array('helvetica', '', 10));
$pdf->setFooterFont(Array('helvetica', '', 8));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Get report content based on type
$date_condition = '';
if (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
    $date_from = $conn->real_escape_string($_POST['date_from']);
    $date_to = $conn->real_escape_string($_POST['date_to']);
    $date_condition = " WHERE timestamp BETWEEN '$date_from' AND '$date_to'";
}

// Report Generation Functions
function generateMachinesReport($conn, $date_condition) {
    $html = '<h1 style="color: #4a69bd;">Machines Report</h1>';
    $html .= '<h2 style="color: #666;">Generated on: ' . date('Y-m-d H:i:s') . '</h2>';
    
    // Current machine status
    $sql = "SELECT * FROM machines";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $html .= '<h3>Machine Status Overview</h3>';
        $html .= '<table border="1" cellpadding="5">
                    <tr style="background-color: #f5f5f5;">
                        <th>Machine ID</th>
                        <th>Machine Name</th>
                        <th>Status</th>
                    </tr>';
        
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>'.$row['id'].'</td>
                        <td>'.$row['machine_name'].'</td>
                        <td>'.$row['operational_status'].'</td>
                    </tr>';
        }
        $html .= '</table>';
    }
    
    return $html;
}

function generateUsersReport($conn) {
    $html = '<h1 style="color: #4a69bd;">Users Report</h1>';
    $html .= '<h2 style="color: #666;">Generated on: ' . date('Y-m-d H:i:s') . '</h2>';
    
    $sql = "SELECT id, username, role, email FROM users";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $html .= '<table border="1" cellpadding="5">
                    <tr style="background-color: #f5f5f5;">
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Email</th>
                    </tr>';
        
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>'.$row['id'].'</td>
                        <td>'.$row['username'].'</td>
                        <td>'.$row['role'].'</td>
                        <td>'.$row['email'].'</td>
                    </tr>';
        }
        $html .= '</table>';
    }
    
    return $html;
}

function generateJobsReport($conn, $date_condition) {
    $html = '<h1 style="color: #4a69bd;">Jobs Report</h1>';
    $html .= '<h2 style="color: #666;">Generated on: ' . date('Y-m-d H:i:s') . '</h2>';
    
    $sql = "SELECT j.*, u.username as operator_name, m.machine_name 
            FROM jobs j 
            LEFT JOIN users u ON j.operator_id = u.id 
            LEFT JOIN machines m ON j.machine_id = m.id" . 
            ($date_condition ? str_replace('timestamp', 'start_time', $date_condition) : '');
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $html .= '<table border="1" cellpadding="5">
                    <tr style="background-color: #f5f5f5;">
                        <th>Job ID</th>
                        <th>Job Name</th>
                        <th>Operator</th>
                        <th>Machine</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>';
        
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>'.$row['job_id'].'</td>
                        <td>'.$row['job_name'].'</td>
                        <td>'.$row['operator_name'].'</td>
                        <td>'.$row['machine_name'].'</td>
                        <td>'.$row['start_time'].'</td>
                        <td>'.$row['end_time'].'</td>
                    </tr>';
        }
        $html .= '</table>';
    }
    
    return $html;
}

function generateMessagesReport($conn, $date_condition) {
    $html = '<h1 style="color: #4a69bd;">Messages Report</h1>';
    $html .= '<h2 style="color: #666;">Generated on: ' . date('Y-m-d H:i:s') . '</h2>';
    
    $sql = "SELECT m.*, 
            sender.username as sender_name, 
            receiver.username as receiver_name,
            mac.machine_name,
            j.job_name
            FROM messages m
            JOIN users sender ON m.sender_id = sender.id
            JOIN users receiver ON m.receiver_id = receiver.id
            LEFT JOIN machines mac ON m.machine_id = mac.id
            LEFT JOIN jobs j ON m.job_id = j.job_id" .
            ($date_condition ? str_replace('timestamp', 'sent_at', $date_condition) : '');
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $html .= '<table border="1" cellpadding="5">
                    <tr style="background-color: #f5f5f5;">
                        <th>From</th>
                        <th>To</th>
                        <th>Message</th>
                        <th>Regarding Machine</th>
                        <th>Regarding Job</th>
                        <th>Sent At</th>
                        <th>Read At</th>
                    </tr>';
        
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>'.$row['sender_name'].'</td>
                        <td>'.$row['receiver_name'].'</td>
                        <td>'.$row['message'].'</td>
                        <td>'.(isset($row['machine_name']) ? $row['machine_name'] : '-').'</td>

                        <td>'.(isset($row['job_name']) ? $row['job_name'] : '-').'</td>
                        <td>'.$row['sent_at'].'</td>
                        <td>'.(isset($row['read_at']) ? $row['read_at'] : 'Unread').'</td>

                    </tr>';
        }
        $html .= '</table>';
    }
    
    return $html;
}

function generatePerformanceReport($conn, $start_date, $end_date) {
    $html = '<h1 style="color: #4a69bd;">Performance Report</h1>';
    $html .= '<h2 style="color: #666;">Period: ' . $start_date . ' to ' . $end_date . '</h2>';
    
    // Add machine performance table
    $html .= '<h3>Machine Performance</h3>';
    // Add your machine performance query and table generation here
    
    // Add job statistics
    $html .= '<h3>Job Statistics</h3>';
    // Add your job statistics query and presentation here
    
    // Add operator performance table
    $html .= '<h3>Operator Performance</h3>';
    // Add your operator performance query and table generation here
    
    return $html;
}

function generateFactoryPerformanceReport($conn, $start_date, $end_date) {
    $html = '<h1 style="color: #4a69bd;">Factory Performance Report</h1>';
    $html .= '<h2 style="color: #666;">Period: ' . $start_date . ' to ' . $end_date . '</h2>';
    
    // Add your machine performance data
    $html .= '<h3>Machine Performance</h3>';
    // Add your SQL queries and table generation here similar to the main page
    
    return $html;
}

// gen report switch
switch ($_POST['report_type']) {
    case 'machines':
        $content = generateMachinesReport($conn, $date_condition);
        break;
    case 'users':
        $content = generateUsersReport($conn);
        break;
    case 'jobs':
        $content = generateJobsReport($conn, $date_condition);
        break;
    case 'messages':
        $content = generateMessagesReport($conn, $date_condition);
        break;
    case 'performance':
        $content = generatePerformanceReport($conn, $_POST['start_date'], $_POST['end_date']);
        break;    
    case 'factory_performance':
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d', strtotime('-7 days'));
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');
        $content = generateFactoryPerformanceReport($conn, $start_date, $end_date);
        break;
    default:
        $content = '<h1>Invalid report type</h1>';
        
}

// Print content
$pdf->writeHTML($content, true, false, true, false, '');

// Close and output PDF document
ob_end_clean();
$pdf->Output('smd_report_' . $_POST['report_type'] . '_' . date('Y-m-d') . '.pdf', 'I');
exit;
?>
