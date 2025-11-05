<?php 
// session_start();
if($_SESSION['user']=="admin"){ ?>
    
<!DOCTYPE html>
<html lang="en">
<head>
  
<style>
  /* Sidebar styling */
.sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #111;
    padding-top: 20px;
}

.sidebar a {
    padding: 10px 15px;
    text-decoration: none;
    font-size: 18px;
    color: white;
    display: block;
}

.sidebar a:hover {
    background-color: #575757;
}

/* Page content */
.content {
    margin-left: 260px; /* Same as the width of the sidebar */
    padding: 20px;
}

/* Sidebar styling */
.sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #111;
    padding-top: 20px;
    display: block;
}

.sidebar a {
    padding: 10px 15px;
    text-decoration: none;
    font-size: 18px;
    color: white;
    display: block;
}

.sidebar a:hover {
    background-color: #575757;
}

/* Page content */
.content {
    margin-left: 110px; /* Same as the width of the sidebar */
    padding: 20px;
}
  </style>
  
  <title>Employee Management</title>
    <!-- Include Bootstrap CSS for styling (optional) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Include custom CSS for sidebar -->
    <link rel="stylesheet" href="styles.css">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

</style>

</head>
<body>
 <!-- Sidebar -->

 <?php
include 'connection.php'; // Database connection

// Set timezone
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Fetch today's leave request count
$leave_count = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM emp_attendance WHERE status = 'Leave' AND date = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$stmt->bind_result($leave_count);
$stmt->fetch();
$stmt->close();
?>

<div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="employeelist.php">Employees</a>
        <a href="user_list.php">Usermanagement</a>
        <a href="admin_attendance.php">Attendance</a>
        <a href="emp_attendance.php">Employee Attendance</a>
        <a href="emp_leaves.php">Leaves
        <?php if($leave_count > 0){ ?>
            <span class="badge badge-light" style="background-color:red; color:white; border-radius:50%; padding:2px 6px;"><?php echo $leave_count; ?></span>
        <?php } ?>
    </a>
        <a href="atte_overview.php">Overview</a>
        <a href="payroll.php">Payroll</a>
        <a href="leave_req.php">Leave Request</a>
        <a href="payslip.php">Payslip</a>
        <a href="salaries_report.php">Salaries</a>
        <!-- <a href="settings.php">Settings</a>  -->
    </div>

    <!-- Page content -->
    <div class="content">
        <div class="container">
            

            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'inserted') {
                echo '<div class="alert alert-success" role="alert">Record inserted successfully</div>';
            } elseif (isset($_GET['message']) && $_GET['message'] == 'deleted') {
                echo '<div class="alert alert-success" role="alert">Record deleted successfully</div>';
            }
            ?>
</body>
</html>

<?php }else{

include('sidemenu1.php');
}?>		