
(function() {
    'use strict';

    // ========== THEME TOGGLE ==========
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

    // ========== REGISTER BUTTON ==========
    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) {
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            this.innerHTML = '✅ Access Granted';
            this.style.background = 'var(--btn-primary-bg)';
            this.style.boxShadow = '0 4px 32px rgba(111, 207, 151, 0.40)';
            setTimeout(() => {
                window.location.href = 'register.html';
            }, 600);
        });
    }

    // ========== ANIMATED STATS ==========
    function animateNumber(el, target, suffix = '') {
        let current = 0;
        const step = Math.max(1, Math.floor(target / 40));
        const interval = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(interval);
            }
            el.textContent = current.toLocaleString() + suffix;
        }, 30);
    }

    const statChallenges = document.getElementById('statChallenges');
    const statPlayers = document.getElementById('statPlayers');
    const statTeams = document.getElementById('statTeams');
    const statFlags = document.getElementById('statFlags');

    if (statChallenges) animateNumber(statChallenges, 12);
    if (statPlayers) animateNumber(statPlayers, 284);
    if (statTeams) animateNumber(statTeams, 47);
    if (statFlags) animateNumber(statFlags, 1240);

    // ========== SMOOTH ANCHOR SCROLL ==========
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ========== NAV ACTIVE STATE ==========
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('nav a:not(.theme-toggle)');

    function updateActiveNav() {
        let current = '';
        sections.forEach(section => {
            const top = section.offsetTop - 120;
            if (window.scrollY >= top) {
                current = section.getAttribute('id');
            }
        });
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', updateActiveNav, { passive: true });
    updateActiveNav();

    // ========== GLASS HOVER SPARK ==========
    document.querySelectorAll('.challenge-card, .glass').forEach(el => {
        el.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width;
            const y = (e.clientY - rect.top) / rect.height;
            this.style.setProperty('--mouse-x', x);
            this.style.setProperty('--mouse-y', y);
        });
    });

})();
