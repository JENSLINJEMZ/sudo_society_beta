<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | Sudo Society CTF</title>
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

            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-display: 'Barlow Condensed', 'Inter', sans-serif;

            --header-bg: rgba(11, 13, 14, 0.70);

            --modal-bg: rgba(11, 13, 14, 0.92);
            --modal-card-bg: var(--bg-primary);
            --event-status-color: var(--accent);
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

            --modal-bg: rgba(244, 247, 246, 0.92);
            --modal-card-bg: #ffffff;
            --event-status-color: var(--accent);
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

        .events-content {
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
        }

        .events-content::before {
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

        .events-header {
            text-align: center;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
            transition: border-color 0.4s ease;
        }

        .events-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 700;
            background: var(--section-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: background 0.5s ease;
            margin-bottom: 6px;
        }

        .events-subtitle {
            color: var(--text-secondary);
            font-size: 1.05rem;
            transition: color 0.4s ease;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 32px 0 18px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-subtle);
            transition: color 0.4s ease, border-color 0.4s ease;
        }

        .section-title i {
            color: var(--accent);
            transition: color 0.4s ease;
        }

        /* ============================================================
           EVENT CARDS
           ============================================================ */
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 10px;
        }

        .event-card {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, background 0.4s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(4px);
        }

        .event-card:hover {
            transform: translateY(-4px);
            border-color: var(--card-hover-border);
            box-shadow: var(--card-hover-shadow);
        }

        .event-card-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-subtle);
            transition: border-color 0.4s ease;
        }

        .event-card-content {
            padding: 18px 18px 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .event-card-title {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
            transition: color 0.4s ease;
        }

        .event-card-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            transition: color 0.4s ease;
        }

        .event-card-meta span {
            display: block;
            margin-bottom: 2px;
        }

        .event-card-meta i {
            width: 18px;
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .event-card-description {
            font-size: 0.9rem;
            color: var(--text-secondary);
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 12px;
            transition: color 0.4s ease;
        }

        .event-card-countdown,
        .event-card-status {
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            padding: 8px 0;
            border-top: 1px solid var(--border-subtle);
            margin-top: 6px;
            transition: color 0.4s ease, border-color 0.4s ease;
        }

        .event-card-countdown {
            color: var(--accent);
        }

        .event-card-status {
            color: var(--text-muted);
        }

        .no-events-message {
            text-align: center;
            color: var(--text-muted);
            padding: 30px 0;
            font-size: 0.95rem;
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
            background: var(--modal-card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px 36px;
            max-width: 700px;
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

        .custom-modal-message {
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .modal-event-image-container {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            border: 1px solid var(--glass-border);
            transition: border-color 0.4s ease;
        }

        .modal-event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-event-meta p {
            display: flex;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .modal-event-meta .label {
            min-width: 100px;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .modal-event-meta .value {
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .modal-section-title {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 18px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid var(--border-subtle);
            transition: color 0.4s ease, border-color 0.4s ease;
        }

        .modal-countdown-container,
        .modal-status-container {
            text-align: center;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid var(--border-subtle);
            transition: border-color 0.4s ease;
        }

        .modal-countdown-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .modal-countdown {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent);
            transition: color 0.4s ease;
        }

        .modal-status {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: color 0.4s ease;
        }

        .results-link {
            display: inline-block;
            margin-top: 6px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s ease;
        }

        .results-link:hover {
            opacity: 0.8;
        }

        .modal-additional-info {
            margin-top: 16px;
            padding: 14px 16px;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            transition: background 0.4s ease, border-color 0.4s ease;
        }

        .modal-additional-info p {
            color: var(--text-secondary);
            transition: color 0.4s ease;
        }

        .custom-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
            flex-wrap: wrap;
            transition: border-color 0.4s ease;
        }

        .custom-modal-btn {
            padding: 10px 24px;
            border-radius: 40px;
            font-family: var(--font-primary);
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, color 0.3s ease;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1px solid var(--glass-border);
        }

        .custom-modal-btn:hover {
            transform: translateY(-2px);
        }

        .custom-modal-btn.primary {
            background: var(--accent-gradient);
            color: var(--bg-primary);
            border: none;
            box-shadow: 0 4px 16px rgba(111, 207, 151, 0.15);
        }

        body.light .custom-modal-btn.primary {
            box-shadow: 0 4px 16px rgba(192, 57, 43, 0.12);
        }

        .custom-modal-btn.primary:hover {
            box-shadow: 0 8px 24px rgba(111, 207, 151, 0.25);
        }

        body.light .custom-modal-btn.primary:hover {
            box-shadow: 0 8px 24px rgba(192, 57, 43, 0.20);
        }

        .custom-modal-btn.secondary {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--glass-border);
        }

        .custom-modal-btn.secondary:hover {
            background: var(--bg-secondary);
            border-color: var(--text-muted);
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

            .events-content {
                padding: 20px 16px;
            }
            .events-title {
                font-size: 1.8rem;
            }
            .events-subtitle {
                font-size: 0.95rem;
            }
            .section-title {
                font-size: 1.2rem;
            }
            .event-grid {
                grid-template-columns: 1fr;
            }
            .event-card-image {
                height: 140px;
            }
            .custom-modal-card {
                padding: 20px;
            }
            .modal-event-meta p {
                flex-direction: column;
            }
            .modal-event-meta .label {
                min-width: auto;
                margin-bottom: 2px;
            }
            .custom-modal-header {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 16px;
            }
            .events-title {
                font-size: 1.5rem;
            }
            .event-card-title {
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
                    <a href="event.php" class="active">Events</a>
                </nav>
                <!-- Theme Toggle -->
                <div class="theme-toggle" id="themeToggle" role="button" tabindex="0" aria-label="Toggle theme">
                    <span class="toggle-icon" id="toggleIcon">🌙</span>
                    <div class="toggle-track">
                        <div class="toggle-dot"></div>
                    </div>
                    <span class="toggle-label" id="toggleLabel">Dark</span>
                </div>
                <div class="user-profile" onclick="toggleDropdown()">
                    <img src="<?php echo isset($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : 'https://i.pravatar.cc/100?img=11'; ?>" alt="User Avatar" class="user-avatar" id="headerUserAvatar">
                    <span class="username" id="headerUsername"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main>
        <div class="container">
            <section class="events-content fade-up">
                <div class="events-header">
                    <h1 class="events-title">Sudo Society Events</h1>
                    <p class="events-subtitle">Stay updated with our upcoming CTFs, webinars, and community meetups.</p>
                </div>

                <section id="upcoming-events-section">
                    <h2 class="section-title"><i class="fas fa-calendar-check"></i> Upcoming Events</h2>
                    <div class="event-grid" id="upcomingEventsGrid">
                        <p class="no-events-message" id="loadingUpcoming">Loading upcoming events...</p>
                    </div>
                    <p class="no-events-message" id="noUpcomingEvents" style="display: none;">
                        No upcoming events at the moment. Check back soon!
                    </p>
                </section>

                <section id="past-events-section">
                    <h2 class="section-title"><i class="fas fa-history"></i> Past Events</h2>
                    <div class="event-grid" id="pastEventsGrid">
                        <p class="no-events-message" id="loadingPast">Loading past events...</p>
                    </div>
                    <p class="no-events-message" id="noPastEvents" style="display: none;">
                        No past events recorded yet.
                    </p>
                </section>
            </section>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society CTF. All rights reserved. Developed by JENSLIN</p>
        </div>
    </footer>

    <!-- ===== MODAL ===== -->
    <div id="customModalOverlay" class="custom-modal-overlay">
        <div id="customModalCard" class="custom-modal-card">
            <h3 id="customModalHeader" class="custom-modal-header"></h3>
            <div id="customModalMessage" class="custom-modal-message"></div>
            <div id="customModalButtons" class="custom-modal-buttons"></div>
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
        const eventsBackendUrl = 'https://sudosocietyctf.unaux.com/api/get_events.php';

        // --- DOM refs ---
        const modalOverlay = document.getElementById('customModalOverlay');
        const modalCard = document.getElementById('customModalCard');
        const modalHeader = document.getElementById('customModalHeader');
        const modalMessage = document.getElementById('customModalMessage');
        const modalButtons = document.getElementById('customModalButtons');
        const upcomingEventsGrid = document.getElementById('upcomingEventsGrid');
        const pastEventsGrid = document.getElementById('pastEventsGrid');
        const noUpcomingEventsMsg = document.getElementById('noUpcomingEvents');
        const noPastEventsMsg = document.getElementById('noPastEvents');
        const loadingUpcomingMsg = document.getElementById('loadingUpcoming');
        const loadingPastMsg = document.getElementById('loadingPast');

        let eventCountdownInterval = null;
        let globalCountdownInterval = null;

        // --- Modal functions ---
        function showAlert(messageHtml, title = 'Sudo Society CTF', buttonsConfig = null) {
            modalButtons.innerHTML = '';
            modalHeader.textContent = title;
            modalMessage.innerHTML = messageHtml;

            if (buttonsConfig && buttonsConfig.length > 0) {
                buttonsConfig.forEach(btnConf => {
                    const button = document.createElement('button');
                    button.textContent = btnConf.text;
                    button.classList.add('custom-modal-btn', ...btnConf.classList);
                    if (btnConf.disabled) button.disabled = true;
                    button.addEventListener('click', () => {
                        hideModal();
                        if (btnConf.onClick) btnConf.onClick();
                    }, { once: true });
                    modalButtons.appendChild(button);
                });
            } else {
                const okButton = document.createElement('button');
                okButton.textContent = 'OK';
                okButton.classList.add('custom-modal-btn', 'primary');
                okButton.addEventListener('click', hideModal, { once: true });
                modalButtons.appendChild(okButton);
            }

            modalOverlay.classList.add('show');
            setTimeout(() => {
                const firstButton = modalButtons.querySelector('button');
                if (firstButton) firstButton.focus();
            }, 300);
        }

        function hideModal() {
            modalOverlay.classList.remove('show');
            modalButtons.innerHTML = '';
            if (eventCountdownInterval) {
                clearInterval(eventCountdownInterval);
                eventCountdownInterval = null;
            }
        }

        // --- Event details modal ---
        function showEventDetails(event) {
            let buttonsConfig = [{
                text: 'Close',
                classList: ['secondary'],
                onClick: () => console.log('Event details closed')
            }];

            let countdownOrStatusHtml = '';
            let registrationSection = '';
            let eventImageUrl = event.image_url || 'https://placehold.co/600x300/6fcf97/0b0d0e?text=Event+Image';

            const eventDate = new Date(event.countdown_date);
            const now = new Date();
            const isUpcoming = eventDate > now;

            if (isUpcoming) {
                countdownOrStatusHtml = `
                    <div class="modal-countdown-container">
                        <p class="modal-countdown-label">Starts in:</p>
                        <p class="modal-countdown" id="modalCountdownDisplay">Loading countdown...</p>
                    </div>
                `;

                if (eventCountdownInterval) clearInterval(eventCountdownInterval);
                eventCountdownInterval = setInterval(() => {
                    const countdownElement = document.getElementById('modalCountdownDisplay');
                    if (countdownElement) {
                        updateCountdown(countdownElement, event.countdown_date);
                    } else {
                        clearInterval(eventCountdownInterval);
                        eventCountdownInterval = null;
                    }
                }, 1000);

                if (event.registration_link) {
                    buttonsConfig.unshift({
                        text: 'Register Now',
                        classList: ['primary'],
                        onClick: () => {
                            console.log('Opening registration link:', event.registration_link);
                            window.open(event.registration_link, '_blank');
                        }
                    });
                }
            } else {
                countdownOrStatusHtml = `
                    <div class="modal-status-container">
                        <p class="modal-status">Event Concluded</p>
                        ${event.results_link ? `
                            <a href="${event.results_link}" target="_blank" class="results-link">
                                View Results
                            </a>
                        ` : ''}
                    </div>
                `;
            }

            if (event.additional_info) {
                registrationSection = `
                    <div class="modal-additional-info">
                        <h4 class="modal-section-title">Additional Information</h4>
                        <p>${event.additional_info}</p>
                    </div>
                `;
            }

            const modalContentHtml = `
                <div class="modal-event-image-container">
                    <img src="${eventImageUrl}" alt="${event.name}" class="modal-event-image"
                         onerror="this.onerror=null;this.src='https://placehold.co/600x300/6fcf97/0b0d0e?text=Event+Image'">
                </div>

                <div class="modal-event-details">
                    <div class="modal-event-meta">
                        <p><span class="label">Type:</span> <span class="value">${event.type || 'General'}</span></p>
                        <p><span class="label">Date:</span> <span class="value">${event.date_str || 'N/A'} at ${event.time_str || 'N/A'}</span></p>
                        <p><span class="label">Location:</span> <span class="value">${event.location || 'N/A'}</span></p>
                        <p><span class="label">Organizer:</span> <span class="value">${event.organizer || 'Sudo Society'}</span></p>
                    </div>

                    <div class="modal-event-description">
                        <h4 class="modal-section-title">Description</h4>
                        <p>${event.description || 'No description provided.'}</p>
                    </div>

                    ${countdownOrStatusHtml}
                    ${registrationSection}
                </div>
            `;

            showAlert(modalContentHtml, event.name, buttonsConfig);
        }

        // --- Countdown updater ---
        function updateCountdown(element, targetDateString) {
            const targetDate = new Date(targetDateString).getTime();
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                element.textContent = 'Event Concluded';
                element.classList.remove('modal-countdown');
                element.classList.add('modal-status');
                fetchEventsFromBackend();
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            element.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        // --- Fetch events from backend ---
        async function fetchEventsFromBackend() {
            loadingUpcomingMsg.style.display = 'block';
            loadingPastMsg.style.display = 'block';
            upcomingEventsGrid.innerHTML = '';
            pastEventsGrid.innerHTML = '';
            noUpcomingEventsMsg.style.display = 'none';
            noPastEventsMsg.style.display = 'none';

            try {
                const response = await fetch(eventsBackendUrl);
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! Status: ${response.status}. Response: ${errorText}`);
                }
                const data = await response.json();

                if (data.success) {
                    renderEvents(data.upcomingEvents, data.pastEvents);
                } else {
                    showAlert(`Failed to load events: ${data.message || 'Unknown error.'}`, 'Data Load Error');
                    loadingUpcomingMsg.textContent = 'Failed to load events.';
                    loadingPastMsg.textContent = 'Failed to load events.';
                    loadingUpcomingMsg.style.display = 'block';
                    loadingPastMsg.style.display = 'block';
                }
            } catch (error) {
                console.error("Error fetching events from backend:", error);
                let errorMessage = "An error occurred while fetching events.";
                if (error instanceof TypeError && error.message === "Failed to fetch") {
                    errorMessage = "<h3>Connection Error!</h3>";
                    errorMessage += "<p>The frontend could not connect to your PHP backend.</p>";
                    errorMessage += `<p>Please check the following:</p>`;
                    errorMessage += `<ul>
                        <li>Is your PHP web server running on <code>https://sudosocietyctf.unaux.com</code>?</li>
                        <li>Is the <code>get_events.php</code> file located correctly at <code>/Sudo_society/api/</code>?</li>
                        <li>Are there any firewalls blocking port 80?</li>
                        <li>Is <code>192.168.1.2</code> the correct IP address?</li>
                    </ul>`;
                    errorMessage += `<p>Error details: <code>${error.message}</code></p>`;
                } else {
                    errorMessage = `<h3>Server Response Error!</h3>`;
                    errorMessage += `<p>The backend responded with an error or invalid data.</p>`;
                    errorMessage += `<p>Details: <code>${error.message}</code></p>`;
                }
                showAlert(errorMessage, "Network Error / Server Issue");
                loadingUpcomingMsg.textContent = 'Error loading data.';
                loadingPastMsg.textContent = 'Error loading data.';
                loadingUpcomingMsg.style.display = 'block';
                loadingPastMsg.style.display = 'block';
            }
        }

        // --- Render events ---
        function renderEvents(upcomingEvents, pastEvents) {
            loadingUpcomingMsg.style.display = 'none';
            loadingPastMsg.style.display = 'none';

            if (upcomingEvents.length === 0) {
                noUpcomingEventsMsg.style.display = 'block';
            } else {
                noUpcomingEventsMsg.style.display = 'none';
                upcomingEvents.forEach(event => {
                    const card = createEventCard(event, true);
                    upcomingEventsGrid.appendChild(card);
                });
            }

            pastEvents.sort((a, b) => new Date(b.countdown_date).getTime() - new Date(a.countdown_date).getTime());
            if (pastEvents.length === 0) {
                noPastEventsMsg.style.display = 'block';
            } else {
                noPastEventsMsg.style.display = 'none';
                pastEvents.forEach(event => {
                    const card = createEventCard(event, false);
                    pastEventsGrid.appendChild(card);
                });
            }

            if (globalCountdownInterval) clearInterval(globalCountdownInterval);
            if (upcomingEvents.length > 0) {
                globalCountdownInterval = setInterval(() => {
                    document.querySelectorAll('.event-card-countdown').forEach(el => {
                        const targetDate = el.dataset.countdownTarget;
                        updateCountdown(el, targetDate);
                    });
                }, 1000);
            }
        }

        // --- Create event card ---
        function createEventCard(event, isUpcoming) {
            const card = document.createElement('div');
            card.classList.add('event-card');
            card.dataset.eventId = event.id;

            const imageUrl = event.image_url || `https://placehold.co/600x300/6fcf97/0b0d0e?text=${event.type || 'Event'}`;

            card.innerHTML = `
                <img src="${imageUrl}" alt="${event.name}" class="event-card-image" onerror="this.onerror=null;this.src='https://placehold.co/600x300/6fcf97/0b0d0e?text=Event+Image';">
                <div class="event-card-content">
                    <div>
                        <h3 class="event-card-title">${event.name}</h3>
                        <div class="event-card-meta">
                            <span><i class="fas fa-tag"></i> ${event.type || 'General'}</span>
                            <span><i class="fas fa-calendar-alt"></i> ${event.date_str || 'N/A'} at ${event.time_str || 'N/A'}</span>
                            <span><i class="fas fa-map-marker-alt"></i> ${event.location || 'N/A'}</span>
                        </div>
                        <p class="event-card-description">${event.description || 'No description provided.'}</p>
                    </div>
                    <div class="${isUpcoming ? 'event-card-countdown' : 'event-card-status'}" data-countdown-target="${event.countdown_date || ''}">
                        ${isUpcoming ? 'Loading countdown...' : 'Event Concluded'}
                    </div>
                </div>
            `;

            card.addEventListener('click', () => showEventDetails(event));
            return card;
        }

        // --- Utility ---
        function toggleDropdown() {
            console.log("User profile clicked! (Dropdown functionality not implemented)");
        }

        // --- Modal close on overlay click ---
        modalOverlay.addEventListener('click', (event) => {
            if (event.target === modalOverlay) {
                hideModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modalOverlay.classList.contains('show')) {
                hideModal();
            }
        });

        // --- Initialize ---
        document.addEventListener('DOMContentLoaded', () => {
            // Ensure active nav link
            document.querySelectorAll('.nav-links a').forEach(link => {
                if (link.getAttribute('href') === 'event.php') {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            fetchEventsFromBackend();
        });
    </script>

</body>
</html>