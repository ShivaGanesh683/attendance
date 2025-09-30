<!DOCTYPE html>
<html lang="en">

<head>

    <style>
        .header {
            margin-top: -20px;
            margin-left: 235px;
            padding: 15px;
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

    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
            <a class="navbar-brand" href="index.php">DASHBOARD</a>
            <div class="ml-auto d-flex align-items-center">
                <!-- Placeholder text -->
                <span id="datetime" class="text-white mr-4" style="font-weight: bold;">Loading time...</span>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employeelist.php">Employees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>


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
            <!-- Run JS immediately after DOM renders -->
            <script>
                function updateDateTime() {
                    const now = new Date();
                    const options = {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true
                    };
                    let formatted = now.toLocaleString('en-IN', options);
                    formatted = formatted.replace(/am|pm/gi, match => match.toUpperCase());

                    document.getElementById('datetime').innerText = formatted;
                }

                updateDateTime(); // Show immediately
                setInterval(updateDateTime, 1000); // Then keep updating every second
            </script>
</body>

</html>