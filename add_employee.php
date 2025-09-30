<?php 
session_start();
if($_SESSION['user']) {
$ses=$_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden; /* 🚀 prevents scrolling */
            background: linear-gradient(135deg, #cfd2dc, #eceef3);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            width: 90%;
            max-width: 600px;
            border: 2px solid #333;
            border-radius: 8px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.15);
        }
        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .floating-input {
            position: relative;
            margin-bottom: 15px;
        }
        .floating-input input,
        .floating-input select {
            width: 100%;
            padding: 12px 10px;
            background: #fff;
            border: 2px solid #999;
            border-radius: 10px;
            color: #333;
            outline: none;
            font-size: 16px;
        }
        .floating-input label {
            position: absolute;
            top: 12px;
            left: 15px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
            pointer-events: none;
        }
        .floating-input input:focus + label,
        .floating-input input:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            background: #f6f7fa;
            padding: 0 4px;
        }
        .gender-group {
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }
        .gender-group label {
            color: #444;
            font-weight: 600;
        }
        .form-actions {
            text-align: center;
            margin-top: 15px;
        }
        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: #666;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin: 0 5px;
            transition: 0.3s;
        }
        .form-actions button:hover {
            background: #444;
        }
        #progress-bar-container {
            width: 100%;
            height: 6px;
            background-color: #ddd;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 5px;
        }
        #progress-bar {
            height: 100%;
            width: 0%;
            background-color: #666;
            transition: width 0.3s;
        }
        small {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>ADD EMPLOYEE</h2>
    <form action="emp_insert.php" method="POST">

        <div class="floating-input">
            <input type="text" name="ecode" id="ecode" placeholder=" " required readonly>
            <label for="ecode">Employee Code</label>
        </div>

        <div class="floating-input">
            <input type="text" name="name" id="name" placeholder=" " required>
            <label for="name">Name</label>
            <input type="hidden" name="user" value="<?php echo $ses; ?>">
        </div>

        <div class="floating-input">
            <input type="email" name="email" id="email" placeholder=" " required>
            <label for="email">Email</label>
        </div>

        <div class="floating-input">
            <input type="text" name="mobile_number" id="mobile_number" placeholder=" " maxlength="10" pattern="\d{10}" required oninput="validateMobileNumber(this)">
            <label for="mobile_number">Mobile Number</label>
        </div>

        <div class="floating-input">
            <input type="number" name="salary" id="salary" placeholder=" " step="0.01" required>
            <label for="salary">Salary / Annum</label>
        </div>

        <div class="floating-input">
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">Password</label>
            <small>Password must be 8+ characters, include uppercase, number, symbol.</small>
            <div id="progress-bar-container">
                <div id="progress-bar"></div>
            </div>
            <input type="checkbox" id="show-password"> <label for="show-password">Show Password</label>
        </div>

        <div class="gender-group">
            <label><input type="radio" name="gender" value="Male" required> Male</label>
            <label><input type="radio" name="gender" value="Female" required> Female</label>
        </div>

        <div class="form-actions">
            <button type="submit" onclick="return validatePassword()">Submit</button>
            <button type="button" onclick="location.href='employeelist.php'">Close</button>
        </div>
    </form>
</div>
<script>
    document.getElementById("password").addEventListener("input", function () {
        const password = this.value;
        let criteriaMet = 0;
        if (/.{8,}/.test(password)) criteriaMet++;
        if (/[A-Z]/.test(password)) criteriaMet++;
        if (/\d/.test(password)) criteriaMet++;
        if (/[^A-Za-z0-9]/.test(password)) criteriaMet++;
        const progress = (criteriaMet / 4) * 100;
        document.getElementById("progress-bar").style.width = progress + "%";
    });
    document.getElementById("show-password").addEventListener("change", function () {
        const pw = document.getElementById("password");
        pw.type = this.checked ? "text" : "password";
    });
    function validatePassword() {
        const pw = document.getElementById("password").value;
        const valid = [/.{8,}/, /[A-Z]/, /\d/, /[^A-Za-z0-9]/].every(r => r.test(pw));
        if (!valid) {
            alert("Password does not meet requirements.");
            return false;
        }
        return true;
    }
    function validateMobileNumber(input) {
        if (!/^\d{10}$/.test(input.value)) {
            input.setCustomValidity("Please enter exactly 10 digits.");
        } else {
            input.setCustomValidity("");
        }
    }
</script>
</body>
</html>
<?php 
} else {
    session_destroy();
    session_unset();
    header('Location:../../index.php?authentication failed');
}
?>
