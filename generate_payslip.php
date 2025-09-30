<?php
session_start();
include('connection.php'); // DB connection

// Get employee code and month
$empcode = isset($_GET['empcode']) ? $_GET['empcode'] : '';
$month   = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

if(!$empcode){
    die("No employee selected.");
}

// Fetch employee and salary data
$sql = "SELECT 
    p.id,
    p.empcode,
    p.name,
    p.email,
    s.basic_salary,
    s.allowances,
    s.deductions,
    s.net_salary,
    s.salary_month
FROM practice p
JOIN salaries s ON p.id = s.emp_id
WHERE p.empcode = ? 
  AND DATE_FORMAT(s.salary_month, '%Y-%m') = ?";



$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $empcode, $month);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("No salary record found for this employee for the selected month.");
}

$row = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - <?php echo $row['name']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f7f7f7; }
        .payslip { max-width: 700px; margin: auto; background: #fff; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .total { font-weight: bold; background-color: #f4f4f4; }
        .print-btn { text-align: center; margin-top: 20px; }
        button { padding: 10px 20px; font-size: 16px; background-color: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #0056b3; }
        @media print {
            button { display: none; }
            body { background: none; margin: 0; }
        }
    </style>
</head>
<body>

<div class="payslip">
    <div class="header">
        <h2>Employee Payslip</h2>
        <p><?php echo date('F Y', strtotime($row['salary_month']."-01")); ?></p>
    </div>

    <table>
        <tr><th>Employee Name</th><td><?php echo $row['name']; ?></td></tr>
        <tr><th>Employee Code</th><td><?php echo $row['empcode']; ?></td></tr>
        <tr><th>Department</th><td><?php echo $row['department']; ?></td></tr>
    </table>

    <table>
        <tr><th>Earnings</th><th>Amount (₹)</th></tr>
        <tr><td>Basic Salary</td><td><?php echo number_format($row['basic_salary'],2); ?></td></tr>
        <tr><td>Allowances</td><td><?php echo number_format($row['allowances'],2); ?></td></tr>
        <tr><th>Deductions</th><th>Amount (₹)</th></tr>
        <tr><td>Deductions</td><td><?php echo number_format($row['deductions'],2); ?></td></tr>
        <tr class="total"><td>Net Salary</td><td><?php echo number_format($row['net_salary'],2); ?></td></tr>
    </table>

    <p style="text-align:right;">Authorized Signature: _______________</p>

    <div class="print-btn">
        <button onclick="window.print();">Download / Print Payslip</button>
    </div>
</div>

</body>
</html>
