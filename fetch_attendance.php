<?php
include("connection.php");

$events_by_date = [];

// Fetch attendance
$query = "SELECT name, status, date FROM emp_attendance";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $color = '';
        if ($row['status'] === 'Present' || $row['status'] === 'WFH') {
            $color = 'green';
        } elseif ($row['status'] === 'Leave' || $row['status'] === 'Absent') {
            $color = 'red';
        }

        // Always push employee info (even if no color, to show status in tooltip)
        $events_by_date[$row['date']][] = [
            'name'  => $row['name'],   // employee name
            'status' => $row['status'], // employee status
            'color'  => $color          // dot color
        ];
    }
}

// Convert grouped dots into one event per date
$events = [];
foreach ($events_by_date as $date => $dots) {
    $events[] = [
        'start' => $date,
        'dots'  => $dots // custom property for dots
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
