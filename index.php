<?php
session_start();
include "includes/db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit;
        } else {
            $msg = "Incorrect password.";
        }
    } else {
        $msg = "Email not registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker - Login</title>
    <link rel="stylesheet" href="css/shared.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #222;
            margin-bottom: 8px;
            font-size: 2.2em;
            font-weight: 700;
            letter-spacing: -1px;
        }
        .login-header p {
            color: #666;
            font-size: 1.1em;
        }
        .msg {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background-color: #ffebee;
            color: #c62828;
            border-radius: 8px;
            font-size: 0.95em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.15s;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.15s;
        }
        .login-btn:hover {
            background-color: #2563eb;
        }
        .register-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .register-link p {
            color: #666;
            margin-bottom: 8px;
        }
        .register-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }
        .register-link a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Fitness Tracker</h1>
                <p>Welcome back! Please sign in to continue.</p>
            </div>
            
            <?php if ($msg): ?>
                <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="login-btn">Sign In</button>
            </form>

            <div class="register-link">
                <p>Don't have an account?</p>
                <a href="register.php">Create a new account</a>
            </div>
        </div>
    </div>
</body>
</html>
