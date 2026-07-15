<?php
session_start();

// Check if admin is logged in, otherwise redirect to login page
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&family=Barlow+Condensed:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ============================================================
           CSS VARIABLES (DARK MODE DEFAULT)
           ============================================================ */
        :root {
            --bg-primary: #0b0d0e;
            --bg-secondary: rgba(255, 255, 255, 0.03);
            --text-primary: #f0f5f3;
            --text-secondary: rgba(240, 245, 243, 0.70);
            --text-muted: rgba(240, 245, 243, 0.40);

            --accent: #6fcf97;
            --accent-dim: rgba(111, 207, 151, 0.12);
            --accent-glow: 0 0 40px rgba(111, 207, 151, 0.10);
            --accent-gradient: linear-gradient(135deg, #6fcf97, #27ae60);

            --glass-bg: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.06);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.40);

            --orb-1: rgba(111, 207, 151, 0.12);
            --orb-2: rgba(46, 204, 113, 0.08);
            --orb-3: rgba(111, 207, 151, 0.06);

            --border-subtle: rgba(255, 255, 255, 0.04);
            --card-hover-border: rgba(111, 207, 151, 0.15);
            --card-hover-shadow: 0 12px 48px rgba(0, 0, 0, 0.50), 0 0 40px rgba(111, 207, 151, 0.06);

            --logo-bg: linear-gradient(135deg, #6fcf97, #27ae60);
            --logo-text-gradient: linear-gradient(135deg, #6fcf97, #27ae60);
            --section-title-gradient: linear-gradient(135deg, #f0f5f3, #6fcf97);

            --toggle-bg: rgba(255, 255, 255, 0.08);
            --toggle-dot: #6fcf97;
            --toggle-border: rgba(255, 255, 255, 0.08);
            --toggle-icon: #f0f5f3;

            --scrollbar-track: #0b0d0e;
            --scrollbar-thumb: #6fcf97;

            --footer-border: rgba(255, 255, 255, 0.04);
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;

            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-display: 'Barlow Condensed', 'Inter', sans-serif;

            --header-bg: rgba(11, 13, 14, 0.70);

            --chart-grid: rgba(255, 255, 255, 0.06);
            --chart-text: rgba(240, 245, 243, 0.50);

            --notification-dot: #e74c3c;
            --sidebar-width: 260px;
        }

        /* ============================================================
           LIGHT MODE
           ============================================================ */
        body.light {
            --bg-primary: #f4f7f6;
            --bg-secondary: rgba(0, 0, 0, 0.02);
            --text-primary: #141817;
            --text-secondary: rgba(20, 24, 23, 0.72);
            --text-muted: rgba(20, 24, 23, 0.45);

            --accent: #c0392b;
            --accent-dim: rgba(192, 57, 43, 0.08);
            --accent-glow: 0 0 40px rgba(192, 57, 43, 0.06);
            --accent-gradient: linear-gradient(135deg, #c0392b, #a93226);

            --glass-bg: rgba(255, 255, 255, 0.60);
            --glass-border: rgba(0, 0, 0, 0.05);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);

            --orb-1: rgba(192, 57, 43, 0.06);
            --orb-2: rgba(180, 40, 30, 0.04);
            --orb-3: rgba(192, 57, 43, 0.03);

            --border-subtle: rgba(0, 0, 0, 0.04);
            --card-hover-border: rgba(192, 57, 43, 0.15);
            --card-hover-shadow: 0 12px 48px rgba(0, 0, 0, 0.06), 0 0 30px rgba(192, 57, 43, 0.04);

            --logo-bg: linear-gradient(135deg, #c0392b, #a93226);
            --logo-text-gradient: linear-gradient(135deg, #c0392b, #a93226);
            --section-title-gradient: linear-gradient(135deg, #141817, #c0392b);

            --toggle-bg: rgba(0, 0, 0, 0.06);
            --toggle-dot: #c0392b;
            --toggle-border: rgba(0, 0, 0, 0.06);
            --toggle-icon: #141817;

            --scrollbar-track: #f4f7f6;
            --scrollbar-thumb: #c0392b;

            --footer-border: rgba(0, 0, 0, 0.04);
            --header-bg: rgba(244, 247, 246, 0.80);

            --chart-grid: rgba(0, 0, 0, 0.06);
            --chart-text: rgba(20, 24, 23, 0.40);

            --notification-dot: #c0392b;
        }

        /* ============================================================
           RESET & BASE
           ============================================================ */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-primary);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            transition: background 0.5s ease, color 0.4s ease;
            display: flex;
            flex-direction: column;
        }

        /* ============================================================
           AMBIENT BACKGROUND ORBS
           ============================================================ */
        .ambient {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .ambient .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: orbFloat 18s ease-in-out infinite alternate;
            transition: background 0.8s ease, opacity 0.8s ease;
        }

        .ambient .orb:nth-child(1) {
            width: 500px;
            height: 500px;
            top: -15%;
            left: -5%;
            background: radial-gradient(circle, var(--orb-1), transparent 70%);
            animation-duration: 22s;
        }

        .ambient .orb:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -10%;
            right: -5%;
            background: radial-gradient(circle, var(--orb-2), transparent 70%);
            animation-duration: 26s;
            animation-delay: -6s;
        }

        .ambient .orb:nth-child(3) {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translateX(-50%);
            background: radial-gradient(circle, var(--orb-3), transparent 70%);
            animation-duration: 20s;
            animation-delay: -10s;
        }

        @keyframes orbFloat {
            0% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(40px, -30px) scale(1.05);
            }
            66% {
                transform: translate(-20px, 40px) scale(0.95);
            }
            100% {
                transform: translate(20px, -20px) scale(1.02);
            }
        }

        /* ============================================================
           SCROLLBAR
           ============================================================ */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
        }

        /* ============================================================
           CONTAINER
           ============================================================ */
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* ============================================================
           GLASS CARD
           ============================================================ */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            border-radius: 20px;
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        /* ============================================================
           HEADER
           ============================================================ */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 14px 0;
            background: var(--header-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-subtle);
            transition: background 0.5s ease, border-color 0.4s ease;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .logo:hover {
            opacity: 0.85;
        }

        .logo-icon-img {
            height: 44px;
            border-radius: 12px;
            transition: filter 0.3s ease;
        }

        .logo-text {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            background: var(--logo-text-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: background 0.5s ease;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* ============================================================
           NOTIFICATION BELL
           ============================================================ */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            transition: background 0.3s ease;
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        .notification-bell:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .notification-bell .badge {
            position: absolute;
            top: 0;
            right: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--notification-dot);
            color: #fff;
            font-size: 0.55rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-primary);
            transition: background 0.4s ease;
        }

        .notification-dropdown {
            position: absolute;
            top: 48px;
            right: 0;
            width: 360px;
            max-height: 420px;
            overflow-y: auto;
            background: var(--bg-primary);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: var(--glass-shadow);
            padding: 12px 0;
            display: none;
            z-index: 200;
            backdrop-filter: blur(20px);
        }

        .notification-dropdown.open {
            display: block;
        }

        .notification-dropdown .notif-item {
            padding: 10px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: background 0.2s ease;
            border-bottom: 1px solid var(--border-subtle);
        }

        .notification-dropdown .notif-item:last-child {
            border-bottom: none;
        }

        .notification-dropdown .notif-item:hover {
            background: var(--bg-secondary);
        }

        .notification-dropdown .notif-item .notif-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .notification-dropdown .notif-item .notif-content {
            flex: 1;
        }

        .notification-dropdown .notif-item .notif-title {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .notification-dropdown .notif-item .notif-time {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .notification-dropdown .notif-empty {
            padding: 30px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* ============================================================
           THEME TOGGLE
           ============================================================ */
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 4px 10px 4px 4px;
            border-radius: 40px;
            background: var(--toggle-bg);
            border: 1px solid var(--toggle-border);
            transition: background 0.4s ease, border-color 0.4s ease;
            user-select: none;
        }

        .theme-toggle:hover {
            opacity: 0.85;
        }

        .toggle-track {
            position: relative;
            width: 40px;
            height: 22px;
            border-radius: 40px;
            background: var(--toggle-bg);
            border: 1px solid var(--toggle-border);
            transition: background 0.4s ease, border-color 0.4s ease;
            flex-shrink: 0;
        }

        .toggle-dot {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--toggle-dot);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.4s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        body.light .toggle-dot {
            transform: translateX(18px);
        }

        .toggle-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-secondary);
            letter-spacing: 0.3px;
            transition: color 0.4s ease;
        }

        .toggle-icon {
            font-size: 0.85rem;
            color: var(--toggle-icon);
            transition: color 0.4s ease;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 4px;
            border-radius: 40px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .admin-user .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bg-primary);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .admin-user .name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .logout-btn {
            padding: 6px 16px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            background: transparent;
            color: var(--text-secondary);
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: var(--font-primary);
            text-decoration: none;
        }

        .logout-btn:hover {
            background: var(--accent-dim);
            color: var(--accent);
            border-color: var(--accent);
        }

        /* ============================================================
           SIDEBAR LAYOUT
           ============================================================ */
        .dashboard-wrapper {
            display: flex;
            gap: 24px;
            padding: 28px 0 40px;
            min-height: calc(100vh - 140px);
        }

        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 12px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
            height: fit-content;
            position: sticky;
            top: 90px;
        }

        .sidebar .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .sidebar .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .sidebar .nav-item:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .sidebar .nav-item:hover i {
            color: var(--accent);
        }

        .sidebar .nav-item.active {
            background: var(--accent-dim);
            color: var(--accent);
        }

        .sidebar .nav-item.active i {
            color: var(--accent);
        }

        .sidebar .nav-divider {
            height: 1px;
            background: var(--border-subtle);
            margin: 8px 14px;
        }

        /* ============================================================
           MAIN CONTENT
           ============================================================ */
        .main-content {
            flex: 1;
            min-width: 0;
        }

        .page-section {
            display: none;
            animation: fadeUp 0.4s ease forwards;
        }

        .page-section.active {
            display: block;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 700;
            background: var(--section-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: background 0.5s ease;
        }

        .section-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 8px 18px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            background: var(--glass-bg);
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: var(--font-primary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .action-btn:hover {
            background: var(--accent-dim);
            color: var(--accent);
            border-color: var(--accent);
        }

        .action-btn.primary {
            background: var(--accent-gradient);
            color: var(--bg-primary);
            border: none;
        }

        .action-btn.primary:hover {
            opacity: 0.85;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(111, 207, 151, 0.20);
        }

        body.light .action-btn.primary:hover {
            box-shadow: 0 4px 16px rgba(192, 57, 43, 0.15);
        }

        .action-btn.danger {
            border-color: var(--error-color);
            color: var(--error-color);
        }

        .action-btn.danger:hover {
            background: var(--error-color);
            color: var(--bg-primary);
        }

        /* ============================================================
           STATS CARDS
           ============================================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 18px 16px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, background 0.5s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--card-hover-border);
            box-shadow: var(--card-hover-shadow);
        }

        .stat-card .stat-icon {
            font-size: 1.4rem;
            color: var(--accent);
            margin-bottom: 6px;
        }

        .stat-card .stat-value {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            transition: color 0.4s ease;
        }

        .stat-card .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-top: 2px;
            transition: color 0.4s ease;
        }

        /* ============================================================
           CHARTS
           ============================================================ */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            padding: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .chart-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chart-card-title {
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.4s ease;
        }

        .chart-card-title i {
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .chart-container {
            position: relative;
            height: 220px;
        }

        .chart-container-lg {
            height: 280px;
        }

        /* ============================================================
           TABLES
           ============================================================ */
        .table-wrapper {
            overflow-x: auto;
            margin-top: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .data-table th,
        .data-table td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid var(--border-subtle);
            transition: border-color 0.4s ease, color 0.4s ease;
        }

        .data-table th {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 2px solid var(--glass-border);
            white-space: nowrap;
        }

        .data-table td {
            color: var(--text-secondary);
        }

        .data-table tr:hover td {
            background: var(--bg-secondary);
        }

        .data-table .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .data-table .actions .action-btn {
            padding: 4px 12px;
            font-size: 0.7rem;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-status.active {
            background: rgba(46, 204, 113, 0.15);
            color: var(--success-color);
        }

        .badge-status.inactive {
            background: rgba(231, 76, 60, 0.15);
            color: var(--error-color);
        }

        /* ============================================================
           SEARCH / FILTER BAR
           ============================================================ */
        .search-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            align-items: center;
        }

        .search-bar input[type="text"],
        .search-bar select {
            padding: 8px 14px;
            background: var(--input-bg, var(--bg-secondary));
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            color: var(--text-primary);
            font-family: var(--font-primary);
            font-size: 0.85rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            min-width: 160px;
        }

        .search-bar input[type="text"]:focus,
        .search-bar select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-dim);
        }

        .search-bar input[type="text"]::placeholder {
            color: var(--text-muted);
        }

        .search-bar .action-btn {
            padding: 8px 18px;
            border-radius: 40px;
        }

        /* ============================================================
           INLINE FORM
           ============================================================ */
        .inline-form-section {
            padding: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            margin-top: 20px;
            display: none;
            transition: background 0.5s ease, border-color 0.4s ease;
        }

        .inline-form-section.open {
            display: block;
        }

        .inline-form-section h3 {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 4px;
            transition: color 0.4s ease;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px 14px;
            background: var(--input-bg, var(--bg-secondary));
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: var(--font-primary);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-dim);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        /* ============================================================
           MODAL
           ============================================================ */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--modal-bg, rgba(11, 13, 14, 0.92));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s ease;
        }

        .modal-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .modal-content {
            background: var(--bg-primary);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px 36px;
            max-width: 600px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--glass-shadow);
            transform: translateY(-20px) scale(0.95);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease, background 0.5s ease, border-color 0.4s ease;
            position: relative;
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 20px;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: var(--accent);
        }

        .modal-title {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 16px;
            transition: color 0.4s ease;
        }

        .modal-body {
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        /* ============================================================
           MESSAGE BOX
           ============================================================ */
        .message-box-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--modal-bg, rgba(11, 13, 14, 0.92));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10001;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s ease;
        }

        .message-box-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .message-box {
            background: var(--bg-primary);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px 36px;
            max-width: 450px;
            width: 95%;
            text-align: center;
            box-shadow: var(--glass-shadow);
            transform: translateY(-20px) scale(0.95);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease, background 0.5s ease, border-color 0.4s ease;
        }

        .message-box-overlay.show .message-box {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .message-box .msg-title {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            transition: color 0.4s ease;
        }

        .message-box .msg-content {
            color: var(--text-secondary);
            margin-bottom: 20px;
            transition: color 0.4s ease;
        }

        .message-box .msg-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        footer {
            padding: 24px 0;
            border-top: 1px solid var(--footer-border);
            text-align: center;
            transition: border-color 0.4s ease;
            margin-top: auto;
        }

        .copyright {
            color: var(--text-muted);
            font-size: 0.75rem;
            opacity: 0.6;
            transition: color 0.4s ease;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 1024px) {
            .dashboard-wrapper {
                flex-direction: column;
            }
            .sidebar {
                position: static;
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                padding: 12px 16px;
                gap: 4px;
            }
            .sidebar .nav-item {
                padding: 6px 14px;
                font-size: 0.8rem;
            }
            .sidebar .nav-divider {
                display: none;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header-inner {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .logo {
                justify-content: center;
            }
            .header-right {
                justify-content: center;
                flex-wrap: wrap;
            }
            .theme-toggle {
                padding: 4px 8px 4px 4px;
            }
            .toggle-track {
                width: 36px;
                height: 20px;
            }
            .toggle-dot {
                width: 14px;
                height: 14px;
                top: 2px;
                left: 2px;
            }
            body.light .toggle-dot {
                transform: translateX(16px);
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            .section-title {
                font-size: 1.3rem;
            }
            .modal-content {
                padding: 24px 20px;
            }
            .sidebar .nav-item {
                font-size: 0.75rem;
                padding: 4px 12px;
            }
            .search-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-bar input,
            .search-bar select {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .data-table th,
            .data-table td {
                padding: 6px 10px;
                font-size: 0.75rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ============================================================
           UTILITY
           ============================================================ */
        .text-accent {
            color: var(--accent);
        }
        .text-success {
            color: var(--success-color);
        }
        .text-danger {
            color: var(--error-color);
        }
        .text-warning {
            color: var(--warning-color);
        }
        .text-muted {
            color: var(--text-muted);
        }
        .fw-bold {
            font-weight: 700;
        }
        .mb-1 {
            margin-bottom: 8px;
        }
        .mb-2 {
            margin-bottom: 16px;
        }
        .mt-2 {
            margin-top: 16px;
        }
        .gap-1 {
            gap: 8px;
        }
        .flex {
            display: flex;
        }
        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .flex-wrap {
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <!-- Ambient Orbs -->
    <div class="ambient" aria-hidden="true">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>

    <!-- ===== HEADER ===== -->
    <header>
        <div class="container header-inner">
            <div class="logo" onclick="window.location.href='admin_dashboard.php'">
                <img src="../sudo_society.png" alt="Sudo Society Logo" class="logo-icon-img">
                <span class="logo-text">Admin Panel</span>
            </div>
            <div class="header-right">
                <!-- Notification Bell -->
                <div class="notification-bell" id="notificationBell" style="position:relative;">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge" id="notifBadge">4</span>
                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-user-plus"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">New user registered: "hacker1337"</div>
                                <div class="notif-time">10 min ago</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-flag"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">Challenge "XSS 101" solved 50 times</div>
                                <div class="notif-time">1 hour ago</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-exclamation-triangle"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">Server load high (85%)</div>
                                <div class="notif-time">3 hours ago</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-award"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">New achievement unlocked by 5 users</div>
                                <div class="notif-time">5 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Theme Toggle -->
                <div class="theme-toggle" id="themeToggle" role="button" tabindex="0" aria-label="Toggle theme">
                    <span class="toggle-icon" id="toggleIcon">🌙</span>
                    <div class="toggle-track">
                        <div class="toggle-dot"></div>
                    </div>
                    <span class="toggle-label" id="toggleLabel">Dark</span>
                </div>
                <!-- Admin User -->
                <div class="admin-user">
                    <span class="avatar">A</span>
                    <span class="name"><?php echo htmlspecialchars($admin_username); ?></span>
                </div>
                <a href="#" id="adminLogoutBtn" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </header>

    <!-- ===== DASHBOARD WRAPPER ===== -->
    <div class="container">
        <div class="dashboard-wrapper">
            <!-- ===== SIDEBAR ===== -->
            <aside class="sidebar" id="sidebar">
                <div class="nav-item active" data-page="dashboard"><i class="fa-solid fa-chart-pie"></i> Dashboard</div>
                <div class="nav-item" data-page="users"><i class="fa-solid fa-users"></i> Users</div>
                <div class="nav-item" data-page="challenges"><i class="fa-solid fa-puzzle-piece"></i> Challenges</div>
                <div class="nav-item" data-page="achievements"><i class="fa-solid fa-award"></i> Achievements</div>
                <div class="nav-item" data-page="activity"><i class="fa-solid fa-clock-rotate-left"></i> Activity Log</div>
                <div class="nav-divider"></div>
                <div class="nav-item" data-page="settings"><i class="fa-solid fa-gear"></i> Settings</div>
            </aside>

            <!-- ===== MAIN CONTENT ===== -->
            <main class="main-content">

                <!-- ===== DASHBOARD PAGE ===== -->
                <section id="page-dashboard" class="page-section active">
                    <div class="section-header">
                        <h1 class="section-title">Dashboard Overview</h1>
                        <div class="section-actions">
                            <button class="action-btn" id="refreshDashboardBtn"><i class="fa-solid fa-rotate"></i> Refresh</button>
                            <button class="action-btn primary" id="exportStatsBtn"><i class="fa-solid fa-file-export"></i> Export Stats</button>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid" id="dashboardStats">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-user"></i></div>
                            <div class="stat-value" id="statTotalUsers">--</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-puzzle-piece"></i></div>
                            <div class="stat-value" id="statTotalChallenges">--</div>
                            <div class="stat-label">Total Challenges</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-check-double"></i></div>
                            <div class="stat-value" id="statTotalSolves">--</div>
                            <div class="stat-label">Total Solves</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                            <div class="stat-value" id="statTotalPoints">--</div>
                            <div class="stat-label">Total Points</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-ranking-star"></i></div>
                            <div class="stat-value" id="statAvgScore">--</div>
                            <div class="stat-label">Avg. Score</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-play"></i></div>
                            <div class="stat-value" id="statActiveChallenges">--</div>
                            <div class="stat-label">Active Challenges</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-users-between-lines"></i></div>
                            <div class="stat-value" id="statNewUsersToday">--</div>
                            <div class="stat-label">New Users (Today)</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                            <div class="stat-value" id="statSolvesToday">--</div>
                            <div class="stat-label">Solves (Today)</div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="charts-grid">
                        <div class="chart-card">
                            <div class="chart-card-header">
                                <span class="chart-card-title"><i class="fa-solid fa-chart-area"></i> Challenges Solved by Category</span>
                            </div>
                            <div class="chart-container chart-container-lg">
                                <canvas id="adminCategoryChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-card-header">
                                <span class="chart-card-title"><i class="fa-solid fa-chart-line"></i> Recent Activity (7 days)</span>
                            </div>
                            <div class="chart-container chart-container-lg">
                                <canvas id="adminActivityChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performers -->
                    <div class="chart-card" style="margin-top: 20px;">
                        <div class="chart-card-header">
                            <span class="chart-card-title"><i class="fa-solid fa-trophy"></i> Top Performers</span>
                            <span class="text-muted" style="font-size:0.8rem;">By total score</span>
                        </div>
                        <div class="table-wrapper">
                            <table class="data-table" id="topPerformersTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Score</th>
                                        <th>Solved</th>
                                        <th>Streak</th>
                                    </tr>
                                </thead>
                                <tbody id="topPerformersBody">
                                    <tr><td colspan="5" class="text-muted" style="text-align:center;">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- ===== USERS PAGE ===== -->
                <section id="page-users" class="page-section">
                    <div class="section-header">
                        <h1 class="section-title"><i class="fa-solid fa-users"></i> User Management</h1>
                        <div class="section-actions">
                            <button class="action-btn" id="exportUsersBtn"><i class="fa-solid fa-file-export"></i> Export CSV</button>
                        </div>
                    </div>

                    <div class="search-bar">
                        <input type="text" id="userSearchInput" placeholder="Search by username or email...">
                        <select id="userSortSelect">
                            <option value="id">Sort by ID</option>
                            <option value="score">Sort by Score</option>
                            <option value="solved">Sort by Solved</option>
                            <option value="streak">Sort by Streak</option>
                            <option value="rank">Sort by Rank</option>
                        </select>
                        <button class="action-btn" id="userSearchBtn"><i class="fa-solid fa-search"></i> Search</button>
                        <button class="action-btn" id="userResetBtn"><i class="fa-solid fa-undo"></i> Reset</button>
                    </div>

                    <div class="table-wrapper">
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
                                    <th>Last Solved</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr><td colspan="10" class="text-muted" style="text-align:center;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ===== CHALLENGES PAGE ===== -->
                <section id="page-challenges" class="page-section">
                    <div class="section-header">
                        <h1 class="section-title"><i class="fa-solid fa-puzzle-piece"></i> Challenge Management</h1>
                        <div class="section-actions">
                            <button class="action-btn primary" id="addChallengeBtn"><i class="fa-solid fa-plus"></i> Add Challenge</button>
                            <button class="action-btn" id="exportChallengesBtn"><i class="fa-solid fa-file-export"></i> Export CSV</button>
                        </div>
                    </div>

                    <div class="search-bar">
                        <input type="text" id="challengeSearchInput" placeholder="Search by name, category...">
                        <select id="challengeCategoryFilter">
                            <option value="">All Categories</option>
                            <option value="web">Web</option>
                            <option value="pwn">Pwn</option>
                            <option value="crypto">Crypto</option>
                            <option value="reversing">Reversing</option>
                            <option value="forensics">Forensics</option>
                            <option value="misc">Misc</option>
                            <option value="intro">Intro</option>
                            <option value="osint">OSINT</option>
                            <option value="steganography">Steganography</option>
                        </select>
                        <button class="action-btn" id="challengeSearchBtn"><i class="fa-solid fa-search"></i> Search</button>
                        <button class="action-btn" id="challengeResetBtn"><i class="fa-solid fa-undo"></i> Reset</button>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table" id="challengesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Points</th>
                                    <th>Flag</th>
                                    <th>Solves</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="challengesTableBody">
                                <tr><td colspan="9" class="text-muted" style="text-align:center;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Inline Add Challenge Form -->
                    <div class="inline-form-section" id="addChallengeForm">
                        <h3><i class="fa-solid fa-plus"></i> Add New Challenge</h3>
                        <form id="newChallengeForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="newChallengeName">Name *</label>
                                    <input type="text" id="newChallengeName" required>
                                </div>
                                <div class="form-group">
                                    <label for="newChallengeCategory">Category *</label>
                                    <select id="newChallengeCategory" required>
                                        <option value="">Select</option>
                                        <option value="web">Web</option>
                                        <option value="pwn">Pwn</option>
                                        <option value="crypto">Crypto</option>
                                        <option value="reversing">Reversing</option>
                                        <option value="forensics">Forensics</option>
                                        <option value="misc">Misc</option>
                                        <option value="intro">Intro</option>
                                        <option value="osint">OSINT</option>
                                        <option value="steganography">Steganography</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="newChallengePoints">Points *</label>
                                    <input type="number" id="newChallengePoints" required min="1">
                                </div>
                                <div class="form-group">
                                    <label for="newChallengeFlag">Flag *</label>
                                    <input type="text" id="newChallengeFlag" required>
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label for="newChallengeDescription">Description</label>
                                    <textarea id="newChallengeDescription" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="newChallengeLink">Link (Optional)</label>
                                    <input type="text" id="newChallengeLink">
                                </div>
                                <div class="form-group flex flex-center" style="gap:12px; justify-content:flex-start;">
                                    <input type="checkbox" id="newChallengeActive" checked>
                                    <label for="newChallengeActive" style="text-transform:none; font-weight:400; color:var(--text-secondary);">Active</label>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="action-btn primary">Add Challenge</button>
                                <button type="button" class="action-btn" id="cancelAddChallengeBtn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- ===== ACHIEVEMENTS PAGE ===== -->
                <section id="page-achievements" class="page-section">
                    <div class="section-header">
                        <h1 class="section-title"><i class="fa-solid fa-award"></i> Achievement Management</h1>
                        <div class="section-actions">
                            <button class="action-btn primary" id="addAchievementBtn"><i class="fa-solid fa-plus"></i> Add Achievement</button>
                            <button class="action-btn" id="exportAchievementsBtn"><i class="fa-solid fa-file-export"></i> Export CSV</button>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table" id="achievementsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Icon</th>
                                    <th>Required</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="achievementsTableBody">
                                <tr><td colspan="6" class="text-muted" style="text-align:center;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Inline Add Achievement Form -->
                    <div class="inline-form-section" id="addAchievementForm">
                        <h3><i class="fa-solid fa-plus"></i> Add New Achievement</h3>
                        <form id="newAchievementForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="newAchievementName">Name *</label>
                                    <input type="text" id="newAchievementName" required>
                                </div>
                                <div class="form-group">
                                    <label for="newAchievementDescription">Description *</label>
                                    <textarea id="newAchievementDescription" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="newAchievementIcon">Icon Class (e.g., fa-star) *</label>
                                    <input type="text" id="newAchievementIcon" required>
                                </div>
                                <div class="form-group">
                                    <label for="newAchievementRequired">Total Required *</label>
                                    <input type="number" id="newAchievementRequired" required min="1">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="action-btn primary">Add Achievement</button>
                                <button type="button" class="action-btn" id="cancelAddAchievementBtn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- ===== ACTIVITY LOG PAGE ===== -->
                <section id="page-activity" class="page-section">
                    <div class="section-header">
                        <h1 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Activity Log</h1>
                        <div class="section-actions">
                            <button class="action-btn" id="exportActivityBtn"><i class="fa-solid fa-file-export"></i> Export CSV</button>
                            <button class="action-btn" id="refreshActivityBtn"><i class="fa-solid fa-rotate"></i> Refresh</button>
                        </div>
                    </div>

                    <div class="search-bar">
                        <input type="text" id="activitySearchInput" placeholder="Search by user or description...">
                        <select id="activityTypeFilter">
                            <option value="">All Types</option>
                            <option value="solved">Solved</option>
                            <option value="flag">Flag</option>
                            <option value="achievement_unlocked">Achievement</option>
                            <option value="rank_update">Rank Update</option>
                        </select>
                        <input type="date" id="activityDateFrom" placeholder="From">
                        <input type="date" id="activityDateTo" placeholder="To">
                        <button class="action-btn" id="activitySearchBtn"><i class="fa-solid fa-search"></i> Search</button>
                        <button class="action-btn" id="activityResetBtn"><i class="fa-solid fa-undo"></i> Reset</button>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table" id="activityTable">
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
                            <tbody id="activityTableBody">
                                <tr><td colspan="6" class="text-muted" style="text-align:center;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ===== SETTINGS PAGE ===== -->
                <section id="page-settings" class="page-section">
                    <div class="section-header">
                        <h1 class="section-title"><i class="fa-solid fa-gear"></i> Settings</h1>
                    </div>
                    <div class="chart-card">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h3 class="text-muted" style="font-weight:600; margin-bottom:12px;">General Settings</h3>
                                <div class="form-group">
                                    <label for="siteName">Site Name</label>
                                    <input type="text" id="siteName" value="Sudo Society CTF">
                                </div>
                                <div class="form-group">
                                    <label for="siteDescription">Site Description</label>
                                    <textarea id="siteDescription" rows="3">Capture The Flag platform for Sudo Society</textarea>
                                </div>
                                <div class="form-actions">
                                    <button class="action-btn primary" id="saveSettingsBtn">Save Settings</button>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-muted" style="font-weight:600; margin-bottom:12px;">System Status</h3>
                                <div id="systemStatus">
                                    <div class="flex" style="justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-subtle);">
                                        <span>API Server</span>
                                        <span><span class="badge-status active">Online</span></span>
                                    </div>
                                    <div class="flex" style="justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-subtle);">
                                        <span>Database</span>
                                        <span><span class="badge-status active">Online</span></span>
                                    </div>
                                    <div class="flex" style="justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-subtle);">
                                        <span>Challenge Service</span>
                                        <span><span class="badge-status active">Online</span></span>
                                    </div>
                                    <div class="flex" style="justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-subtle);">
                                        <span>Uptime</span>
                                        <span>99.98%</span>
                                    </div>
                                    <div class="flex" style="justify-content:space-between; padding:8px 0;">
                                        <span>Active Users (24h)</span>
                                        <span id="activeUsersCount">47</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society CTF Admin. All rights reserved.</p>
        </div>
    </footer>

    <!-- ===== MODALS ===== -->
    <!-- User Edit Modal -->
    <div id="userEditModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="hideModal('userEditModal')">&times;</button>
            <h3 class="modal-title">Edit User</h3>
            <div class="modal-body">
                <form id="userEditForm">
                    <input type="hidden" id="editUserId">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" id="editUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail">
                    </div>
                    <div class="form-group">
                        <label for="editScore">Total Score</label>
                        <input type="number" id="editScore" required>
                    </div>
                    <div class="form-group">
                        <label for="editSolved">Challenges Solved</label>
                        <input type="number" id="editSolved" required>
                    </div>
                    <div class="form-group">
                        <label for="editStreak">Daily Streak</label>
                        <input type="number" id="editStreak" required>
                    </div>
                    <div class="form-group">
                        <label for="editLastSolved">Last Solved Date</label>
                        <input type="date" id="editLastSolved">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="action-btn primary">Save Changes</button>
                        <button type="button" class="action-btn" onclick="hideModal('userEditModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Challenge Edit Modal -->
    <div id="challengeEditModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="hideModal('challengeEditModal')">&times;</button>
            <h3 class="modal-title">Edit Challenge</h3>
            <div class="modal-body">
                <form id="challengeEditForm">
                    <input type="hidden" id="editChallengeId">
                    <div class="form-group">
                        <label for="editChallengeName">Name</label>
                        <input type="text" id="editChallengeName" required>
                    </div>
                    <div class="form-group">
                        <label for="editChallengeCategory">Category</label>
                        <select id="editChallengeCategory" required>
                            <option value="web">Web</option>
                            <option value="pwn">Pwn</option>
                            <option value="crypto">Crypto</option>
                            <option value="reversing">Reversing</option>
                            <option value="forensics">Forensics</option>
                            <option value="misc">Misc</option>
                            <option value="intro">Intro</option>
                            <option value="osint">OSINT</option>
                            <option value="steganography">Steganography</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editChallengePoints">Points</label>
                        <input type="number" id="editChallengePoints" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="editChallengeDescription">Description</label>
                        <textarea id="editChallengeDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editChallengeFlag">Flag</label>
                        <input type="text" id="editChallengeFlag" required>
                    </div>
                    <div class="form-group">
                        <label for="editChallengeLink">Link (Optional)</label>
                        <input type="text" id="editChallengeLink">
                    </div>
                    <div class="form-group flex" style="align-items:center; gap:12px;">
                        <input type="checkbox" id="editChallengeActive">
                        <label for="editChallengeActive" style="text-transform:none; font-weight:400; color:var(--text-secondary);">Active</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="action-btn primary">Save Changes</button>
                        <button type="button" class="action-btn" onclick="hideModal('challengeEditModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Achievement Edit Modal -->
    <div id="achievementEditModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="hideModal('achievementEditModal')">&times;</button>
            <h3 class="modal-title">Edit Achievement</h3>
            <div class="modal-body">
                <form id="achievementEditForm">
                    <input type="hidden" id="editAchievementId">
                    <div class="form-group">
                        <label for="editAchievementName">Name</label>
                        <input type="text" id="editAchievementName" required>
                    </div>
                    <div class="form-group">
                        <label for="editAchievementDescription">Description</label>
                        <textarea id="editAchievementDescription" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editAchievementIcon">Icon Class</label>
                        <input type="text" id="editAchievementIcon" required>
                    </div>
                    <div class="form-group">
                        <label for="editAchievementRequired">Total Required</label>
                        <input type="number" id="editAchievementRequired" required min="1">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="action-btn primary">Save Changes</button>
                        <button type="button" class="action-btn" onclick="hideModal('achievementEditModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Challenges Modal -->
    <div id="categoryModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="hideModal('categoryModal')">&times;</button>
            <h3 class="modal-title" id="categoryModalTitle">Challenges in Category</h3>
            <div class="modal-body">
                <ul id="categoryModalList" style="list-style:none; padding:0; margin:0;"></ul>
            </div>
        </div>
    </div>

    <!-- ===== MESSAGE BOX ===== -->
    <div id="messageBoxOverlay" class="message-box-overlay">
        <div class="message-box">
            <div class="msg-title" id="msgTitle">Success</div>
            <div class="msg-content" id="msgContent">Operation completed successfully.</div>
            <div class="msg-buttons" id="msgButtons"></div>
        </div>
    </div>

    <!-- ============================================================
    SCRIPT — FULLY CORRECTED WITH NUMERIC CONVERSION FIXES
    ============================================================ -->
    <script>
        // --- Configuration ---
        const ADMIN_API_URL = 'https://sudosocietyctf.unaux.com/admin/admin_api.php';
        const ADMIN_LOGOUT_URL = 'admin_login.php';

        // --- DOM References ---
        const sidebarItems = document.querySelectorAll('.sidebar .nav-item');
        const pageSections = document.querySelectorAll('.page-section');

        // --- Theme Toggle ---
        (function() {
            const toggle = document.getElementById('themeToggle');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleLabel = document.getElementById('toggleLabel');
            const body = document.body;

            const savedTheme = localStorage.getItem('sudo-theme');
            if (savedTheme === 'light') {
                body.classList.add('light');
                toggleIcon.textContent = '☀️';
                toggleLabel.textContent = 'Light';
            } else {
                toggleIcon.textContent = '🌙';
                toggleLabel.textContent = 'Dark';
            }

            function toggleTheme() {
                body.classList.toggle('light');
                const isLight = body.classList.contains('light');
                localStorage.setItem('sudo-theme', isLight ? 'light' : 'dark');
                toggleIcon.textContent = isLight ? '☀️' : '🌙';
                toggleLabel.textContent = isLight ? 'Light' : 'Dark';
            }

            toggle.addEventListener('click', toggleTheme);
            toggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleTheme();
                }
            });
        })();

        // --- Notification Bell ---
        document.getElementById('notificationBell').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('notifDropdown').classList.toggle('open');
        });
        document.addEventListener('click', function() {
            document.getElementById('notifDropdown').classList.remove('open');
        });

        // --- Sidebar Navigation ---
        sidebarItems.forEach(item => {
            item.addEventListener('click', function() {
                const page = this.dataset.page;
                sidebarItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                pageSections.forEach(s => s.classList.remove('active'));
                document.getElementById('page-' + page).classList.add('active');
                document.querySelectorAll('.inline-form-section').forEach(f => f.classList.remove('open'));
            });
        });

        // --- Message Box ---
        function showMessage(title, content, buttons = [{ text: 'OK', type: 'ok' }]) {
            const overlay = document.getElementById('messageBoxOverlay');
            document.getElementById('msgTitle').textContent = title;
            document.getElementById('msgContent').textContent = content;
            const btnContainer = document.getElementById('msgButtons');
            btnContainer.innerHTML = '';
            buttons.forEach(btn => {
                const el = document.createElement('button');
                el.textContent = btn.text;
                el.className = 'action-btn' + (btn.type === 'ok' ? ' primary' : '');
                el.addEventListener('click', () => {
                    hideMessageBox();
                    if (btn.callback) btn.callback();
                });
                btnContainer.appendChild(el);
            });
            overlay.classList.add('show');
        }

        function hideMessageBox() {
            document.getElementById('messageBoxOverlay').classList.remove('show');
        }

        // --- Modal Helpers ---
        function showModal(id) {
            document.getElementById(id).classList.add('show');
        }

        function hideModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });

        // --- API Helper ---
        async function callAdminApi(action, method = 'GET', data = null, queryParams = {}) {
            const url = new URL(ADMIN_API_URL);
            url.searchParams.append('action', action);
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
                    if (response.status === 401) {
                        showMessage("Session Expired", "Please log in again.", [{ text: 'Login', type: 'ok',
                            callback: () => window.location.href = ADMIN_LOGOUT_URL }]);
                        return null;
                    }
                    throw new Error(result.error || result.message || `API error for ${action}`);
                }
                return result.data || result;
            } catch (error) {
                console.error(`API Error (${action}):`, error);
                return null;
            }
        }

        // --- Logout ---
        document.getElementById('adminLogoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            const result = await callAdminApi('adminLogout');
            window.location.href = ADMIN_LOGOUT_URL;
        });

        // ============================================================
        // GLOBAL DATA CACHES
        // ============================================================
        let allUsers = [];
        let allChallenges = [];
        let allAchievements = [];
        let allActivity = [];

        // ============================================================
        // LOAD ALL DATA FUNCTIONS
        // ============================================================
        async function loadAllData() {
            const [users, challenges, achievements, activity] = await Promise.all([
                callAdminApi('getUsers'),
                callAdminApi('getChallenges'),
                callAdminApi('getAchievements'),
                callAdminApi('getActivityLog')
            ]);

            if (users) allUsers = users;
            if (challenges) allChallenges = challenges;
            if (achievements) allAchievements = achievements;
            if (activity) allActivity = activity;

            renderUsersTable(allUsers);
            renderChallengesTable(allChallenges);
            renderAchievementsTable(allAchievements);
            renderActivityTable(allActivity);
            loadDashboard();
        }

        // ============================================================
        // DASHBOARD — ✅ FIXED: Proper numeric conversion
        // ============================================================
        function loadDashboard() {
            const totalUsers = allUsers.length;
            const totalChallenges = allChallenges.length;

            // ✅ FIX: Convert to numbers before summing
            const totalSolves = allChallenges.reduce((sum, ch) => sum + parseInt(ch.solves || 0, 10), 0);
            const totalPoints = allUsers.reduce((sum, u) => sum + parseFloat(u.total_score || 0), 0);
            const avgScore = totalUsers > 0 ? (totalPoints / totalUsers) : 0;
            const activeChallenges = allChallenges.filter(ch => ch.active == 1).length;

            // New users today (fallback to 0 if no created_at)
            const newUsersToday = allUsers.filter(u => {
                if (!u.created_at) return false;
                const today = new Date().toDateString();
                return new Date(u.created_at).toDateString() === today;
            }).length;

            // Solves today (from activity log)
            const solvesToday = allActivity.filter(a => {
                if (a.activity_type !== 'solved') return false;
                const today = new Date().toDateString();
                return new Date(a.timestamp).toDateString() === today;
            }).length;

            // Update stats cards
            document.getElementById('statTotalUsers').textContent = totalUsers;
            document.getElementById('statTotalChallenges').textContent = totalChallenges;
            document.getElementById('statTotalSolves').textContent = totalSolves.toLocaleString();
            document.getElementById('statTotalPoints').textContent = totalPoints.toLocaleString();
            document.getElementById('statAvgScore').textContent = avgScore.toFixed(2);
            document.getElementById('statActiveChallenges').textContent = activeChallenges;
            document.getElementById('statNewUsersToday').textContent = newUsersToday || 0;
            document.getElementById('statSolvesToday').textContent = solvesToday || 0;

            renderCategoryChart();
            renderActivityChart();
            renderTopPerformers();
        }

        // --- Category Chart — ✅ FIXED numeric conversion ---
        let categoryChartInstance = null;

        function renderCategoryChart() {
            const categoryMap = {};
            allChallenges.forEach(ch => {
                const cat = ch.category || 'Uncategorized';
                // ✅ FIX: Convert solves to number
                categoryMap[cat] = (categoryMap[cat] || 0) + parseInt(ch.solves || 0, 10);
            });
            const labels = Object.keys(categoryMap);
            const values = Object.values(categoryMap);

            const ctx = document.getElementById('adminCategoryChart').getContext('2d');
            if (categoryChartInstance) categoryChartInstance.destroy();
            if (labels.length === 0) {
                categoryChartInstance = null;
                return;
            }

            const bgColors = [
                'rgba(111,207,151,0.8)',
                'rgba(64,224,208,0.8)',
                'rgba(255,107,107,0.8)',
                'rgba(255,159,67,0.8)',
                'rgba(153,102,255,0.8)',
                'rgba(54,162,235,0.8)',
                'rgba(255,205,86,0.8)',
                'rgba(201,203,207,0.8)'
            ];

            categoryChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: bgColors.slice(0, labels.length),
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim() || '#0b0d0e',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)',
                                font: { size: 11 },
                                boxWidth: 12,
                                padding: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.label + ': ' + ctx.parsed + ' solves';
                                }
                            }
                        }
                    },
                    onClick: function(e, elements) {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const category = this.data.labels[index];
                            showCategoryChallenges(category);
                        }
                    }
                }
            });
        }

        async function showCategoryChallenges(category) {
            const challenges = allChallenges.filter(ch => ch.category === category);
            const modal = document.getElementById('categoryModal');
            document.getElementById('categoryModalTitle').textContent = 'Challenges in: ' + category;
            const list = document.getElementById('categoryModalList');
            list.innerHTML = '';
            if (challenges.length > 0) {
                challenges.forEach(ch => {
                    const li = document.createElement('li');
                    li.style.cssText = 'padding:8px 12px; border-bottom:1px solid var(--border-subtle); display:flex; justify-content:space-between;';
                    li.innerHTML = `<span><strong>${ch.name}</strong> (${ch.category})</span><span>${ch.points} pts | ${ch.solves} solves</span>`;
                    list.appendChild(li);
                });
            } else {
                list.innerHTML = '<li class="text-muted">No challenges in this category.</li>';
            }
            showModal('categoryModal');
        }

        // --- Activity Chart ---
        let activityChartInstance = null;

        function renderActivityChart() {
            const days = 7;
            const dateLabels = [];
            const solveCounts = [];
            const now = new Date();

            for (let i = days - 1; i >= 0; i--) {
                const d = new Date();
                d.setDate(now.getDate() - i);
                const key = d.toISOString().split('T')[0];
                dateLabels.push(key);
                const count = allActivity.filter(a => {
                    if (a.activity_type !== 'solved') return false;
                    const aDate = new Date(a.timestamp).toISOString().split('T')[0];
                    return aDate === key;
                }).length;
                solveCounts.push(count);
            }

            const ctx = document.getElementById('adminActivityChart').getContext('2d');
            if (activityChartInstance) activityChartInstance.destroy();

            activityChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dateLabels.map(d => {
                        const dt = new Date(d + 'T00:00:00');
                        return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Solves',
                        data: solveCounts,
                        backgroundColor: 'rgba(111,207,151,0.6)',
                        borderColor: 'rgba(111,207,151,1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.parsed.y + ' solves';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)' }
                        }
                    }
                }
            });
        }

        // --- Top Performers ---
        function renderTopPerformers() {
            const sorted = [...allUsers].sort((a, b) => (b.total_score || 0) - (a.total_score || 0));
            const top = sorted.slice(0, 10);
            const tbody = document.getElementById('topPerformersBody');
            tbody.innerHTML = '';
            if (top.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-muted" style="text-align:center;">No users</td></tr>';
                return;
            }
            top.forEach((user, i) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${i+1}</td>
                    <td>${user.username}</td>
                    <td>${user.total_score || 0}</td>
                    <td>${user.challenges_solved || 0}</td>
                    <td>${user.daily_streak || 0}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // ============================================================
        // USERS TABLE
        // ============================================================
        function renderUsersTable(users) {
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';
            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-muted" style="text-align:center;">No users found.</td></tr>';
                return;
            }
            users.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email || 'N/A'}</td>
                    <td>${user.total_score}</td>
                    <td>${user.challenges_solved}</td>
                    <td>${user.current_rank || 'N/A'}</td>
                    <td>${user.daily_streak}</td>
                    <td>${user.last_solved_date ? new Date(user.last_solved_date).toLocaleDateString() : 'Never'}</td>
                    <td>${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A'}</td>
                    <td class="actions">
                        <button class="action-btn" onclick="editUser(${user.id})">Edit</button>
                        <button class="action-btn danger" onclick="confirmDeleteUser(${user.id}, '${user.username}')">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- User search/filter ---
        document.getElementById('userSearchBtn').addEventListener('click', filterUsers);
        document.getElementById('userResetBtn').addEventListener('click', function() {
            document.getElementById('userSearchInput').value = '';
            document.getElementById('userSortSelect').value = 'id';
            filterUsers();
        });
        document.getElementById('userSearchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') filterUsers();
        });

        function filterUsers() {
            const query = document.getElementById('userSearchInput').value.toLowerCase();
            const sort = document.getElementById('userSortSelect').value;
            let filtered = allUsers.filter(u =>
                u.username.toLowerCase().includes(query) ||
                (u.email && u.email.toLowerCase().includes(query))
            );
            if (sort === 'score') filtered.sort((a, b) => b.total_score - a.total_score);
            else if (sort === 'solved') filtered.sort((a, b) => b.challenges_solved - a.challenges_solved);
            else if (sort === 'streak') filtered.sort((a, b) => b.daily_streak - a.daily_streak);
            else if (sort === 'rank') filtered.sort((a, b) => (a.current_rank || 999) - (b.current_rank || 999));
            else filtered.sort((a, b) => a.id - b.id);
            renderUsersTable(filtered);
        }

        // --- User edit/delete ---
        async function editUser(id) {
            const user = allUsers.find(u => u.id === id);
            if (!user) {
                showMessage("Error", "User not found.");
                return;
            }
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editUsername').value = user.username;
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editScore').value = user.total_score;
            document.getElementById('editSolved').value = user.challenges_solved;
            document.getElementById('editStreak').value = user.daily_streak;
            document.getElementById('editLastSolved').value = user.last_solved_date ? new Date(user.last_solved_date)
                .toISOString().split('T')[0] : '';
            showModal('userEditModal');
        }

        document.getElementById('userEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                id: parseInt(document.getElementById('editUserId').value),
                username: document.getElementById('editUsername').value,
                email: document.getElementById('editEmail').value,
                total_score: parseInt(document.getElementById('editScore').value),
                challenges_solved: parseInt(document.getElementById('editSolved').value),
                daily_streak: parseInt(document.getElementById('editStreak').value),
                last_solved_date: document.getElementById('editLastSolved').value || null
            };
            const result = await callAdminApi('editUser', 'POST', data);
            if (result) {
                showMessage("Success", result.message || "User updated.");
                hideModal('userEditModal');
                await loadAllData();
            }
        });

        function confirmDeleteUser(id, username) {
            showMessage("Confirm Delete", `Delete user "${username}"? This cannot be undone.`, [
                { text: 'Delete', type: 'ok', callback: () => deleteUser(id) },
                { text: 'Cancel', type: 'cancel' }
            ]);
        }

        async function deleteUser(id) {
            const result = await callAdminApi('deleteUser', 'POST', { id: id });
            if (result) {
                showMessage("Deleted", result.message);
                await loadAllData();
            }
        }

        // ============================================================
        // CHALLENGES TABLE
        // ============================================================
        function renderChallengesTable(challenges) {
            const tbody = document.getElementById('challengesTableBody');
            tbody.innerHTML = '';
            if (!challenges || challenges.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="9" class="text-muted" style="text-align:center;">No challenges found.</td></tr>';
                return;
            }
            challenges.forEach(ch => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ch.id}</td>
                    <td>${ch.name}</td>
                    <td>${ch.category}</td>
                    <td>${ch.points}</td>
                    <td><code style="font-size:0.75rem; background:var(--bg-secondary); padding:2px 8px; border-radius:4px;">${ch.flag}</code></td>
                    <td>${ch.solves}</td>
                    <td><span class="badge-status ${ch.active ? 'active' : 'inactive'}">${ch.active ? 'Active' : 'Inactive'}</span></td>
                    <td>${ch.created_at ? new Date(ch.created_at).toLocaleDateString() : 'N/A'}</td>
                    <td class="actions">
                        <button class="action-btn" onclick="editChallenge(${ch.id})">Edit</button>
                        <button class="action-btn danger" onclick="confirmDeleteChallenge(${ch.id}, '${ch.name}')">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- Challenge search/filter ---
        document.getElementById('challengeSearchBtn').addEventListener('click', filterChallenges);
        document.getElementById('challengeResetBtn').addEventListener('click', function() {
            document.getElementById('challengeSearchInput').value = '';
            document.getElementById('challengeCategoryFilter').value = '';
            filterChallenges();
        });
        document.getElementById('challengeSearchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') filterChallenges();
        });

        function filterChallenges() {
            const query = document.getElementById('challengeSearchInput').value.toLowerCase();
            const category = document.getElementById('challengeCategoryFilter').value;
            let filtered = allChallenges.filter(ch =>
                ch.name.toLowerCase().includes(query) ||
                ch.category.toLowerCase().includes(query)
            );
            if (category) filtered = filtered.filter(ch => ch.category === category);
            renderChallengesTable(filtered);
        }

        // --- Add Challenge form ---
        document.getElementById('addChallengeBtn').addEventListener('click', function() {
            const form = document.getElementById('addChallengeForm');
            form.classList.toggle('open');
            if (form.classList.contains('open')) {
                this.textContent = 'Cancel';
                this.classList.remove('primary');
            } else {
                this.innerHTML = '<i class="fa-solid fa-plus"></i> Add Challenge';
                this.classList.add('primary');
            }
        });
        document.getElementById('cancelAddChallengeBtn').addEventListener('click', function() {
            document.getElementById('addChallengeForm').classList.remove('open');
            document.getElementById('addChallengeBtn').innerHTML = '<i class="fa-solid fa-plus"></i> Add Challenge';
            document.getElementById('addChallengeBtn').classList.add('primary');
            document.getElementById('newChallengeForm').reset();
        });

        document.getElementById('newChallengeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                name: document.getElementById('newChallengeName').value,
                category: document.getElementById('newChallengeCategory').value,
                points: parseInt(document.getElementById('newChallengePoints').value),
                description: document.getElementById('newChallengeDescription').value,
                flag: document.getElementById('newChallengeFlag').value,
                link: document.getElementById('newChallengeLink').value,
                active: document.getElementById('newChallengeActive').checked ? 1 : 0
            };
            const result = await callAdminApi('addChallenge', 'POST', data);
            if (result) {
                showMessage("Success", result.message || "Challenge added.");
                document.getElementById('newChallengeForm').reset();
                document.getElementById('addChallengeForm').classList.remove('open');
                document.getElementById('addChallengeBtn').innerHTML =
                '<i class="fa-solid fa-plus"></i> Add Challenge';
                document.getElementById('addChallengeBtn').classList.add('primary');
                await loadAllData();
            }
        });

        // --- Edit/Delete Challenge ---
        async function editChallenge(id) {
            const ch = allChallenges.find(c => c.id === id);
            if (!ch) {
                showMessage("Error", "Challenge not found.");
                return;
            }
            document.getElementById('editChallengeId').value = ch.id;
            document.getElementById('editChallengeName').value = ch.name;
            document.getElementById('editChallengeCategory').value = ch.category;
            document.getElementById('editChallengePoints').value = ch.points;
            document.getElementById('editChallengeDescription').value = ch.description || '';
            document.getElementById('editChallengeFlag').value = ch.flag;
            document.getElementById('editChallengeLink').value = ch.link || '';
            document.getElementById('editChallengeActive').checked = ch.active === 1;
            showModal('challengeEditModal');
        }

        document.getElementById('challengeEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                id: parseInt(document.getElementById('editChallengeId').value),
                name: document.getElementById('editChallengeName').value,
                category: document.getElementById('editChallengeCategory').value,
                points: parseInt(document.getElementById('editChallengePoints').value),
                description: document.getElementById('editChallengeDescription').value,
                flag: document.getElementById('editChallengeFlag').value,
                link: document.getElementById('editChallengeLink').value,
                active: document.getElementById('editChallengeActive').checked ? 1 : 0
            };
            const result = await callAdminApi('editChallenge', 'POST', data);
            if (result) {
                showMessage("Success", result.message || "Challenge updated.");
                hideModal('challengeEditModal');
                await loadAllData();
            }
        });

        function confirmDeleteChallenge(id, name) {
            showMessage("Confirm Delete", `Delete challenge "${name}"? This will also remove all solves.`, [
                { text: 'Delete', type: 'ok', callback: () => deleteChallenge(id) },
                { text: 'Cancel', type: 'cancel' }
            ]);
        }

        async function deleteChallenge(id) {
            const result = await callAdminApi('deleteChallenge', 'POST', { id: id });
            if (result) {
                showMessage("Deleted", result.message);
                await loadAllData();
            }
        }

        // ============================================================
        // ACHIEVEMENTS TABLE
        // ============================================================
        function renderAchievementsTable(achievements) {
            const tbody = document.getElementById('achievementsTableBody');
            tbody.innerHTML = '';
            if (!achievements || achievements.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-muted" style="text-align:center;">No achievements found.</td></tr>';
                return;
            }
            achievements.forEach(ach => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ach.id}</td>
                    <td>${ach.name}</td>
                    <td>${ach.description}</td>
                    <td><i class="fa-solid ${ach.icon}"></i> ${ach.icon}</td>
                    <td>${ach.total_required}</td>
                    <td class="actions">
                        <button class="action-btn" onclick="editAchievement(${ach.id})">Edit</button>
                        <button class="action-btn danger" onclick="confirmDeleteAchievement(${ach.id}, '${ach.name}')">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- Add Achievement form ---
        document.getElementById('addAchievementBtn').addEventListener('click', function() {
            const form = document.getElementById('addAchievementForm');
            form.classList.toggle('open');
            if (form.classList.contains('open')) {
                this.textContent = 'Cancel';
                this.classList.remove('primary');
            } else {
                this.innerHTML = '<i class="fa-solid fa-plus"></i> Add Achievement';
                this.classList.add('primary');
            }
        });
        document.getElementById('cancelAddAchievementBtn').addEventListener('click', function() {
            document.getElementById('addAchievementForm').classList.remove('open');
            document.getElementById('addAchievementBtn').innerHTML = '<i class="fa-solid fa-plus"></i> Add Achievement';
            document.getElementById('addAchievementBtn').classList.add('primary');
            document.getElementById('newAchievementForm').reset();
        });

        document.getElementById('newAchievementForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                name: document.getElementById('newAchievementName').value,
                description: document.getElementById('newAchievementDescription').value,
                icon: document.getElementById('newAchievementIcon').value,
                total_required: parseInt(document.getElementById('newAchievementRequired').value)
            };
            const result = await callAdminApi('addAchievement', 'POST', data);
            if (result) {
                showMessage("Success", result.message || "Achievement added.");
                document.getElementById('newAchievementForm').reset();
                document.getElementById('addAchievementForm').classList.remove('open');
                document.getElementById('addAchievementBtn').innerHTML =
                    '<i class="fa-solid fa-plus"></i> Add Achievement';
                document.getElementById('addAchievementBtn').classList.add('primary');
                await loadAllData();
            }
        });

        // --- Edit/Delete Achievement ---
        async function editAchievement(id) {
            const ach = allAchievements.find(a => a.id === id);
            if (!ach) {
                showMessage("Error", "Achievement not found.");
                return;
            }
            document.getElementById('editAchievementId').value = ach.id;
            document.getElementById('editAchievementName').value = ach.name;
            document.getElementById('editAchievementDescription').value = ach.description;
            document.getElementById('editAchievementIcon').value = ach.icon;
            document.getElementById('editAchievementRequired').value = ach.total_required;
            showModal('achievementEditModal');
        }

        document.getElementById('achievementEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = {
                id: parseInt(document.getElementById('editAchievementId').value),
                name: document.getElementById('editAchievementName').value,
                description: document.getElementById('editAchievementDescription').value,
                icon: document.getElementById('editAchievementIcon').value,
                total_required: parseInt(document.getElementById('editAchievementRequired').value)
            };
            const result = await callAdminApi('editAchievement', 'POST', data);
            if (result) {
                showMessage("Success", result.message || "Achievement updated.");
                hideModal('achievementEditModal');
                await loadAllData();
            }
        });

        function confirmDeleteAchievement(id, name) {
            showMessage("Confirm Delete", `Delete achievement "${name}"?`, [
                { text: 'Delete', type: 'ok', callback: () => deleteAchievement(id) },
                { text: 'Cancel', type: 'cancel' }
            ]);
        }

        async function deleteAchievement(id) {
            const result = await callAdminApi('deleteAchievement', 'POST', { id: id });
            if (result) {
                showMessage("Deleted", result.message);
                await loadAllData();
            }
        }

        // ============================================================
        // ACTIVITY LOG
        // ============================================================
        function renderActivityTable(activity) {
            const tbody = document.getElementById('activityTableBody');
            tbody.innerHTML = '';
            if (!activity || activity.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-muted" style="text-align:center;">No activity found.</td></tr>';
                return;
            }
            activity.forEach(log => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${log.id}</td>
                    <td>${log.username || 'N/A'}</td>
                    <td>${log.activity_type}</td>
                    <td>${log.description}</td>
                    <td>${log.points_change !== null ? (log.points_change > 0 ? '+' : '') + log.points_change : 'N/A'}</td>
                    <td>${new Date(log.timestamp).toLocaleString()}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- Activity filter ---
        document.getElementById('activitySearchBtn').addEventListener('click', filterActivity);
        document.getElementById('activityResetBtn').addEventListener('click', function() {
            document.getElementById('activitySearchInput').value = '';
            document.getElementById('activityTypeFilter').value = '';
            document.getElementById('activityDateFrom').value = '';
            document.getElementById('activityDateTo').value = '';
            filterActivity();
        });
        document.getElementById('refreshActivityBtn').addEventListener('click', loadAllData);

        function filterActivity() {
            const query = document.getElementById('activitySearchInput').value.toLowerCase();
            const type = document.getElementById('activityTypeFilter').value;
            const from = document.getElementById('activityDateFrom').value;
            const to = document.getElementById('activityDateTo').value;
            let filtered = allActivity.filter(log =>
                (log.username && log.username.toLowerCase().includes(query)) ||
                log.description.toLowerCase().includes(query)
            );
            if (type) filtered = filtered.filter(log => log.activity_type === type);
            if (from) {
                const fromDate = new Date(from);
                filtered = filtered.filter(log => new Date(log.timestamp) >= fromDate);
            }
            if (to) {
                const toDate = new Date(to);
                toDate.setHours(23, 59, 59);
                filtered = filtered.filter(log => new Date(log.timestamp) <= toDate);
            }
            renderActivityTable(filtered);
        }

        // ============================================================
        // EXPORT FUNCTIONS
        // ============================================================
        function exportCSV(data, filename) {
            if (!data || data.length === 0) {
                showMessage("No Data", "No data available to export.");
                return;
            }
            const headers = Object.keys(data[0]);
            const rows = data.map(row => headers.map(h => row[h] || '').join(','));
            const csv = [headers.join(','), ...rows].join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
            showMessage("Export", "File exported successfully.");
        }

        document.getElementById('exportUsersBtn').addEventListener('click', () => {
            exportCSV(allUsers, 'users_export.csv');
        });
        document.getElementById('exportChallengesBtn').addEventListener('click', () => {
            exportCSV(allChallenges, 'challenges_export.csv');
        });
        document.getElementById('exportAchievementsBtn').addEventListener('click', () => {
            exportCSV(allAchievements, 'achievements_export.csv');
        });
        document.getElementById('exportActivityBtn').addEventListener('click', () => {
            exportCSV(allActivity, 'activity_export.csv');
        });
        document.getElementById('exportStatsBtn').addEventListener('click', () => {
            showMessage("Export", "Stats exported (mock).");
        });

        // ============================================================
        // SETTINGS
        // ============================================================
        document.getElementById('saveSettingsBtn').addEventListener('click', function() {
            showMessage("Settings", "Settings saved (simulated).");
        });

        // ============================================================
        // REFRESH DASHBOARD
        // ============================================================
        document.getElementById('refreshDashboardBtn').addEventListener('click', function() {
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing...';
            loadAllData().then(() => {
                this.innerHTML = '<i class="fa-solid fa-rotate"></i> Refresh';
            });
        });

        // ============================================================
        // INIT
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            loadAllData();
            document.getElementById('activeUsersCount').textContent = Math.floor(Math.random() * 80) + 20;
        });
    </script>
</body>
</html>