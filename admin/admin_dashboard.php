<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Sudo Society CTF</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Color Variables */
        :root {
            --neon-green: #00ff88;
            --neon-pink: #ff00c8;
            --neon-blue: #00ffff;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: rgba(20, 20, 20, 0.7);
            --border-color: rgba(0, 255, 136, 0.2);
            --glow-light: 0 0 8px rgba(0, 255, 136, 0.5);
            --glow-medium: 0 0 15px rgba(0, 255, 136, 0.7);
            --glow-intense: 0 0 25px rgba(0, 255, 136, 0.9);
            --text-light: #c0fccc;
            --text-dimmed: rgba(192, 252, 204, 0.6);
            --positive-change: #28a745;
            --negative-change: #dc3545;
            --button-hover-bg: rgba(0, 255, 136, 0.1);

            /* Extended Chart Colors (Direct RGBA for Chart.js compatibility) */
            --chart-color-1: rgba(0, 255, 136, 0.8); /* Neon Green */
            --chart-color-2: rgba(0, 200, 255, 0.8); /* Neon Blue */
            --chart-color-3: rgba(255, 0, 200, 0.8); /* Neon Pink */
            --chart-color-4: rgba(255, 150, 0, 0.8); /* Orange */
            --chart-color-5: rgba(150, 0, 255, 0.8); /* Purple */
            --chart-color-6: rgba(0, 255, 255, 0.8); /* Aqua */
            --chart-color-7: rgba(255, 255, 0, 0.8); /* Yellow */
            --chart-color-8: rgba(50, 205, 50, 0.8); /* Lime Green */
            --chart-color-9: rgba(255, 99, 71, 0.8); /* Tomato */
            --chart-color-10: rgba(100, 149, 237, 0.8); /* Cornflower Blue */
            --chart-color-11: rgba(218, 112, 214, 0.8); /* Orchid */
            --chart-color-12: rgba(127, 255, 212, 0.8); /* Aquamarine */
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Share Tech Mono', monospace;
            background-color: var(--dark-bg);
            color: var(--text-light);
            overflow-x: hidden;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(0, 255, 136, 0.05) 0%, transparent 25%),
                radial-gradient(circle at 80% 70%, rgba(0, 200, 255, 0.05) 0%, transparent 25%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to bottom, transparent, var(--neon-green), transparent);
            animation: scan 4s linear infinite;
            z-index: 1000;
            pointer-events: none;
            opacity: 0.3;
        }

        @keyframes scan {
            0% { top: -2px; }
            100% { top: 100%; }
        }

        /* Header */
        header {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo-img {
            height: 40px;
            filter: drop-shadow(0 0 8px var(--neon-green));
        }

        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--neon-green);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: var(--glow-light);
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-username {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            font-weight: 700;
            font-size: 1rem;
        }

        .logout-btn {
            background: rgba(255, 0, 200, 0.2);
            border: 1px solid var(--neon-pink);
            color: var(--neon-pink);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--neon-pink);
            color: var(--dark-bg);
            box-shadow: 0 0 15px var(--neon-pink);
        }

        /* Main Dashboard Layout */
        .admin-dashboard-content {
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Tabs Navigation */
        .tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
        }

        .tab-btn {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-bottom: none; /* Hide bottom border for selected tab effect */
            border-radius: 8px 8px 0 0;
            padding: 0.8rem 1.5rem;
            color: var(--text-dimmed);
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tab-btn:hover {
            color: var(--neon-green);
            border-color: var(--neon-green);
            box-shadow: 0 -5px 15px rgba(0, 255, 136, 0.1);
        }

        .tab-btn.active {
            background: var(--darker-bg);
            color: var(--neon-blue);
            border-color: var(--neon-blue);
            box-shadow: 0 -5px 20px rgba(0, 255, 255, 0.5);
            transform: translateY(-3px);
            z-index: 1;
        }

        /* Tab Content */
        .tab-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            backdrop-filter: blur(5px);
            display: none; /* Hidden by default */
        }

        .tab-content.active {
            display: block; /* Show active tab content */
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-green);
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 5px rgba(0, 255, 136, 0.3);
        }

        .add-btn, .action-btn {
            background: var(--neon-blue);
            color: var(--dark-bg);
            border: none;
            border-radius: 5px;
            padding: 0.7rem 1.2rem;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        .add-btn:hover, .action-btn:hover {
            background: var(--neon-green);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.7);
            transform: translateY(-2px);
        }
        .action-btn.delete {
            background: var(--neon-pink);
            box-shadow: 0 0 10px rgba(255, 0, 200, 0.5);
        }
        .action-btn.delete:hover {
            background: #ff3399;
            box-shadow: 0 0 15px rgba(255, 0, 200, 0.7);
        }
        .action-btn.toggle-active {
            background: rgba(0, 255, 136, 0.2);
            color: var(--neon-green);
            border: 1px solid var(--neon-green);
            box-shadow: none;
        }
        .action-btn.toggle-active:hover {
            background: var(--neon-green);
            color: var(--dark-bg);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.7);
        }
        .action-btn.toggle-active.inactive {
            background: rgba(255, 0, 200, 0.2);
            color: var(--neon-pink);
            border: 1px solid var(--neon-pink);
        }
        .action-btn.toggle-active.inactive:hover {
            background: var(--neon-pink);
            color: var(--dark-bg);
        }


        /* Data Tables */
        .data-table-container {
            overflow-x: auto; /* Enable horizontal scrolling for tables on small screens */
            width: 100%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            min-width: 700px; /* Ensure table doesn't get too narrow */
        }

        .data-table th, .data-table td {
            border: 1px solid var(--border-color);
            padding: 0.8rem;
            text-align: left;
            font-size: 0.9rem;
        }

        .data-table th {
            background-color: rgba(0, 255, 136, 0.1);
            color: var(--neon-green);
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            white-space: nowrap; /* Prevent headers from wrapping */
        }

        .data-table tr:nth-child(even) {
            background-color: rgba(10, 10, 10, 0.2);
        }

        .data-table tr:hover {
            background-color: rgba(0, 255, 136, 0.05);
        }

        .data-table td {
            color: var(--text-light);
        }

        .data-table td .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap; /* Allow action buttons to wrap */
        }

        .data-table td .actions button {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }

        /* Forms (Add/Edit) - Inline Forms for Challenges/Achievements Add */
        .inline-form-section {
            background: rgba(10, 10, 10, 0.5);
            border: 1px solid rgba(0, 255, 136, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            display: none; /* Hidden by default */
        }

        .inline-form-section h3 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="email"],
        .form-group textarea,
        .form-group select,
        .form-group input[type="date"] {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 5px;
            color: var(--neon-green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group input[type="email"]:focus,
        .form-group textarea:focus,
        .form-group select:focus,
        .form-group input[type="date"]:focus {
            border-color: var(--neon-green);
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .form-actions button {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }

        /* Dashboard Stats Grid */
        .dashboard-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(10, 10, 10, 0.5);
            border: 1px solid rgba(0, 255, 136, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s ease;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--neon-green);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-green);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: var(--glow-light);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-dimmed);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Chart for Category Distribution */
        .chart-container {
            height: 300px;
            width: 100%;
            margin-top: 1.5rem;
            position: relative; /* For chart messages */
        }

        /* --- MODAL STYLES --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s ease-in-out;
        }

        .modal-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--neon-blue);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.6);
            max-width: 600px;
            width: 90%;
            transform: translateY(-20px) scale(0.9);
            opacity: 0;
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            position: relative;
            max-height: 90vh; /* Limit height for scrollable content */
            overflow-y: auto; /* Enable scrolling for modal content */
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-content h3 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            margin-bottom: 15px;
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
        }

        .modal-close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: var(--text-dimmed);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .modal-close-btn:hover {
            color: var(--neon-pink);
        }

        /* Specific modal content styling */
        .category-challenges-list {
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }
        .category-challenges-list li {
            background: rgba(10, 10, 10, 0.3);
            border: 1px solid rgba(0, 255, 136, 0.1);
            border-radius: 5px;
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }
        .category-challenges-list li strong {
            color: var(--neon-green);
        }
        .category-challenges-list li span {
            color: var(--text-light);
        }
        .category-challenges-list li .points {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
        }

        /* Custom Message Box (from previous code) - ensure it has higher z-index than modals */
        .custom-message-box-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95); /* Darker background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10001; /* Higher than regular modals */
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s ease-in-out;
        }

        .custom-message-box {
            background: var(--card-bg);
            border: 1px solid var(--neon-green);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.8); /* More intense glow */
            max-width: 450px;
            text-align: center;
            transform: translateY(-20px) scale(0.9);
            opacity: 0;
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .custom-message-box-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .custom-message-box-overlay.show .custom-message-box {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .custom-message-box h3 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            margin-bottom: 15px;
            font-size: 1.6rem;
            font-weight: 700;
        }

        .custom-message-box p {
            font-size: 1rem;
            margin-bottom: 20px;
            line-height: 1.6;
            color: var(--text-light);
        }

        .custom-message-box button.confirm-btn,
        .custom-message-box button.ok-btn {
            background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
            color: var(--dark-bg);
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
            margin: 0 5px;
        }
        .custom-message-box button.cancel-btn {
            background: rgba(255, 0, 200, 0.2);
            color: var(--neon-pink);
            border: 1px solid var(--neon-pink);
            padding: 10px 25px;
            border-radius: 5px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0 5px;
        }

        .custom-message-box button.confirm-btn:hover {
            background: linear-gradient(45deg, var(--neon-blue), var(--neon-green));
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.8);
        }
        .custom-message-box button.cancel-btn:hover {
            background: var(--neon-pink);
            color: var(--dark-bg);
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(255, 0, 200, 0.8);
        }


        /* Footer */
        footer {
            padding: 2rem 0;
            background: rgba(5, 5, 5, 0.95);
            border-top: 1px solid var(--border-color);
            text-align: center;
            margin-top: auto;
            position: relative;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--neon-green), transparent);
            box-shadow: 0 0 10px var(--neon-green);
        }

        .copyright {
            color: var(--text-dimmed);
            font-size: 0.9rem;
        }

        /* Responsive Media Queries */
        @media (max-width: 1200px) {
            .container {
                padding: 0 1.5rem;
            }
            .data-table {
                min-width: 600px; /* Allow tables to shrink slightly on larger tablets */
            }
        }

        @media (max-width: 1024px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            .admin-info {
                width: 100%;
                justify-content: space-between;
                margin-top: 1rem;
            }
            .tabs {
                flex-direction: column;
                align-items: flex-start;
                border-bottom: none; /* Remove bottom border when stacked */
            }
            .tab-btn {
                width: 100%;
                border-radius: 8px; /* Full border radius when stacked */
                border-bottom: 1px solid var(--border-color); /* Add back bottom border */
            }
            .tab-btn:last-child {
                border-bottom: 1px solid var(--border-color); /* Ensure last tab also has border */
            }
            .tab-btn.active {
                transform: translateY(0); /* No transform when stacked */
            }
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .data-table-container {
                overflow-x: auto; /* Ensure horizontal scroll is available */
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            .logo-text {
                font-size: 1.2rem;
            }
            .admin-username {
                font-size: 0.9rem;
            }
            .logout-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            .tab-content {
                padding: 1rem;
            }
            .section-title {
                font-size: 1.2rem;
            }
            .add-btn, .action-btn {
                padding: 0.6rem 1rem;
                font-size: 0.8rem;
            }
            .data-table th, .data-table td {
                padding: 0.6rem;
                font-size: 0.8rem;
            }
            .data-table {
                min-width: 500px; /* Adjust minimum width for smaller tablets */
            }
            .form-group label {
                font-size: 0.8rem;
            }
            .form-group input, .form-group textarea, .form-group select {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }
            .form-actions button {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
            .dashboard-stats-grid {
                grid-template-columns: 1fr; /* Stack stats cards on small screens */
            }
            .stat-value {
                font-size: 2rem;
            }
            .stat-label {
                font-size: 0.8rem;
            }
            .modal-content {
                width: 95%;
                padding: 20px;
            }
            .modal-content h3 {
                font-size: 1.4rem;
            }
            .modal-content p {
                font-size: 0.9rem;
            }
            .custom-message-box {
                max-width: 90%;
                padding: 20px;
            }
            .custom-message-box h3 {
                font-size: 1.4rem;
            }
            .custom-message-box p {
                font-size: 0.9rem;
            }
            .custom-message-box button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .data-table {
                min-width: 380px; /* Even narrower tables for very small mobiles */
            }
            .data-table th, .data-table td {
                font-size: 0.75rem;
                padding: 0.4rem;
            }
            .data-table td .actions button {
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }
            .stat-value {
                font-size: 1.8rem;
            }
            .stat-label {
                font-size: 0.8rem;
            }
            .chart-container {
                height: 250px; /* Slightly smaller chart on very small screens */
            }
        }
    </style>
</head>
<body>
    <div class="scanline"></div>

    <header>
        <div class="container">
            <a href="admin_dashboard.php" class="logo">
                <!-- Sudo Society Logo SVG -->
                <img src="../sudo_society.png" alt="Sudo Society Logo" class="logo-img">
                <span class="logo-text">Admin Panel</span>
            </a>
            <div class="admin-info">
                <span class="admin-username">Welcome, <?php echo htmlspecialchars($admin_username); ?></span>
                <a href="#" id="adminLogoutBtn" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <main class="container admin-dashboard-content">
        <div class="tabs">
            <button class="tab-btn active" data-tab="dashboard">Dashboard</button>
            <button class="tab-btn" data-tab="users">Users</button>
            <button class="tab-btn" data-tab="challenges">Challenges</button>
            <button class="tab-btn" data-tab="achievements">Achievements</button>
            <button class="tab-btn" data-tab="activity-log">Activity Log</button>
        </div>

        <!-- Dashboard Tab Content -->
        <div id="dashboard" class="tab-content active">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-chart-pie"></i> Overview</h2>
            </div>
            <div class="dashboard-stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="totalUsers">--</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="totalChallenges">--</div>
                    <div class="stat-label">Total Challenges</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="totalSolves">--</div>
                    <div class="stat-label">Total Solves</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="totalPoints">--</div>
                    <div class="stat-label">Total Points</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="averageScore">--</div>
                    <div class="stat-label">Avg. Score</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="activeChallengesCount">--</div>
                    <div class="stat-label">Active Chals</div>
                </div>
            </div>
            <div class="chart-container">
                <h3 style="text-align: center; color: var(--neon-blue); margin-bottom: 1rem; font-family: 'Orbitron', sans-serif;">Challenges Solved by Category</h3>
                <canvas id="adminCategoryChart"></canvas>
            </div>
        </div>

        <!-- Users Tab Content -->
        <div id="users" class="tab-content">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-users"></i> User Management</h2>
            </div>
            <div class="data-table-container">
                <table class="data-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Score</th>
                            <th>Solved</th>
                            <th>Rank</th>
                            <th>Streak</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- User data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Challenges Tab Content -->
        <div id="challenges" class="tab-content">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-puzzle-piece"></i> Challenge Management</h2>
                <button class="add-btn" id="addChallengeBtn"><i class="fa-solid fa-plus"></i> Add New Challenge</button>
            </div>
            <div class="data-table-container">
                <table class="data-table" id="challengesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Points</th>
                            <th>Flag</th>
                            <th>Solves</th>
                            <th>Active</th>
                            <th>Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Challenge data will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Inline Form for Adding New Challenge -->
            <div class="inline-form-section" id="addChallengeFormSection">
                <h3>Add New Challenge</h3>
                <form id="newChallengeForm">
                    <div class="form-group">
                        <label for="newChallengeName">Name</label>
                        <input type="text" id="newChallengeName" required>
                    </div>
                    <div class="form-group">
                        <label for="newChallengeCategory">Category</label>
                        <select id="newChallengeCategory" required>
                            <option value="">Select Category</option>
                            <option value="web">Web</option>
                            <option value="pwn">Pwn</option>
                            <option value="crypto">Crypto</option>
                            <option value="reversing">Reversing</option>
                            <option value="forensics">Forensics</option>
                            <option value="misc">Misc</option>
                            <option value="intro">Intro</option>
                            <option value="log analysis">Log Analysis</option>
                            <option value="osint">OSINT</option>
                            <option value="steganography">Steganography</option>
                            <option value="networking">Networking</option>
                            <option value="vulnerability management">Vulnerability Management</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="newChallengePoints">Points</label>
                        <input type="number" id="newChallengePoints" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="newChallengeDescription">Description</label>
                        <textarea id="newChallengeDescription"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="newChallengeFlag">Flag</label>
                        <input type="text" id="newChallengeFlag" required>
                    </div>
                    <div class="form-group">
                        <label for="newChallengeLink">Link (Optional)</label>
                        <input type="text" id="newChallengeLink">
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="newChallengeActive" checked>
                        <label for="newChallengeActive" style="display: inline;">Active</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="action-btn">Add Challenge</button>
                        <button type="button" class="action-btn cancel-btn" id="cancelAddChallengeBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Achievements Tab Content -->
        <div id="achievements" class="tab-content">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-award"></i> Achievement Management</h2>
                <button class="add-btn" id="addAchievementBtn"><i class="fa-solid fa-plus"></i> Add New Achievement</button>
            </div>
            <div class="data-table-container">
                <table class="data-table" id="achievementsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Icon Class</th>
                            <th>Required</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Achievement data will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Inline Form for Adding New Achievement -->
            <div class="inline-form-section" id="addAchievementFormSection">
                <h3>Add New Achievement</h3>
                <form id="newAchievementForm">
                    <div class="form-group">
                        <label for="newAchievementName">Name</label>
                        <input type="text" id="newAchievementName" required>
                    </div>
                    <div class="form-group">
                        <label for="newAchievementDescription">Description</label>
                        <textarea id="newAchievementDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="newAchievementIcon">Icon Class (Font Awesome, e.g., fa-star)</label>
                        <input type="text" id="newAchievementIcon" required>
                    </div>
                    <div class="form-group">
                        <label for="newAchievementRequired">Total Required (e.g., 5 for 5 solves)</label>
                        <input type="number" id="newAchievementRequired" required min="1">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="action-btn">Add Achievement</button>
                        <button type="button" class="action-btn cancel-btn" id="cancelAddAchievementBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Log Tab Content -->
        <div id="activity-log" class="tab-content">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Activity Log</h2>
            </div>
            <div class="data-table-container">
                <table class="data-table" id="activityLogTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Points Change</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Activity log data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society CTF Admin. All rights reserved.</p>
        </div>
    </footer>

    <!-- --- MODALS --- -->

    <!-- User Edit Modal -->
    <div id="userEditModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-btn" onclick="hideModal(userEditModal)"><i class="fas fa-times"></i></button>
            <h3>Edit User</h3>
            <form id="userEditForm">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label for="usernameInput">Username</label>
                    <input type="text" id="usernameInput" required>
                </div>
                <div class="form-group">
                    <label for="emailInput">Email</label>
                    <input type="email" id="emailInput">
                </div>
                <div class="form-group">
                    <label for="scoreInput">Total Score</label>
                    <input type="number" id="scoreInput" required>
                </div>
                <div class="form-group">
                    <label for="solvedInput">Challenges Solved</label>
                    <input type="number" id="solvedInput" required>
                </div>
                <div class="form-group">
                    <label for="streakInput">Daily Streak</label>
                    <input type="number" id="streakInput" required>
                </div>
                <div class="form-group">
                    <label for="lastSolvedDateInput">Last Solved Date</label>
                    <input type="date" id="lastSolvedDateInput">
                </div>
                <div class="form-actions">
                    <button type="button" class="action-btn" id="saveUserBtn">Save Changes</button>
                    <button type="button" class="action-btn cancel-btn" onclick="hideModal(userEditModal)">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Challenge Edit Modal -->
    <div id="challengeEditModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-btn" onclick="hideModal(challengeEditModal)"><i class="fas fa-times"></i></button>
            <h3>Edit Challenge</h3>
            <form id="challengeEditForm">
                <input type="hidden" id="editChallengeId">
                <div class="form-group">
                    <label for="editChallengeName">Name</label>
                    <input type="text" id="editChallengeName" required>
                </div>
                <div class="form-group">
                    <label for="editChallengeCategory">Category</label>
                    <select id="editChallengeCategory" required>
                        <option value="">Select Category</option>
                        <option value="web">Web</option>
                        <option value="pwn">Pwn</option>
                        <option value="crypto">Crypto</option>
                        <option value="reversing">Reversing</option>
                        <option value="forensics">Forensics</option>
                        <option value="misc">Misc</option>
                        <option value="intro">Intro</option>
                        <option value="log analysis">Log Analysis</option>
                        <option value="osint">OSINT</option>
                        <option value="steganography">Steganography</option>
                        <option value="networking">Networking</option>
                        <option value="vulnerability management">Vulnerability Management</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editChallengePoints">Points</label>
                    <input type="number" id="editChallengePoints" required min="1">
                </div>
                <div class="form-group">
                    <label for="editChallengeDescription">Description</label>
                    <textarea id="editChallengeDescription"></textarea>
                </div>
                <div class="form-group">
                    <label for="editChallengeFlag">Flag</label>
                    <input type="text" id="editChallengeFlag" required>
                </div>
                <div class="form-group">
                    <label for="editChallengeLink">Link (Optional)</label>
                    <input type="text" id="editChallengeLink">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="editChallengeActive">
                    <label for="editChallengeActive" style="display: inline;">Active</label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="action-btn">Save Challenge</button>
                    <button type="button" class="action-btn cancel-btn" onclick="hideModal(challengeEditModal)">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Achievement Edit Modal -->
    <div id="achievementEditModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-btn" onclick="hideModal(achievementEditModal)"><i class="fas fa-times"></i></button>
            <h3>Edit Achievement</h3>
            <form id="achievementEditForm">
                <input type="hidden" id="editAchievementId">
                <div class="form-group">
                    <label for="editAchievementName">Name</label>
                    <input type="text" id="editAchievementName" required>
                </div>
                <div class="form-group">
                    <label for="editAchievementDescription">Description</label>
                    <textarea id="editAchievementDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editAchievementIcon">Icon Class (Font Awesome, e.g., fa-star)</label>
                    <input type="text" id="editAchievementIcon" required>
                </div>
                <div class="form-group">
                    <label for="editAchievementRequired">Total Required (e.g., 5 for 5 solves)</label>
                    <input type="number" id="editAchievementRequired" required min="1">
                </div>
                <div class="form-actions">
                    <button type="submit" class="action-btn">Save Achievement</button>
                    <button type="button" class="action-btn cancel-btn" onclick="hideModal(achievementEditModal)">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Challenges List Modal (for chart click) -->
    <div id="categoryChallengesModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close-btn" onclick="hideModal(categoryChallengesModal)"><i class="fas fa-times"></i></button>
            <h3 id="categoryChallengesTitle">Challenges in Category: </h3>
            <ul id="categoryChallengesList" class="category-challenges-list">
                <!-- Challenges will be loaded here -->
            </ul>
        </div>
    </div>


    <!-- Custom Message Box HTML Structure -->
    <div id="customMessageBoxOverlay" class="custom-message-box-overlay" role="dialog" aria-modal="true" aria-labelledby="messageBoxTitle">
        <div class="custom-message-box">
            <h3 id="messageBoxTitle"></h3>
            <p id="messageBoxContent"></p>
            <div id="messageBoxButtons">
                <!-- Buttons will be added here dynamically -->
            </div>
        </div>
    </div>

    <script>
        // --- Configuration ---
        // !!! IMPORTANT: Adjust this URL to your actual admin_api.php path !!!
        const ADMIN_API_URL = 'http://192.168.1.2/Sudo_society_beta/admin/admin_api.php'; 
        const ADMIN_LOGOUT_URL = 'admin_login.php'; // URL to redirect after logout

        // --- DOM Elements ---
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        const adminLogoutBtn = document.getElementById('adminLogoutBtn');

        // Dashboard Elements
        const totalUsersEl = document.getElementById('totalUsers');
        const totalChallengesEl = document.getElementById('totalChallenges');
        const totalSolvesEl = document.getElementById('totalSolves');
        const totalPointsEl = document.getElementById('totalPoints');
        const averageScoreEl = document.getElementById('averageScore');
        const activeChallengesCountEl = document.getElementById('activeChallengesCount');
        let adminCategoryChartInstance; // Chart.js instance

        // User Management Elements
        const usersTableBody = document.querySelector('#usersTable tbody');
        const userEditModal = document.getElementById('userEditModal');
        const userEditForm = userEditModal.querySelector('#userEditForm'); // Select form within modal
        const userIdInput = userEditModal.querySelector('#userId');
        const usernameInput = userEditModal.querySelector('#usernameInput');
        const emailInput = userEditModal.querySelector('#emailInput');
        const scoreInput = userEditModal.querySelector('#scoreInput');
        const solvedInput = userEditModal.querySelector('#solvedInput');
        const streakInput = userEditModal.querySelector('#streakInput');
        const lastSolvedDateInput = userEditModal.querySelector('#lastSolvedDateInput');
        const saveUserBtn = userEditModal.querySelector('#saveUserBtn');

        // Challenge Management Elements
        const challengesTableBody = document.querySelector('#challengesTable tbody');
        const addChallengeBtn = document.getElementById('addChallengeBtn');
        const addChallengeFormSection = document.getElementById('addChallengeFormSection'); // Inline form section
        const newChallengeForm = document.getElementById('newChallengeForm'); // Form inside inline section
        const newChallengeNameInput = document.getElementById('newChallengeName');
        const newChallengeCategoryInput = document.getElementById('newChallengeCategory');
        const newChallengePointsInput = document.getElementById('newChallengePoints');
        const newChallengeDescriptionInput = document.getElementById('newChallengeDescription');
        const newChallengeFlagInput = document.getElementById('newChallengeFlag');
        const newChallengeLinkInput = document.getElementById('newChallengeLink');
        const newChallengeActiveInput = document.getElementById('newChallengeActive');
        const cancelAddChallengeBtn = document.getElementById('cancelAddChallengeBtn');

        const challengeEditModal = document.getElementById('challengeEditModal'); // Edit modal
        const challengeEditForm = challengeEditModal.querySelector('#challengeEditForm'); // Form inside edit modal
        const editChallengeIdInput = challengeEditModal.querySelector('#editChallengeId');
        const editChallengeNameInput = challengeEditModal.querySelector('#editChallengeName');
        const editChallengeCategoryInput = challengeEditModal.querySelector('#editChallengeCategory');
        const editChallengePointsInput = challengeEditModal.querySelector('#editChallengePoints');
        const editChallengeDescriptionInput = challengeEditModal.querySelector('#editChallengeDescription');
        const editChallengeFlagInput = challengeEditModal.querySelector('#editChallengeFlag');
        const editChallengeLinkInput = challengeEditModal.querySelector('#editChallengeLink');
        const editChallengeActiveInput = challengeEditModal.querySelector('#editChallengeActive');


        // Achievement Management Elements
        const achievementsTableBody = document.querySelector('#achievementsTable tbody');
        const addAchievementBtn = document.getElementById('addAchievementBtn');
        const addAchievementFormSection = document.getElementById('addAchievementFormSection'); // Inline form section
        const newAchievementForm = document.getElementById('newAchievementForm'); // Form inside inline section
        const newAchievementNameInput = document.getElementById('newAchievementName');
        const newAchievementDescriptionInput = document.getElementById('newAchievementDescription');
        const newAchievementIconInput = document.getElementById('newAchievementIcon');
        const newAchievementRequiredInput = document.getElementById('newAchievementRequired');
        const cancelAddAchievementBtn = document.getElementById('cancelAddAchievementBtn');

        const achievementEditModal = document.getElementById('achievementEditModal'); // Edit modal
        const achievementEditForm = achievementEditModal.querySelector('#achievementEditForm'); // Form inside edit modal
        const editAchievementIdInput = achievementEditModal.querySelector('#editAchievementId');
        const editAchievementNameInput = achievementEditModal.querySelector('#editAchievementName');
        const editAchievementDescriptionInput = achievementEditModal.querySelector('#editAchievementDescription');
        const editAchievementIconInput = achievementEditModal.querySelector('#editAchievementIcon');
        const editAchievementRequiredInput = achievementEditModal.querySelector('#editAchievementRequired');


        // Activity Log Elements
        const activityLogTableBody = document.querySelector('#activityLogTable tbody');

        // Category Challenges Modal Elements
        const categoryChallengesModal = document.getElementById('categoryChallengesModal');
        const categoryChallengesTitle = document.getElementById('categoryChallengesTitle');
        const categoryChallengesList = document.getElementById('categoryChallengesList');


        // Custom Message Box Elements
        const messageBoxOverlay = document.getElementById('customMessageBoxOverlay');
        const messageBoxTitle = document.getElementById('messageBoxTitle');
        const messageBoxContent = document.getElementById('messageBoxContent');
        const messageBoxButtons = document.getElementById('messageBoxButtons');

        // --- Global State / Cached Data (will be populated from API) ---
        let allUsers = [];
        let allChallenges = []; 
        let allAchievements = []; 
        let activityLog = []; // To store activity log data

        // --- Custom Message Box Functions ---
        function showMessageBox(title, message, type = 'info', buttons = [{ text: 'OK', type: 'ok' }]) {
            messageBoxTitle.textContent = title;
            messageBoxContent.textContent = message;
            messageBoxButtons.innerHTML = ''; // Clear previous buttons

            buttons.forEach(btn => {
                const buttonElement = document.createElement('button');
                buttonElement.textContent = btn.text;
                buttonElement.classList.add(btn.type === 'ok' ? 'ok-btn' : (btn.type === 'confirm' ? 'confirm-btn' : 'cancel-btn'));
                buttonElement.onclick = () => {
                    hideMessageBox();
                    if (btn.callback) {
                        btn.callback();
                    }
                };
                messageBoxButtons.appendChild(buttonElement);
            });

            messageBoxOverlay.classList.add('show');
            // Use inert to prevent interaction with background content
            document.querySelector('main').setAttribute('inert', '');
            document.querySelector('header').setAttribute('inert', '');
            document.querySelector('footer').setAttribute('inert', '');
        }

        function hideMessageBox() {
            messageBoxOverlay.classList.remove('show');
            document.querySelector('main').removeAttribute('inert');
            document.querySelector('header').removeAttribute('inert');
            document.querySelector('footer').removeAttribute('inert');
        }

        // --- Generic Modal Functions ---
        // Accepts the modal HTML element directly
        function showModal(modalElement) {
            modalElement.classList.add('show');
            // Apply inert to main content to prevent interaction
            document.querySelector('main').setAttribute('inert', '');
            document.querySelector('header').setAttribute('inert', '');
            document.querySelector('footer').setAttribute('inert', '');
        }

        // Accepts the modal HTML element directly
        function hideModal(modalElement) {
            modalElement.classList.remove('show');
            // Remove inert from main content
            document.querySelector('main').removeAttribute('inert');
            document.querySelector('header').removeAttribute('inert');
            document.querySelector('footer').removeAttribute('inert');
        }

        // --- API Helper Function (Now makes real fetch calls) ---
        async function callAdminApi(action, method = 'GET', data = null, queryParams = {}) {
            const url = new URL(ADMIN_API_URL);
            url.searchParams.append('action', action);

            // Add query parameters for GET requests
            if (method === 'GET' && queryParams) {
                Object.keys(queryParams).forEach(key => url.searchParams.append(key, queryParams[key]));
            }

            const options = { method: method };
            if (method === 'POST' || method === 'PUT' || method === 'DELETE') {
                options.headers = { 'Content-Type': 'application/json' };
                options.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(url, options);
                const result = await response.json();

                if (!response.ok || !result.success) {
                    // If unauthorized, redirect to login
                    if (response.status === 401) {
                        showMessageBox("Session Expired", "Your admin session has expired. Please log in again.", 'error', [{ text: 'Login', type: 'ok', callback: () => window.location.href = ADMIN_LOGOUT_URL }]);
                        return null;
                    }
                    throw new Error(result.error || result.message || `API call failed for ${action}: HTTP status ${response.status}`);
                }
                return result.data || result; // API might return data in 'data' or directly
            } catch (error) {
                console.error(`Error calling admin API action "${action}":`, error);
                showMessageBox("API Error", `An error occurred: ${error.message}`, 'error');
                return null;
            }
        }

        // --- Tab Switching Logic ---
        function switchTab(tabId) {
            tabButtons.forEach(btn => {
                if (btn.dataset.tab === tabId) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            tabContents.forEach(content => {
                if (content.id === tabId) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });

            // Hide all inline forms when switching tabs
            addChallengeFormSection.style.display = 'none';
            newChallengeForm.reset();
            addAchievementFormSection.style.display = 'none';
            newAchievementForm.reset();

            // Load data for the active tab
            loadTabContent(tabId);
        }

        async function loadTabContent(tabId) {
            switch (tabId) {
                case 'dashboard':
                    await fetchDashboardStats();
                    break;
                case 'users':
                    await fetchUsers();
                    break;
                case 'challenges':
                    await fetchChallenges();
                    break;
                case 'achievements':
                    await fetchAchievements();
                    break;
                case 'activity-log':
                    await fetchActivityLog();
                    break;
            }
        }

        // --- Dashboard Functions ---
        async function fetchDashboardStats() {
            const stats = await callAdminApi('getDashboardStats');
            if (stats) {
                totalUsersEl.textContent = stats.total_users || 0;
                // Note: total_challenges now comes from 'challenges' table
                totalChallengesEl.textContent = stats.total_challenges || 0; 
                totalSolvesEl.textContent = stats.total_solves || 0;
                totalPointsEl.textContent = stats.total_points || 0;
                averageScoreEl.textContent = stats.average_score ? stats.average_score.toFixed(2) : '0.00';
                // Note: active_challenges_count now comes from 'challenges' table
                activeChallengesCountEl.textContent = stats.active_challenges_count || 0; 
                // Note: challenges_by_category now comes from 'challenges' table
                renderAdminCategoryChart(stats.challenges_by_category); 
            }
        }

        function renderAdminCategoryChart(categoryData) {
            const chartElement = document.getElementById('adminCategoryChart');
            const ctx = chartElement.getContext('2d');

            const labels = Object.keys(categoryData);
            const dataValues = Object.values(categoryData);

            // Directly using RGBA values for Chart.js to ensure colors render
            const backgroundColors = [
                'rgba(0, 255, 136, 0.8)',  // Neon Green
                'rgba(0, 200, 255, 0.8)',  // Neon Blue
                'rgba(255, 0, 200, 0.8)',  // Neon Pink
                'rgba(255, 150, 0, 0.8)',  // Orange
                'rgba(150, 0, 255, 0.8)',  // Purple
                'rgba(0, 255, 255, 0.8)',  // Aqua
                'rgba(255, 255, 0, 0.8)',  // Yellow
                'rgba(50, 205, 50, 0.8)',  // Lime Green
                'rgba(255, 99, 71, 0.8)',  // Tomato
                'rgba(100, 149, 237, 0.8)', // Cornflower Blue
                'rgba(218, 112, 214, 0.8)', // Orchid
                'rgba(127, 255, 212, 0.8)'  // Aquamarine
            ];

            if (adminCategoryChartInstance) {
                adminCategoryChartInstance.destroy();
            }

            adminCategoryChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: backgroundColors.slice(0, labels.length), // Use enough colors for categories
                        borderColor: 'var(--darker-bg)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: 'var(--text-light)',
                                font: { family: "'Share Tech Mono', monospace" }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'var(--darker-bg)',
                            borderColor: 'var(--neon-green)',
                            borderWidth: 1,
                            titleColor: 'var(--neon-green)',
                            bodyColor: 'var(--text-light)',
                            titleFont: { family: "'Share Tech Mono', monospace" },
                            bodyFont: { family: "'Share Tech Mono', monospace" },
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed !== null) { label += context.parsed + ' solves'; }
                                    return label;
                                }
                            }
                        }
                    },
                    cutout: '70%',
                    onClick: handleCategoryChartClick // Add click event handler
                }
            });
        }

        // Handle click on category chart segment
        async function handleCategoryChartClick(event, elements) {
            if (elements.length > 0) {
                const clickedElementIndex = elements[0].index;
                const label = adminCategoryChartInstance.data.labels[clickedElementIndex];
                
                // Fetch challenges for the clicked category (now from 'challenges' table)
                const challengesInCat = await callAdminApi('getChallengesByCategoryDetails', 'GET', null, { category: label });
                
                if (challengesInCat && challengesInCat.length > 0) {
                    categoryChallengesTitle.textContent = `Challenges in Category: ${label}`;
                    categoryChallengesList.innerHTML = '';
                    challengesInCat.forEach(challenge => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <strong>${challenge.name}</strong> 
                            <span>(Points: <span class="points">${challenge.points}</span>, Solves: ${challenge.solves})</span>
                        `;
                        categoryChallengesList.appendChild(li);
                    });
                    showModal(categoryChallengesModal);
                } else {
                    showMessageBox("No Challenges", `No challenges found for category: ${label}.`, 'info');
                }
            }
        }


        // --- User Management Functions ---
        async function fetchUsers() {
            const users = await callAdminApi('getUsers');
            if (users) {
                allUsers = users; // Cache user data
                usersTableBody.innerHTML = '';
                users.forEach(user => {
                    const row = usersTableBody.insertRow();
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email || 'N/A'}</td>
                        <td>${user.total_score}</td>
                        <td>${user.challenges_solved}</td>
                        <td>${user.current_rank || 'N/A'}</td>
                        <td>${user.daily_streak}</td>
                        <td>${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A'}</td>
                        <td class="actions">
                            <button class="action-btn" onclick="editUser(${user.id})">Edit</button>
                            <button class="action-btn delete" onclick="confirmDeleteUser(${user.id}, '${user.username}')">Delete</button>
                        </td>
                    `;
                });
            }
        }

        async function editUser(userId) {
            // Fetch the specific user data from the backend to ensure it's fresh
            const userToEdit = await callAdminApi('getUserById', 'GET', null, { id: userId });
            
            if (userToEdit) {
                userIdInput.value = userToEdit.id;
                usernameInput.value = userToEdit.username;
                emailInput.value = userToEdit.email || '';
                scoreInput.value = userToEdit.total_score;
                solvedInput.value = userToEdit.challenges_solved;
                streakInput.value = userToEdit.daily_streak;
                // Format date for input type="date"
                lastSolvedDateInput.value = userToEdit.last_solved_date ? new Date(userToEdit.last_solved_date).toISOString().split('T')[0] : '';

                showModal(userEditModal);
            } else {
                showMessageBox("Error", "Failed to load user data for editing. Please refresh and try again.", 'error');
                console.error("User data not found for editing from API:", userId);
            }
        }

        saveUserBtn.addEventListener('click', async () => {
            const updatedUser = {
                id: parseInt(userIdInput.value),
                username: usernameInput.value,
                email: emailInput.value,
                total_score: parseInt(scoreInput.value),
                challenges_solved: parseInt(solvedInput.value),
                daily_streak: parseInt(streakInput.value),
                last_solved_date: lastSolvedDateInput.value || null // Send null if empty
            };

            const result = await callAdminApi('editUser', 'POST', updatedUser);
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchUsers(); // Refresh table
                hideModal(userEditModal);
            }
        });

        function confirmDeleteUser(userId, username) {
            showMessageBox(
                "Confirm Deletion",
                `Are you sure you want to delete user "${username}" (ID: ${userId})? This action cannot be undone and will remove all their associated data (solved challenges, activity, etc.).`,
                'warning',
                [
                    { text: 'Delete', type: 'confirm', callback: () => deleteUser(userId) },
                    { text: 'Cancel', type: 'cancel' }
                ]
            );
        }

        async function deleteUser(userId) {
            const result = await callAdminApi('deleteUser', 'POST', { id: userId });
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchUsers(); // Refresh table
            }
        }

        // --- Challenge Management Functions ---
        async function fetchChallenges() {
            // Now fetching from the 'challenges' table
            const challenges = await callAdminApi('getChallenges');
            if (challenges) {
                allChallenges = challenges; // Cache for quick access if needed, though edit fetches fresh
                challengesTableBody.innerHTML = '';
                challenges.forEach(challenge => {
                    const row = challengesTableBody.insertRow();
                    row.innerHTML = `
                        <td>${challenge.id}</td>
                        <td>${challenge.name}</td>
                        <td>${challenge.category}</td>
                        <td>${challenge.points}</td>
                        <td>${challenge.flag}</td>
                        <td>${challenge.solves}</td>
                        <td>
                            <button class="action-btn toggle-active ${challenge.active ? '' : 'inactive'}" 
                                onclick="toggleChallengeActive(${challenge.id}, ${challenge.active ? 0 : 1})">
                                ${challenge.active ? 'Active' : 'Inactive'}
                            </button>
                        </td>
                        <td>${challenge.link ? `<a href="${challenge.link}" target="_blank"><i class="fas fa-external-link-alt"></i> Link</a>` : 'N/A'}</td>
                        <td class="actions">
                            <button class="action-btn" onclick="showEditChallengeForm(${challenge.id})">Edit</button>
                            <button class="action-btn delete" onclick="confirmDeleteChallenge(${challenge.id}, '${challenge.name}')">Delete</button>
                        </td>
                    `;
                });
            }
        }

        // Show inline form for adding new challenge
        addChallengeBtn.addEventListener('click', () => {
            newChallengeForm.reset(); // Clear previous data
            newChallengeActiveInput.checked = true; // Default to active
            addChallengeFormSection.style.display = 'block';
            window.scrollTo({ top: addChallengeFormSection.offsetTop, behavior: 'smooth' });
        });

        // Handle submission of new challenge form (inline)
        newChallengeForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const challengeData = {
                name: newChallengeNameInput.value,
                category: newChallengeCategoryInput.value,
                points: parseInt(newChallengePointsInput.value),
                description: newChallengeDescriptionInput.value,
                flag: newChallengeFlagInput.value,
                link: newChallengeLinkInput.value,
                active: newChallengeActiveInput.checked ? 1 : 0
            };

            const result = await callAdminApi('addChallenge', 'POST', challengeData);
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchChallenges(); // Refresh table
                addChallengeFormSection.style.display = 'none'; // Hide form
                newChallengeForm.reset();
            }
        });

        // Cancel adding new challenge
        cancelAddChallengeBtn.addEventListener('click', () => {
            addChallengeFormSection.style.display = 'none';
            newChallengeForm.reset();
        });


        // Show modal for editing existing challenge
        async function showEditChallengeForm(challengeId) {
            // Fetch the specific challenge data from the backend to ensure it's fresh (now from 'challenges' table)
            const challengeToEdit = await callAdminApi('getChallengeById', 'GET', null, { id: challengeId });

            if (challengeToEdit) {
                editChallengeIdInput.value = challengeToEdit.id;
                editChallengeNameInput.value = challengeToEdit.name;
                editChallengeCategoryInput.value = challengeToEdit.category;
                editChallengePointsInput.value = challengeToEdit.points;
                editChallengeDescriptionInput.value = challengeToEdit.description || '';
                editChallengeFlagInput.value = challengeToEdit.flag;
                editChallengeLinkInput.value = challengeToEdit.link || '';
                editChallengeActiveInput.checked = challengeToEdit.active === 1;

                showModal(challengeEditModal);
            } else {
                showMessageBox("Error", "Failed to load challenge data for editing. Please refresh and try again.", 'error');
                console.error("Challenge data not found for editing from API:", challengeId);
            }
        }

        // Handle submission of edit challenge form (modal)
        challengeEditForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const challengeData = {
                id: parseInt(editChallengeIdInput.value),
                name: editChallengeNameInput.value,
                category: editChallengeCategoryInput.value,
                points: parseInt(editChallengePointsInput.value),
                description: editChallengeDescriptionInput.value,
                flag: editChallengeFlagInput.value,
                link: editChallengeLinkInput.value,
                active: editChallengeActiveInput.checked ? 1 : 0
            };

            const result = await callAdminApi('editChallenge', 'POST', challengeData);
            
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchChallenges(); // Refresh table
                hideModal(challengeEditModal);
            }
        });


        async function toggleChallengeActive(challengeId, currentActiveStatus) {
            const result = await callAdminApi('toggleChallengeActive', 'POST', { id: challengeId, active: currentActiveStatus });
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchChallenges(); // Refresh table to show updated status
            }
        }

        function confirmDeleteChallenge(challengeId, challengeName) {
            showMessageBox(
                "Confirm Deletion",
                `Are you sure you want to delete challenge "${challengeName}" (ID: ${challengeId})? This will also remove all associated solved entries.`,
                'warning',
                [
                    { text: 'Delete', type: 'confirm', callback: () => deleteChallenge(challengeId) },
                    { text: 'Cancel', type: 'cancel' }
                ]
            );
        }

        async function deleteChallenge(challengeId) {
            const result = await callAdminApi('deleteChallenge', 'POST', { id: challengeId });
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchChallenges(); // Refresh table
            }
        }

        // --- Achievement Management Functions ---
        async function fetchAchievements() {
            const achievements = await callAdminApi('getAchievements');
            if (achievements) {
                allAchievements = achievements; // Cache for quick access if needed, though edit fetches fresh
                achievementsTableBody.innerHTML = '';
                achievements.forEach(ach => {
                    const row = achievementsTableBody.insertRow();
                    row.innerHTML = `
                        <td>${ach.id}</td>
                        <td>${ach.name}</td>
                        <td>${ach.description}</td>
                        <td><i class="fa-solid ${ach.icon}"></i> ${ach.icon}</td>
                        <td>${ach.total_required}</td>
                        <td class="actions">
                            <button class="action-btn" onclick="showEditAchievementForm(${ach.id})">Edit</button>
                            <button class="action-btn delete" onclick="confirmDeleteAchievement(${ach.id}, '${ach.name}')">Delete</button>
                        </td>
                    `;
                });
            }
        }

        // Show inline form for adding new achievement
        addAchievementBtn.addEventListener('click', () => {
            newAchievementForm.reset(); // Clear previous data
            addAchievementFormSection.style.display = 'block';
            window.scrollTo({ top: addAchievementFormSection.offsetTop, behavior: 'smooth' });
        });

        // Handle submission of new achievement form (inline)
        newAchievementForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const achievementData = {
                name: newAchievementNameInput.value,
                description: newAchievementDescriptionInput.value,
                icon: newAchievementIconInput.value,
                total_required: parseInt(newAchievementRequiredInput.value)
            };

            const result = await callAdminApi('addAchievement', 'POST', achievementData);
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchAchievements(); // Refresh table
                addAchievementFormSection.style.display = 'none'; // Hide form
                newAchievementForm.reset();
            }
        });

        // Cancel adding new achievement
        cancelAddAchievementBtn.addEventListener('click', () => {
            addAchievementFormSection.style.display = 'none';
            newAchievementForm.reset();
        });


        // Show modal for editing existing achievement
        async function showEditAchievementForm(achievementId) {
            // Fetch the specific achievement data from the backend to ensure it's fresh
            const achievementToEdit = await callAdminApi('getAchievementById', 'GET', null, { id: achievementId });
            
            if (achievementToEdit) {
                editAchievementIdInput.value = achievementToEdit.id;
                editAchievementNameInput.value = achievementToEdit.name;
                editAchievementDescriptionInput.value = achievementToEdit.description;
                editAchievementIconInput.value = achievementToEdit.icon;
                editAchievementRequiredInput.value = achievementToEdit.total_required;

                showModal(achievementEditModal);
            } else {
                showMessageBox("Error", "Failed to load achievement data for editing. Please refresh and try again.", 'error');
                console.error("Achievement data not found for editing from API:", achievementId);
            }
        }

        // Handle submission of edit achievement form (modal)
        achievementEditForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const achievementData = {
                id: parseInt(editAchievementIdInput.value),
                name: editAchievementNameInput.value,
                description: editAchievementDescriptionInput.value,
                icon: editAchievementIconInput.value,
                total_required: parseInt(editAchievementRequiredInput.value)
            };

            let result = await callAdminApi('editAchievement', 'POST', achievementData);
            
            if (result && result.success) {
                showMessageBox("Success", result.message, 'success');
                await fetchAchievements(); // Refresh table
                hideModal(achievementEditModal);
            }
        });


        // --- Activity Log Functions ---
        async function fetchActivityLog() {
            const activityLogData = await callAdminApi('getActivityLog');
            if (activityLogData) {
                activityLog = activityLogData; // Cache activity log data
                activityLogTableBody.innerHTML = '';
                activityLog.forEach(log => {
                    const row = activityLogTableBody.insertRow();
                    row.innerHTML = `
                        <td>${log.id}</td>
                        <td>${log.username || 'N/A'}</td>
                        <td>${log.activity_type}</td>
                        <td>${log.description}</td>
                        <td>${log.points_change !== null ? (log.points_change > 0 ? '+' : '') + log.points_change : 'N/A'}</td>
                        <td>${new Date(log.timestamp).toLocaleString()}</td>
                    `;
                });
            }
        }

        // --- Admin Logout ---
        adminLogoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const result = await callAdminApi('adminLogout');
            if (result && result.success) {
                window.location.href = ADMIN_LOGOUT_URL;
            } else {
                // Fallback logout if API fails (e.g., network error before API call)
                window.location.href = ADMIN_LOGOUT_URL;
            }
        });

        // --- Initialize Dashboard ---
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial active tab
            switchTab('dashboard');

            // Add event listeners for tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    switchTab(button.dataset.tab);
                });
            });
        });
    </script>
</body>
</html>
