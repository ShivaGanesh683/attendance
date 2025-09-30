<?php
ob_start();
require_once 'connection.php';

session_start();

// Clear the error message if the user has refreshed the page
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the message after displaying
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from Form
    $myusername = addslashes($_POST['uname']);
    $mypassword = addslashes($_POST['pwd']);
    $password = md5($mypassword);

    // Query to check for matching credentials
    $sql = "SELECT name1, passowrd FROM login WHERE name1='$myusername' AND passowrd='$password'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row = mysqli_fetch_array($result);
    $count = mysqli_num_rows($result);
    $user = $row['name1'];

    if ($count == 1) {
        // Successful login, set session variables
        $_SESSION['user'] = $user;
        header("location: dashboard.php");
        exit();
    } else {
        // Store the error message in the session
        $_SESSION['error_message'] = "Wrong Username or Password!";
        // Reload the page to show the error message
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            background-image: url('background1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        table {
            border: 1px solid;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.5); /* White background with transparency */
        }

        #type {
            width: 300px;
            height: 15px;
            border: 0;
            outline: 0;
            background: #1a1a1a;
            border-bottom: 3px solid black;
            color: white;
            font-size: 15px;
            padding: 5px;
            border-radius: 5px;
        }

        input::-webkit-input-placeholder {
            font-size: 20px;
            line-height: 3;
            color: white;
        }

        #btn {
            width: 300px;
            height: 35px;
            font-size: 20px;
            border-bottom: 3px solid black;
        }

        .error-message {
            color: red;
            text-align: center;
            font-size: 16px;
            margin-top: 10px;
        }
        marquee {
            color: red; /* Red color for the text */
            font-size: 24px; /* Set font size */
        }
    </style>
    <title>Login Page</title>
</head>
<body>
    <marquee direction="left" scrollamount="10" scrolldelay="50" behavior="alternate">
       <b> Welcome to Employee Management System...! </b>
    </marquee>          

<table width="20%" border="3" align="center" cellspacing="20" style="margin-top:100px">
    <form action="" method="POST">
        <tr>
            <td align="center">
                <img src="https://img.icons8.com/?size=100&id=3225&format=png&color=000000" width="150px">
            </td>
        </tr> 
        <tr>
            <td><input type="text" name="uname" placeholder="User Name" id="type"></td>
        </tr>
        <tr>
            <td><input type="password" name="pwd" placeholder="Password" id="type"></td>
        </tr>
        <tr>
            <td align="center"><input type="submit" value="Login" id="btn"></td>
        </tr>
    </form>
</table>

<?php
// Display the error message if it exists
if (isset($error_message)) {
    echo "<div class='error-message'>$error_message</div>";
}
?>

</body>
</html>
