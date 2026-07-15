<?php

require_once __DIR__ . '/functions.php'; 
require_once __DIR__ . '/db_config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; 

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, password_hash, username FROM admin_users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            redirect('../dashboard.html'); 
        } else {
            $message = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Sudo Society</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Basic styles for admin login - can be merged into admin.css */
        body {
            background-color: #0a0a0a;
            color: #00ff88;
            font-family: 'Share Tech Mono', monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden; /* For scanline effect */
        }
        .login-container {
            background: rgba(10, 10, 10, 0.9);
            border: 2px solid #00ff88;
            border-radius: 10px;
            padding: 3rem;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.7);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .login-container h2 {
            font-family: 'Orbitron', sans-serif;
            color: #00ffff;
            margin-bottom: 2rem;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #00ffff;
            background-color: rgba(0, 0, 0, 0.7);
            color: #00ff88;
            border-radius: 5px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 1rem;
        }
        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            outline: none;
            border-color: #ff00c8;
            box-shadow: 0 0 8px rgba(255, 0, 200, 0.5);
        }
        .login-container button {
            background-color: #00ff88;
            color: #0a0a0a;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .login-container button:hover {
            background-color: #00e673;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.8);
        }
        .message {
            color: #ff00c8;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .scanline { /* Re-use scanline from main site */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to bottom, transparent, #00ff88, transparent);
            animation: scan 4s linear infinite;
            z-index: 1000;
            pointer-events: none;
            opacity: 0.3;
        }
        @keyframes scan {
            0% { top: -2px; }
            100% { top: 100%; }
        }
    </style>
</head>
<body>
    <div class="scanline"></div>
    <div class="login-container">
        <h2>Sudo Society Admin Login</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="admin.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>