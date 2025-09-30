<?php include('header.php'); ?>
<?php 
session_start();
include('sidemenu.php'); ?>
<?php include('connection.php');?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip Dashboard</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f7f7f7; 
            margin: 0; 
            padding: 0; 
            overflow: hidden; /* no scroll bar */
        }
        .card {
            position: absolute;      /* position relative to screen */
            top: 50%;                /* move to center vertically */
            left: 50%;               /* move to center horizontally */
            transform: translate(-50%, -50%); /* perfect centering */
            background: #fff;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            border-radius: 8px;
            width: 550px;
            max-width: 90%;
        }
        h2 { text-align: center; margin-bottom: 30px; }
        form { display: flex; flex-direction: column; gap: 20px; }
        label { font-weight: bold; }
        select, input[type="month"] { 
            padding: 10px; 
            font-size: 16px; 
            width: 100%; 
            border-radius: 5px; 
            border: 1px solid #ccc; 
        }
        button { 
            padding: 12px; 
            font-size: 16px; 
            background-color: #007bff; 
            color: #fff; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="card">
    <h2>Generate Employee Payslip</h2>
    <form action="generate_payslip.php" method="get">
        <!-- Employee Selection -->
        <div>
            <label for="empcode">Select Employee:</label>
            <select name="empcode" id="empcode" required>
                <option value="">-- Select Employee --</option>
                <?php
                $sql = "SELECT empcode, name FROM practice ORDER BY name";
                $result = $conn->query($sql);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<option value='{$row['empcode']}'>{$row['name']} ({$row['empcode']})</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- Month Selection -->
        <div>
            <label for="month">Select Month:</label>
            <input type="month" name="month" id="month" value="<?php echo date('Y-m'); ?>" required>
        </div>

        <!-- Button -->
        <div style="display:flex; justify-content:center;">
            <button type="submit">Generate Payslip PDF</button>
        </div>
    </form>
</div>

</body>
</html>
