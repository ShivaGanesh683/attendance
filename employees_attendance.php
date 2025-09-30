
<?php

include 'header.php'; ?>
<?php
    session_start();
    include 'sidemenu.php';

    // include('auto_mark_absent.php');
    // $empcode = $_SESSION['empcode'];
    //  $name = $_SESSION['user'] ?? ''; // Check if 'name1' exists in $_SESSION
if ($_SESSION['user'] == "admin") {?>


<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            width: 105%;          /* or 100% if you want it flush edge‑to‑edge */
            margin-left: auto;
            margin-right: auto;
        }
        .card-topline {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: red;
            border-radius: 5px 5px 0 0;
        }
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            border-top: 1px solid #ddd;
        }
        nav a {
            margin: 0 5px;
            text-decoration: none;
            color: blue;
        }
        nav a.active {
            font-weight: bold;
            text-decoration: underline;
        }
        nav a:hover {
            border-color: #4CAF50;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
    function checkTimeAndMarkAbsent() {
        const now = new Date();

        // Format the current time as HH:mm:ss
        const currentTime = now.toTimeString().split(' ')[0];

        // Define the target time range
        const targetStartTime = "18:00:00";
        const targetEndTime = "18:01:00";

        // Check if the current time is within the target range
        if (currentTime >= targetStartTime && currentTime < targetEndTime) {
            // Send an AJAX request to mark absentees
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "auto_mark_absent.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText); // Log the response from the server
                }
            };
            xhr.send();
        }
    }

    // Align the interval to start at the next full minute
    const now = new Date();
    const delay = 60000 - (now.getSeconds() * 1000 + now.getMilliseconds());
    setTimeout(function () {
        checkTimeAndMarkAbsent();
        setInterval(checkTimeAndMarkAbsent, 60000);
    }, delay);
</script>
   <script>
    function openModal(empId, action) {
        document.getElementById("modal").style.display = "block";
        document.getElementById("emp_id").value = empId;
        document.getElementById("action").value = action;

        const now = new Date();
        const formattedDate = now.getDate().toString().padStart(2, '0') + '-' +
                            (now.getMonth() + 1).toString().padStart(2, '0') + '-' +
                            now.getFullYear();
        document.getElementById("date").value = formattedDate;
        document.getElementById("time").value = now.toTimeString().split(' ')[0];
    }

    function closeModal() {
        document.getElementById("modal").style.display = "none";
    }

    function openLeaveModal(empId) {
        document.getElementById("leaveModal").style.display = "block";
        document.getElementById("leave_emp_id").value = empId;

        const now = new Date();
        const formattedDate = now.getDate().toString().padStart(2, '0') + '-' +
                            (now.getMonth() + 1).toString().padStart(2, '0') + '-' +
                            now.getFullYear();
        document.getElementById("leave_date").value = formattedDate;
    }

    function closeLeaveModal() {
        document.getElementById("leaveModal").style.display = "none";
    }

    function showAlert(message) {
        alert(message);
    }
    </script>

