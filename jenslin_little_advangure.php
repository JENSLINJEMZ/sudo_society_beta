<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenslin's Little Adventure | Sudo Society</title>
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

            --completed-color: #2ecc71;
            --pending-color: #f39c12;

            --modal-bg: rgba(11, 13, 14, 0.92);
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

            --completed-color: #27ae60;
            --pending-color: #f39c12;

            --modal-bg: rgba(244, 247, 246, 0.92);
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

        .action-button {
            padding: 8px 18px;
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            background: var(--glass-bg);
            color: var(--text-secondary);
            font-family: var(--font-primary);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .action-button:hover {
            background: var(--accent-dim);
            color: var(--accent);
            border-color: var(--accent);
        }

        .action-button.primary {
            background: var(--accent-gradient);
            color: var(--bg-primary);
            border: none;
            font-weight: 600;
        }

        .action-button.primary:hover {
            opacity: 0.85;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(111, 207, 151, 0.25);
        }

        body.light .action-button.primary:hover {
            box-shadow: 0 4px 20px rgba(192, 57, 43, 0.20);
        }

        /* ============================================================
           MAIN CONTENT
           ============================================================ */
        main {
            flex: 1;
            padding: 32px 0 40px;
        }

        .main-grid {
            display: grid;
            gap: 28px;
        }

        /* ===== ADVENT OVERVIEW ===== */
        .advent-overview {
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

        .advent-overview::before {
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

        .advent-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .advent-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--accent-dim);
            border: 2px solid var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: var(--accent);
            transition: background 0.4s ease, border-color 0.4s ease, color 0.4s ease;
        }

        .advent-title-group {
            text-align: left;
        }

        .advent-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 700;
            background: var(--section-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: background 0.5s ease;
        }

        .advent-meta {
            font-size: 0.95rem;
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .advent-description {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 800px;
            margin: 0 auto 20px;
            transition: color 0.4s ease;
        }

        .advent-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .progress-bar-container {
            width: 100%;
            max-width: 600px;
            height: 8px;
            background: var(--bg-secondary);
            border-radius: 10px;
            overflow: hidden;
            margin: 16px auto 8px;
            border: 1px solid var(--glass-border);
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: var(--accent-gradient);
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .progress-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        /* ===== TASK LIST ===== */
        .task-list-section {
            padding: 24px 28px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            transition: background 0.5s ease, border-color 0.4s ease, box-shadow 0.4s ease;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-subtle);
            transition: color 0.4s ease, border-color 0.4s ease;
        }

        .section-title i {
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .task-item {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            margin-bottom: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }

        .task-item:hover {
            border-color: var(--card-hover-border);
            box-shadow: var(--card-hover-shadow);
        }

        .task-item.solved {
            border-color: var(--completed-color);
            background: rgba(46, 204, 113, 0.05);
        }

        .task-item.solved .task-main-title {
            color: var(--completed-color);
            text-decoration: line-through;
            opacity: 0.7;
        }

        .task-item.solved .task-type {
            background: rgba(46, 204, 113, 0.10);
            color: var(--completed-color);
        }

        .task-header {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            gap: 14px;
            flex-wrap: wrap;
        }

        .task-status-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .task-status-icon.completed {
            color: var(--completed-color);
        }

        .task-status-icon.pending {
            color: var(--pending-color);
        }

        .task-title-group {
            flex: 1;
            min-width: 150px;
        }

        .task-day {
            font-size: 0.75rem;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .task-main-title {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            transition: color 0.4s ease;
        }

        .task-type {
            font-size: 0.75rem;
            font-weight: 500;
            background: var(--accent-dim);
            color: var(--accent);
            padding: 4px 12px;
            border-radius: 20px;
            white-space: nowrap;
            transition: background 0.4s ease, color 0.4s ease;
        }

        .task-arrow-icon {
            color: var(--text-muted);
            font-size: 1rem;
            transition: transform 0.3s ease, color 0.4s ease;
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
           MODAL
           ============================================================ */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--modal-bg);
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

        .custom-modal-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .custom-modal-card {
            background: var(--bg-primary);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px 36px;
            max-width: 900px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--glass-shadow);
            transform: translateY(-20px) scale(0.95);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease, background 0.5s ease, border-color 0.4s ease;
            position: relative;
        }

        .custom-modal-overlay.show .custom-modal-card {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .modal-close-btn {
            position: absolute;
            top: 16px;
            right: 20px;
            background: none;
            border: none;
            font-size: 1.8rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 4px 8px;
        }

        .modal-close-btn:hover {
            color: var(--accent);
        }

        .custom-modal-header {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-subtle);
            transition: color 0.4s ease, border-color 0.4s ease;
        }

        .custom-modal-content {
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .custom-modal-content h4 {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-top: 20px;
            margin-bottom: 8px;
            transition: color 0.4s ease;
        }

        .custom-modal-content p {
            margin-bottom: 8px;
        }

        .custom-modal-content ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 12px;
        }

        .custom-modal-content ul li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 4px;
        }

        .custom-modal-content ul li::before {
            content: '›';
            color: var(--accent);
            position: absolute;
            left: 0;
            top: 0;
            font-weight: 700;
        }

        .modal-image {
            max-width: 100%;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            margin: 10px 0;
        }

        .flag-submission-section {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .flag-submission-section h4 {
            margin-top: 0;
        }

        .flag-submission-section input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: var(--font-primary);
            font-size: 0.95rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
        }

        .flag-submission-section input[type="text"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-dim);
        }

        .flag-submission-section input[type="text"]::placeholder {
            color: var(--text-muted);
        }

        .flag-submission-section button {
            padding: 12px;
            border: none;
            border-radius: 60px;
            background: var(--accent-gradient);
            color: var(--bg-primary);
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .flag-submission-section button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(111, 207, 151, 0.25);
        }

        .flag-submission-section button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        body.light .flag-submission-section button:hover:not(:disabled) {
            box-shadow: 0 4px 20px rgba(192, 57, 43, 0.20);
        }

        #flagMessage {
            font-weight: 600;
            text-align: center;
            margin-top: 4px;
        }

        .custom-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
            flex-wrap: wrap;
        }

        .custom-modal-buttons .action-button {
            font-size: 0.9rem;
            padding: 10px 24px;
        }

        /* ============================================================
           AUTH MODAL
           ============================================================ */
        #authModalCard {
            max-width: 480px;
        }

        #authModalCard h3 {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 16px;
            transition: color 0.4s ease;
        }

        #authModalCard form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        #authModalCard input {
            padding: 12px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: var(--font-primary);
            font-size: 0.95rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
        }

        #authModalCard input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-dim);
        }

        #authModalCard input::placeholder {
            color: var(--text-muted);
        }

        #authModalCard button[type="submit"] {
            padding: 12px;
            border: none;
            border-radius: 60px;
            background: var(--accent-gradient);
            color: var(--bg-primary);
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        #authModalCard button[type="submit"]:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(111, 207, 151, 0.25);
        }

        body.light #authModalCard button[type="submit"]:hover:not(:disabled) {
            box-shadow: 0 4px 20px rgba(192, 57, 43, 0.20);
        }

        #authModalCard button[type="submit"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .auth-switch-text {
            text-align: center;
            margin-top: 14px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .auth-switch-text a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .auth-switch-text a:hover {
            opacity: 0.8;
        }

        #authMessage {
            text-align: center;
            font-weight: 600;
            margin-top: 10px;
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

            .advent-header {
                flex-direction: column;
                text-align: center;
            }
            .advent-title-group {
                text-align: center;
            }
            .advent-title {
                font-size: 1.8rem;
            }

            .task-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
            .task-arrow-icon {
                position: absolute;
                top: 14px;
                right: 18px;
            }
            .task-item {
                position: relative;
            }

            .custom-modal-card {
                padding: 24px 20px;
            }
            .custom-modal-header {
                font-size: 1.5rem;
            }
            #authModalCard {
                padding: 24px 20px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }
            .advent-overview {
                padding: 20px 16px;
            }
            .task-list-section {
                padding: 20px 16px;
            }
            .advent-icon {
                width: 64px;
                height: 64px;
                font-size: 2rem;
            }
            .advent-title {
                font-size: 1.5rem;
            }
            .section-title {
                font-size: 1.2rem;
            }
            .task-main-title {
                font-size: 1rem;
            }
            .custom-modal-card {
                padding: 16px;
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
                    <a href="#" class="active">Advent Calendar</a>
                    <a href="leaderboard.php">Leaderboard</a>
                    <a href="dashboard.php">Dashboard</a>
                </nav>
                <!-- Theme Toggle -->
                <div class="theme-toggle" id="themeToggle" role="button" tabindex="0" aria-label="Toggle theme">
                    <span class="toggle-icon" id="toggleIcon">🌙</span>
                    <div class="toggle-track">
                        <div class="toggle-dot"></div>
                    </div>
                    <span class="toggle-label" id="toggleLabel">Dark</span>
                </div>
                    <div class="user-profile" id="userProfileButton" onclick="window.location.href='settings.html'" style="cursor: pointer;">
                        <img id="headerUserAvatar" src="<?php echo isset($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : 'https://i.pravatar.cc/100?img=11'; ?>" alt="User Avatar" class="user-avatar">
                        <span id="headerUsername" class="username">Guest</span>
                    </div>
                <button id="authButton" class="action-button">Login / Register</button>
                <button id="logoutButton" class="action-button" style="display: none;">Logout</button>
            </div>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main>
        <div class="container main-grid">
            <!-- Advent Overview -->
            <section class="advent-overview fade-up">
                <div class="advent-header">
                    <div class="advent-icon"><i class="fa-solid fa-robot"></i></div>
                    <div class="advent-title-group">
                        <h1 class="advent-title">Jenslin's Little Adventure</h1>
                        <p class="advent-meta">A Sudo Society CTF Event</p>
                    </div>
                </div>
                <p class="advent-description">
                    Welcome to Jenslin's Little Adventure! This Capture The Flag (CTF) event challenges your skills across various cybersecurity domains. Each "task" is a challenge with a flag to find. Submit the correct flag to earn points and progress! Good luck, hacker.
                </p>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="completionBar"></div>
                </div>
                <p class="progress-label">Completion: <span id="completionPercentage">0</span>%</p>
                <div class="advent-actions">
                    <!-- optional action buttons -->
                </div>
            </section>

            <!-- Task List -->
            <section class="task-list-section fade-up">
                <h2 class="section-title"><i class="fas fa-list-check"></i> Challenges</h2>
                <div id="taskList" class="task-list">
                    <p style="color: var(--text-muted);">Loading challenges...</p>
                </div>
            </section>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society. All rights reserved. Developed by JENSLIN</p>
        </div>
    </footer>

    <!-- ===== TASK MODAL ===== -->
    <div class="custom-modal-overlay" id="taskModalOverlay">
        <div class="custom-modal-card">
            <button class="modal-close-btn" id="modalCloseBtn">&times;</button>
            <h3 class="custom-modal-header" id="modalTaskTitle">Task Title</h3>
            <div class="custom-modal-content">
                <p><strong>Category:</strong> <span id="modalTaskCategory"></span></p>
                <p><strong>Points:</strong> <span id="modalTaskPoints"></span></p>
                <p><strong>Solves:</strong> <span id="modalTaskSolves"></span></p>

                <h4><i class="fas fa-book-open"></i> Story</h4>
                <p id="modalTaskStory"></p>

                <h4><i class="fas fa-lightbulb"></i> Learning Objectives</h4>
                <ul id="modalLearningObjectives"></ul>

                <h4><i class="fas fa-info-circle"></i> Learning Details</h4>
                <div id="modalLearningDetails"></div>

                <div id="modalResourcesContainer" style="display:none;">
                    <h4><i class="fas fa-link"></i> Resources</h4>
                    <ul id="modalResources"></ul>
                </div>

                <div id="modalQuestionsContainer" style="display:none;">
                    <h4><i class="fas fa-question-circle"></i> Questions</h4>
                    <ul id="modalQuestions"></ul>
                </div>

                <div class="flag-submission-section">
                    <h4>Submit Flag</h4>
                    <input type="text" id="flagInput" placeholder="Enter your flag (e.g., SUDO{th1s_1s_th3_fl4g})">
                    <button id="submitFlagBtn"><i class="fas fa-paper-plane"></i> Submit Flag</button>
                    <p id="flagMessage" style="text-align: center; margin-top: 10px; font-weight: bold;"></p>
                </div>
            </div>
            <div class="custom-modal-buttons">
                <a href="#" id="modalMachineLinkButton" class="action-button primary" target="_blank" style="display: none;"><i class="fas fa-play"></i> Start Machine</a>
                <button class="action-button" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- ===== AUTH MODAL ===== -->
    <div class="custom-modal-overlay" id="authModalOverlay">
        <div class="custom-modal-card" id="authModalCard">
            <button class="modal-close-btn" id="authModalCloseBtn">&times;</button>
            <h3 id="authModalTitle">Login</h3>
            <form id="authForm">
                <input type="text" id="authUsername" placeholder="Username" required>
                <input type="email" id="authEmail" placeholder="Email (for registration)" style="display: none;">
                <input type="password" id="authPassword" placeholder="Password" required>
                <button type="submit" class="action-button primary" id="authSubmitBtn">Login</button>
            </form>
            <p class="auth-switch-text">
                Don't have an account? <a href="#" id="switchToRegister">Register here</a>
            </p>
            <p class="auth-switch-text" style="display: none;" id="switchToLoginContainer">
                Already have an account? <a href="#" id="switchToLogin">Login here</a>
            </p>
            <p id="authMessage" style="text-align: center; margin-top: 10px; font-weight: bold;"></p>
        </div>
    </div>

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
        const BACKEND_API_URL = 'https://sudosocietyctf.unaux.com/api/jenslin.php';

        // --- DOM refs ---
        const headerUsername = document.getElementById('headerUsername');
        const headerUserAvatar = document.getElementById('headerUserAvatar');
        const completionPercentage = document.getElementById('completionPercentage');
        const completionBar = document.getElementById('completionBar');
        const taskList = document.getElementById('taskList');

        const taskModalOverlay = document.getElementById('taskModalOverlay');
        const modalCloseBtn = document.getElementById('modalCloseBtn');
        const modalTaskTitle = document.getElementById('modalTaskTitle');
        const modalTaskCategory = document.getElementById('modalTaskCategory');
        const modalTaskPoints = document.getElementById('modalTaskPoints');
        const modalTaskSolves = document.getElementById('modalTaskSolves');
        const modalTaskStory = document.getElementById('modalTaskStory');
        const modalLearningObjectives = document.getElementById('modalLearningObjectives');
        const modalLearningDetails = document.getElementById('modalLearningDetails');
        const modalResourcesContainer = document.getElementById('modalResourcesContainer');
        const modalResources = document.getElementById('modalResources');
        const modalQuestionsContainer = document.getElementById('modalQuestionsContainer');
        const modalQuestions = document.getElementById('modalQuestions');
        const modalMachineLinkButton = document.getElementById('modalMachineLinkButton');
        const flagInput = document.getElementById('flagInput');
        const submitFlagBtn = document.getElementById('submitFlagBtn');
        const flagMessage = document.getElementById('flagMessage');

        const authModalOverlay = document.getElementById('authModalOverlay');
        const authModalCloseBtn = document.getElementById('authModalCloseBtn');
        const authModalTitle = document.getElementById('authModalTitle');
        const authForm = document.getElementById('authForm');
        const authUsernameInput = document.getElementById('authUsername');
        const authEmailInput = document.getElementById('authEmail');
        const authPasswordInput = document.getElementById('authPassword');
        const authSubmitBtn = document.getElementById('authSubmitBtn');
        const authMessage = document.getElementById('authMessage');
        const switchToRegisterLink = document.getElementById('switchToRegister');
        const switchToLoginLink = document.getElementById('switchToLogin');
        const switchToLoginContainer = document.getElementById('switchToLoginContainer');
        const authButton = document.getElementById('authButton');
        const logoutButton = document.getElementById('logoutButton');

        let allChallenges = [];
        let currentUserId = null;
        let currentChallengeId = null;
        let isRegisterMode = false;

        // --- API Functions ---
        async function checkLoginStatus() {
            try {
                const response = await fetch(`${BACKEND_API_URL}?action=checkLoginStatus`);
                const data = await response.json();

                if (data.success && data.loggedIn) {
                    headerUsername.textContent = data.user.username;
                    headerUserAvatar.src = data.user.avatar_url || 'https://i.pravatar.cc/100?img=11';
                    currentUserId = data.user.id;
                    authButton.style.display = 'none';
                    logoutButton.style.display = 'inline-block';
                    console.log('User logged in:', data.user.username);
                } else {
                    headerUsername.textContent = 'Guest';
                    headerUserAvatar.src = 'https://i.pravatar.cc/100?img=11';
                    currentUserId = null;
                    authButton.style.display = 'inline-block';
                    logoutButton.style.display = 'none';
                    console.log('Not logged in:', data.message);
                }
            } catch (error) {
                console.error('Error checking login status:', error);
                headerUsername.textContent = 'Guest (Error)';
                headerUserAvatar.src = 'https://i.pravatar.cc/100?img=11';
                authButton.style.display = 'inline-block';
                logoutButton.style.display = 'none';
            }
        }

        async function fetchChallenges() {
            try {
                const response = await fetch(`${BACKEND_API_URL}?action=getChallenges`);
                const data = await response.json();

                if (data.success) {
                    allChallenges = data.data;
                    renderChallenges();
                    updateCompletionStatus();
                } else {
                    taskList.innerHTML = `<p style="color: var(--error-color);">Failed to load challenges: ${data.error}</p>`;
                    console.error('Failed to fetch challenges:', data.error);
                }
            } catch (error) {
                taskList.innerHTML = `<p style="color: var(--error-color);">Error connecting to backend: ${error.message}</p>`;
                console.error('Network error fetching challenges:', error);
            }
        }

        async function submitFlag(challengeId, flag) {
            flagMessage.textContent = 'Submitting...';
            flagMessage.style.color = 'var(--pending-color)';
            submitFlagBtn.disabled = true;

            try {
                const response = await fetch(`${BACKEND_API_URL}?action=submitFlag`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ challenge_id: challengeId, flag: flag })
                });
                const data = await response.json();

                if (data.success) {
                    flagMessage.textContent = data.message;
                    flagMessage.style.color = 'var(--completed-color)';
                    await fetchChallenges();
                    setTimeout(closeModal, 1500);
                } else {
                    flagMessage.textContent = data.message;
                    flagMessage.style.color = 'var(--error-color)';
                }
            } catch (error) {
                flagMessage.textContent = `Submission error: ${error.message}`;
                flagMessage.style.color = 'var(--error-color)';
                console.error('Error submitting flag:', error);
            } finally {
                submitFlagBtn.disabled = false;
            }
        }

        async function handleAuth(event) {
            event.preventDefault();
            authMessage.textContent = '';
            authSubmitBtn.disabled = true;

            const username = authUsernameInput.value.trim();
            const password = authPasswordInput.value.trim();
            const email = authEmailInput.value.trim();

            let actionType = isRegisterMode ? 'register' : 'login';
            let payload = { username, password };
            if (isRegisterMode) {
                payload.email = email;
            }

            try {
                const response = await fetch(`${BACKEND_API_URL}?action=${actionType}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();

                if (data.success) {
                    authMessage.textContent = data.message;
                    authMessage.style.color = 'var(--completed-color)';
                    await checkLoginStatus();
                    await fetchChallenges();
                    setTimeout(closeAuthModal, 1500);
                } else {
                    authMessage.textContent = data.message;
                    authMessage.style.color = 'var(--error-color)';
                }
            } catch (error) {
                authMessage.textContent = `Error: ${error.message}`;
                authMessage.style.color = 'var(--error-color)';
                console.error('Auth error:', error);
            } finally {
                authSubmitBtn.disabled = false;
            }
        }

        async function handleLogout() {
            try {
                const response = await fetch(`${BACKEND_API_URL}?action=logout`);
                const data = await response.json();
                if (data.success) {
                    await checkLoginStatus();
                    await fetchChallenges();
                    console.log(data.message);
                } else {
                    console.error('Logout failed:', data.message);
                }
            } catch (error) {
                console.error('Error during logout:', error);
            }
        }

        // --- Render Functions ---
        function renderChallenges() {
            taskList.innerHTML = '';
            if (allChallenges.length === 0) {
                taskList.innerHTML = '<p style="color: var(--text-muted);">No challenges available yet. Check back later!</p>';
                return;
            }

            allChallenges.forEach(challenge => {
                const taskItem = document.createElement('div');
                taskItem.classList.add('task-item');
                if (challenge.solved) {
                    taskItem.classList.add('solved');
                }
                taskItem.dataset.challengeId = challenge.id;

                taskItem.innerHTML = `
                    <div class="task-header">
                        <span class="task-status-icon ${challenge.solved ? 'completed' : 'pending'}">
                            <i class="${challenge.solved ? 'fas fa-check-circle' : 'fas fa-hourglass-half'}"></i>
                        </span>
                        <div class="task-title-group">
                            <div class="task-day">${challenge.day_label || 'Challenge'} | Points: ${challenge.points} | Solves: ${challenge.solves}</div>
                            <div class="task-main-title">${challenge.title}</div>
                        </div>
                        <span class="task-type">${challenge.category}</span>
                        <i class="fas fa-chevron-right task-arrow-icon"></i>
                    </div>
                `;
                taskList.appendChild(taskItem);

                taskItem.addEventListener('click', () => openTaskModal(challenge));
            });
        }

        function updateCompletionStatus() {
            const completedTasks = allChallenges.filter(challenge => challenge.solved).length;
            const totalTasks = allChallenges.length;
            const percentage = totalTasks > 0 ? (completedTasks / totalTasks) * 100 : 0;

            completionPercentage.textContent = Math.round(percentage);
            completionBar.style.width = percentage + '%';
        }

        // --- Modal Controls ---
        function openTaskModal(challenge) {
            currentChallengeId = challenge.id;

            modalTaskTitle.textContent = `${challenge.day_label || 'Challenge'}: ${challenge.title}`;
            modalTaskCategory.textContent = challenge.category;
            modalTaskPoints.textContent = challenge.points;
            modalTaskSolves.textContent = challenge.solves;
            modalTaskStory.textContent = challenge.story || 'No story available.';
            modalLearningDetails.innerHTML = challenge.learning_details_html || '<p>No further details available for this task.</p>';

            modalLearningObjectives.innerHTML = '';
            if (challenge.learning_objectives && challenge.learning_objectives.length > 0) {
                challenge.learning_objectives.forEach(obj => {
                    const li = document.createElement('li');
                    li.textContent = obj;
                    modalLearningObjectives.appendChild(li);
                });
            } else {
                modalLearningObjectives.innerHTML = '<li>No specific learning objectives listed.</li>';
            }

            modalResources.innerHTML = '';
            if (challenge.resources && challenge.resources.length > 0) {
                modalResourcesContainer.style.display = 'block';
                challenge.resources.forEach(res => {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.href = res.url;
                    a.target = '_blank';
                    a.style.color = 'var(--accent)';
                    a.style.textDecoration = 'none';
                    a.innerHTML = `${res.name} <i class="fas fa-external-link-alt" style="font-size: 0.8em;"></i>`;
                    li.appendChild(a);
                    modalResources.appendChild(li);
                });
            } else {
                modalResourcesContainer.style.display = 'none';
            }

            modalQuestions.innerHTML = '';
            if (challenge.questions && challenge.questions.length > 0) {
                modalQuestionsContainer.style.display = 'block';
                challenge.questions.forEach((q, index) => {
                    const li = document.createElement('li');
                    li.innerHTML = `<strong>Q${index + 1}:</strong> ${q.question}<br><strong>A:</strong> <code>${q.answer}</code>`;
                    modalQuestions.appendChild(li);
                });
            } else {
                modalQuestionsContainer.style.display = 'none';
            }

            if (challenge.machine_link) {
                modalMachineLinkButton.href = challenge.machine_link;
                modalMachineLinkButton.style.display = 'inline-flex';
            } else {
                modalMachineLinkButton.style.display = 'none';
            }

            flagInput.value = '';
            flagMessage.textContent = '';
            flagInput.disabled = false;
            submitFlagBtn.disabled = false;

            if (challenge.solved) {
                flagInput.placeholder = 'Challenge already solved!';
                flagInput.disabled = true;
                submitFlagBtn.disabled = true;
                flagMessage.textContent = 'You have already solved this challenge.';
                flagMessage.style.color = 'var(--completed-color)';
            } else if (currentUserId === null) {
                flagInput.placeholder = 'Log in to submit flags!';
                flagInput.disabled = true;
                submitFlagBtn.disabled = true;
                flagMessage.textContent = 'Please log in to submit flags.';
                flagMessage.style.color = 'var(--error-color)';
            }

            taskModalOverlay.classList.add('show');
        }

        function closeModal() {
            taskModalOverlay.classList.remove('show');
            currentChallengeId = null;
        }

        function openAuthModal(registerMode = false) {
            isRegisterMode = registerMode;
            authModalTitle.textContent = isRegisterMode ? 'Register' : 'Login';
            authEmailInput.style.display = isRegisterMode ? 'block' : 'none';
            authSubmitBtn.textContent = isRegisterMode ? 'Register' : 'Login';
            switchToRegisterLink.parentElement.style.display = isRegisterMode ? 'none' : 'block';
            switchToLoginContainer.style.display = isRegisterMode ? 'block' : 'none';

            authForm.reset();
            authMessage.textContent = '';
            authSubmitBtn.disabled = false;

            authModalOverlay.classList.add('show');
        }

        function closeAuthModal() {
            authModalOverlay.classList.remove('show');
        }

        // --- Event Listeners ---
        modalCloseBtn.addEventListener('click', closeModal);
        taskModalOverlay.addEventListener('click', (event) => {
            if (event.target === taskModalOverlay) {
                closeModal();
            }
        });

        authModalCloseBtn.addEventListener('click', closeAuthModal);
        authModalOverlay.addEventListener('click', (event) => {
            if (event.target === authModalOverlay) {
                closeAuthModal();
            }
        });

        submitFlagBtn.addEventListener('click', () => {
            const flag = flagInput.value.trim();
            if (flag && currentChallengeId !== null) {
                submitFlag(currentChallengeId, flag);
            } else {
                flagMessage.textContent = 'Please enter a flag.';
                flagMessage.style.color = 'var(--error-color)';
            }
        });

        authButton.addEventListener('click', () => openAuthModal(false));
        logoutButton.addEventListener('click', handleLogout);

        switchToRegisterLink.addEventListener('click', (e) => {
            e.preventDefault();
            openAuthModal(true);
        });
        switchToLoginLink.addEventListener('click', (e) => {
            e.preventDefault();
            openAuthModal(false);
        });

        authForm.addEventListener('submit', handleAuth);

        // --- Initialize ---
        document.addEventListener('DOMContentLoaded', async () => {
            // Set active nav link
            document.querySelectorAll('.nav-links a').forEach(link => {
                if (link.getAttribute('href') === '#') {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            await checkLoginStatus();
            await fetchChallenges();
        });
    </script>

</body>
</html>