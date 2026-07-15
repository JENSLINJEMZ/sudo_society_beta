<?php
session_start();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenslins Adventure Leaderboard | Sudo Society</title>
    <!-- Google Fonts: Orbitron for titles, Share Tech Mono for body text -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --primary-color: #00ff88; /* Neon Green */
            --secondary-color: #00ffff; /* Neon Blue */
            --accent-color: #ff00c8; /* Neon Pink */
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: rgba(20, 20, 20, 0.7);
            --border-color: rgba(0, 255, 136, 0.2);
            --text-light: #c0fccc;
            --text-dark: #0a0a0a;
            --glow-light: 0 0 8px rgba(0, 255, 136, 0.5);
            --glow-medium: 0 0 15px rgba(0, 255, 136, 0.7);
            --glow-intense: 0 0 25px rgba(0, 255, 136, 0.9);

            /* Leaderboard specific colors */
            --gold-medal: #FFD700;
            --silver-medal: #C0C0C0;
            --bronze-medal: #CD7F32;
            --table-header-bg: rgba(0, 255, 136, 0.1);
            --table-row-even-bg: rgba(0, 255, 136, 0.02);
            --table-row-odd-bg: rgba(0, 255, 136, 0.05);
            --table-row-hover-bg: rgba(0, 255, 136, 0.15);
        }

        /* Basic Reset & Box Model */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: 'Share Tech Mono', monospace;
            background-color: var(--dark-bg);
            color: var(--text-light);
            overflow-x: hidden; /* Prevent horizontal scroll */
            background-image:
                radial-gradient(circle at 20% 30%, rgba(0, 255, 136, 0.05) 0%, transparent 25%),
                radial-gradient(circle at 80% 70%, rgba(0, 200, 255, 0.05) 0%, transparent 25%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* === SCANLINE EFFECT === */
        /* Creates a subtle animated scanline effect for retro/cyber feel */
        .scanline {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to bottom, transparent, var(--primary-color), transparent);
            animation: scan 4s linear infinite;
            z-index: 1000;
            pointer-events: none; /* Allows clicks to pass through */
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
            backdrop-filter: blur(10px); /* Frosted glass effect */
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
            filter: drop-shadow(0 0 8px var(--primary-color)); /* Neon glow for logo */
        }

        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: var(--glow-light);
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: flex-start; /* Align to left */
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
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a.active {
            color: var(--primary-color);
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
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            object-fit: cover;
            box-shadow: var(--glow-light);
        }

        .username {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary-color);
            font-weight: 700;
        }

        /* === MAIN CONTENT === */
        main {
            flex-grow: 1; /* Allows main content to take available space */
            padding: 2rem 0;
        }

        .leaderboard-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            backdrop-filter: blur(5px);
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .leaderboard-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--primary-color);
            text-shadow: var(--glow-intense);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .leaderboard-title i {
            font-size: 2.2rem;
            color: var(--secondary-color);
        }

        /* Top 3 Podium Styling */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end; /* Align to bottom for podium effect */
            gap: 2rem;
            margin-bottom: 3rem;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .podium-card {
            background: var(--darker-bg);
            border: 1px solid;
            border-radius: 12px;
            padding: 1.5rem; /* Reverted to original padding */
            width: 250px;
            text-align: center;
            box-shadow: var(--glow-light);
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
            /* No longer need flex properties for image overlay */
        }

        .podium-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--glow-medium);
        }

        .podium-card.second {
            border-color: var(--silver-medal);
            height: 200px; /* Shorter for 2nd place */
            margin-bottom: 1rem;
        }
        .podium-card.first {
            border-color: var(--gold-medal);
            height: 240px; /* Tallest for 1st place */
            margin-bottom: 0;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.6); /* More intense glow for gold */
        }
        .podium-card.third {
            border-color: var(--bronze-medal);
            height: 180px; /* Shortest for 3rd place */
            margin-bottom: 2rem;
        }

        /* Re-enabled and styled medal icons for podium cards */
        .podium-card .medal-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 0.8rem;
            display: block; /* Ensure icons are visible */
            font-size: 3rem; /* Make icons larger */
        }
        .podium-card.first .medal-icon { color: var(--gold-medal); text-shadow: 0 0 15px var(--gold-medal); }
        .podium-card.second .medal-icon { color: var(--silver-medal); text-shadow: 0 0 10px var(--silver-medal); }
        .podium-card.third .medal-icon { color: var(--bronze-medal); text-shadow: 0 0 8px var(--bronze-medal); }


        .podium-card .username {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem; /* Reverted to original size */
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
            text-shadow: 0 0 5px rgba(0, 255, 255, 0.3);
        }
        .podium-card.first .username { color: var(--gold-medal); }
        .podium-card.second .username { color: var(--silver-medal); }
        .podium-card.third .username { color: var(--bronze-medal); }


        .podium-card .points {
            font-size: 1.1rem; /* Reverted to original size */
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.3rem;
        }

        .podium-card .solves {
            font-size: 0.9rem; /* Reverted to original size */
            color: rgba(192, 252, 204, 0.8);
        }

        /* Removed profile image as background for podium cards */
        .podium-profile-bg-img {
            display: none; /* Hide the background image */
        }

        /* Removed podium-content div styling as it's no longer needed */
        .podium-content {
            /* No specific styling needed as content is direct child of podium-card */
        }

        /* Loading Spinner */
        .loading-spinner {
            border: 4px solid rgba(0, 255, 136, 0.3);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 3rem auto;
            display: none; /* Hidden by default */
        }

        .loading-spinner.show {
            display: block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Leaderboard Table */
        .leaderboard-table-container {
            overflow-x: auto; /* Enable horizontal scroll for table on small screens */
            margin-top: 2rem;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px; /* Ensure table doesn't shrink too much */
        }

        .leaderboard-table th,
        .leaderboard-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid rgba(0, 255, 136, 0.1);
        }

        .leaderboard-table th {
            background-color: var(--table-header-bg);
            font-family: 'Orbitron', sans-serif;
            color: var(--secondary-color);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .leaderboard-table tbody tr:nth-child(even) {
            background-color: var(--table-row-even-bg);
        }

        .leaderboard-table tbody tr:nth-child(odd) {
            background-color: var(--table-row-odd-bg);
        }

        .leaderboard-table tbody tr:hover {
            background-color: var(--table-row-hover-bg);
            box-shadow: inset 0 0 10px rgba(0, 255, 136, 0.2);
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .leaderboard-table .rank {
            font-weight: bold;
            color: var(--primary-color);
        }

        .leaderboard-table .username-col {
            font-family: 'Orbitron', sans-serif;
            color: var(--text-light);
        }

        /* === FOOTER === */
        footer {
            padding: 2rem 0;
            background: rgba(5, 5, 5, 0.95);
            border-top: 1px solid var(--border-color);
            text-align: center;
            position: relative;
            margin-top: 3rem;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
            box-shadow: 0 0 10px var(--primary-color);
        }

        .copyright {
            color: rgba(192, 252, 204, 0.6);
            font-size: 0.9rem;
        }

        /* --- MEDIA QUERIES FOR RESPONSIVENESS --- */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
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
            .leaderboard-title {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 0.5rem;
            }
            .leaderboard-title i {
                font-size: 1.8rem;
            }
            .podium-container {
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
            }
            .podium-card {
                width: 90%; /* Make cards wider on small screens */
                max-width: 300px;
                height: auto !important; /* Reset fixed height for responsiveness */
                padding: 1rem;
            }
            .podium-card .username {
                font-size: 1.1rem;
            }
            .podium-card .points {
                font-size: 1rem;
            }
            .podium-card .solves {
                font-size: 0.85rem;
            }
            .leaderboard-table-container {
                overflow-x: scroll; /* Ensure table is scrollable if content overflows */
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
                    <img src="sudo_society.png" alt="Sudo Society Logo" class="logo-img">
                    <span class="logo-text">Sudo Society</span>
                </div>
                <div class="user-menu">
                    <nav class="nav-links">
                        <a href="dashboard.php">Dashboard</a>
                        <!-- <a href="challenges.html">Challenges</a> -->
                        <a href="leaderboard.php" class="active">Leaderboard</a> <!-- Active link for this page -->
                        <a href="jenslin_little_advangure.php">Jenslins Little Adventure</a>
                    </nav>
                    <div class="user-profile" id="headerUserProfile">
                        <!-- Placeholder user avatar -->
                        <img src="<?php print_r($_SESSION['avatar_url']); ?>" alt="User Avatar" class="user-avatar" id="headerUserAvatar">
                        <span class="username" id="headerUsername">Guest</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="leaderboard-section">
            <h1 class="leaderboard-title">
                <i class="fas fa-trophy"></i> Jenslin's Adventure Leaderboard <i class="fas fa-trophy"></i>
            </h1>

            <div class="podium-container" id="podiumContainer">
                <!-- Podium cards will be dynamically loaded here -->
                <p style="text-align: center; width: 100%; color: var(--text-light);">Loading top players...</p>
            </div>

            <div class="loading-spinner" id="loadingSpinner"></div>

            <div class="leaderboard-table-container" id="leaderboardTableContainer">
                <!-- Leaderboard table will be dynamically loaded here -->
                <p style="text-align: center; width: 100%; color: var(--text-light);">Loading leaderboard table...</p>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p class="copyright">&copy; 2025 Sudo Society CTF. All rights reserved. Developed by JENSLIN</p>
        </div>
    </footer>

    <script>
        // Configuration
        // *** IMPORTANT: You MUST change this to the actual URL where your api.php file is hosted. ***
        // Based on your error, it. seems to be: http://192.168.1.2/Sudo_society_beta/api/leaderboard.php
        const BACKEND_API_URL = 'http://192.168.1.2/Sudo_society_beta/api/leaderboard.php'; 

        // --- DOM Element References ---
        const headerUsername = document.getElementById('headerUsername');
        const headerUserAvatar = document.getElementById('headerUserAvatar');
        const podiumContainer = document.getElementById('podiumContainer');
        const leaderboardTableContainer = document.getElementById('leaderboardTableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const navLeaderboard = document.querySelector('.nav-links a[href="leaderboard.html"]'); // Select the correct nav link

        let currentUserId = null; // To store the logged-in user's ID

        /**
         * Dummy function for header user profile click.
         * In a real application, this would toggle a user dropdown menu.
         */
        function toggleDropdown() {
            console.log("User profile clicked! (Dropdown functionality not implemented)");
            // You could add logic here to show/hide a user menu, or redirect to a profile page
        }
        document.getElementById('headerUserProfile').addEventListener('click', toggleDropdown);


        // --- API Calls ---

        /**
         * Fetches login status and user details from the backend.
         */
        async function checkLoginStatus() {
            try {
                const response = await fetch(`${BACKEND_API_URL}?action=checkLoginStatus`);
                const data = await response.json();

                if (data.success && data.loggedIn) {
                    headerUsername.textContent = data.user.username;
                    // Uncomment the line below if your backend provides avatar_url for users
                    // headerUserAvatar.src = data.user.avatar_url || 'https://i.imgur.com/JqYeSzn.png';
                    currentUserId = data.user.id;
                    console.log('User logged in:', data.user.username);
                } else {
                    headerUsername.textContent = 'Guest';
                    headerUserAvatar.src = 'https://i.imgur.com/JqYeSzn.png'; // Default avatar for guest
                    currentUserId = null;
                    console.log('Not logged in:', data.message);
                }
            } catch (error) {
                console.error('Error checking login status:', error);
                headerUsername.textContent = 'Guest (Error)';
                headerUserAvatar.src = 'https://i.imgur.com/JqYeSzn.png';
            }
        }

        /**
         * Fetches leaderboard data from the backend.
         */
        async function fetchLeaderboard() {
            loadingSpinner.classList.add('show'); // Show spinner
            podiumContainer.innerHTML = '<p style="text-align: center; width: 100%; color: var(--text-light);">Loading top players...</p>';
            leaderboardTableContainer.innerHTML = '<p style="text-align: center; width: 100%; color: var(--text-light);">Loading leaderboard table...</p>';

            try {
                const response = await fetch(`${BACKEND_API_URL}?action=getLeaderboard`);
                const data = await response.json();
                console.log('Leaderboard data received:', data); // Log the full data for debugging

                if (data.success && data.data) {
                    renderLeaderboard(data.data);
                } else {
                    const errorMessage = data.error || 'Unknown error fetching leaderboard.';
                    podiumContainer.innerHTML = `<p style="color: var(--accent-color); text-align: center; width: 100%;">Failed to load top players: ${errorMessage}</p>`;
                    leaderboardTableContainer.innerHTML = `<p style="color: var(--accent-color); text-align: center; width: 100%;">Failed to load leaderboard: ${errorMessage}</p>`;
                    console.error('Failed to fetch leaderboard:', errorMessage);
                }
            } catch (error) {
                const errorMessage = error.message || 'Network error.';
                podiumContainer.innerHTML = `<p style="color: var(--accent-color); text-align: center; width: 100%;">Error connecting to backend: ${errorMessage}</p>`;
                leaderboardTableContainer.innerHTML = `<p style="color: var(--accent-color); text-align: center; width: 100%;">Error connecting to backend: ${errorMessage}</p>`;
                console.error('Network error fetching leaderboard:', error);
            } finally {
                loadingSpinner.classList.remove('show'); // Hide spinner
            }
        }

        // --- UI Rendering ---

        /**
         * Renders the top 3 players on the podium and the full leaderboard table.
         * @param {Array<Object>} data - Array of user objects for the leaderboard.
         */
        function renderLeaderboard(data) {
            podiumContainer.innerHTML = ''; // Clear existing podium
            leaderboardTableContainer.innerHTML = ''; // Clear existing table

            if (data.length === 0) {
                podiumContainer.innerHTML = '<p style="text-align: center; width: 100%;">No users on the leaderboard yet.</p>';
                leaderboardTableContainer.innerHTML = '<p style="text-align: center; width: 100%;">No users on the leaderboard yet.</p>';
                return;
            }

            // Render Top 3 Players on Podium
            const top3 = data.slice(0, 3);
            const podiumOrder = [
                { player: top3[1], className: 'second', icon: '<i class="fas fa-medal medal-icon"></i>' }, // 2nd place
                { player: top3[0], className: 'first', icon: '<i class="fas fa-crown medal-icon"></i>' },  // 1st place
                { player: top3[2], className: 'third', icon: '<i class="fas fa-award medal-icon"></i>' }   // 3rd place
            ];

            podiumOrder.forEach(item => {
                if (item.player) { // Ensure player exists
                    const podiumCard = document.createElement('div');
                    podiumCard.classList.add('podium-card', item.className);
                    
                    // Reverted to original structure with icon
                    podiumCard.innerHTML = `
                        ${item.icon}
                        <div class="username">${item.player.username}</div>
                        <div class="points">${item.player.total_score} Points</div>
                        <div class="solves">${item.player.challenges_solved} Solves</div>
                    `;
                    podiumContainer.appendChild(podiumCard);
                }
            });

            // Render the full leaderboard table
            const table = document.createElement('table');
            table.classList.add('leaderboard-table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Score</th>
                        <th>Challenges Solved</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            `;
            leaderboardTableContainer.appendChild(table);
            const tableBody = table.querySelector('tbody');

            data.forEach((player, index) => { // Iterate through all players for the table
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="rank">${index + 1}</td>
                    <td class="username-col">${player.username}</td>
                    <td>${player.total_score}</td>
                    <td>${player.challenges_solved}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // --- Initial Page Load ---
        document.addEventListener('DOMContentLoaded', async () => {
            // Set active navigation link
            if (navLeaderboard) {
                navLeaderboard.classList.add('active');
            }

            // Check login status and then fetch leaderboard
            await checkLoginStatus();
            fetchLeaderboard();
        });
    </script>
</body>
</html>
