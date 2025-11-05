<?php
include('header.php');
session_start();
include('sidemenu.php');
include('connection.php');

$name = $_SESSION['user'] ?? 'User';
$today = date('Y-m-d');
$currentMonth = date('m');
$currentYear = date('Y');

// Fetch summary data — monthly working days added
$attendanceData = $conn->query("
    SELECT 
        (SELECT status FROM emp_attendance WHERE name='$name' AND date='$today' LIMIT 1) AS status,
        (SELECT COUNT(*) FROM emp_attendance 
            WHERE name='$name' 
            AND MONTH(date) = '$currentMonth' 
            AND YEAR(date) = '$currentYear') AS totalDays,
        (SELECT COUNT(*) FROM practice) AS totalLeaves,
        (SELECT COUNT(*) FROM practice WHERE gender='Male') AS pendingLeaves,
        (SELECT COUNT(*) FROM practice WHERE gender='Female') AS remainingLeaves
")->fetch_assoc();

$status = $attendanceData['status'] ?? 'Not Marked';
$totalDays = $attendanceData['totalDays'] ?? 0;
$totalLeaves = $attendanceData['totalLeaves'] ?? 0;
$pendingLeaves = $attendanceData['pendingLeaves'] ?? 0;
$remainingLeaves = $attendanceData['remainingLeaves'] ?? 0;

// Random motivational quote
$quotes = [
    "Stay positive, work hard, and make it happen!",
    "Success is the sum of small efforts repeated daily.",
    "Discipline is the bridge between goals and achievement.",
    "Don’t count the days, make the days count.",
    "Your dedication inspires progress every day!"
];
$randomQuote = $quotes[array_rand($quotes)];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js" defer></script>
    <style>
        body,
        html {
            overflow-x: hidden;
        }

        .footer-container {
            width: 100%;
            text-align: center;
            background-color: #f8f9fa;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h4 {
            margin: 15px 0;
            font-weight: 600;
        }

        /* Floating greeting box (top-right) */
        #greetingModal {
            position: fixed;
            top: 40px;
            /* ↓ moved slightly higher */
            right: 30px;
            background: #fff;
            border-radius: 10px;
            width: 250px;
            padding: 6px 10px;
            /* ↓ reduced top-bottom padding */
            text-align: left;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
            z-index: 2000;
            animation: slideIn 0.4s ease-in-out;
        }

        .close-btn {
            float: right;
            font-size: 12px;
            cursor: pointer;
            color: #888;
            font-weight: bold;
        }

        .close-btn:hover {
            color: #000;
        }

        .greeting {
            font-size: 14px;
            font-weight: 600;
            color: #007bff;
            margin-top: 2px;
            /* ↓ less space */
            margin-bottom: 4px;
        }

        .summary-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 4px 6px;
            /* ↓ reduced internal spacing */
            margin-top: 4px;
            /* ↓ less vertical gap */
            font-size: 11.5px;
            line-height: 1.3;
        }

        .quote {
            margin-top: 4px;
            /* ↓ reduced */
            font-size: 10.5px;
            color: #555;
            font-style: italic;
        }


        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>


<?php
// Set timezone to India
date_default_timezone_set('Asia/Kolkata');

$hour = date('H');

if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

$name = isset($_SESSION['user']) && !empty($_SESSION['user']) ? $_SESSION['user'] : 'User';
?>
<div class="greeting">👋 <?= $greeting; ?>, <?= ucfirst(htmlspecialchars($name)); ?>!</div>


<body>
    <h4>My Leave Summary</h4>

    <!-- Greeting Box -->
    <div id="greetingModal">
        <span class="close-btn" id="closeModal">&times;</span>
        <div class="greeting">👋 Hello, <?= htmlspecialchars($name); ?>!</div>
        <div class="summary-box">
            <p><b>📅 Date:</b> <?= date('l, d M Y'); ?></p>
            <p><b>Status Today:</b>
                <?php
                $color = ($status == 'Present') ? 'green' : (($status == 'Absent') ? 'red' : 'gray');
                echo "<span style='color:$color;font-weight:600;'>$status</span>";
                ?>
            </p>
            <p><b>Working Days (<?= date('M Y'); ?>):</b> <?= $totalDays; ?></p>
        </div>
        <div class="quote">“<?= $randomQuote; ?>”</div>
    </div>
    <!-- <div class="greeting">👋 Hello, <?= htmlspecialchars($name); ?>!</div> -->
    <!-- Summary Cards -->
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3" style="border-radius: 8px;">
                <div class="card-body">
                    <h5>Total Leaves</h5>
                    <p><?= $totalLeaves; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3" style="border-radius: 8px;">
                <div class="card-body">
                    <h5>Pending Leave Requests</h5>
                    <p><?= $pendingLeaves; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3" style="border-radius: 8px;">
                <div class="card-body">
                    <h5>Remaining Leaves</h5>
                    <p><?= $remainingLeaves; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div id="calendar"></div>

    <div class="footer-container">
        <footer><?php include("footer.php"); ?></footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Floating popup auto-close
            const modal = document.getElementById('greetingModal');
            const closeModal = document.getElementById('closeModal');
            closeModal.onclick = () => modal.style.display = 'none';
            setTimeout(() => modal.style.display = 'none', 6000);

            // Load calendar
            setTimeout(() => {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: 'fetch_attendance.php',
                    dayCellDidMount: function(info) {
                        if (info.date.getDay() === 0) {
                            info.el.style.backgroundColor = '#eaf4fc';
                            info.el.style.color = '#004085';
                            info.el.style.border = '1px solid #bee5eb';
                        }
                    }
                });
                calendar.render();
            }, 500);
        });
    </script>
</body>

</html>