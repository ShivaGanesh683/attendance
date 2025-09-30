<?php
include 'header.php';
session_start();
include 'sidemenu.php';

if ($_SESSION['user'] == "admin") {
?>
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
            width: 105%;
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
    </style>
</head>
<body>
    <h5><b>Employee Leave Records - <?php echo date('F Y'); ?></b></h5>

    <div class="card">
        <div class="card-topline"></div>

        <div class="table-scrollable">
            <table class="table table-hover table-striped table-checkable order-column full-width">
                <thead>
                    <tr style="text-align: center">
                        <th>ID</th>
                        <th>Emp Code</th>
                        <th>Name</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Leave Type</th>
                        <th>Leave Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include "connection.php";

                        $limit  = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
                        $page   = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        // Only fetch leave records
                        $sql = "SELECT
                            ea.id, ea.from_date, ea.to_date, ea.leave_type, ea.leave_reason, ea.status,
                            e.empcode, e.name
                        FROM emp_attendance AS ea
                        JOIN practice AS e ON ea.empcode = e.id
                        WHERE ea.status = 'Leave'
                        ORDER BY ea.from_date DESC, ea.id DESC
                        LIMIT $offset, $limit";

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $from_date = date('d-m-Y', strtotime($row['from_date']));
                                $to_date   = date('d-m-Y', strtotime($row['to_date']));
                                echo "<tr style='text-align:center'>
                                    <td>{$row['id']}</td>
                                    <td>{$row['empcode']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$from_date}</td>
                                    <td>{$to_date}</td>
                                    <td>{$row['leave_type']}</td>
                                    <td>{$row['leave_reason']}</td>
                                    <td>{$row['status']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align: center;'>No leave records found</td></tr>";
                        }

                        $countResult = $conn->query("SELECT COUNT(*) AS total FROM emp_attendance WHERE status='Leave'");
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
</body>
</html>
<?php
} else {
    include 'user_attend.php';
}
?>
<br><br><br><br><br>
<?php include 'footer.php'; ?>
