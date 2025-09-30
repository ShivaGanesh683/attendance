<?php
include('header.php'); ?>
<?php 
session_start();
include('sidemenu.php');
include('connection.php');

// Check if the ID is set in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No attendance ID provided'); window.location.href='emp_attendance.php';</script>";
    exit;
}

// Fetch the attendance record based on the ID
$attendance_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT 
                            ea.id, ea.date, ea.check_in_time, ea.check_out_time, ea.work_hours, ea.status, 
                            e.empcode, e.name
                        FROM emp_attendance AS ea
                        JOIN practice AS e ON ea.empcode = e.id
                        WHERE ea.id = ?");
$stmt->bind_param("i", $attendance_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a record was found
if ($result->num_rows == 0) {
    echo "<script>alert('No record found with the given ID'); window.location.href='emp_attendance.php';</script>";
    exit;
}

// Fetch the record
$attendance = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin: 5px auto;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            background-color: #f9f9f9;
        }
        .card-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .card-content {
            margin: 15px 0;
        }
        .card-content label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .card-content input {
            width: calc(100% - 160px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .back-btn {
            text-align: center;
            margin-top: 20px;
        }
        .back-btn a {
            text-decoration: none;
            background-color: blue;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">Attendance Details</div>
        <form>
            <div class="card-content form-group">
                <label>ID:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['id'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Emp Code:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['empcode'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Name:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['name'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Date:</label>
                <input type="text" value="<?php echo isset($attendance['date']) ? date('d-m-Y', strtotime($attendance['date'])) : 'N/A'; ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Check-in Time:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['check_in_time'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Check-out Time:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['check_out_time'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Work Hours:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['work_hours'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="card-content form-group">
                <label>Status:</label>
                <input type="text" value="<?php echo htmlspecialchars($attendance['status'] ?? 'N/A'); ?>" readonly>
            </div>
            <div class="back-btn">
                <a href="admin_attendance.php">Back to Attendance</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php include('footer.php'); ?>
