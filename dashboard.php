<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Player Dashboard | Sudo Society CTF</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <style>

    /* === Extra Small Devices (<= 400px) === */
    @media (max-width: 400px) {
      .container {
        padding: 0 1rem;
      }

      .logo-text {
        font-size: 1.1rem;
        letter-spacing: 1px;
      }

      .user-avatar {
        width: 32px;
        height: 32px;
      }

      .username {
        font-size: 0.8rem;
      }

      .score-card-title {
        font-size: 0.8rem;
      }

      .score-card-value {
        font-size: 1.5rem;
      }

      .period-btn {
        font-size: 0.6rem;
        padding: 0.2rem 0.5rem;
      }

      .chart-title {
        font-size: 0.85rem;
      }

      .section-title {
        font-size: 1rem;
      }

      .view-all {
        font-size: 0.7rem;
      }

      .achievement-title {
        font-size: 0.8rem;
      }

      .achievement-desc {
        font-size: 0.65rem;
      }

      .activity-title {
        font-size: 0.8rem;
      }

      .activity-time {
        font-size: 0.65rem;
      }

      .activity-points {
        font-size: 0.8rem;
      }

      .leaderboard-item .rank,
      .leaderboard-item .score {
        font-size: 0.75rem;
      }

      .leaderboard-item .username {
        font-size: 0.75rem;
      }
    }

    /* === Narrow Mobile Screens Fix for Sidebar === */
    @media (max-width: 600px) {
      .dashboard {
        grid-template-columns: 1fr;
      }

      .sidebar {
        order: 2;
        margin-top: 2rem;
      }

      .main-content {
        order: 1;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .score-cards {
        grid-template-columns: 1fr;
      }

      .achievements-grid {
        grid-template-columns: 1fr;
      }

      .chart-card {
        padding: 1rem;
      }
    }

    /* CSS Variables for consistent theming */
    :root {
      --neon-green: #00ff88;
      --neon-pink: #ff00c8;
      --neon-blue: #00ffff;
      --dark-bg: #0a0a0a;
      --darker-bg: #050505;
      --card-bg: rgba(20, 20, 20, 0.7);
      --border-color: rgba(0, 255, 136, 0.2);
      --text-light: #c0fccc;
      --text-dim: rgba(192, 252, 204, 0.6);
      --glow-light: 0 0 8px rgba(0, 255, 136, 0.5);
      --glow-medium: 0 0 15px rgba(0, 255, 136, 0.7);
      --glow-intense: 0 0 25px rgba(0, 255, 136, 0.9);
    }

    /* Basic Reset & Box Model */
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

    /* === SCANLINE EFFECT === */
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

    /* === HEADER === */
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
      flex-wrap: wrap; /* Allow wrapping on small screens */
      gap: 1rem;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 1rem;
      cursor: pointer;
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

    .nav-links {
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      justify-content: center;
    }

    .nav-links a {
      color: var(--text-light);
      text-decoration: none;
      font-size: 0.9rem;
      position: relative;
      padding: 0.5rem 0;
      transition: all 0.3s ease;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--neon-green);
      transition: width 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--neon-green);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-links a.active {
      color: var(--neon-green);
      text-shadow: var(--glow-light);
    }

    .nav-links a.active::after {
      width: 100%;
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      cursor: pointer;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid var(--neon-green);
      object-fit: cover;
      box-shadow: var(--glow-light);
    }

    .username {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-weight: 700;
    }

    /* === DASHBOARD LAYOUT === */
    .dashboard {
      display: grid;
      grid-template-columns: 250px 1fr;
      gap: 2rem;
      padding: 2rem 0;
    }

    /* === SIDEBAR === */
    .sidebar {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.5rem;
      backdrop-filter: blur(5px);
      height: fit-content;
      position: sticky;
      top: 100px; /* Adjust if header height changes */
    }

    .sidebar-section {
      margin-bottom: 2rem;
    }

    .sidebar-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .sidebar-title i {
      font-size: 1.2rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
    }

    .stat-card {
      background: rgba(10, 10, 10, 0.5);
      border: 1px solid rgba(0, 255, 136, 0.1);
      border-radius: 4px;
      padding: 0.8rem;
      text-align: center;
    }

    .stat-value {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.3rem;
    }

    .stat-label {
      font-size: 0.7rem;
      color: var(--text-dim);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .streak-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 1rem;
    }

    .streak-count {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 2.5rem;
      font-weight: 700;
      position: relative;
    }

    .streak-count::after {
      content: '🔥';
      position: absolute;
      right: -25px;
      top: 0;
      font-size: 1.5rem;
    }

    .streak-label {
      font-size: 0.8rem;
      color: var(--text-dim);
      margin-top: 0.3rem;
    }

    .streak-calendar {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 0.3rem;
      margin-top: 1rem;
      width: 100%; /* Ensure it fills its container */
    }

    .streak-day {
      width: 100%;
      aspect-ratio: 1; /* Make it square */
      border-radius: 2px;
      background: rgba(0, 255, 136, 0.1);
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      color: var(--text-dim);
    }

    .streak-day.active {
      background: var(--neon-green);
      box-shadow: 0 0 5px var(--neon-green);
      color: var(--dark-bg); /* Dark text on neon green */
      font-weight: bold;
    }

    .streak-day.today {
      border: 1px solid var(--neon-blue); /* Highlight today's date */
      box-shadow: 0 0 8px var(--neon-blue);
    }

    /* === MAIN CONTENT === */
    .main-content {
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .welcome-banner {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.5rem;
      backdrop-filter: blur(5px);
      position: relative;
      overflow: hidden;
    }

    .welcome-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(0, 255, 136, 0.1), transparent);
      transition: all 0.6s ease;
    }

    .welcome-banner:hover::before {
      left: 100%;
    }

    .welcome-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
      color: var(--text-dim);
      font-size: 0.9rem;
    }

    /* === SCORE CARDS === */
    .score-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .score-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.5rem;
      backdrop-filter: blur(5px);
      transition: all 0.3s ease;
    }

    .score-card:hover {
      transform: translateY(-5px);
      border-color: var(--neon-green);
      box-shadow: 0 10px 20px rgba(0, 255, 136, 0.1);
    }

    .score-card-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .score-card-value {
      font-family: 'Orbitron', sans-serif;
      font-size: 2rem;
      font-weight: 700;
      color: white;
      margin-bottom: 0.5rem;
    }

    .score-card-change {
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.3rem;
    }

    .score-card-change.positive {
      color: var(--neon-green);
    }

    .score-card-change.negative {
      color: var(--neon-pink);
    }

    /* === CHARTS SECTION === */
    .charts-section {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    @media (min-width: 1200px) {
      .charts-section {
        grid-template-columns: 2fr 1fr;
      }
    }

    .chart-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.5rem;
      backdrop-filter: blur(5px);
    }

    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .chart-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .chart-period {
      display: flex;
      gap: 0.5rem;
    }

    .period-btn {
      background: rgba(10, 10, 10, 0.5);
      border: 1px solid var(--border-color);
      border-radius: 4px;
      padding: 0.3rem 0.7rem;
      color: var(--text-light);
      font-size: 0.7rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .period-btn.active {
      background: rgba(0, 255, 136, 0.2);
      color: var(--neon-green);
      border-color: var(--neon-green);
    }

    .period-btn:hover {
      border-color: var(--neon-green);
    }

    /* --- CHART HEIGHT FIX --- */
    .chart-container {
      height: 300px; /* Fixed height for the container */
      position: relative;
    }
    #scoreProgressionChart,
    #challengesByCategoryChart {
      height: 100% !important;
      width: 100% !important;
    }

    /* --- ApexCharts specific for Radial Bar --- */
    .apexcharts-canvas {
      font-family: 'Share Tech Mono', monospace !important;
    }
    .apexcharts-tooltip {
      background: var(--darker-bg) !important;
      border: 1px solid var(--neon-green) !important;
      color: var(--text-light) !important;
      box-shadow: var(--glow-light) !important;
    }
    .apexcharts-tooltip-title {
        background: rgba(0, 255, 136, 0.1) !important;
        border-bottom: 1px solid var(--neon-green) !important;
        color: var(--neon-green) !important;
    }
    .apexcharts-xaxis-label, .apexcharts-yaxis-label, .apexcharts-legend-text {
        fill: var(--text-light) !important;
    }
    .progress-radial-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    .progress-radial-card .chart-container {
        height: 200px;
        width: 100%;
        max-width: 200px;
    }

    /* === ACHIEVEMENTS & ACTIVITY === */
    .achievements-section {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.5rem;
      backdrop-filter: blur(5px);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .section-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1.2rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .view-all {
      color: var(--neon-green);
      font-size: 0.8rem;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .view-all:hover {
      text-shadow: var(--glow-light);
    }

    .achievements-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
    }

    .achievement-card {
      background: rgba(10, 10, 10, 0.5);
      border: 1px solid rgba(0, 255, 136, 0.1);
      border-radius: 6px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: all 0.3s ease;
    }

    .achievement-card:hover {
      transform: translateY(-3px);
      border-color: var(--neon-green);
      box-shadow: 0 5px 15px rgba(0, 255, 136, 0.1);
    }

    .achievement-icon {
      width: 50px;
      height: 50px;
      background: rgba(0, 255, 136, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 0.8rem;
      color: var(--neon-green);
    }
 
    .achievement-title {
      font-family: 'Orbitron', sans-serif;
      font-size: 0.9rem;
    }

    .achievement-desc {
      font-size: 0.7rem;
      color: var(--text-dim);
    }

    .achievement-progress {
      width: 100%;
      height: 4px;
      background: rgba(0, 255, 136, 0.1);
      border-radius: 2px;
      margin-top: 0.8rem;
      overflow: hidden;
    }

    .achievement-progress-bar {
      height: 100%;
      background: var(--neon-green);
      width: 0%;
      transition: width 1s ease;
    }

    .activity-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.8rem;
      background: rgba(10, 10, 10, 0.3);
      border-radius: 6px;
      border-left: 3px solid var(--neon-green);
    }

    .activity-icon {
      width: 30px;
      height: 30px;
      background: rgba(0, 255, 136, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      color: var(--neon-green);
    }

    .activity-details {
      flex: 1;
    }

    .activity-title {
      font-size: 0.9rem;
      margin-bottom: 0.2rem;
    }

    .activity-time {
      font-size: 0.7rem;
      color: var(--text-dim);
    }

    .activity-points {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 0.9rem;
    }

    /* Leaderboard Focus Effect */
    .leaderboard-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .leaderboard-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 1rem;
        background: rgba(10, 10, 10, 0.3);
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .leaderboard-item.focused {
        background: var(--card-bg);
        border: 1px solid var(--neon-green);
        box-shadow: var(--glow-intense);
    }

    .leaderboard-item.dimmed {
        opacity: 0.5;
        background: rgba(10, 10, 10, 0.1);
        border: 1px solid rgba(0, 255, 136, 0.05);
        color: var(--text-dim);
    }

    .leaderboard-item.dimmed .rank,
    .leaderboard-item.dimmed .username,
    .leaderboard-item.dimmed .score {
        color: var(--text-dim);
    }

    .leaderboard-item .rank {
        font-family: 'Orbitron', sans-serif;
        color: var(--neon-blue);
        font-weight: 700;
        width: 40px; /* Fixed width for alignment */
        text-align: left;
    }

    .leaderboard-item .username {
        flex-grow: 1;
        font-family: 'Share Tech Mono', monospace;
        color: var(--text-light);
        text-align: left;
    }

    .leaderboard-item .score {
        font-family: 'Orbitron', sans-serif;
        color: var(--neon-green);
        font-weight: 700;
        width: 80px; /* Fixed width for alignment */
        text-align: right;
    }

    /* === FOOTER === */
    footer {
      padding: 2rem 0;
      background: rgba(5, 5, 5, 0.95);
      border-top: 1px solid var(--border-color);
      text-align: center;
      position: relative;
      margin-top: 3rem; /* Ensure space from content above */
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
      color: var(--text-dim);
      font-size: 0.9rem;
    }

    /* === RESPONSIVE === */
    @media (max-width: 1024px) {
      .dashboard {
        grid-template-columns: 1fr;
      }
      .sidebar {
        position: static;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .header-content {
        flex-direction: column;
        align-items: flex-start;
      }
      .nav-links {
        width: 100%;
        justify-content: flex-start;
      }
      .user-menu {
        width: 100%;
        justify-content: space-between;
      }
      .score-cards {
        grid-template-columns: 1fr 1fr;
      }
      .achievements-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 480px) {
      .score-cards {
        grid-template-columns: 1fr;
      }
      .achievements-grid {
        grid-template-columns: 1fr;
      }
      .container {
        padding: 0 1rem;
      }
      .logo-text {
        font-size: 1.2rem;
      }
      .user-avatar {
        width: 32px;
        height: 32px;
      }
      .username {
        font-size: 0.8rem;
      }
      .score-card-title {
        font-size: 0.9rem;
      }
      .score-card-value {
        font-size: 1.8rem;
      }
      .period-btn {
        font-size: 0.6rem;
        padding: 0.2rem 0.5rem;
      }
      .chart-title, .section-title {
        font-size: 1rem;
      }
      .view-all {
        font-size: 0.7rem;
      }
    }
  </style>
</head>
<body>
  <div class="scanline"></div>

  <header>
    <div class="container">
      <div class="header-content">
        <div class="logo" onclick="window.location.href='index.html'">
          <!-- Sudo Society Logo SVG -->
          <img src="sudo_society.png" alt="Sudo Society" class="logo-img">
            <style>.st0{fill:#00ff88;}</style>
            <path class="st0" d="M256 512c-141.4 0-256-114.6-256-256S114.6 0 256 0s256 114.6 256 256-114.6 256-256 256zm0-448c-106.1 0-192 85.9-192 192s85.9 192 192 192 192-85.9 192-192-85.9-192-192-192z"/>
            <path class="st0" d="M256 384c-70.7 0-128-57.2-128-128s57.3-128 128-128 128 57.3 128 128-57.3 128-128 128z"/>
            <path class="st0" d="M256 320c-35.3 0-64-28.7-64-64s28.7-64 64-64 64 28.7 64 64-28.7 64-64 64z"/>
          </svg>
          <span class="logo-text">Sudo Society</span>
        </div>
        <div class="user-menu">
          <nav class="nav-links">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="jenslin_little_advangure.php">Challenges</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="http://192.168.1.2/Sudo_society_beta/event.php">Event</a>
            <a href="#" id="logout">Logout</a>
          </nav>
          <div class="user-profile" onclick="toggleDropdown()">
            <!-- User avatar will be loaded dynamically or use PHP session -->
            <img src="<?php echo isset($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : 'https://i.imgur.com/JqYeSzn.png'; ?>" alt="User Avatar" class="user-avatar" id="headerUserAvatar">
            <span class="username" id="username">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="dashboard">
      <aside class="sidebar">
        <div class="sidebar-section">
          <div class="sidebar-title">
            <i class="fa-solid fa-chart-line"></i> Stats
          </div>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-value" id="stat-challenges">--</div>
              <div class="stat-label">Challenges</div>
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

        <div class="sidebar-section">
          <div class="sidebar-title">
            <i class="fa-solid fa-fire-flame-curved"></i> Streak
          </div>
          <div class="streak-container">
            <div class="streak-count" id="streak-count">--</div>
            <div class="streak-label">Day Streak</div>
            <div class="streak-calendar" id="streak-calendar">
              <!-- Streak days will be dynamically inserted here -->
            </div>
          </div>
        </div>

        <div class="sidebar-section">
          <div class="sidebar-title">
            <i class="fa-solid fa-trophy"></i> Next Milestone
          </div>
          <div class="milestone-card achievement-card">
            <div class="achievement-icon"><i class="fa-solid fa-medal"></i></div>
            <div class="achievement-title">Elite Hacker</div>
            <div class="achievement-desc">Reach top 50 on leaderboard</div>
            <div class="achievement-progress">
              <div class="achievement-progress-bar" style="width: 65%"></div>
            </div>
          </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="fa-solid fa-chart-pie"></i> Overall Progress
            </div>
            <div class="progress-radial-card">
                <div id="overallProgressChart" class="chart-container"></div>
                <div class="stat-label">Challenges Solved</div>
            </div>
        </div>
      </aside>

      <main class="main-content">
        <div class="welcome-banner">
          <h2 class="welcome-title" id="welcome-title">Welcome back...</h2>
          <p class="welcome-subtitle" id="welcome-subtitle">Loading...</p>
        </div>

        <div class="score-cards">
          <div class="score-card">
            <div class="score-card-title"><i class="fa-solid fa-star"></i> Total Score</div>
            <div class="score-card-value" id="score-total">--</div>
            <div class="score-card-change positive">
              <span><i class="fa-solid fa-arrow-up"></i></span> +250 (24h) <!-- Placeholder -->
            </div>
          </div>
          <div class="score-card">
            <div class="score-card-title"><i class="fa-solid fa-terminal"></i> Challenges Solved</div>
            <div class="score-card-value" id="score-solved">--</div>
            <div class="score-card-change positive">
              <span><i class="fa-solid fa-arrow-up"></i></span> +5 (last week) <!-- Placeholder -->
            </div>
          </div>
          <div class="score-card">
            <div class="score-card-title"><i class="fa-solid fa-ranking-star"></i> Current Rank</div>
            <div class="score-card-value" id="score-rank">--</div>
            <div class="score-card-change positive">
              <span><i class="fa-solid fa-arrow-up"></i></span> +3 (overall) <!-- Placeholder -->
            </div>
          </div>
          <div class="score-card">
            <div class="score-card-title"><i class="fa-solid fa-hourglass-start"></i> Time Spent</div>
            <div class="score-card-value" id="score-time">--</div>
            <div class="score-card-change positive">
              <span><i class="fa-solid fa-arrow-up"></i></span> +10h (this week) <!-- Placeholder -->
            </div>
          </div>
        </div>

        <div class="charts-section">
          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title"><i class="fa-solid fa-chart-area"></i> Score Progression</h3>
              <div class="chart-period">
                <button class="period-btn" data-period="7d">7D</button>
                <button class="period-btn" data-period="30d">30D</button>
                <button class="period-btn" data-period="90d">90D</button>
                <button class="period-btn active" data-period="all">ALL</button>
              </div>
            </div>
            <div class="chart-container"> <canvas id="scoreProgressionChart"></canvas>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title"><i class="fa-solid fa-cubes"></i> Challenges by Category</h3>
            </div>
            <div class="chart-container"> <canvas id="challengesByCategoryChart"></canvas>
            </div>
          </div>
        </div>

        <div class="achievements-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fa-solid fa-award"></i> Latest Achievements</h2>
            <a href="achievements.html" class="view-all">View All <i class="fa-solid fa-chevron-right"></i></a>
          </div>
          <div class="achievements-grid" id="achievements-grid">
            <div style="text-align: center; padding: 2rem; color: var(--text-dim);">Loading achievements...</div>
          </div>
        </div>

        <div class="achievements-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h2>
                <a href="#" class="view-all">View All <i class="fa-solid fa-chevron-right"></i></a>
            </div>
            <div class="activity-list" id="activity-list">
                <div style="text-align: center; padding: 2rem; color: var(--text-dim);">Loading activity...</div>
            </div>
        </div>

        <div class="achievements-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fa-solid fa-users"></i> Leaderboard Preview</h2>
                <a href="leaderboard.php" class="view-all">Full Leaderboard <i class="fa-solid fa-chevron-right"></i></a>
            </div>
            <div class="leaderboard-list" id="leaderboardList">
                <div style="text-align: center; padding: 2rem; color: var(--text-dim);">Loading leaderboard...</div>
            </div>
        </div>

      </main>
    </div>
  </div>

  <footer>
    <div class="container">
      <p class="copyright">&copy; 2025 Sudo Society CTF. All rights reserved. Developed by JENSLIN</p>
    </div>
  </footer>

  <script>
    // --- Configuration: UPDATE THIS URL to your API endpoint ---
    const API_URL = 'http://192.168.1.2/Sudo_society_beta/api/api.php'; // Ensure this points to your dashapi.php
    document.getElementById('logout').href = "http://192.168.1.2/Sudo_society_beta/logout.php"; // Update this if logout.php path changes

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
            return data.data || data; // Return the 'data' key or the whole response if 'data' is not present (for challengesByCategory)
        } catch (error) {
            console.error(`Error fetching data from ${endpoint}:`, error);
            return null;
        }
    }

    // --- Chart instances (will be initialized later) ---
    let scoreProgressionChartInstance;
    let challengesByCategoryChartInstance;
    let overallProgressChart; // ApexCharts instance

    // --- 1. Render Score Progression Chart (Chart.js) ---
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
            messageDiv.style.color = 'var(--text-dim)';
            messageDiv.style.paddingTop = '100px';
            messageDiv.textContent = 'No score data available for this period.';
            chartContainer.appendChild(messageDiv);

            return;
        }

        chartElement.style.display = 'block';

        const scores = chartData.map(item => item.score);
        const timestamps = chartData.map(item => new Date(item.timestamp).toLocaleDateString('en-US', { day: 'numeric', month: 'short' }));

        const data = {
            labels: timestamps,
            datasets: [{
                label: 'Score',
                data: scores,
                borderColor: 'var(--neon-green)',
                backgroundColor: 'rgba(0, 255, 136, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'var(--neon-green)',
                pointBorderColor: 'var(--dark-bg)',
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
                    backgroundColor: 'var(--darker-bg)',
                    borderColor: 'var(--neon-green)',
                    borderWidth: 1,
                    titleColor: 'var(--neon-green)',
                    bodyColor: 'var(--text-light)',
                    titleFont: { family: "'Share Tech Mono', monospace" },
                    bodyFont: { family: "'Share Tech Mono', monospace" },
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(192, 252, 204, 0.1)',
                        borderColor: 'rgba(192, 252, 204, 0.2)'
                    },
                    ticks: {
                        color: 'var(--text-dim)',
                        font: { family: "'Share Tech Mono', monospace" }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(192, 252, 204, 0.1)',
                        borderColor: 'rgba(192, 252, 204, 0.2)'
                    },
                    ticks: {
                        color: 'var(--text-dim)',
                        font: { family: "'Share Tech Mono', monospace" }
                    }
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

    // --- 2. Render Challenges by Category Chart (Chart.js) ---
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
            messageDiv.style.color = 'var(--text-dim)';
            messageDiv.style.paddingTop = '100px';
            messageDiv.textContent = 'No solved challenges data available.';
            chartContainer.appendChild(messageDiv);

            return;
        }

        chartElement.style.display = 'block';

        // Filter out 'Unknown Category' from labels and series
        const filteredLabels = [];
        const filteredSeries = [];
        chartResponse.labels.forEach((label, index) => {
            if (label !== 'Unknown Category') {
                filteredLabels.push(label);
                filteredSeries.push(chartResponse.series[index]);
            }
        });

        // If after filtering, there's no data, show message
        if (filteredSeries.length === 0) {
            if (challengesByCategoryChartInstance) {
                challengesByCategoryChartInstance.destroy();
                challengesByCategoryChartInstance = null;
            }
            chartElement.style.display = 'none';

            let messageDiv = document.createElement('div');
            messageDiv.id = 'challengesByCategoryMessage';
            messageDiv.style.textAlign = 'center';
            messageDiv.style.color = 'var(--text-dim)';
            messageDiv.style.paddingTop = '100px';
            messageDiv.textContent = 'No solved challenges data available for known categories.';
            chartContainer.appendChild(messageDiv);
            return;
        }


        const data = {
            labels: filteredLabels, // Use filtered labels
            datasets: [{
                data: filteredSeries, // Use filtered series
                backgroundColor: [
                    'rgba(0, 255, 136, 0.7)',  // neon-green
                    'rgba(0, 200, 255, 0.7)',  // light blue
                    'rgba(255, 0, 200, 0.7)',  // neon-pink
                    'rgba(255, 150, 0, 0.7)',  // orange
                    'rgba(150, 0, 255, 0.7)',  // purple
                    'rgba(0, 100, 255, 0.7)'   // darker blue
                ],
                borderColor: 'rgba(10, 10, 10, 0.8)',
                borderWidth: 2
            }]
        };

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: 'var(--text-dim)',
                        font: { family: "'Share Tech Mono', monospace" }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed + ' Chals';
                            }
                            return label;
                        }
                    },
                    backgroundColor: 'var(--darker-bg)',
                    borderColor: 'var(--neon-green)',
                    borderWidth: 1,
                    titleColor: 'var(--neon-green)',
                    bodyColor: 'var(--text-light)',
                    titleFont: { family: "'Share Tech Mono', monospace" },
                    bodyFont: { family: "'Share Tech Mono', monospace" },
                }
            },
            cutout: '70%'
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

    // --- 3. Render Overall Progress Radial Bar (ApexCharts) ---
    async function renderOverallProgressChart(solvedCount) {
        const totalChallenges = await fetchData('getTotalChallenges');
        const chartElement = document.getElementById('overallProgressChart');

        if (totalChallenges === null || totalChallenges === 0) {
            chartElement.innerHTML = '<div style="text-align: center; color: var(--text-dim); padding-top: 50px;">N/A</div>';
            if (overallProgressChart) overallProgressChart.destroy();
            overallProgressChart = null;
            return;
        }

        const progressPercent = Math.round((solvedCount / totalChallenges) * 100);

        const options = {
            series: [progressPercent],
            chart: {
                height: 200,
                type: 'radialBar',
                sparkline: { enabled: true },
                toolbar: { show: false }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: 'rgba(0, 255, 136, 0.1)',
                        strokeWidth: '97%',
                        margin: 5,
                    },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: -5,
                            fontSize: '22px',
                            fontFamily: 'Orbitron',
                            color: 'var(--neon-green)',
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

    // --- 4. Populate Latest Achievements ---
    async function populateAchievements() {
        const achievements = await fetchData('getLatestAchievements');
        const achievementsGrid = document.getElementById('achievements-grid');
        achievementsGrid.innerHTML = '';

        if (!achievements || achievements.length === 0) {
            achievementsGrid.innerHTML = '<div style="text-align: center; color: var(--text-dim);">No achievements earned yet.</div>';
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

    // --- 5. Populate Recent Activity ---
    async function populateRecentActivity() {
        const recentActivity = await fetchData('getRecentActivity');
        const activityList = document.getElementById('activity-list');
        activityList.innerHTML = '';

        if (!recentActivity || recentActivity.length === 0) {
            activityList.innerHTML = '<div style="text-align: center; color: var(--text-dim);">No recent activity.</div>';
            return;
        }

        recentActivity.forEach(activity => {
            let iconClass = '';
            // Assign icons based on activity type
            if (activity.activity_type === 'solved') iconClass = 'fa-check';
            else if (activity.activity_type === 'rank_update') iconClass = 'fa-chart-line';
            else if (activity.activity_type === 'achievement_unlocked') iconClass = 'fa-award'; // Corrected activity type
            else if (activity.activity_type === 'flag') iconClass = 'fa-flag';
            else iconClass = 'fa-question'; // Default icon

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

    // --- 6. Populate Leaderboard Preview ---
    async function populateLeaderboardPreview(currentUser) {
        const leaderboardData = await fetchData('getLeaderboard');
        const leaderboardList = document.getElementById('leaderboardList');
        leaderboardList.innerHTML = '';

        if (!leaderboardData || leaderboardData.length === 0) {
            leaderboardList.innerHTML = '<div style="text-align: center; color: var(--text-dim);">Leaderboard is empty.</div>';
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

            item.innerHTML = `
                <span class="rank">#${player.rank}</span>
                <span class="username">${player.username}</span>
                <span class="score">${player.score}</span>
            `;
            leaderboardList.appendChild(item);
        });
    }

    // --- 7. Render Streak Calendar ---
    function renderStreakCalendar(lastSolvedDateStr, dailyStreak) {
        const streakCalendar = document.getElementById('streak-calendar');
        streakCalendar.innerHTML = ''; // Clear existing days

        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalize to start of day

        let lastSolvedDate = null;
        if (lastSolvedDateStr) {
            lastSolvedDate = new Date(lastSolvedDateStr);
            lastSolvedDate.setHours(0, 0, 0, 0); // Normalize
        }

        // Generate days for the last 7 days including today
        for (let i = 6; i >= 0; i--) {
            const day = new Date(today);
            day.setDate(today.getDate() - i);
            
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('streak-day');
            dayDiv.textContent = day.getDate(); // Display day number

            // Check if this day is part of the streak
            if (lastSolvedDate) {
                // Calculate difference in days
                const diffTime = Math.abs(day.getTime() - lastSolvedDate.getTime());
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                // A simple way to check if a day *could* be part of the streak
                // This is a visual representation, the backend handles true streak logic
                if (day.getTime() <= today.getTime() && day.getTime() >= (new Date(today).setDate(today.getDate() - (dailyStreak - 1))) ) {
                    // This is a very basic visual representation.
                    // A more robust one would involve fetching a list of solved dates from the backend.
                    // For now, it assumes the streak is consecutive up to lastSolvedDate.
                    const tempDate = new Date(lastSolvedDate);
                    tempDate.setDate(lastSolvedDate.getDate() - (dailyStreak - 1)); // Start of the streak
                    if (day.getTime() >= tempDate.getTime() && day.getTime() <= lastSolvedDate.getTime()) {
                        dayDiv.classList.add('active');
                    }
                }
            }

            // Highlight today
            if (day.toDateString() === today.toDateString()) {
                dayDiv.classList.add('today');
            }

            streakCalendar.appendChild(dayDiv);
        }
    }


    // --- Main function to fetch all data and populate the dashboard ---
    async function populateDashboard() {
        const userStats = await fetchData('getUserStats');

        if (userStats) {
            document.getElementById('username').textContent = userStats.username;
            // Set header user avatar, fallback to default if not provided by backend
            const headerUserAvatar = document.getElementById('headerUserAvatar');
            if (headerUserAvatar) {
                headerUserAvatar.src = userStats.avatar_url || 'https://i.imgur.com/JqYeSzn.png';
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

            // Render streak calendar
            renderStreakCalendar(userStats.last_solved_date, userStats.daily_streak);

            // Fetch and render other sections concurrently
            await Promise.all([
                renderScoreProgressionChart('all'),
                renderChallengesByCategoryChart(),
                renderOverallProgressChart(userStats.challenges_solved),
                populateAchievements(),
                populateRecentActivity(),
                populateLeaderboardPreview(userStats.username)
            ]);

            // Animate achievement progress bars
            document.querySelectorAll('.achievement-progress-bar').forEach(bar => {
                const targetWidth = bar.style.width;
                bar.style.width = '0%'; // Reset to 0 for animation
                setTimeout(() => {
                    bar.style.width = targetWidth; // Animate to target
                }, 500);
            });

        } else {
            // Display error messages on relevant sections
            const errorMessage = "Failed to load dashboard data. Please try again later or contact support.";
            document.getElementById('username').textContent = 'Error';
            document.getElementById('welcome-title').textContent = "Error Loading Dashboard";
            document.getElementById('welcome-subtitle').textContent = errorMessage;
            
            // Clear and display error on other sections
            document.getElementById('stat-challenges').textContent = '--';
            document.getElementById('stat-points').textContent = '--';
            document.getElementById('stat-streak').textContent = '--';
            document.getElementById('stat-rank').textContent = '--';
            document.getElementById('streak-count').textContent = '--';

            document.getElementById('score-total').textContent = '--';
            document.getElementById('score-solved').textContent = '--';
            document.getElementById('score-rank').textContent = '--';
            document.getElementById('score-time').textContent = '--';

            document.getElementById('streak-calendar').innerHTML = '<div style="text-align: center; width: 100%; color: var(--text-dim);">No streak data.</div>';
            document.getElementById('achievements-grid').innerHTML = '<div style="text-align: center; color: var(--text-dim);">Failed to load achievements.</div>';
            document.getElementById('activity-list').innerHTML = '<div style="text-align: center; color: var(--text-dim);">Failed to load activity.</div>';
            document.getElementById('leaderboardList').innerHTML = '<div style="text-align: center; color: var(--text-dim);">Failed to load leaderboard.</div>';

            // Destroy charts if they were initialized
            if (scoreProgressionChartInstance) scoreProgressionChartInstance.destroy();
            if (challengesByCategoryChartInstance) challengesByCategoryChartInstance.destroy();
            if (overallProgressChart) overallProgressChart.destroy();
        }
    }

    // --- Event listeners for chart period buttons (Score Progression) ---
    document.querySelectorAll('.period-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const period = this.dataset.period;
            renderScoreProgressionChart(period);
        });
    });

    // --- Simple dropdown toggle (placeholder, can be expanded) ---
    function toggleDropdown() {
      console.log("User profile dropdown toggled!");
      // Implement actual dropdown visibility logic here (e.g., show a small menu)
    }

    // --- Initialize the Dashboard on page load ---
    document.addEventListener('DOMContentLoaded', populateDashboard);
  </script>
</body>
</html>