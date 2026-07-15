<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenslin's Adventure Leaderboard | Sudo Society</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&family=Barlow+Condensed:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

            --rank-gold: #f1c40f;
            --rank-silver: #bdc3c7;
            --rank-bronze: #e67e22;

            --table-header-bg: rgba(111, 207, 151, 0.06);
            --table-row-hover-bg: rgba(111, 207, 151, 0.05);
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

            --rank-gold: #b7950b;
            --rank-silver: #7f8c8d;
            --rank-bronze: #a04000;

            --table-header-bg: rgba(192, 57, 43, 0.05);
            --table-row-hover-bg: rgba(192, 57, 43, 0.04);
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

        .logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--logo-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 0 30px rgba(111, 207, 151, 0.15);
            transition: background 0.5s ease, box-shadow 0.4s ease;
            font-family: var(--font-display);
        }

        body.light .logo-icon {
            color: #fff;
            box-shadow: 0 0 30px rgba(192, 57, 43, 0.12);
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
           MAIN CONTENT
           ============================================================ */
        main {
            flex: 1;
            padding: 32px 0 40px;
        }

        .leaderboard-section {
            padding: 28px 30px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .leaderboard-section::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, var(--accent-dim), transparent 60%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            transition: background 0.5s ease;
        }

        .leaderboard-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 700;
            background: var(--section-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: background 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .leaderboard-title i {
            color: var(--accent);
            font-size: 1.8rem;
            transition: color 0.4s ease;
        }

        /* ===== PODIUM ===== */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .podium-card {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px 16px;
            width: 200px;
            text-align: center;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, background 0.4s ease;
            backdrop-filter: blur(4px);
        }

        .podium-card:hover {
            transform: translateY(-4px);
            border-color: var(--card-hover-border);
            box-shadow: var(--card-hover-shadow);
        }

        .podium-card.second {
            height: 180px;
            margin-bottom: 10px;
        }
        .podium-card.first {
            height: 220px;
            border-color: var(--rank-gold);
            box-shadow: 0 0 30px rgba(241, 196, 15, 0.15);
        }
        .podium-card.third {
            height: 160px;
            margin-bottom: 20px;
        }

        .podium-card .medal-icon {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 8px;
        }
        .podium-card.first .medal-icon { color: var(--rank-gold); }
        .podium-card.second .medal-icon { color: var(--rank-silver); }
        .podium-card.third .medal-icon { color: var(--rank-bronze); }

        .podium-card .podium-username {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
            transition: color 0.4s ease;
        }
        .podium-card.first .podium-username { color: var(--rank-gold); }
        .podium-card.second .podium-username { color: var(--rank-silver); }
        .podium-card.third .podium-username { color: var(--rank-bronze); }

        .podium-card .podium-points {
            font-weight: 600;
            color: var(--accent);
            font-size: 1.1rem;
            transition: color 0.4s ease;
        }

        .podium-card .podium-solves {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        /* ===== LOADING SPINNER ===== */
        .loading-spinner {
            border: 3px solid var(--bg-secondary);
            border-top: 3px solid var(--accent);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 0.8s linear infinite;
            margin: 20px auto;
            display: none;
        }

        .loading-spinner.show {
            display: block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ===== LEADERBOARD TABLE ===== */
        .leaderboard-table-container {
            overflow-x: auto;
            margin-top: 12px;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
            font-size: 0.9rem;
        }

        .leaderboard-table th,
        .leaderboard-table td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid var(--border-subtle);
            transition: border-color 0.4s ease, color 0.4s ease;
        }

        .leaderboard-table th {
            background: var(--table-header-bg);
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            border-bottom: 2px solid var(--glass-border);
        }

        .leaderboard-table tbody tr {
            transition: background 0.2s ease;
        }

        .leaderboard-table tbody tr:hover {
            background: var(--table-row-hover-bg);
        }

        .leaderboard-table .rank-col {
            font-weight: 700;
            color: var(--accent);
            font-feature-settings: "tnum";
        }

        .leaderboard-table .rank-col.gold { color: var(--rank-gold); }
        .leaderboard-table .rank-col.silver { color: var(--rank-silver); }
        .leaderboard-table .rank-col.bronze { color: var(--rank-bronze); }

        .leaderboard-table .username-col {
            font-weight: 600;
            color: var(--text-primary);
            transition: color 0.4s ease;
        }

        .leaderboard-table .points-col {
            font-weight: 600;
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .leaderboard-table .solves-col {
            color: var(--text-secondary);
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

            .leaderboard-title {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 8px;
            }

            .podium-container {
                flex-direction: column;
                align-items: center;
            }
            .podium-card {
                width: 90%;
                max-width: 280px;
                height: auto !important;
                margin-bottom: 12px !important;
            }

            .leaderboard-table-container {
                overflow-x: scroll;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }
            .leaderboard-section {
                padding: 20px 16px;
            }
            .leaderboard-title {
                font-size: 1.4rem;
            }
            .leaderboard-table th,
            .leaderboard-table td {
                padding: 10px 12px;
                font-size: 0.8rem;
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

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <!-- ===== AMBIENT ORBS ===== -->
    <div class="ambient" aria-hidden="true">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>

    <!-- ===== HEADER ===== -->
    <header>
        <div class="container header-inner">
            <div class="logo" onclick="window.location.href='index.html'">
                <span class="logo-icon">§</span>
                <span class="logo-text">Sudo Society</span>
            </div>
            <div class="user-menu">
                <nav class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="leaderboard.php" class="active">Leaderboard</a>
                    <a href="jenslin_little_advangure.php">Jenslin's Adventure</a>
                </nav>
                <!-- Theme Toggle -->
                <div class="theme-toggle" id="themeToggle" role="button" tabindex="0" aria-label="Toggle theme">
                    <span class="toggle-icon" id="toggleIcon">🌙</span>
                    <div class="toggle-track">
                        <div class="toggle-dot"></div>
                    </div>
                    <span class="toggle-label" id="toggleLabel">Dark</span>
                </div>
                <div class="user-profile" id="headerUserProfile">
                    <img src="<?php echo isset($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : 'https://i.pravatar.cc/100?img=11'; ?>" alt="User Avatar" class="user-avatar" id="headerUserAvatar">
                    <span class="username" id="headerUsername">Guest</span>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main>
        <div class="container">
            <section class="leaderboard-section fade-up">
                <h1 class="leaderboard-title">
                    <i class="fas fa-trophy"></i>
                    Jenslin's Adventure Leaderboard
                    <i class="fas fa-trophy"></i>
                </h1>

                <div class="podium-container" id="podiumContainer">
                    <p style="text-align: center; width: 100%; color: var(--text-muted);">Loading top players...</p>
                </div>

                <div class="loading-spinner" id="loadingSpinner"></div>

                <div class="leaderboard-table-container" id="leaderboardTableContainer">
                    <p style="text-align: center; width: 100%; color: var(--text-muted);">Loading leaderboard table...</p>
                </div>
            </section>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society CTF. All rights reserved. Developed by JENSLIN</p>
        </div>
    </footer>

    <!-- ============================================================
    SCRIPT (ALL FUNCTIONALITY PRESERVED)
    ============================================================ -->
    <script>
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

        // --- Backend API URL ---
        const BACKEND_API_URL = 'https://sudosocietyctf.unaux.com/api/leaderboard.php';

        // --- DOM refs ---
        const headerUsername = document.getElementById('headerUsername');
        const headerUserAvatar = document.getElementById('headerUserAvatar');
        const podiumContainer = document.getElementById('podiumContainer');
        const leaderboardTableContainer = document.getElementById('leaderboardTableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');

        let currentUserId = null;

        // --- Helper: toggle dropdown (placeholder) ---
        function toggleDropdown() {
            console.log("User profile clicked! (Dropdown functionality not implemented)");
        }
        document.getElementById('headerUserProfile').addEventListener('click', toggleDropdown);

        // --- Check login status ---
        async function checkLoginStatus() {
            try {
                const response = await fetch(`${BACKEND_API_URL}?action=checkLoginStatus`);
                const data = await response.json();

                if (data.success && data.loggedIn) {
                    headerUsername.textContent = data.user.username;
                    headerUserAvatar.src = data.user.avatar_url || 'https://i.pravatar.cc/100?img=11';
                    currentUserId = data.user.id;
                    console.log('User logged in:', data.user.username);
                } else {
                    headerUsername.textContent = 'Guest';
                    headerUserAvatar.src = 'https://i.pravatar.cc/100?img=11';
                    currentUserId = null;
                    console.log('Not logged in:', data.message);
                }
            } catch (error) {
                console.error('Error checking login status:', error);
                headerUsername.textContent = 'Guest (Error)';
                headerUserAvatar.src = 'https://i.pravatar.cc/100?img=11';
            }
        }

        // --- Fetch leaderboard data ---
        async function fetchLeaderboard() {
            loadingSpinner.classList.add('show');
            podiumContainer.innerHTML = '<p style="text-align: center; width: 100%; color: var(--text-muted);">Loading top players...</p>';
            leaderboardTableContainer.innerHTML = '<p style="text-align: center; width: 100%; color: var(--text-muted);">Loading leaderboard table...</p>';

            try {
                const response = await fetch(`${BACKEND_API_URL}?action=getLeaderboard`);
                const data = await response.json();
                console.log('Leaderboard data received:', data);

                if (data.success && data.data) {
                    renderLeaderboard(data.data);
                } else {
                    const errorMessage = data.error || 'Unknown error fetching leaderboard.';
                    podiumContainer.innerHTML = `<p style="color: var(--error-color); text-align: center; width: 100%;">Failed to load top players: ${errorMessage}</p>`;
                    leaderboardTableContainer.innerHTML = `<p style="color: var(--error-color); text-align: center; width: 100%;">Failed to load leaderboard: ${errorMessage}</p>`;
                    console.error('Failed to fetch leaderboard:', errorMessage);
                }
            } catch (error) {
                const errorMessage = error.message || 'Network error.';
                podiumContainer.innerHTML = `<p style="color: var(--error-color); text-align: center; width: 100%;">Error connecting to backend: ${errorMessage}</p>`;
                leaderboardTableContainer.innerHTML = `<p style="color: var(--error-color); text-align: center; width: 100%;">Error connecting to backend: ${errorMessage}</p>`;
                console.error('Network error fetching leaderboard:', error);
            } finally {
                loadingSpinner.classList.remove('show');
            }
        }

        // --- Render leaderboard ---
        function renderLeaderboard(data) {
            podiumContainer.innerHTML = '';
            leaderboardTableContainer.innerHTML = '';

            if (data.length === 0) {
                podiumContainer.innerHTML = '<p style="text-align: center; width: 100%; color: var(--text-muted);">No users on the leaderboard yet.</p>';
                leaderboardTableContainer.innerHTML = '<p style="text-align: center; width: 100%; color: var(--text-muted);">No users on the leaderboard yet.</p>';
                return;
            }

            // Top 3 podium
            const top3 = data.slice(0, 3);
            const podiumOrder = [
                { player: top3[1], className: 'second', icon: '<i class="fas fa-medal medal-icon"></i>' },
                { player: top3[0], className: 'first', icon: '<i class="fas fa-crown medal-icon"></i>' },
                { player: top3[2], className: 'third', icon: '<i class="fas fa-award medal-icon"></i>' }
            ];

            podiumOrder.forEach(item => {
                if (item.player) {
                    const card = document.createElement('div');
                    card.classList.add('podium-card', item.className);
                    card.innerHTML = `
                        ${item.icon}
                        <div class="podium-username">${item.player.username}</div>
                        <div class="podium-points">${item.player.total_score} Points</div>
                        <div class="podium-solves">${item.player.challenges_solved} Solves</div>
                    `;
                    podiumContainer.appendChild(card);
                }
            });

            // Full table
            const table = document.createElement('table');
            table.classList.add('leaderboard-table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Score</th>
                        <th>Solves</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;
            leaderboardTableContainer.appendChild(table);
            const tbody = table.querySelector('tbody');

            data.forEach((player, index) => {
                const row = document.createElement('tr');
                let rankClass = '';
                if (index === 0) rankClass = 'gold';
                else if (index === 1) rankClass = 'silver';
                else if (index === 2) rankClass = 'bronze';

                row.innerHTML = `
                    <td class="rank-col ${rankClass}">#${index + 1}</td>
                    <td class="username-col">${player.username}</td>
                    <td class="points-col">${player.total_score}</td>
                    <td class="solves-col">${player.challenges_solved}</td>
                `;
                tbody.appendChild(row);
            });
        }

        // --- Initial load ---
        document.addEventListener('DOMContentLoaded', async () => {
            // Set active nav link (already active in HTML)
            await checkLoginStatus();
            fetchLeaderboard();
        });
    </script>

</body>
</html>
