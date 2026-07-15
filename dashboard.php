<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard | Sudo Society CTF</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&family=Barlow+Condensed:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

            --rank-gold: #f1c40f;
            --rank-silver: #bdc3c7;
            --rank-bronze: #e67e22;

            --notification-dot: #e74c3c;
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

            --rank-gold: #b7950b;
            --rank-silver: #7f8c8d;
            --rank-bronze: #a04000;

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
            max-width: 1400px;
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

        .nav-links {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 40px;
            transition: color 0.3s ease, background 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--text-primary);
            background: var(--bg-secondary);
        }

        .nav-links a.active {
            color: var(--accent);
            background: var(--accent-dim);
        }

        /* ============================================================
           USER MENU
           ============================================================ */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 4px 12px 4px 4px;
            border-radius: 40px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .user-profile:hover {
            background: var(--bg-secondary);
            border-color: var(--accent-dim);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
            transition: border-color 0.4s ease;
        }

        .username {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            transition: color 0.4s ease;
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

        /* ============================================================
           DASHBOARD LAYOUT
           ============================================================ */
        .dashboard {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            padding: 28px 0 40px;
        }

        /* ============================================================
           SIDEBAR
           ============================================================ */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: sticky;
            top: 90px;
            height: fit-content;
        }

        .sidebar-card {
            padding: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .sidebar-title {
            font-family: var(--font-display);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-title i {
            color: var(--accent);
            font-size: 0.9rem;
            transition: color 0.4s ease;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-card {
            text-align: center;
            padding: 10px 6px;
            background: var(--bg-secondary);
            border-radius: 10px;
            transition: background 0.4s ease;
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .stat-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-top: 2px;
            transition: color 0.4s ease;
        }

        /* Streak */
        .streak-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .streak-count {
            font-family: var(--font-display);
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            transition: color 0.4s ease;
            position: relative;
        }

        .streak-count::after {
            content: '🔥';
            font-size: 2rem;
            margin-left: 4px;
        }

        .streak-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .streak-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            width: 100%;
            margin-top: 4px;
        }

        .streak-day {
            aspect-ratio: 1;
            border-radius: 4px;
            background: var(--bg-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            color: var(--text-muted);
            transition: all 0.3s ease;
        }

        .streak-day.active {
            background: var(--accent);
            color: var(--bg-primary);
            font-weight: 700;
            box-shadow: 0 0 12px rgba(111, 207, 151, 0.25);
        }

        body.light .streak-day.active {
            color: #fff;
        }

        .streak-day.today {
            border: 1px solid var(--accent);
            box-shadow: 0 0 8px rgba(111, 207, 151, 0.15);
        }

        /* Milestone */
        .milestone-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
            padding: 12px;
            background: var(--bg-secondary);
            border-radius: 12px;
            transition: background 0.4s ease;
        }

        .milestone-icon {
            font-size: 1.8rem;
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .milestone-title {
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.4s ease;
        }

        .milestone-desc {
            font-size: 0.75rem;
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .milestone-bar {
            width: 100%;
            height: 4px;
            background: var(--bg-secondary);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
        }

        .milestone-bar-fill {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 4px;
            transition: width 1.2s ease;
        }

        /* Overall Progress */
        .progress-radial-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .progress-radial-card .chart-container {
            height: 160px;
            width: 100%;
            max-width: 160px;
        }

        .progress-radial-card .stat-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        /* ============================================================
           MAIN CONTENT
           ============================================================ */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Welcome Banner */
        .welcome-banner {
            padding: 20px 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--accent-dim), transparent);
            transition: left 0.8s ease;
            pointer-events: none;
        }

        .welcome-banner:hover::before {
            left: 100%;
        }

        .welcome-left {
            flex: 1;
        }

        .welcome-title {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--section-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: background 0.5s ease;
        }

        .welcome-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 2px;
            transition: color 0.4s ease;
        }

        .welcome-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            padding: 8px 18px;
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            background: var(--glass-bg);
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-primary);
        }

        .quick-action-btn:hover {
            background: var(--accent-dim);
            color: var(--accent);
            border-color: var(--accent);
        }

        .quick-action-btn.primary {
            background: var(--accent-gradient);
            color: var(--bg-primary);
            border: none;
        }

        .quick-action-btn.primary:hover {
            opacity: 0.85;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(111, 207, 151, 0.20);
        }

        body.light .quick-action-btn.primary:hover {
            box-shadow: 0 4px 16px rgba(192, 57, 43, 0.15);
        }

        /* Score Cards */
        .score-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .score-card {
            padding: 18px 16px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, background 0.5s ease;
        }

        .score-card:hover {
            transform: translateY(-3px);
            border-color: var(--card-hover-border);
            box-shadow: var(--card-hover-shadow);
        }

        .score-card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .score-card-title i {
            color: var(--accent);
            font-size: 0.9rem;
            transition: color 0.4s ease;
        }

        .score-card-value {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            transition: color 0.4s ease;
        }

        .score-card-change {
            font-size: 0.65rem;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .score-card-change.positive {
            color: var(--success-color);
        }
        .score-card-change.negative {
            color: var(--error-color);
        }

        /* ============================================================
           DASHBOARD GRID (CRM STYLE)
           ============================================================ */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Charts */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
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

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chart-title {
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.4s ease;
        }

        .chart-title i {
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .chart-period {
            display: flex;
            gap: 4px;
        }

        .period-btn {
            padding: 4px 12px;
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: var(--font-primary);
        }

        .period-btn:hover {
            border-color: var(--accent);
            color: var(--text-primary);
        }

        .period-btn.active {
            background: var(--accent-dim);
            border-color: var(--accent);
            color: var(--accent);
        }

        .chart-container {
            position: relative;
            height: 220px;
        }

        /* ============================================================
           RIGHT SIDEBAR WIDGETS (CRM)
           ============================================================ */
        .right-widgets {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Challenge Status Overview */
        .status-overview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .status-item {
            text-align: center;
            padding: 10px 8px;
            background: var(--bg-secondary);
            border-radius: 10px;
            transition: background 0.4s ease;
        }

        .status-item .status-value {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            transition: color 0.4s ease;
        }

        .status-item .status-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .status-item.solved .status-value {
            color: var(--success-color);
        }
        .status-item.unsolved .status-value {
            color: var(--text-muted);
        }
        .status-item.in-progress .status-value {
            color: var(--warning-color);
        }

        /* Daily Goals */
        .daily-goals {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .goal-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: var(--bg-secondary);
            border-radius: 10px;
            transition: background 0.4s ease;
        }

        .goal-item .goal-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
            font-size: 0.6rem;
            color: transparent;
        }

        .goal-item.done .goal-check {
            background: var(--success-color);
            border-color: var(--success-color);
            color: var(--bg-primary);
        }

        .goal-item .goal-text {
            flex: 1;
            font-size: 0.85rem;
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .goal-item.done .goal-text {
            text-decoration: line-through;
            color: var(--text-muted);
        }

        .goal-item .goal-progress {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* Upcoming Events */
        .event-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: var(--bg-secondary);
            border-radius: 10px;
            margin-bottom: 8px;
            transition: background 0.4s ease;
        }

        .event-item:last-child {
            margin-bottom: 0;
        }

        .event-item .event-date {
            font-family: var(--font-display);
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--accent);
            min-width: 50px;
            text-align: center;
            transition: color 0.4s ease;
        }

        .event-item .event-info {
            flex: 1;
        }

        .event-item .event-name {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: color 0.4s ease;
        }

        .event-item .event-location {
            font-size: 0.7rem;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        /* ============================================================
           NEW CRM WIDGETS
           ============================================================ */

        /* Recent Challenges */
        .recent-challenge-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: var(--bg-secondary);
            border-radius: 10px;
            margin-bottom: 8px;
            transition: background 0.4s ease;
        }
        .recent-challenge-item:last-child {
            margin-bottom: 0;
        }
        .recent-challenge-item .rc-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .recent-challenge-item .rc-status.solved {
            background: var(--success-color);
        }
        .recent-challenge-item .rc-status.attempted {
            background: var(--warning-color);
        }
        .recent-challenge-item .rc-status.unsolved {
            background: var(--text-muted);
        }
        .recent-challenge-item .rc-name {
            flex: 1;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        .recent-challenge-item .rc-points {
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 600;
        }

        /* Skill Radar Chart Container */
        .radar-chart-container {
            height: 220px;
            width: 100%;
        }

        /* Points Breakdown */
        .points-breakdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 0;
            border-bottom: 1px solid var(--border-subtle);
        }
        .points-breakdown-item:last-child {
            border-bottom: none;
        }
        .points-breakdown-item .pb-category {
            flex: 1;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        .points-breakdown-item .pb-bar {
            flex: 2;
            height: 4px;
            background: var(--bg-secondary);
            border-radius: 4px;
            overflow: hidden;
        }
        .points-breakdown-item .pb-bar-fill {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 4px;
            transition: width 1s ease;
        }
        .points-breakdown-item .pb-value {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--accent);
            min-width: 40px;
            text-align: right;
        }

        /* System Status */
        .system-status-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 0;
            border-bottom: 1px solid var(--border-subtle);
        }
        .system-status-item:last-child {
            border-bottom: none;
        }
        .system-status-item .ss-label {
            flex: 1;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        .system-status-item .ss-value {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        .system-status-item .ss-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .system-status-item .ss-dot.online {
            background: var(--success-color);
        }
        .system-status-item .ss-dot.offline {
            background: var(--error-color);
        }
        .system-status-item .ss-dot.maintenance {
            background: var(--warning-color);
        }

        /* ============================================================
           ACHIEVEMENTS & ACTIVITY
           ============================================================ */
        .achievements-section {
            padding: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.4s ease;
        }

        .section-title i {
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .view-all {
            font-size: 0.8rem;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .view-all:hover {
            opacity: 0.8;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        .achievement-card {
            padding: 12px 10px;
            background: var(--bg-secondary);
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease, background 0.4s ease;
        }

        .achievement-card:hover {
            transform: translateY(-2px);
            background: var(--accent-dim);
        }

        .achievement-icon {
            font-size: 1.4rem;
            color: var(--accent);
            margin-bottom: 4px;
            transition: color 0.4s ease;
        }

        .achievement-title {
            font-weight: 600;
            font-size: 0.8rem;
            transition: color 0.4s ease;
        }

        .achievement-desc {
            font-size: 0.65rem;
            color: var(--text-secondary);
            margin-top: 2px;
            transition: color 0.4s ease;
        }

        .achievement-progress {
            width: 100%;
            height: 3px;
            background: var(--bg-secondary);
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .achievement-progress-bar {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 4px;
            transition: width 1s ease;
        }

        /* Activity List */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: var(--bg-secondary);
            border-radius: 10px;
            transition: background 0.4s ease;
            border-left: 3px solid var(--accent);
        }

        .activity-icon {
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
            transition: background 0.4s ease, color 0.4s ease;
        }

        .activity-details {
            flex: 1;
        }

        .activity-title {
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.4s ease;
        }

        .activity-time {
            font-size: 0.7rem;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .activity-points {
            font-family: var(--font-display);
            font-weight: 600;
            color: var(--accent);
            font-size: 0.9rem;
            transition: color 0.4s ease;
        }

        /* Leaderboard */
        .leaderboard-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .leaderboard-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
        }

        .leaderboard-item.focused {
            background: var(--accent-dim);
            border: 1px solid var(--accent);
            box-shadow: 0 0 20px rgba(111, 207, 151, 0.06);
        }

        .leaderboard-item.dimmed {
            opacity: 0.5;
        }

        .leaderboard-item .rank {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.85rem;
            width: 32px;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .leaderboard-item .rank.gold {
            color: var(--rank-gold);
        }
        .leaderboard-item .rank.silver {
            color: var(--rank-silver);
        }
        .leaderboard-item .rank.bronze {
            color: var(--rank-bronze);
        }

        .leaderboard-item .username {
            flex: 1;
            font-weight: 500;
            font-size: 0.85rem;
            transition: color 0.4s ease;
        }

        .leaderboard-item .score {
            font-family: var(--font-display);
            font-weight: 600;
            color: var(--accent);
            font-size: 0.85rem;
            transition: color 0.4s ease;
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
            .dashboard {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: static;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
            }
            .sidebar-card {
                margin-bottom: 0;
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
            .nav-links {
                justify-content: center;
            }
            .nav-links a {
                font-size: 0.8rem;
                padding: 6px 14px;
            }
            .user-menu {
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

            .score-cards {
                grid-template-columns: 1fr 1fr;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .achievements-grid {
                grid-template-columns: 1fr 1fr;
            }
            .sidebar {
                grid-template-columns: 1fr 1fr;
            }
            .welcome-banner {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            .welcome-actions {
                justify-content: center;
            }
            .notification-dropdown {
                width: 300px;
                right: -60px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }
            .score-cards {
                grid-template-columns: 1fr;
            }
            .achievements-grid {
                grid-template-columns: 1fr;
            }
            .sidebar {
                grid-template-columns: 1fr;
            }
            .dashboard {
                padding: 16px 0 30px;
                gap: 16px;
            }
            .welcome-banner {
                padding: 16px;
            }
            .welcome-title {
                font-size: 1.2rem;
            }
            .score-card-value {
                font-size: 1.6rem;
            }
            .chart-container {
                height: 180px;
            }
            .notification-dropdown {
                width: 280px;
                right: -80px;
            }
        }

        /* ============================================================
           ANIMATIONS
           ============================================================ */
        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.7s ease forwards;
        }

        .fade-up:nth-child(2) {
            animation-delay: 0.06s;
        }
        .fade-up:nth-child(3) {
            animation-delay: 0.12s;
        }
        .fade-up:nth-child(4) {
            animation-delay: 0.18s;
        }
        .fade-up:nth-child(5) {
            animation-delay: 0.24s;
        }
        .fade-up:nth-child(6) {
            animation-delay: 0.30s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================================
           CHART.JS OVERRIDES
           ============================================================ */
        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .apexcharts-canvas {
            font-family: var(--font-primary) !important;
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
            <div class="logo" onclick="window.location.href='index.html'">
                <img src="sudo_society.png" alt="Sudo Society Logo" class="logo-icon-img">
                <span class="logo-text">Sudo Society</span>
            </div>
            <div class="user-menu">
                <nav class="nav-links">
                    <a href="dashboard.php" class="active">Dashboard</a>
                    <a href="jenslin_little_advangure.php">Challenges</a>
                    <a href="leaderboard.php">Leaderboard</a>
                    <a href="https://sudosocietyctf.unaux.com/event.php">Event</a>
                    <a href="#" id="logout">Logout</a>
                </nav>
                <!-- Notification Bell -->
                <div class="notification-bell" id="notificationBell" style="position:relative;">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge" id="notifBadge">3</span>
                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-trophy"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">You reached rank #15!</div>
                                <div class="notif-time">2 hours ago</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-flag"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">New challenge: "Buffer Overflow"</div>
                                <div class="notif-time">5 hours ago</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fa-solid fa-award"></i></div>
                            <div class="notif-content">
                                <div class="notif-title">Achievement unlocked: "Speed Demon"</div>
                                <div class="notif-time">1 day ago</div>
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
                <div class="user-profile" onclick="window.location.href='settings.html'" style="cursor: pointer;">
                    <img src="<?php echo isset($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : 'https://i.pravatar.cc/100?img=11'; ?>" alt="User Avatar" class="user-avatar" id="headerUserAvatar">
                    <span class="username" id="username">Loading...</span>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== DASHBOARD ===== -->
    <div class="container">
        <div class="dashboard">
            <!-- ===== SIDEBAR ===== -->
            <aside class="sidebar">
                <!-- Stats -->
                <div class="sidebar-card fade-up">
                    <div class="sidebar-title"><i class="fa-solid fa-chart-line"></i> Stats</div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value" id="stat-challenges">--</div>
                            <div class="stat-label">Solved</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="stat-points">--</div>
                            <div class="stat-label">Points</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="stat-streak">--</div>
                            <div class="stat-label">Streak</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="stat-rank">--</div>
                            <div class="stat-label">Rank</div>
                        </div>
                    </div>
                </div>

                <!-- Streak -->
                <div class="sidebar-card fade-up">
                    <div class="sidebar-title"><i class="fa-solid fa-fire"></i> Streak</div>
                    <div class="streak-container">
                        <div class="streak-count" id="streak-count">--</div>
                        <div class="streak-label">Day Streak</div>
                        <div class="streak-calendar" id="streak-calendar">
                            <!-- JS populates -->
                        </div>
                    </div>
                </div>

                <!-- Milestone -->
                <div class="sidebar-card fade-up">
                    <div class="sidebar-title"><i class="fa-solid fa-trophy"></i> Milestone</div>
                    <div class="milestone-card">
                        <div class="milestone-icon">🏆</div>
                        <div class="milestone-title">Elite Hacker</div>
                        <div class="milestone-desc">Reach top 50 on leaderboard</div>
                        <div class="milestone-bar">
                            <div class="milestone-bar-fill" id="milestoneBar" style="width: 65%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Overall Progress -->
                <div class="sidebar-card fade-up">
                    <div class="sidebar-title"><i class="fa-solid fa-circle-check"></i> Progress</div>
                    <div class="progress-radial-card">
                        <div id="overallProgressChart" class="chart-container"></div>
                        <div class="stat-label">Challenges Solved</div>
                    </div>
                </div>
            </aside>

            <!-- ===== MAIN CONTENT ===== -->
            <div class="main-content">
                <!-- Welcome Banner with Quick Actions -->
                <div class="welcome-banner fade-up">
                    <div class="welcome-left">
                        <h2 class="welcome-title" id="welcome-title">Welcome back...</h2>
                        <p class="welcome-subtitle" id="welcome-subtitle">Loading...</p>
                    </div>
                    <div class="welcome-actions">
                        <a href="jenslin_little_advangure.php" class="quick-action-btn primary"><i class="fa-solid fa-play"></i> Start Challenge</a>
                        <a href="leaderboard.php" class="quick-action-btn"><i class="fa-solid fa-ranking-star"></i> Leaderboard</a>
                        <a href="settings.html" class="quick-action-btn"><i class="fa-solid fa-gear"></i> Settings</a>
                        <button class="quick-action-btn" id="refreshBtn"><i class="fa-solid fa-rotate"></i> Refresh</button>
                    </div>
                </div>

                <!-- Score Cards -->
                <div class="score-cards fade-up">
                    <div class="score-card">
                        <div class="score-card-title"><i class="fa-solid fa-star"></i> Total Score</div>
                        <div class="score-card-value" id="score-total">--</div>
                        <div class="score-card-change positive">
                            <i class="fa-solid fa-arrow-up"></i> +250 (24h)
                        </div>
                    </div>
                    <div class="score-card">
                        <div class="score-card-title"><i class="fa-solid fa-terminal"></i> Solved</div>
                        <div class="score-card-value" id="score-solved">--</div>
                        <div class="score-card-change positive">
                            <i class="fa-solid fa-arrow-up"></i> +5 (week)
                        </div>
                    </div>
                    <div class="score-card">
                        <div class="score-card-title"><i class="fa-solid fa-ranking-star"></i> Rank</div>
                        <div class="score-card-value" id="score-rank">--</div>
                        <div class="score-card-change positive">
                            <i class="fa-solid fa-arrow-up"></i> +3 overall
                        </div>
                    </div>
                    <div class="score-card">
                        <div class="score-card-title"><i class="fa-solid fa-clock"></i> Time</div>
                        <div class="score-card-value" id="score-time">--</div>
                        <div class="score-card-change positive">
                            <i class="fa-solid fa-arrow-up"></i> +12h this week
                        </div>
                    </div>
                </div>

                <!-- Dashboard Grid: Charts + Right Widgets -->
                <div class="dashboard-grid fade-up">
                    <!-- LEFT: Charts and additional widgets -->
                    <div class="charts-section">
                        <!-- Score Progression Chart -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-chart-area"></i> Score Progression</span>
                                <div class="chart-period">
                                    <button class="period-btn" data-period="7d">7D</button>
                                    <button class="period-btn" data-period="30d">30D</button>
                                    <button class="period-btn" data-period="90d">90D</button>
                                    <button class="period-btn active" data-period="all">ALL</button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="scoreProgressionChart"></canvas>
                            </div>
                        </div>

                        <!-- Two-column grid for smaller charts -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <!-- Challenges by Category (Doughnut) -->
                            <div class="chart-card">
                                <div class="chart-header">
                                    <span class="chart-title"><i class="fa-solid fa-cubes"></i> Categories</span>
                                </div>
                                <div class="chart-container" style="height:180px;">
                                    <canvas id="challengesByCategoryChart"></canvas>
                                </div>
                            </div>
                            <!-- Skill Radar Chart -->
                            <div class="chart-card">
                                <div class="chart-header">
                                    <span class="chart-title"><i class="fa-solid fa-radar"></i> Skill Radar</span>
                                </div>
                                <div class="radar-chart-container">
                                    <canvas id="skillRadarChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Challenges (new) -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Challenges</span>
                                <a href="jenslin_little_advangure.php" class="view-all">View All</a>
                            </div>
                            <div id="recentChallenges">
                                <div class="recent-challenge-item">
                                    <span class="rc-status solved"></span>
                                    <span class="rc-name">SQL Injection 101</span>
                                    <span class="rc-points">+150</span>
                                </div>
                                <div class="recent-challenge-item">
                                    <span class="rc-status attempted"></span>
                                    <span class="rc-name">Buffer Overflow</span>
                                    <span class="rc-points">+200</span>
                                </div>
                                <div class="recent-challenge-item">
                                    <span class="rc-status unsolved"></span>
                                    <span class="rc-name">XSS Challenge</span>
                                    <span class="rc-points">+120</span>
                                </div>
                                <div class="recent-challenge-item">
                                    <span class="rc-status solved"></span>
                                    <span class="rc-name">Crypto Basics</span>
                                    <span class="rc-points">+100</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: CRM Widgets -->
                    <div class="right-widgets">
                        <!-- Challenge Status Overview -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-chart-pie"></i> Challenge Status</span>
                            </div>
                            <div class="status-overview-grid">
                                <div class="status-item solved">
                                    <div class="status-value" id="statusSolved">--</div>
                                    <div class="status-label">Solved</div>
                                </div>
                                <div class="status-item unsolved">
                                    <div class="status-value" id="statusUnsolved">--</div>
                                    <div class="status-label">Unsolved</div>
                                </div>
                                <div class="status-item in-progress">
                                    <div class="status-value" id="statusInProgress">--</div>
                                    <div class="status-label">In Progress</div>
                                </div>
                                <div class="status-item">
                                    <div class="status-value" id="statusTotal">--</div>
                                    <div class="status-label">Total</div>
                                </div>
                            </div>
                        </div>

                        <!-- Points Breakdown (new) -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-chart-simple"></i> Points Breakdown</span>
                            </div>
                            <div id="pointsBreakdown">
                                <div class="points-breakdown-item">
                                    <span class="pb-category">Web</span>
                                    <span class="pb-bar"><span class="pb-bar-fill" style="width:75%;"></span></span>
                                    <span class="pb-value">750</span>
                                </div>
                                <div class="points-breakdown-item">
                                    <span class="pb-category">Pwn</span>
                                    <span class="pb-bar"><span class="pb-bar-fill" style="width:45%;"></span></span>
                                    <span class="pb-value">450</span>
                                </div>
                                <div class="points-breakdown-item">
                                    <span class="pb-category">Crypto</span>
                                    <span class="pb-bar"><span class="pb-bar-fill" style="width:60%;"></span></span>
                                    <span class="pb-value">600</span>
                                </div>
                                <div class="points-breakdown-item">
                                    <span class="pb-category">Rev</span>
                                    <span class="pb-bar"><span class="pb-bar-fill" style="width:30%;"></span></span>
                                    <span class="pb-value">300</span>
                                </div>
                                <div class="points-breakdown-item">
                                    <span class="pb-category">Forensic</span>
                                    <span class="pb-bar"><span class="pb-bar-fill" style="width:20%;"></span></span>
                                    <span class="pb-value">200</span>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Goals -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-check-double"></i> Daily Goals</span>
                                <span style="font-size:0.7rem; color:var(--text-muted);" id="goalProgress">0/3</span>
                            </div>
                            <div class="daily-goals">
                                <div class="goal-item done">
                                    <span class="goal-check"><i class="fa-solid fa-check"></i></span>
                                    <span class="goal-text">Solve 3 challenges</span>
                                    <span class="goal-progress">3/3</span>
                                </div>
                                <div class="goal-item done">
                                    <span class="goal-check"><i class="fa-solid fa-check"></i></span>
                                    <span class="goal-text">Earn 150 points</span>
                                    <span class="goal-progress">150/150</span>
                                </div>
                                <div class="goal-item">
                                    <span class="goal-check"></span>
                                    <span class="goal-text">Complete 1 crypto challenge</span>
                                    <span class="goal-progress">0/1</span>
                                </div>
                                <div class="goal-item">
                                    <span class="goal-check"></span>
                                    <span class="goal-text">Maintain 7-day streak</span>
                                    <span class="goal-progress">5/7</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-calendar"></i> Upcoming Events</span>
                                <a href="https://sudosocietyctf.unaux.com/event.php" class="view-all">View All</a>
                            </div>
                            <div id="upcomingEventsWidget">
                                <div class="event-item">
                                    <div class="event-date">Jul 20</div>
                                    <div class="event-info">
                                        <div class="event-name">CTF Finals</div>
                                        <div class="event-location">Online</div>
                                    </div>
                                </div>
                                <div class="event-item">
                                    <div class="event-date">Jul 25</div>
                                    <div class="event-info">
                                        <div class="event-name">Web Security Workshop</div>
                                        <div class="event-location">Virtual</div>
                                    </div>
                                </div>
                                <div class="event-item">
                                    <div class="event-date">Aug 01</div>
                                    <div class="event-info">
                                        <div class="event-name">Binary Exploitation 101</div>
                                        <div class="event-location">Online</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Status (new) -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <span class="chart-title"><i class="fa-solid fa-server"></i> System Status</span>
                            </div>
                            <div id="systemStatus">
                                <div class="system-status-item">
                                    <span class="ss-label">API Server</span>
                                    <span class="ss-value"><span class="ss-dot online"></span> Online</span>
                                </div>
                                <div class="system-status-item">
                                    <span class="ss-label">Database</span>
                                    <span class="ss-value"><span class="ss-dot online"></span> Online</span>
                                </div>
                                <div class="system-status-item">
                                    <span class="ss-label">Challenge Service</span>
                                    <span class="ss-value"><span class="ss-dot online"></span> Online</span>
                                </div>
                                <div class="system-status-item">
                                    <span class="ss-label">Uptime</span>
                                    <span class="ss-value">99.98%</span>
                                </div>
                                <div class="system-status-item">
                                    <span class="ss-label">Active Users</span>
                                    <span class="ss-value" id="activeUsers">47</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Achievements -->
                <div class="achievements-section fade-up">
                    <div class="section-header">
                        <span class="section-title"><i class="fa-solid fa-award"></i> Latest Achievements</span>
                        <a href="achievements.html" class="view-all">View All <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                    <div class="achievements-grid" id="achievements-grid">
                        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading...</div>
                    </div>
                </div>

                <!-- Activity & Leaderboard -->
                <div class="achievements-section fade-up">
                    <div class="section-header">
                        <span class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</span>
                        <a href="#" class="view-all">View All <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                    <div class="activity-list" id="activity-list">
                        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading...</div>
                    </div>
                </div>

                <!-- Leaderboard Preview -->
                <div class="achievements-section fade-up">
                    <div class="section-header">
                        <span class="section-title"><i class="fa-solid fa-users"></i> Leaderboard Preview</span>
                        <a href="leaderboard.php" class="view-all">Full Leaderboard <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                    <div class="leaderboard-list" id="leaderboardList">
                        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society CTF. All rights reserved. Developed by JENSLIN</p>
        </div>
    </footer>

    <!-- ============================================================
    SCRIPT
    ============================================================ -->
    <script>
        // --- Configuration ---
        const API_URL = 'https://sudosocietyctf.unaux.com/api/api.php';
        document.getElementById('logout').href = "https://sudosocietyctf.unaux.com/logout.php";

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
            const dropdown = document.getElementById('notifDropdown');
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', function() {
            document.getElementById('notifDropdown').classList.remove('open');
        });

        // --- Refresh Button ---
        document.getElementById('refreshBtn').addEventListener('click', function() {
            populateDashboard();
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing...';
            setTimeout(() => {
                this.innerHTML = '<i class="fa-solid fa-rotate"></i> Refresh';
            }, 1500);
        });

        // --- Reusable API Fetching Function ---
        async function fetchData(endpoint, params = {}) {
            try {
                const url = new URL(API_URL);
                url.searchParams.append('action', endpoint);
                for (const key in params) {
                    url.searchParams.append(key, params[key]);
                }
                const response = await fetch(url);
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`HTTP error! Status: ${response.status}. Message: ${errorData.message || errorData.error || 'Unknown API error.'}`);
                }
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || data.error || 'API request failed with success: false.');
                }
                return data.data || data;
            } catch (error) {
                console.error(`Error fetching data from ${endpoint}:`, error);
                return null;
            }
        }

        // --- Chart instances ---
        let scoreProgressionChartInstance;
        let challengesByCategoryChartInstance;
        let overallProgressChart;
        let skillRadarChartInstance;

        // --- 1. Render Score Progression Chart ---
        async function renderScoreProgressionChart(period = 'all') {
            const chartData = await fetchData('getScoreProgression', { period: period });
            const chartContainer = document.querySelector('#scoreProgressionChart').parentNode;
            const chartElement = document.getElementById('scoreProgressionChart');
            let scoreCtx = chartElement.getContext('2d');

            const existingMessage = document.getElementById('scoreProgressionMessage');
            if (existingMessage) {
                existingMessage.remove();
            }

            if (!chartData || chartData.length === 0) {
                if (scoreProgressionChartInstance) {
                    scoreProgressionChartInstance.destroy();
                    scoreProgressionChartInstance = null;
                }
                chartElement.style.display = 'none';

                let messageDiv = document.createElement('div');
                messageDiv.id = 'scoreProgressionMessage';
                messageDiv.style.textAlign = 'center';
                messageDiv.style.color = 'var(--text-muted)';
                messageDiv.style.paddingTop = '100px';
                messageDiv.textContent = 'No score data available for this period.';
                chartContainer.appendChild(messageDiv);

                return;
            }

            chartElement.style.display = 'block';

            const scores = chartData.map(item => item.score);
            const timestamps = chartData.map(item => new Date(item.timestamp).toLocaleDateString('en-US', { day: 'numeric', month: 'short' }));

            const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97';
            const bgPrimary = getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim() || '#0b0d0e';
            const chartGrid = getComputedStyle(document.documentElement).getPropertyValue('--chart-grid').trim() || 'rgba(255,255,255,0.06)';
            const chartText = getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)';

            const data = {
                labels: timestamps,
                datasets: [{
                    label: 'Score',
                    data: scores,
                    borderColor: accent,
                    backgroundColor: 'rgba(111, 207, 151, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: accent,
                    pointBorderColor: bgPrimary,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            };

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString() + ' points';
                                }
                                return label;
                            }
                        },
                        backgroundColor: bgPrimary,
                        borderColor: accent,
                        borderWidth: 1,
                        titleColor: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() || '#f0f5f3',
                        bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || 'rgba(240,245,243,0.70)',
                        titleFont: { family: "'Inter', sans-serif", weight: '600' },
                        bodyFont: { family: "'Inter', sans-serif" },
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartGrid, borderColor: chartGrid },
                        ticks: { color: chartText, font: { family: "'Inter', sans-serif" } }
                    },
                    x: {
                        grid: { color: chartGrid, borderColor: chartGrid },
                        ticks: { color: chartText, font: { family: "'Inter', sans-serif" }, maxTicksLimit: 8 }
                    }
                }
            };

            if (scoreProgressionChartInstance) {
                scoreProgressionChartInstance.data = data;
                scoreProgressionChartInstance.options = options;
                scoreProgressionChartInstance.update();
            } else {
                scoreProgressionChartInstance = new Chart(scoreCtx, {
                    type: 'line',
                    data: data,
                    options: options
                });
            }
        }

        // --- 2. Render Challenges by Category Chart ---
        async function renderChallengesByCategoryChart() {
            const chartResponse = await fetchData('getChallengesByCategory');
            const chartContainer = document.querySelector('#challengesByCategoryChart').parentNode;
            const chartElement = document.getElementById('challengesByCategoryChart');
            let categoryCtx = chartElement.getContext('2d');

            const existingMessage = document.getElementById('challengesByCategoryMessage');
            if (existingMessage) {
                existingMessage.remove();
            }

            if (!chartResponse || !chartResponse.series || chartResponse.series.length === 0) {
                if (challengesByCategoryChartInstance) {
                    challengesByCategoryChartInstance.destroy();
                    challengesByCategoryChartInstance = null;
                }
                chartElement.style.display = 'none';

                let messageDiv = document.createElement('div');
                messageDiv.id = 'challengesByCategoryMessage';
                messageDiv.style.textAlign = 'center';
                messageDiv.style.color = 'var(--text-muted)';
                messageDiv.style.paddingTop = '100px';
                messageDiv.textContent = 'No solved challenges data available.';
                chartContainer.appendChild(messageDiv);

                return;
            }

            chartElement.style.display = 'block';

            const filteredLabels = [];
            const filteredSeries = [];
            chartResponse.labels.forEach((label, index) => {
                if (label !== 'Unknown Category') {
                    filteredLabels.push(label);
                    filteredSeries.push(chartResponse.series[index]);
                }
            });

            if (filteredSeries.length === 0) {
                if (challengesByCategoryChartInstance) {
                    challengesByCategoryChartInstance.destroy();
                    challengesByCategoryChartInstance = null;
                }
                chartElement.style.display = 'none';

                let messageDiv = document.createElement('div');
                messageDiv.id = 'challengesByCategoryMessage';
                messageDiv.style.textAlign = 'center';
                messageDiv.style.color = 'var(--text-muted)';
                messageDiv.style.paddingTop = '100px';
                messageDiv.textContent = 'No solved challenges data available for known categories.';
                chartContainer.appendChild(messageDiv);
                return;
            }

            const data = {
                labels: filteredLabels,
                datasets: [{
                    data: filteredSeries,
                    backgroundColor: [
                        'rgba(111, 207, 151, 0.8)',
                        'rgba(64, 224, 208, 0.8)',
                        'rgba(255, 107, 107, 0.8)',
                        'rgba(255, 159, 67, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim() || '#0b0d0e',
                    borderWidth: 2
                }]
            };

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)',
                            font: { family: "'Inter', sans-serif", size: 10 },
                            boxWidth: 10,
                            padding: 6,
                        }
                    },
                    tooltip: {
                        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim() || '#0b0d0e',
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97',
                        borderWidth: 1,
                        titleColor: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() || '#f0f5f3',
                        bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || 'rgba(240,245,243,0.70)',
                        titleFont: { family: "'Inter', sans-serif", weight: '600' },
                        bodyFont: { family: "'Inter', sans-serif" },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            };

            if (challengesByCategoryChartInstance) {
                challengesByCategoryChartInstance.data = data;
                challengesByCategoryChartInstance.options = options;
                challengesByCategoryChartInstance.update();
            } else {
                challengesByCategoryChartInstance = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: data,
                    options: options
                });
            }
        }

        // --- 3. Render Overall Progress Radial Bar ---
        async function renderOverallProgressChart(solvedCount) {
            const totalChallenges = await fetchData('getTotalChallenges');
            const chartElement = document.getElementById('overallProgressChart');

            if (totalChallenges === null || totalChallenges === 0) {
                chartElement.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding-top: 50px;">N/A</div>';
                if (overallProgressChart) overallProgressChart.destroy();
                overallProgressChart = null;
                return;
            }

            const progressPercent = Math.round((solvedCount / totalChallenges) * 100);
            const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97';

            const options = {
                series: [progressPercent],
                chart: {
                    height: 160,
                    type: 'radialBar',
                    sparkline: { enabled: true },
                    toolbar: { show: false }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        track: {
                            background: 'rgba(111, 207, 151, 0.1)',
                            strokeWidth: '97%',
                            margin: 5,
                        },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                offsetY: -5,
                                fontSize: '20px',
                                fontFamily: 'Barlow Condensed',
                                color: accent,
                                formatter: function (val) { return val + '%'; }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#00ffff'],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    },
                },
                stroke: { lineCap: 'round' },
                labels: ['Progress'],
            };

            if (overallProgressChart) {
                overallProgressChart.updateSeries([progressPercent]);
            } else {
                overallProgressChart = new ApexCharts(chartElement, options);
                overallProgressChart.render();
            }
        }

        // --- 4. Render Skill Radar Chart (new) ---
        async function renderSkillRadarChart() {
            const radarCtx = document.getElementById('skillRadarChart').getContext('2d');
            // Mock data - in production, fetch from API
            const data = {
                labels: ['Web', 'Pwn', 'Crypto', 'Rev', 'Forensic', 'Misc'],
                datasets: [{
                    label: 'Your Skills',
                    data: [85, 65, 70, 45, 50, 75],
                    backgroundColor: 'rgba(111, 207, 151, 0.2)',
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97',
                    borderWidth: 2,
                    pointBackgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97',
                }]
            };
            const options = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: getComputedStyle(document.documentElement).getPropertyValue('--chart-grid').trim() || 'rgba(255,255,255,0.06)' },
                        grid: { color: getComputedStyle(document.documentElement).getPropertyValue('--chart-grid').trim() || 'rgba(255,255,255,0.06)' },
                        pointLabels: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)',
                            font: { family: "'Inter', sans-serif", size: 10 }
                        },
                        ticks: {
                            backdropColor: 'transparent',
                            color: getComputedStyle(document.documentElement).getPropertyValue('--chart-text').trim() || 'rgba(240,245,243,0.50)',
                            maxTicksLimit: 3,
                            stepSize: 20,
                            font: { size: 8 }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary').trim() || '#0b0d0e',
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#6fcf97',
                        borderWidth: 1,
                        titleColor: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() || '#f0f5f3',
                        bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || 'rgba(240,245,243,0.70)',
                        titleFont: { family: "'Inter', sans-serif", weight: '600' },
                        bodyFont: { family: "'Inter', sans-serif" },
                        callbacks: {
                            label: function(context) {
                                return context.raw + '%';
                            }
                        }
                    }
                }
            };

            if (skillRadarChartInstance) {
                skillRadarChartInstance.destroy();
            }
            skillRadarChartInstance = new Chart(radarCtx, {
                type: 'radar',
                data: data,
                options: options
            });
        }

        // --- 5. Populate Latest Achievements ---
        async function populateAchievements() {
            const achievements = await fetchData('getLatestAchievements');
            const achievementsGrid = document.getElementById('achievements-grid');
            achievementsGrid.innerHTML = '';

            if (!achievements || achievements.length === 0) {
                achievementsGrid.innerHTML = '<div style="text-align: center; color: var(--text-muted);">No achievements earned yet.</div>';
                return;
            }

            achievements.forEach(ach => {
                const card = document.createElement('div');
                card.classList.add('achievement-card');
                card.innerHTML = `
                    <div class="achievement-icon"><i class="fa-solid ${ach.icon}"></i></div>
                    <div class="achievement-title">${ach.name}</div>
                    <div class="achievement-desc">${ach.description}</div>
                    <div class="achievement-progress">
                        <div class="achievement-progress-bar" style="width: ${ach.progress_percent}%"></div>
                    </div>
                `;
                achievementsGrid.appendChild(card);
            });
        }

        // --- 6. Populate Recent Activity ---
        async function populateRecentActivity() {
            const recentActivity = await fetchData('getRecentActivity');
            const activityList = document.getElementById('activity-list');
            activityList.innerHTML = '';

            if (!recentActivity || recentActivity.length === 0) {
                activityList.innerHTML = '<div style="text-align: center; color: var(--text-muted);">No recent activity.</div>';
                return;
            }

            recentActivity.forEach(activity => {
                let iconClass = '';
                if (activity.activity_type === 'solved') iconClass = 'fa-check';
                else if (activity.activity_type === 'rank_update') iconClass = 'fa-chart-line';
                else if (activity.activity_type === 'achievement_unlocked') iconClass = 'fa-award';
                else if (activity.activity_type === 'flag') iconClass = 'fa-flag';
                else iconClass = 'fa-question';

                const item = document.createElement('div');
                item.classList.add('activity-item');
                item.innerHTML = `
                    <div class="activity-icon"><i class="fa-solid ${iconClass}"></i></div>
                    <div class="activity-details">
                        <div class="activity-title">${activity.description}</div>
                        <div class="activity-time">${new Date(activity.timestamp).toLocaleString()}</div>
                    </div>
                    ${activity.points_change ? `<div class="activity-points">+${activity.points_change} pts</div>` : ''}
                `;
                activityList.appendChild(item);
            });
        }

        // --- 7. Populate Leaderboard Preview ---
        async function populateLeaderboardPreview(currentUser) {
            const leaderboardData = await fetchData('getLeaderboard');
            const leaderboardList = document.getElementById('leaderboardList');
            leaderboardList.innerHTML = '';

            if (!leaderboardData || leaderboardData.length === 0) {
                leaderboardList.innerHTML = '<div style="text-align: center; color: var(--text-muted);">Leaderboard is empty.</div>';
                return;
            }

            const userIndex = leaderboardData.findIndex(player => player.username === currentUser);
            let startIndex, endIndex;

            if (userIndex === -1) {
                startIndex = 0;
                endIndex = Math.min(5, leaderboardData.length);
            } else {
                startIndex = Math.max(0, userIndex - 2);
                endIndex = Math.min(leaderboardData.length, userIndex + 3);
            }

            const previewData = leaderboardData.slice(startIndex, endIndex);

            previewData.forEach(player => {
                const item = document.createElement('div');
                item.classList.add('leaderboard-item');
                if (player.username === currentUser) {
                    item.classList.add('focused');
                } else {
                    item.classList.add('dimmed');
                }

                let rankClass = '';
                if (player.rank === 1) rankClass = 'gold';
                else if (player.rank === 2) rankClass = 'silver';
                else if (player.rank === 3) rankClass = 'bronze';

                item.innerHTML = `
                    <span class="rank ${rankClass}">#${player.rank}</span>
                    <span class="username">${player.username}</span>
                    <span class="score">${player.score}</span>
                `;
                leaderboardList.appendChild(item);
            });
        }

        // --- 8. Render Streak Calendar ---
        function renderStreakCalendar(lastSolvedDateStr, dailyStreak) {
            const streakCalendar = document.getElementById('streak-calendar');
            streakCalendar.innerHTML = '';

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            let lastSolvedDate = null;
            if (lastSolvedDateStr) {
                lastSolvedDate = new Date(lastSolvedDateStr);
                lastSolvedDate.setHours(0, 0, 0, 0);
            }

            for (let i = 6; i >= 0; i--) {
                const day = new Date(today);
                day.setDate(today.getDate() - i);
                
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('streak-day');
                dayDiv.textContent = day.getDate();

                if (lastSolvedDate && dailyStreak > 0) {
                    const streakStart = new Date(lastSolvedDate);
                    streakStart.setDate(lastSolvedDate.getDate() - (dailyStreak - 1));
                    if (day >= streakStart && day <= lastSolvedDate && day <= today) {
                        dayDiv.classList.add('active');
                    }
                }

                if (day.toDateString() === today.toDateString()) {
                    dayDiv.classList.add('today');
                }

                streakCalendar.appendChild(dayDiv);
            }
        }

        // --- 9. Update Status Overview ---
        function updateStatusOverview(solved, total) {
            const unsolved = total - solved;
            document.getElementById('statusSolved').textContent = solved;
            document.getElementById('statusUnsolved').textContent = unsolved;
            document.getElementById('statusTotal').textContent = total;
            document.getElementById('statusInProgress').textContent = '0'; // placeholder
        }

        // --- 10. Update Points Breakdown (mock) ---
        function updatePointsBreakdown() {
            // In a real implementation, fetch from API
            const categories = ['Web', 'Pwn', 'Crypto', 'Rev', 'Forensic', 'Misc'];
            const points = [750, 450, 600, 300, 200, 400];
            const max = Math.max(...points);
            const container = document.getElementById('pointsBreakdown');
            container.innerHTML = '';
            categories.forEach((cat, i) => {
                const pct = max > 0 ? (points[i] / max) * 100 : 0;
                const item = document.createElement('div');
                item.className = 'points-breakdown-item';
                item.innerHTML = `
                    <span class="pb-category">${cat}</span>
                    <span class="pb-bar"><span class="pb-bar-fill" style="width:${pct}%;"></span></span>
                    <span class="pb-value">${points[i]}</span>
                `;
                container.appendChild(item);
            });
        }

        // --- Main populate function ---
        async function populateDashboard() {
            const userStats = await fetchData('getUserStats');

            if (userStats) {
                document.getElementById('username').textContent = userStats.username;
                const headerUserAvatar = document.getElementById('headerUserAvatar');
                if (headerUserAvatar) {
                    headerUserAvatar.src = userStats.avatar_url || 'https://i.pravatar.cc/100?img=11';
                }

                document.getElementById('welcome-title').textContent = `Welcome back, ${userStats.username}!`;
                document.getElementById('welcome-subtitle').textContent = `You're performing well! Keep up the great work.`;

                document.getElementById('stat-challenges').textContent = userStats.challenges_solved;
                document.getElementById('stat-points').textContent = userStats.total_score.toLocaleString();
                document.getElementById('stat-streak').textContent = userStats.daily_streak;
                document.getElementById('stat-rank').textContent = `#${userStats.current_rank}`;
                document.getElementById('streak-count').textContent = userStats.daily_streak;

                document.getElementById('score-total').textContent = userStats.total_score.toLocaleString();
                document.getElementById('score-solved').textContent = userStats.challenges_solved;
                document.getElementById('score-rank').textContent = `#${userStats.current_rank}`;
                document.getElementById('score-time').textContent = `${userStats.time_spent_hours}h`;

                renderStreakCalendar(userStats.last_solved_date, userStats.daily_streak);
                updateStatusOverview(userStats.challenges_solved, userStats.total_challenges || 20);
                updatePointsBreakdown();

                await Promise.all([
                    renderScoreProgressionChart('all'),
                    renderChallengesByCategoryChart(),
                    renderOverallProgressChart(userStats.challenges_solved),
                    renderSkillRadarChart(),
                    populateAchievements(),
                    populateRecentActivity(),
                    populateLeaderboardPreview(userStats.username)
                ]);

                document.querySelectorAll('.achievement-progress-bar').forEach(bar => {
                    const targetWidth = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 500);
                });

            } else {
                const errorMessage = "Failed to load dashboard data. Please try again later or contact support.";
                document.getElementById('username').textContent = 'Error';
                document.getElementById('welcome-title').textContent = "Error Loading Dashboard";
                document.getElementById('welcome-subtitle').textContent = errorMessage;
                
                document.getElementById('stat-challenges').textContent = '--';
                document.getElementById('stat-points').textContent = '--';
                document.getElementById('stat-streak').textContent = '--';
                document.getElementById('stat-rank').textContent = '--';
                document.getElementById('streak-count').textContent = '--';

                document.getElementById('score-total').textContent = '--';
                document.getElementById('score-solved').textContent = '--';
                document.getElementById('score-rank').textContent = '--';
                document.getElementById('score-time').textContent = '--';

                document.getElementById('streak-calendar').innerHTML = '<div style="text-align: center; width: 100%; color: var(--text-muted);">No streak data.</div>';
                document.getElementById('achievements-grid').innerHTML = '<div style="text-align: center; color: var(--text-muted);">Failed to load achievements.</div>';
                document.getElementById('activity-list').innerHTML = '<div style="text-align: center; color: var(--text-muted);">Failed to load activity.</div>';
                document.getElementById('leaderboardList').innerHTML = '<div style="text-align: center; color: var(--text-muted);">Failed to load leaderboard.</div>';

                if (scoreProgressionChartInstance) scoreProgressionChartInstance.destroy();
                if (challengesByCategoryChartInstance) challengesByCategoryChartInstance.destroy();
                if (overallProgressChart) overallProgressChart.destroy();
                if (skillRadarChartInstance) skillRadarChartInstance.destroy();
            }
        }

        // --- Event listeners for chart period buttons ---
        document.querySelectorAll('.period-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.period-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const period = this.dataset.period;
                renderScoreProgressionChart(period);
            });
        });

        // --- Initialize ---
        document.addEventListener('DOMContentLoaded', populateDashboard);
    </script>
</body>
</html>