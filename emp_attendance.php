<?php
include('header.php'); ?>
<?php
session_start();
include('sidemenu.php');

// Employee Dropdown and Attendance Form

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empcode'])) {
    $empcode = $_POST['empcode'];
    $current_date = date('Y-m-d');

    // Connection to the database
    include("connection.php");

    if (isset($_POST['check_in'])) {
        // Check if the employee has already checked in today
        $stmt = $conn->prepare("SELECT e.name FROM emp_attendance AS ea 
                                JOIN practice AS e ON ea.empcode = e.id 
                                WHERE ea.empcode = ? AND ea.date = ?");
        $stmt->bind_param("ss", $empcode, $current_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the employee's name
            $row = $result->fetch_assoc();
            $employee_name = $row['name'];

            // Employee has already checked in today
            echo "<script>alert('$employee_name has already checked in today!');</script>";
        } else {
            // Fetch employee name from the practice table
            $stmt = $conn->prepare("SELECT name FROM practice WHERE id = ?");
            $stmt->bind_param("s", $empcode);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $employee_name = $row['name'];

            // Record Check-in time
            date_default_timezone_set('Asia/Kolkata');
            $check_in_time = date('H:i:s'); // Get current time
            $check_out_time = ""; // Empty initially
            $work_hours = ""; // Work hours will be calculated after check-out

            // Insert check-in record into the database
            $stmt = $conn->prepare("INSERT INTO emp_attendance (empcode, name, date, check_in_time, check_out_time, work_hours, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'present')");
            $stmt->bind_param("ssssss", $empcode, $employee_name, $current_date, $check_in_time, $check_out_time, $work_hours);
            $stmt->execute();

            echo "<script>alert('Check-in successful!');</script>";
        }
    } elseif (isset($_POST['check_out'])) {
        // Record Check-out time
        date_default_timezone_set('Asia/Kolkata');
        $check_out_time = date('H:i:s'); // Get current time

        // Check if there is an existing check-in record without a check-out time
        $stmt = $conn->prepare("SELECT check_in_time FROM emp_attendance WHERE empcode = ? AND date = ? AND check_out_time = ''");
        $stmt->bind_param("ss", $empcode, $current_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $check_in_time = $row['check_in_time'];

            // Calculate Work Hours
            $check_in_timestamp = strtotime($check_in_time);
            $check_out_timestamp = strtotime($check_out_time);

            if ($check_out_timestamp < $check_in_timestamp) {
                $check_out_timestamp += 86400; // Add 24 hours if crossed midnight
            }

            $work_duration = $check_out_timestamp - $check_in_timestamp;

            // Convert the work duration to hours, minutes, and seconds
            $hours = floor($work_duration / 3600);
            $minutes = floor(($work_duration % 3600) / 60);
            $seconds = $work_duration % 60;

            $work_hours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

            // Update Check-out record and Work Hours
            $stmt = $conn->prepare("UPDATE emp_attendance SET check_out_time = ?, work_hours = ? 
                                    WHERE empcode = ? AND date = ? AND check_out_time = ''");
            $stmt->bind_param("ssss", $check_out_time, $work_hours, $empcode, $current_date);
            $stmt->execute();

            echo "<script>alert('Check-out successful!');</script>";
        } else {
            // No check-in record found for today
            echo "<script>alert('Error: No check-in record found for this employee today. Cannot check out without checking in.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            /* Prevent horizontal scrolling */
            font-family: Arial, sans-serif;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            width: 102%;
            /* full width of container */
            max-width: 1200px;
            /* optional: match header width */
            margin-left: auto;
            /* center horizontally */
            margin-right: auto;
            box-sizing: border-box;
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

        /* Flexbox layout for form */
        .form-container {
            display: flex;
            justify-content: flex-start;
            /* align form to left (same as header) */
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            margin-left: 550px;
            /* remove fixed pixel margin */
        }

        .form-container select,
        .form-container input,
        .form-container button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 170px;
            /* Set width to keep things aligned */
            box-sizing: border-box;
        }

        .form-container button {
            cursor: pointer;
            width: 120px;
            /* Set button width to match */
        }

        .form-container button[type="submit"]:first-child {
            background-color: green;
            color: white;
        }

        .form-container button[type="submit"]:nth-child(2) {
            background-color: red;
            color: white;
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
                xhr.onreadystatechange = function() {
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
        setTimeout(function() {
            checkTimeAndMarkAbsent();
            setInterval(checkTimeAndMarkAbsent, 60000);
        }, delay);
    </script>
    <script>
        function openModal(empId, action) {
            document.getElementById("modal").style.display = "block";
            document.getElementById("emp_id").value = empId;
            document.getElementById("action").value = action;

            // Get current date
            const now = new Date();

            // Format the date to dd-mm-yyyy
            const formattedDate = now.getDate().toString().padStart(2, '0') + '-' +
                (now.getMonth() + 1).toString().padStart(2, '0') + '-' +
                now.getFullYear();

            // Set the formatted date to the input field
            document.getElementById("date").value = formattedDate;

            // Format the time to HH:mm:ss
            const formattedTime = now.toTimeString().split(' ')[0];

            // Set the time to the input field
            document.getElementById("time").value = formattedTime;
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        function showAlert(message) {
            alert(message);
        }
    </script>

</head>

<body>
    <h5>
        <b> Employee Attendance - <?php echo date('d F Y'); ?></b>
    </h5>

    <div class="card">
        <div class="card-topline"></div>

        <form method="post" action="emp_attendance.php" class="form-container" style="margin-left:600px;">
            <!-- Employee Selection Dropdown -->
            <select name="empcode" id="empcode" required style="padding: 5px 10px">
                <option value="" disabled selected>Select Employee</option>
                <?php
                include("connection.php");
                $result = $conn->query("SELECT id, name FROM practice");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <!-- Display Check-in Time -->
            <input type="hidden" id="check_in_time" name="check_in_time" readonly placeholder="Check-in Time">

            <!-- Display Check-out Time -->
            <input type="hidden" id="check_out_time" name="check_out_time" readonly placeholder="Check-out Time">

            <!-- Work Hours -->
            <input type="hidden" id="work_hours" name="work_hours" readonly placeholder="Work Hours">

            <!-- Buttons to Check-in and Check-out -->
            <button type="submit" name="check_in" onclick="openModal(1, 'check_in')" style="background-color: green; color: white; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer;">Check-in</button>
            <button type="submit" name="check_out" onclick="openModal(1, 'check-out')" style="background-color: red; color: white; padding: 4px 10px; border: none; border-radius: 5px; cursor: pointer;">Check-out</button>
        </form>

        <!-- Employee Attendance Table -->
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
                    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Fetch today's attendance records only
                    $sql = "SELECT ea.id, ea.date, ea.check_in_time, ea.check_out_time, ea.status, ea.work_hours, e.empcode, e.name
                    FROM emp_attendance AS ea
                    JOIN practice AS e ON ea.empcode = e.id
                    WHERE ea.date = CURDATE()
                    ORDER BY ea.date DESC
                    LIMIT $offset, $limit";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $formattedDate = date('d-m-Y', strtotime($row['date']));
                            echo "<tr style='text-align:center'>
                        <td>{$row['id']}</td>
                        <td>{$row['empcode']}</td>
                        <td>{$row['name']}</td>
                        <td>{$formattedDate}</td>
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
                        echo "<tr><td colspan='9' style='text-align: center;'>No attendance records for today</td></tr>";
                    }
                    // Get today's date
                    $current_date = date('Y-m-d');

                    // Count only today's records
                    $countResult = $conn->query("SELECT COUNT(*) AS total FROM emp_attendance WHERE date = '$current_date'");
                    $total = $countResult->fetch_assoc()['total'];
                    $totalPages = ceil($total / $limit);

                    ?>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <div>
                <?php
                $start = $offset + 1;
                $end = min($offset + $limit, $total);
                echo "<p>Showing $start to $end of $total entries</p>";
                ?>
            </div>

            <nav>
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" <?php echo ($i === $page) ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>">Next</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    </div>
</body>

</html>
<br>
<?php include('footer.php'); ?>