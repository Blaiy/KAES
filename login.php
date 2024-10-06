<?php
// login.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KAES</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            color: maroon;
            margin-bottom: 20px;
            font-style: oblique;

        }

        .form-input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-input:focus {
            outline: none;
            border-color: maroon;
        }

        .login-btn {
            background-color: maroon;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 80%;
        }

        .login-btn:hover {
            background-color: darkred;
        }

        .register-link {
            display: block;
            margin-top: 15px;
            text-align: center;
        }

        .register-link a {
            color: maroon;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>KAES</h2>
    <form action="login_handler.php" method="POST">
        <input type="email"  class="form-input"name="email" placeholder="Email" required>
        <input type="password" class="form-input" name="password" placeholder="Password" required>
        <button type="submit" class="login-btn">Login</button>
    </form>
    <div class="register-link">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>
