<?php
include("connection.php");

// Read Input
$empcode   = $_GET['empcode'] ?? "";
$monthYear = $_GET['month']   ?? date("Y-m");

$month = date("m", strtotime($monthYear));
$year  = date("Y", strtotime($monthYear));

$fromDate = "$year-$month-01";
$toDate   = date("Y-m-t", strtotime($fromDate));

// Filter employee
$where = "";
if ($empcode != "") {
    $where = " AND a.empcode = '$empcode' ";
}

// Query
$sql = "
SELECT 
    p.empcode,
    p.name,
    COALESCE(SUM(CASE WHEN LOWER(a.status)='present' THEN 1 ELSE 0 END),0) AS present_days,
    COALESCE(SUM(CASE WHEN LOWER(a.status)='absent' THEN 1 ELSE 0 END),0)  AS absent_days,
    COALESCE(SUM(CASE WHEN LOWER(a.status)='leave'  THEN 1 ELSE 0 END),0) AS leave_days
FROM practice p
LEFT JOIN emp_attendance a 
       ON TRIM(p.empcode)=TRIM(a.empcode)
      AND DATE(a.date) BETWEEN '$fromDate' AND '$toDate'
WHERE 1 $where
GROUP BY p.empcode, p.name
ORDER BY p.name;
";

$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Attendance Report</title>
    <style>
        table { border-collapse: collapse; width: 70%; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background: #007bff; color: white; }
        h3 { font-family: Arial; }
    </style>
</head>
<body>

<h3>Employee Attendance Report (<?= $monthYear ?>)</h3>

<table>
<tr>
    <th>Code</th>
    <th>Name</th>
    <th>Present</th>
    <th>Absent</th>
    <th>Leave</th>
</tr>

<?php
if ($res && $res->num_rows > 0) {
    while($r = $res->fetch_assoc()) { ?>
        <tr>
            <td><?= $r['empcode'] ?></td>
            <td><?= $r['name'] ?></td>
            <td><?= $r['present_days'] ?></td>
            <td><?= $r['absent_days'] ?></td>
            <td><?= $r['leave_days'] ?></td>
        </tr>
<?php 
    }
} else {
    echo "<tr><td colspan='5'>No Records Found</td></tr>";
}
?>
</table>

</body>
</html>
