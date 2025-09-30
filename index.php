<?php
ob_start();
require_once 'connection.php';
session_start();

// Clear error message on refresh
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = mysqli_real_escape_string($conn, $_POST['uname']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['pwd']);
    $password = md5($mypassword);

    $sql = "SELECT name1 FROM login WHERE name1='$myusername' AND passowrd='$password'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $count = mysqli_num_rows($result);

    if ($count == 1) {
        $_SESSION['user'] = $row['name1'];
        header("location: dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "❌ Invalid Username or Password!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Employee Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('background1.jpg') no-repeat center center/cover;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
        }

        .login-box {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 50px;
            /* increased from 40px */
            border-radius: 20px;
            width: 420px;
            /* increased from 350px */
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            color: white;
            animation: slideIn 1s ease-out;
        }

        .login-box h2 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .input-box {
            position: relative;
            margin: 20px 0;
        }

        .input-box input {
            width: 100%;
            padding: 15px;
            /* increased from 12px */
            border: none;
            outline: none;
            background: transparent;
            border-bottom: 2px solid white;
            color: white;
            font-size: 17px;
            /* slightly bigger */
        }

        .input-box label {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #ddd;
            pointer-events: none;
            transition: 0.3s;
        }

        .input-box input:focus~label,
        .input-box input:valid~label {
            top: -8px;
            font-size: 12px;
            color: #00f2fe;
        }

        .toggle-pwd {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 14px;
            color: #00f2fe;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 25px;
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-box button:hover {
            box-shadow: 0 0 15px #00f2fe;
        }

        .error-message {
            margin-top: 15px;
            color: #ff6b6b;
            font-weight: bold;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer {
            margin-top: 15px;
            font-size: 12px;
            color: #ccc;
        }
    </style>
</head>

<body>
    <div class="overlay"></div>
    <div class="login-box">
        <h2>Employee Management</h2>
        <form method="POST">
            <div class="input-box">
                <input type="text" name="uname" required>
                <label>Username</label>
            </div>
            <div class="input-box">
                <input type="password" name="pwd" id="pwd" required>
                <label>Password</label>
                <span class="toggle-pwd" onclick="togglePassword()">👁️</span>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>
        <footer><b>© <?php echo date("Y"); ?> Employee Management</b></footer>
    </div>

    <script>
        function togglePassword() {
            let pwd = document.getElementById("pwd");
            pwd.type = (pwd.type === "password") ? "text" : "password";
        }
    </script>
</body>

</html>