<?php include('header.php'); ?>
<?php
session_start();
include('sidemenu.php') ?>
<?php

if ($_SESSION['user'] == "admin") { ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <style>
            /* Popup box styling */
            .custom-tooltip {
                position: absolute;
                z-index: 9999;
                background: #fff;
                color: #333;
                padding: 8px 12px;
                border-radius: 6px;
                border: 1px solid #ccc;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
                font-size: 13px;
                line-height: 1.4;
                max-width: 220px;
                display: none;
                /* hidden by default */
            }

            .custom-tooltip strong {
                color: #007bff;
            }

            .custom-tooltip .present {
                color: green;
                font-weight: 600;
            }

            .custom-tooltip .absent {
                color: red;
                font-weight: 600;
            }
        </style>
        <style>
            * {
                padding: 0;
                margin: 0;
                box-sizing: border-box;
            }

            body,
            html {
                overflow-x: hidden;
            }

            .footer-container {
                width: 101%;
                text-align: center;
                padding: 0px 0;
                background-color: #f8f9fa;
                /* Adjust as needed */
            }

            footer {
                margin: 0 auto;
                display: flex;
                justify-content: center;
            }

            #calendar {
                max-width: 100%;
                margin: 0 auto;
            }

            .card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
                cursor: pointer;
            }
        </style>
    </head>

    <body>

        <h4>Employees Summary</h4>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3" style="border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Total Employees</h5>
                        <p class="card-text">
                            <?php
                            include("connection.php");
                            $result = $conn->query("SELECT COUNT(*) AS total FROM practice");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3" style="border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Male </h5>
                        <p class="card-text">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) AS total FROM practice WHERE gender='Male'");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-secondary mb-3" style="border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Female</h5>
                        <p class="card-text">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) AS total FROM practice WHERE gender='Female'");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3" style="border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Present Employees</h5>
                        <p class="card-text">
                            <?php
                            // Replace 'attendance_date' with the actual column name for the attendance date in your table
                            $today_date = date('Y-m-d'); // Get today's date in 'YYYY-MM-DD' format
                            $query = "
                                SELECT COUNT(*) AS total 
                                FROM emp_attendance 
                                WHERE status = 'Present' AND date = '$today_date'
                            ";
                            $result = $conn->query($query);
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                            } else {
                                echo "0"; // Handle query errors gracefully
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3" style="border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Absent Employees</h5>
                        <p class="card-text">
                            <?php
                            // Replace 'attendance_date' with the actual column name for the attendance date in your table
                            $today_date = date('Y-m-d'); // Get today's date in 'YYYY-MM-DD' format
                            $query = "
                            SELECT COUNT(*) AS total 
                            FROM emp_attendance 
                            WHERE status = 'Absent' AND date = '$today_date'
                        ";
                            $result = $conn->query($query);
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                            } else {
                                echo "0"; // Handle query errors gracefully
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3" style="border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Late By(9:45:00)</h5>
                        <p class="card-text">
                            <?php
                            // Replace 'attendance_date' with the actual column name for the attendance date in your table
                            $today_date = date('Y-m-d'); // Get today's date in 'YYYY-MM-DD' format
                            $query = "
                    SELECT COUNT(*) AS total 
                    FROM emp_attendance 
                    WHERE check_in_time > '09:45:00' AND date = '$today_date'
                ";
                            $result = $conn->query($query);
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                            } else {
                                echo "0"; // Handle query errors gracefully
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Full Calendar Section -->
        <div id="calendar"></div>
        <div id="tooltip" class="custom-tooltip"></div>

        <!-- <br><br><br><br><br><br><br> -->
        </div>
        <div class="footer-container">
            <footer>
                <?php include("footer.php"); ?>
            </footer>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var tooltip = document.getElementById('tooltip');

                function ymd(d) {
                    return d.toISOString().split('T')[0]; // YYYY-MM-DD
                }

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: 'fetch_attendance.php',
                    displayEventTime: false,
                    displayEventEnd: false,

                    eventDidMount: function(info) {
                        info.el.style.background = 'none';
                        info.el.style.border = 'none';
                        info.el.style.boxShadow = 'none';
                    },

                    eventContent: function(arg) {
                        var wrapper = document.createElement('div');
                        wrapper.style.display = 'flex';
                        wrapper.style.flexDirection = 'column';
                        wrapper.style.justifyContent = 'flex-end';
                        wrapper.style.height = '100%';
                        wrapper.style.pointerEvents = 'none';

                        var container = document.createElement('div');
                        container.style.display = 'flex';
                        container.style.justifyContent = 'center';
                        container.style.gap = '4px';
                        container.style.margin = '0 4px 4px 4px';

                        if (arg.event.extendedProps && arg.event.extendedProps.dots) {
                            arg.event.extendedProps.dots.forEach(function(dotInfo) {
                                var dot = document.createElement('span');
                                dot.style.width = '10px';
                                dot.style.height = '10px';
                                dot.style.borderRadius = '50%';
                                dot.style.backgroundColor = dotInfo.color;
                                dot.style.boxShadow = '0 0 2px rgba(0,0,0,0.25)';
                                dot.style.pointerEvents = 'none';
                                container.appendChild(dot);
                            });
                        }

                        wrapper.appendChild(container);
                        return {
                            domNodes: [wrapper]
                        };
                    },

                    dayCellDidMount: function(info) {
                        let cellEl = info.el;

                        cellEl.addEventListener('mouseenter', function(e) {
                            let dateStr = ymd(info.date);
                            let eventsForDay = calendar.getEvents().filter(ev => ymd(ev.start) === dateStr);

                            if (!eventsForDay.length) return;

                            let html = `<div style="font-size:14px; font-weight:600; margin-bottom:6px; color:#007bff;">Attendance Details</div>`;

                            eventsForDay.forEach(ev => {
                                if (ev.extendedProps && ev.extendedProps.dots) {
                                    ev.extendedProps.dots.forEach(d => {
                                        let statusClass = '';
                                        let emoji = '';
                                        if (d.status === 'Present' || d.status === 'WFH') {
                                            statusClass = 'present';
                                            emoji = '✅';
                                        } else if (d.status === 'Leave') {
                                            statusClass = 'absent';
                                            emoji = '📅';
                                        } else if (d.status === 'Absent') {
                                            statusClass = 'absent';
                                            emoji = '❌';
                                        } else {
                                            emoji = 'ℹ️';
                                        }

                                        html += `
                               <b> <div style="display:flex; align-items:center; margin-bottom:4px;">
                                    <span style="font-size:16px; margin-right:6px;">${emoji}</span>
                                    <span class="${statusClass}" style="font-weight:600;">${d.name}</span>
                                    <span style="margin-left:auto; font-size:12px; color:#555;">${d.status}</span>
                                </div></b>
                            `;
                                    });
                                }
                            });

                            tooltip.innerHTML = html;
                            tooltip.style.display = 'block';
                            tooltip.style.opacity = '1';
                            tooltip.style.transform = 'translateY(0)';
                            tooltip.style.top = (e.pageY + 12) + "px";
                            tooltip.style.left = (e.pageX + 12) + "px";
                        });

                        cellEl.addEventListener('mousemove', function(e) {
                            if (tooltip.style.display === 'block') {
                                tooltip.style.top = (e.pageY + 12) + "px";
                                tooltip.style.left = (e.pageX + 12) + "px";
                            }
                        });

                        cellEl.addEventListener('mouseleave', function() {
                            tooltip.style.display = 'none'; // <- FIXED: fully hide, reset every time
                        });
                    }
                });

                calendar.render();
            });
        </script>
    </body>

    </html>
<?php } else {
    include('dashboard1.php');
} ?>