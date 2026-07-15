<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events | Sudo Society CTF</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <style>
    
    :root {
      --neon-green: #00ff88;
      --neon-pink: #ff00c8;
      --neon-blue: #00ffff;
      --dark-bg: #0a0a0a;
      --darker-bg: #050505;
      --card-bg: rgba(20, 20, 20, 0.7);
      --glow: 0 0 15px rgba(0, 255, 136, 0.5);
      --glow-intense: 0 0 25px rgba(0, 255, 136, 0.9);
      --danger-red: #ff0000;
      --warning-orange: #ffc800;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Share Tech Mono', monospace;
      background-color: var(--dark-bg);
      color: #c0fccc;
      overflow-x: hidden;
      background-image:
        radial-gradient(circle at 20% 30%, rgba(0, 255, 136, 0.05) 0%, transparent 25%),
        radial-gradient(circle at 80% 70%, rgba(0, 200, 255, 0.05) 0%, transparent 25%);
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
      border-bottom: 1px solid rgba(0, 255, 136, 0.2);
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
      text-shadow: var(--glow);
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
      box-shadow: var(--glow);
    }

    .username {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-weight: 700;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
    }

    .nav-links a {
      color: #c0fccc;
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
      text-shadow: var(--glow);
    }

    .nav-links a.active::after {
      width: 100%;
    }

    /* === EVENTS CONTENT === */
    .events-content {
      background: var(--card-bg);
      border: 1px solid rgba(0, 255, 136, 0.2);
      border-radius: 8px;
      padding: 2rem;
      backdrop-filter: blur(5px);
      margin: 2rem auto;
      max-width: 1400px;
    }

    .events-header {
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(0, 255, 136, 0.2);
      text-align: center;
    }

    .events-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 2.2rem;
      margin-bottom: 0.5rem;
      text-shadow: var(--glow-intense);
    }

    .events-subtitle {
      color: rgba(192, 252, 204, 0.7);
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
    }

    .section-title {
      font-family: 'Orbitron', sans-serif;
      color: var(--neon-green);
      font-size: 1.5rem;
      margin-top: 2.5rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }

    .section-title::after {
      content: '';
      flex: 1;
      height: 1px;
      background: linear-gradient(90deg, var(--neon-green), transparent);
      margin-left: 1rem;
    }

    /* === EVENT CARD GRID === */
    .event-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .event-card {
      background: rgba(0, 255, 136, 0.05);
      border: 1px solid rgba(0, 255, 136, 0.2);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
      transition: all 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      display: flex;
      flex-direction: column;
    }

    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 20px rgba(0, 255, 136, 0.7);
    }

    .event-card-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 1px solid rgba(0, 255, 136, 0.2);
    }

    .event-card-content {
      padding: 1.5rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .event-card-title {
      font-family: 'Orbitron', sans-serif;
      font-size: 1.2rem;
      color: var(--neon-blue);
      margin-bottom: 0.5rem;
    }

    .event-card-meta {
      font-size: 0.85rem;
      color: rgba(192, 252, 204, 0.8);
      margin-bottom: 1rem;
    }
    .event-card-meta span {
        display: block;
        margin-bottom: 0.2rem;
    }

    .event-card-description {
      font-size: 0.9rem;
      color: #c0fccc;
      margin-bottom: 1rem;
      flex-grow: 1;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .event-card-countdown, .event-card-status {
      font-family: 'Orbitron', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      text-align: center;
      padding: 0.5rem 0;
      border-top: 1px solid rgba(0, 255, 136, 0.1);
      margin-top: 1rem;
    }

    .event-card-countdown {
      color: var(--neon-green);
      text-shadow: 0 0 8px rgba(0, 255, 136, 0.5);
    }

    .event-card-status {
      color: var(--neon-pink);
      text-shadow: 0 0 8px rgba(255, 0, 200, 0.5);
    }

    .no-events-message {
        text-align: center;
        color: rgba(192, 252, 204, 0.5);
        padding: 2rem 0;
    }

    /* === FOOTER === */
    footer {
      padding: 2rem 0;
      background: rgba(5, 5, 5, 0.95);
      border-top: 1px solid rgba(0, 255, 136, 0.2);
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
      background: linear-gradient(90deg, transparent, var(--neon-green), transparent);
      box-shadow: 0 0 10px var(--neon-green);
    }

    .copyright {
      color: rgba(192, 252, 204, 0.6);
      font-size: 0.9rem;
    }

    /* --- Custom Modal CSS --- */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        visibility: hidden;
        opacity: 0;
        transition: visibility 0.3s, opacity 0.3s ease-in-out;
    }

    .custom-modal-overlay.show {
        visibility: visible;
        opacity: 1;
    }

    .custom-modal-card {
        background: var(--card-bg);
        border: 1px solid var(--neon-green);
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
        max-width: 700px;
        min-width: 300px;
        text-align: center;
        transform: translateY(-50px) scale(0.8);
        opacity: 0;
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        color: #c0fccc;
        backdrop-filter: blur(8px);
    }

    .custom-modal-overlay.show .custom-modal-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .custom-modal-header {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.5rem;
        color: var(--neon-blue);
        margin-bottom: 1rem;
        text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
    }

    .custom-modal-message {
        font-size: 1em;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        color: #c0fccc;
    }

    /* Enhanced Modal Event Details */
    .modal-event-image-container {
        width: 100%;
        height: 200px;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid rgba(0, 255, 136, 0.2);
    }

    .modal-event-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-event-meta {
        margin-bottom: 1.5rem;
    }

    .modal-event-meta p {
        margin-bottom: 0.5rem;
        display: flex;
    }

    .modal-event-meta .label {
        min-width: 100px;
        color: var(--neon-green);
    }

    .modal-event-meta .value {
        flex: 1;
    }

    .modal-section-title {
        font-family: 'Orbitron', sans-serif;
        color: var(--neon-blue);
        font-size: 1.1rem;
        margin: 1.5rem 0 0.5rem 0;
        padding-bottom: 0.3rem;
        border-bottom: 1px solid rgba(0, 255, 136, 0.2);
    }

    .modal-countdown-container, 
    .modal-status-container {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 255, 136, 0.1);
    }

    .modal-countdown-label {
        font-size: 0.9rem;
        color: rgba(192, 252, 204, 0.7);
        margin-bottom: 0.3rem;
    }

    .results-link {
        display: inline-block;
        margin-top: 0.5rem;
        color: var(--neon-blue);
        text-decoration: none;
        font-family: 'Orbitron', sans-serif;
        transition: all 0.2s ease;
    }

    .results-link:hover {
        text-shadow: 0 0 8px rgba(0, 255, 255, 0.5);
    }

    .modal-additional-info {
        margin-top: 1.5rem;
        padding: 1rem;
        background: rgba(0, 255, 136, 0.05);
        border: 1px solid rgba(0, 255, 136, 0.1);
        border-radius: 4px;
    }

    .custom-modal-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .custom-modal-btn {
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
    }

    .custom-modal-btn.primary {
        background: linear-gradient(45deg, var(--neon-green), var(--neon-blue));
        color: var(--darker-bg);
        box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
    }

    .custom-modal-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 255, 136, 0.8);
    }

    .custom-modal-btn.secondary {
        background: transparent;
        color: var(--neon-green);
        border: 1px solid var(--neon-green);
    }

    .custom-modal-btn.secondary:hover {
        background: rgba(0, 255, 136, 0.1);
        box-shadow: 0 0 8px rgba(0, 255, 136, 0.3);
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
      .header-content {
        flex-direction: column;
        gap: 1rem;
      }
      .user-menu {
        width: 100%;
        justify-content: space-between;
      }
      .events-content {
          padding: 1rem;
      }
      .events-title {
          font-size: 1.8rem;
      }
      .events-subtitle {
          font-size: 0.9rem;
      }
      .event-grid {
          grid-template-columns: 1fr;
      }
      .event-card {
          padding-bottom: 1rem;
      }
      .event-card-title {
          font-size: 1.1rem;
      }
      .event-card-meta, .event-card-description {
          font-size: 0.85rem;
      }
      .event-card-countdown, .event-card-status {
          font-size: 0.9rem;
      }
      .custom-modal-card {
          max-width: 95%;
          padding: 20px;
      }
      .modal-event-meta p {
          flex-direction: column;
      }
      .modal-event-meta .label {
          min-width: auto;
          margin-bottom: 0.2rem;
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
          <img src="sudo_society.png" alt="Sudo Society Logo" class="logo-img">
          <span class="logo-text">Sudo Society</span>
        </div>
        <div class="user-menu">
          <nav class="nav-links">
            <!-- <a href="dashboard.php">Dashboard</a>
            <a href="challenges.html">Challenges</a> -->
            <!-- <a href="leaderboard.html">Leaderboard</a>
            <a href="settings.html">Settings</a>
            <a href="billing-history.html">Billing</a>
            <a href="reward-place.html">Rewards</a>
            <a href="lab-place.html">Labs</a> -->
            <a href="event.php" class="active">Events</a>
          </nav>
          <div class="user-profile" onclick="toggleDropdown()">
            <img src="<?php print_r($_SESSION['avatar_url']); ?>" alt="User Avatar" class="user-avatar" id="headerUserAvatar">
            <span class="username" id="headerUsername"><?php print_r($_SESSION['username']); ?></span>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="container">
    <main class="events-content">
      <div class="events-header">
        <h1 class="events-title">Sudo Society Events</h1>
        <p class="events-subtitle">Stay updated with our upcoming CTFs, webinars, and community meetups.</p>
      </div>

      <section id="upcoming-events-section">
        <h2 class="section-title"><i>📅</i> Upcoming Events</h2>
        <div class="event-grid" id="upcomingEventsGrid">
          <!-- Upcoming events will be populated here by JavaScript -->
          <p class="no-events-message" id="loadingUpcoming">Loading upcoming events...</p>
        </div>
        <p class="no-events-message" id="noUpcomingEvents" style="display: none;">
            No upcoming events at the moment. Check back soon!
        </p>
      </section>

      <section id="past-events-section">
        <h2 class="section-title"><i>📜</i> Past Events</h2>
        <div class="event-grid" id="pastEventsGrid">
          <!-- Past events will be populated here by JavaScript -->
          <p class="no-events-message" id="loadingPast">Loading past events...</p>
        </div>
        <p class="no-events-message" id="noPastEvents" style="display: none;">
            No past events recorded yet.
        </p>
      </section>
    </main>
  </div>

  <footer>
    <div class="container">
      <p class="copyright">&copy; 2025 Sudo Society CTF. All rights reserved. Developed by JENSLIN </p>
    </div>
  </footer>

  <!-- Custom Modal -->
  <div id="customModalOverlay" class="custom-modal-overlay">
    <div id="customModalCard" class="custom-modal-card">
      <h3 id="customModalHeader" class="custom-modal-header"></h3>
      <div id="customModalMessage" class="custom-modal-message"></div>
      <div id="customModalButtons" class="custom-modal-buttons">
        <!-- Buttons will be injected here by JavaScript -->
      </div>
    </div>
  </div>

  <script>
    // --- Global Variables ---
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
    const eventsBackendUrl = 'http://192.168.1.2/Sudo_society_beta/api/get_events.php';

    // --- Modal Functions ---
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

    // --- Event Handling Functions ---
    function showEventDetails(event) {
        let buttonsConfig = [{ 
            text: 'Close', 
            classList: ['secondary'],
            onClick: () => console.log('Event details closed')
        }];
        
        let countdownOrStatusHtml = '';
        let registrationSection = '';
        let eventImageUrl = event.image_url || 'https://placehold.co/600x300/00ff88/0a0a0a?text=Event+Image';

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
                     onerror="this.onerror=null;this.src='https://placehold.co/600x300/00ff88/0a0a0a?text=Event+Image'">
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

    function updateCountdown(element, targetDateString) {
        const targetDate = new Date(targetDateString).getTime();
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            element.textContent = 'Event Concluded';
            element.classList.remove('event-card-countdown');
            element.classList.add('event-card-status');
            fetchEventsFromBackend();
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        element.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }

    // --- Event Data Functions ---
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
                    <li>Is your PHP web server running on <code>http://192.168.1.2</code>?</li>
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

    function createEventCard(event, isUpcoming) {
        const card = document.createElement('div');
        card.classList.add('event-card');
        card.dataset.eventId = event.id;

        const imageUrl = event.image_url || `https://placehold.co/600x300/00ff88/0a0a0a?text=${event.type || 'Event'}`;

        card.innerHTML = `
            <img src="${imageUrl}" alt="${event.name}" class="event-card-image" onerror="this.onerror=null;this.src='https://placehold.co/600x300/00ff88/0a0a0a?text=Event+Image';">
            <div class="event-card-content">
                <div>
                    <h3 class="event-card-title">${event.name}</h3>
                    <p class="event-card-meta">
                        <span>Type: ${event.type || 'General'}</span>
                        <span>Date: ${event.date_str || 'N/A'} at ${event.time_str || 'N/A'}</span>
                        <span>Location: ${event.location || 'N/A'}</span>
                    </p>
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

    // --- Event Listeners ---
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

    function toggleDropdown() {
      console.log("User profile clicked! (Dropdown functionality not implemented)");
    }

    // --- Initialization ---
    document.addEventListener('DOMContentLoaded', () => {
      const navLinks = document.querySelectorAll('.nav-links a');
      navLinks.forEach(link => {
        if (link.getAttribute('href') === 'events.html') {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });

      // document.getElementById('headerUsername').textContent = 'hacker1337';
      // const headerUserAvatar = document.getElementById('headerUserAvatar');
      // if (headerUserAvatar && !headerUserAvatar.src) {
      //   headerUserAvatar.src = 'https://i.imgur.com/JqYeSzn.png';
      // }

      fetchEventsFromBackend();
    });
  </script>
</body>
</html>