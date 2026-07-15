<?php
require_once '../lib/includes/Database.class.php';
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




$conn = DataBase::connection();


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$role_permissions_map = [
    'superadmin' => [
        'view_dashboard',
        'view_users', 'manage_users', 
        'view_challenges', 'manage_challenges', 
        'view_achievements', 'manage_achievements', 
        'view_activity_log',
        'manage_admin_users' 
    ],
    'editor' => [
        'view_dashboard',
        'view_challenges', 'manage_challenges',
        'view_achievements', 'manage_achievements',
        'view_activity_log'
    ],
    'viewer' => [
        'view_dashboard',
        'view_users',
        'view_challenges',
        'view_achievements',
        'view_activity_log'
    ]
];

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    
    $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM admin_users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin_user = $result->fetch_assoc();

        
        if (password_verify($password, $admin_user['password_hash'])) {
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $admin_user['id']; 
            $_SESSION['admin_username'] = $admin_user['username'];
            $_SESSION['admin_role'] = $admin_user['role'];

            
            $_SESSION['admin_permissions'] = $role_permissions_map[$admin_user['role']] ?? [];

            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error_message = 'Invalid username or password.';
        }
    } else {
        $error_message = 'Invalid username or password.';
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Sudo Society CTF</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --neon-green: #00ff88;
            --neon-pink: #ff00c8;
            --neon-blue: #00ffff;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: rgba(20, 20, 20, 0.7);
            --border-color: rgba(0, 255, 136, 0.2);
            --text-light: #c0fccc;
            --text-dark: #0a0a0a;
            --glow-light: 0 0 8px rgba(0, 255, 136, 0.5);
            --glow-medium: 0 0 15px rgba(0, 255, 136, 0.7);
        }

        body {
            font-family: 'Share Tech Mono', monospace;
            background-color: var(--darker-bg);
            color: var(--text-light);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Background Grid Effect */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(to right, rgba(0, 255, 136, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 255, 136, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: -1;
            animation: panBackground 60s linear infinite;
        }

        @keyframes panBackground {
            from {
                background-position: 0 0;
            }
            to {
                background-position: -1000px -1000px;
            }
        }

        .login-container {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 40px;
            box-shadow: var(--glow-medium);
            text-align: center;
            width: 90%;
            max-width: 400px;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-green);
            margin-bottom: 30px;
            font-size: 2em;
            text-shadow: var(--glow-light);
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--neon-blue);
            font-size: 0.9em;
        }

        .input-group input {
            width: calc(100% - 20px);
            padding: 12px 10px;
            border: 1px solid var(--neon-blue);
            background-color: var(--dark-bg);
            color: var(--text-light);
            border-radius: 8px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 1em;
            box-shadow: inset 0 0 5px rgba(0, 255, 255, 0.3);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--neon-green);
            box-shadow: inset 0 0 8px rgba(0, 255, 136, 0.5), 0 0 10px rgba(0, 255, 136, 0.5);
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background-color: var(--neon-green);
            color: var(--dark-bg);
            border: none;
            border-radius: 8px;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 0 10px var(--neon-green);
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
        }

        .login-button:hover {
            background-color: #00e676; /* Slightly darker green */
            box-shadow: 0 0 15px var(--neon-green), 0 0 25px rgba(0, 255, 136, 0.8);
            transform: translateY(-2px);
        }

        .error-message {
            color: var(--neon-pink);
            margin-top: 15px;
            font-size: 0.9em;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .login-container {
                padding: 30px 20px;
            }
            .login-container h2 {
                font-size: 1.8em;
            }
            .input-group input, .login-button {
                padding: 10px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="admin_login.php" method="POST">
            <div class="input-group">
                <label for="username"><i class="fas fa-user-secret"></i> Username</label>
                <input type="text" id="username" name="username" required autocomplete="off">
            </div>
            <div class="input-group">
                <label for="password"><i class="fas fa-key"></i> Password</label>
                <input type="password" id="password" name="password" required autocomplete="off">
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>
