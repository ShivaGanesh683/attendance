<?php
session_start();
include("header.php");
include("sidemenu.php");
include("connection.php");

// Ensure DB connection
if (!isset($conn) && isset($con)) {
    $conn = $con;
}
if (!($conn instanceof mysqli)) {
    die("DB connection missing! Check connection.php");
}

// Month list
$months = [
    1 => "January",
    2 => "February",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "December"
];

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date("n");
$fromDateRaw = $_GET['from'] ?? "";
$toDateRaw   = $_GET['to'] ?? "";

// Validate Dates
$fromDate = "";
$toDate = "";

if ($fromDateRaw && DateTime::createFromFormat("Y-m-d", $fromDateRaw)) {
    $fromDate = $conn->real_escape_string($fromDateRaw);
}
if ($toDateRaw && DateTime::createFromFormat("Y-m-d", $toDateRaw)) {
    $toDate = $conn->real_escape_string($toDateRaw);
}

// Auto assign month dates if empty
if (!$fromDate || !$toDate) {
    $year = date("Y");
    $fromDate = date("Y-m-01", strtotime("$year-$month-01"));
    $toDate   = date("Y-m-t", strtotime($fromDate));
}

// working days
function daysBetween($from, $to)
{
    $d1 = strtotime($from);
    $d2 = strtotime($to);
    return ($d1 && $d2 && $d2 >= $d1) ? (floor(($d2 - $d1) / 86400) + 1) : 0;
}
$daysRange = daysBetween($fromDate, $toDate);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Attendance Report</title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        td.num,
        th.num {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <!-- FILTER -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Attendance Report</h4>
            </div>

            <div class="card-body">
                <form method="get" class="row g-3">

                    <!-- EMP CODE DROPDOWN -->
                    <div class="col-md-3">
                        <label class="form-label">Employee Code</label>
                        <select name="empcode" class="form-select">
                            <option value="">-- All Employees --</option>
                            <?php
                            include("connection.php");

                            $empcode   = $_GET['empcode'] ?? "";
                            $monthYear = $_GET['month']   ?? date("Y-m");

                            $month = date("m", strtotime($monthYear));
                            $year  = date("Y", strtotime($monthYear));

                            $fromDate = "$year-$month-01";
                            $toDate   = date("Y-m-t", strtotime($fromDate));

                            $where = "";
                            if ($empcode != "") {
                                $where = " AND a.empcode = '$empcode' ";
                            }

                            $sql = "
SELECT 
    p.empcode,
    p.name,
    COALESCE(SUM(CASE WHEN LOWER(a.status)='present' THEN 1 ELSE 0 END),0) AS present_days,
    COALESCE(SUM(CASE WHEN LOWER(a.status)='absent' THEN 1 ELSE 0 END),0)  AS absent_days,
    COALESCE(SUM(CASE WHEN LOWER(a.status)='leave'  THEN 1 ELSE 0 END),0) AS leave_days,
    COALESCE(p.salary,0) AS annual_salary
FROM practice p
LEFT JOIN emp_attendance a 
       ON TRIM(p.empcode)=TRIM(a.empcode)
      AND DATE(a.date) BETWEEN '$fromDate' AND '$toDate'
WHERE 1 $where
GROUP BY p.empcode, p.name, p.salary
ORDER BY p.name;
";

                            $res = $conn->query($sql);
                            ?>
                        </select>
                    </div>

                    <!-- MONTH PICKER -->
                    <div class="col-md-3">
                        <label>Month</label>
                        <input type="month" class="form-control"
                            name="month" value="<?= $monthYear ?? date("Y-m") ?>">
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-success w-50" formaction="attendance_excel.php">Report</button>
                        <a href="salaries_report.php" class="btn btn-secondary w-50">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <br>

        <!-- RESULT -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Employee Salaries</h5>
            </div>
            <div class="card-body">
                <?php

                $q = "
SELECT 
    p.empcode,
    p.name,
    COALESCE(p.salary,0) AS annual_salary,

    SUM(CASE WHEN LOWER(TRIM(a.status))='present' THEN 1 ELSE 0 END) AS present_days,
    SUM(CASE WHEN LOWER(TRIM(a.status))='absent'  THEN 1 ELSE 0 END) AS absent_days,
    SUM(CASE WHEN LOWER(TRIM(a.status))='leave'   THEN 1 ELSE 0 END) AS leave_days

FROM practice p
LEFT JOIN emp_attendance a
   ON TRIM(p.empcode)=TRIM(a.empcode)
   AND DATE(a.date) BETWEEN '$fromDate' AND '$toDate'

GROUP BY p.empcode,p.name,p.salary
ORDER BY p.name
";

                $res = $conn->query($q);
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Emp Code</th>
                                <th class="text-center">Emp Name</th>
                                <th class="text-center num">Annual Salary</th>
                                <th class="text-center num">Monthly Salary</th>
                                <th class="text-center num">Per Day</th>
                                <th class="text-center num">Net Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($res && $res->num_rows > 0) {
                                while ($r = $res->fetch_assoc()) {

                                    $yearSal = floatval($r['annual_salary']);
                                    $monthlySal = round($yearSal / 12, 2);

                                    $P = intval($r['present_days']);
                                    $A = intval($r['absent_days']);
                                    $L = intval($r['leave_days']);

                                    $workDays = $P + $A + $L;
                                    if ($workDays <= 0) $workDays = $daysRange;

                                    $perDay = ($workDays > 0) ? round($monthlySal / $workDays, 2) : 0;
                                    $ded = round($A * $perDay, 2);
                                    $net = round($monthlySal - $ded, 2);

                                    echo "
                                    <tr>
                                        <td class='text-center'>" . htmlspecialchars($r['empcode']) . "</td>
                                        <td class='text-center'>" . htmlspecialchars($r['name']) . "</td>

                                        <td class='text-center num'>" . number_format($yearSal, 2) . "</td>
                                        <td class='text-center num'>" . number_format($monthlySal, 2) . "</td>

                                        <td class='text-center num'>" . number_format($perDay, 2) . "</td>
                                        <td class='text-center num'>" . number_format($net, 2) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No Records Found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>