</head>
    <body>
        <!--             <?php echo htmlspecialchars($name); ?> -->
                <h5><b>
                  Employee Attendance  - <?php echo date('F Y'); ?></b>
                </h5>

                <div class="card">
                 <div class="card-topline"></div>
                   <form method="post" action="admin_attendance.php" style="margin-left:675px; margin-bottom:10px; display: flex; gap: 13px; align-items: center;">
                        <input type="hidden" name="id" value="{$row['id']}">
                        <button type="button" onclick="openLeaveModal(1)" style="background-color: orange; color: white; padding: 4px 10px; border: none; border-radius: 5px;">Leave Request</button>
                        <button type="button" onclick="openModal(1, 'check_in')" style="background-color: blue; color: white; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer; text-align:right">Check-in</button>

                        <button type="button" onclick="openModal(1, 'check-out')" style="background-color: blue; color: white; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer;">Check-out</button>

                        <!-- <button type="submit" name="absent" value="1" style="background-color: blue; color: white; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer;">Absent</button> -->
                    </form>

                    <div class="table-scrollable">
                        <table class="table table-hover table-striped table-checkable order-column full-width">
                            <thead>
                                <tr style="text-align: center">
                                    <th>ID</th>
                                    <th>Emp Code</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>In-Time</th>
                                    <th>Out-Time</th>
                                    <th>Work Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include "connection.php";

                                        $limit  = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
                                        $page   = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                        $offset = ($page - 1) * $limit;

                                        $sql = "SELECT
                                            ea.id, ea.date, ea.check_in_time, ea.check_out_time,
                                            ea.status, ea.work_hours,
                                            e.empcode, e.name
                                        FROM emp_attendance AS ea
                                        JOIN practice AS e ON ea.empcode = e.id
                                        ORDER BY ea.date DESC, ea.id DESC
                                        LIMIT $offset, $limit";

                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                // Format the date to dd-mm-yyyy
                                                $formattedDate = date('d-m-Y', strtotime($row['date']));

                                                echo "<tr style='text-align:center'>
                                            <td>{$row['id']}</td>
                                            <td>{$row['empcode']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$formattedDate}</td>  <!-- Displaying formatted date -->
                                            <td>{$row['check_in_time']}</td>
                                            <td>{$row['check_out_time']}</td>
                                            <td>{$row['work_hours']}</td>
                                            <td>{$row['status']}</td>
                                            <td>
                                                <a href='view_attendance.php?id={$row['id']}'>View</a>

                                            </td>
                                        </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9' style='text-align: center;'>No attendance records found</td></tr>";
                                        }

                                        $countResult = $conn->query("SELECT COUNT(*) AS total FROM emp_attendance");
                                        $total       = $countResult->fetch_assoc()['total'];
                                        $totalPages  = ceil($total / $limit);
                                    ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination">
                        <div>
                            <?php
                                $start = $offset + 1;
                                    $end   = min($offset + $limit, $total);
                                    echo "<p>Showing $start to $end of $total entries</p>";
                                ?>
                        </div>

                        <nav>
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>">Previous</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>"<?php echo($i === $page) ? 'class="active"' : ''; ?>>
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>">Next</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>

                <!-- Modal for check-in/check-out -->
                <div id="modal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()" style="position: absolute; top: 10px; right: 10px; font-size: 30px; cursor: pointer;">&times;</span>
                        <form method="post" action="admin_attendance.php">
                            <input type="hidden" name="id" id="emp_id">
                            <input type="hidden" name="action" id="action">
                            <label for="date"><b>Date:</b></label>
                            <input type="text" id="date" name="date" class="form-control" readonly>
                            <br>
                            <label for="time"><b>Time:</b></label>
                            <input type="text" id="time" name="time" class="form-control" readonly>
                            <br>
                            <div style="display: flex; justify-content: center; gap: 10px;">
                                <button type="submit" name="check_in" style="background-color: green; color: white; padding: 5px 14px; border: none; border-radius: 5px; cursor: pointer;">Check-in</button>
                                <button type="submit" name="check_out" style="background-color: red; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Check-out</button>
                            </div>
                        </form>
                    </div>
                </div>

      <!-- Leave Request Modal -->
    <div id="leaveModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLeaveModal()"  style="position: absolute; top: 10px; right: 10px; font-size: 30px; cursor: pointer;">&times;</span>
            <form method="post" action="admin_attendance.php">
                <input type="hidden" name="leave_emp_id" id="leave_emp_id">
                <label><b>Date:</b></label>
                <input type="text" id="leave_date" name="leave_date" class="form-control" readonly><br>
                <label><b>Reason:</b></label>
                <textarea name="leave_reason" class="form-control" required></textarea><br>
                <div style="text-align: center;">
                    <button type="submit" name="submit_leave" style="background-color: orange; color: white; padding: 5px 14px; border: none; border-radius: 5px; cursor: pointer;">Submit Leave</button>
                </div>
            </form>
        </div>
    </div>

                <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            include "connection.php";

                            $id           = $_POST['id'] ?? null; // Default to null if 'id' is not set
                            $current_date = date('Y-m-d');
                            date_default_timezone_set('Asia/Kolkata');
                            $current_time = date('H:i:s');

                            if (isset($_POST['check_in'])) {
                                // Fetch the employee name from the database
                                $stmt = $conn->prepare("SELECT name FROM practice WHERE id = ?");
                                $stmt->bind_param("i", $id); // Assuming $id is the empcode
                                $stmt->execute();
                                $stmt->bind_result($name);
                                $stmt->fetch();
                                $stmt->close();

                                if ($name) { // Ensure the name was found
                                                 // Check if already checked in
                                    $stmt = $conn->prepare("SELECT id FROM emp_attendance WHERE empcode = ? AND date = ? AND check_in_time IS NOT NULL");
                                    $stmt->bind_param("is", $id, $current_date);
                                    $stmt->execute();
                                    $stmt->store_result();

                                    if ($stmt->num_rows > 0) {
                                        echo "<script>showAlert('Check-in already recorded for today');</script>";
                                    } else {
                                        // Insert attendance record
                                        $stmt = $conn->prepare("INSERT INTO emp_attendance (empcode, name, date, check_in_time, status) VALUES (?, ?, ?, ?, 'Present')");
                                        $stmt->bind_param("isss", $id, $name, $current_date, $current_time);
                                        $stmt->execute();
                                        echo "<script>showAlert('Check-in successful');</script>";
                                    }
                                    $stmt->close();
                                } else {
                                    echo "<script>showAlert('Employee not found');</script>";
                                }
                            }

                            if (isset($_POST['check_out'])) {
                                // Check if a valid check-in exists and no check-out has been recorded yet
                                $stmt = $conn->prepare("SELECT id, check_in_time FROM emp_attendance WHERE empcode = ? AND date = ? AND check_out_time IS NULL");
                                $stmt->bind_param("is", $id, $current_date);
                                $stmt->execute();
                                $stmt->bind_result($attendance_id, $check_in_time);
                                $stmt->fetch();
                                $stmt->close();

                                if ($attendance_id) {
                                    $check_in   = new DateTime($check_in_time);
                                    $check_out  = new DateTime($current_time);
                                    $interval   = $check_in->diff($check_out);
                                    $work_hours = $interval->format('%H:%I:%S');

                                    $stmt = $conn->prepare("UPDATE emp_attendance SET check_out_time = ?, work_hours = ? WHERE id = ?");
                                    $stmt->bind_param("ssi", $current_time, $work_hours, $attendance_id);
                                    $stmt->execute();
                                    echo "<script>showAlert('Check-out successful');</script>";
                                } else {
                                    echo "<script>showAlert('Check-out failed: No matching check-in record found or already checked out');</script>";
                                }
                            }
                        }

                        if (isset($_POST['submit_leave'])) {
                            $emp_id       = $_POST['leave_emp_id'];
                            $leave_reason = $_POST['leave_reason'];
                            $leave_date   = date('Y-m-d');

                            $stmt = $conn->prepare("SELECT name FROM practice WHERE id = ?");
                            $stmt->bind_param("i", $emp_id);
                            $stmt->execute();
                            $stmt->bind_result($name);
                            $stmt->fetch();
                            $stmt->close();

                            if ($name) {
                                $stmt = $conn->prepare("SELECT id FROM emp_attendance WHERE empcode = ? AND date = ?");
                                $stmt->bind_param("is", $emp_id, $leave_date);
                                $stmt->execute();
                                $stmt->store_result();

                                if ($stmt->num_rows > 0) {
                                    echo "<script>showAlert('Leave already marked for today');</script>";
                                } else {
                                    $stmt = $conn->prepare("INSERT INTO emp_attendance (empcode, name, date, check_in_time, check_out_time, status, work_hours) VALUES (?, ?, ?, '0', '0', 'Leave', ?)");
                                    $stmt->bind_param("isss", $emp_id, $name, $leave_date, $leave_reason);
                                    $stmt->execute();
                                    echo "<script>showAlert('Leave submitted successfully');</script>";
                                }
                                $stmt->close();
                            } else {
                                echo "<script>showAlert('Employee not found');</script>";
                            }
                        }
                    ?>

    </body>
</html>
<?php } else {
        include 'user_attend.php';
}?>
<br><br><br><br><br>
<?php include 'footer.php'; ?>