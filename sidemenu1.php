<?php
//session_start();
$name = $_SESSION['user'] ?? ''; // Check if 'name1' exists in $_SESSION
?>

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
</style>

<?php
include "connection.php";

$emp_id = $name; // Use the session variable for emp_id
$r = mysqli_query($conn, "SELECT ename FROM login WHERE name1='$emp_id'") or die(mysqli_error($conn));
$row = mysqli_fetch_array($r);
$name = $row['ename'] ?? ''; // Check if 'ename' exists

$p = "SELECT D.menus FROM hr_user as D, login as M WHERE D.emp_id = M.ename AND D.emp_id = '$name'";

$sql = mysqli_query($conn, $p);
$menu2 = "0";
$menu0200 = "0";
$menu3 = "0";
$menu31 = "0";
$menu4 = "0";
$menu41 = "0";
$menu5 = "0";
$menu51 = "0";
$menu6 = "0";
$menu61 = "0";
if ($sql) {
    while ($row = mysqli_fetch_array($sql)) {
        $menu = $row[0];
        // echo $menu;
        if ($menu == "2") {$menu2 = "2";}
        if ($menu == "0200") {$menu0200 = "0200";}
        if ($menu == "3") {$menu3 = "3";}
        if ($menu == "31") {$menu31 = "31";}
        if ($menu == "4") {$menu4 = "4";}
        if ($menu == "41") {$menu41 = "41";}
        if ($menu == "5") {$menu5 = "5";}
        if ($menu == "51") {$menu51 = "51";}
        if ($menu == "6") {$menu6 = "6";}
        if ($menu == "61") {$menu61 = "61";}

    }
}
?>


    <div class="sidebar">
      <?php if ($menu2 == "2") {
    ?>
        <a href="dashboard1.php">Dashboard</a><?php
}?>

        <?php if ($menu3 == "3") {
    ?>

        <a href="user_list.php">Usermanagement</a> <?php
}?>

        <?php if ($menu41 == "41") {
    ?>

        <a href="employees_attendance.php">Attendance</a> <?php
}?>

        <?php if ($menu5 == "5") {
    ?>
        <a href="emp_attendance.php">Employee Attendance</a> <?php
}?>

        <?php if ($menu61 == "61") {
    ?>

        <a href="employeelist.php">Employees</a> <?php
}?>

    </div>


    <!-- $name = $_SESSION['user'] ?? ''; // Check if 'name1' exists in $_SESSION -->
