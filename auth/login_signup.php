<?php
include '../config/conn.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['userType'] = $user['userType'];

        $currentTime = date('Y-m-d H:i:s');
        $updateQuery = "
        UPDATE users 
        SET last_login = '$currentTime', status = 'active' 
        WHERE user_id = " . $user['user_id'];
        mysqli_query($conn, $updateQuery);

        if ($user['userType'] == 'admin') {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../index.php');
        }
        exit();
    } else {
        $login_error = "Invalid credentials!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $existingUser = mysqli_fetch_assoc($result);

    if ($existingUser) {
        $signup_error = "Email already exists!";
    } else {
        $query = "INSERT INTO users (username, email, password, userType, status, last_login) 
                  VALUES ('$username', '$email', '$hashedPassword', 'customer', 'active', NOW())";
        if (mysqli_query($conn, $query)) {
            $signup_success = "Account created successfully! Please login.";
        } else {
            $signup_error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 350px;
            padding: 30px;
            text-align: center;
        }

        h2 {
            font-weight: 500;
            margin-bottom: 20px;
            font-size: 22px;
        }

        p {
            color: #666;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .switch-link {
            color: #4CAF50;
            text-decoration: none;
            cursor: pointer;
            font-weight: 500;
        }

        .switch-link:hover {
            text-decoration: underline;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        .error,
        .success {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-container active" id="login-form">
            <h2>Login</h2>
            <form action="login_signup.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            <?php if (isset($login_error)) {
                echo "<p class='error'>$login_error</p>";
            } ?>
            <p>Don't have an account? <span class="switch-link" onclick="switchForm('signup')">Sign Up</span></p>
        </div>

        <div class="form-container" id="signup-form">
            <h2>Sign Up</h2>
            <form action="login_signup.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup" class="btn">Sign Up</button>
            </form>
            <?php if (isset($signup_error)) {
                echo "<p class='error'>$signup_error</p>";
            } ?>
            <?php if (isset($signup_success)) {
                echo "<p class='success'>$signup_success</p>";
            } ?>
            <p>Already have an account? <span class="switch-link" onclick="switchForm('login')">Login</span></p>
        </div>
    </div>

    <script>
        function switchForm(formType) {
            if (formType === 'login') {
                document.getElementById('login-form').classList.add('active');
                document.getElementById('signup-form').classList.remove('active');
            } else if (formType === 'signup') {
                document.getElementById('signup-form').classList.add('active');
                document.getElementById('login-form').classList.remove('active');
            }
        }
    </script>

</body>

</html>