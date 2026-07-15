
// Sample CTF Challenges
const challenges = [
    {
    id: 1,
    title: "Cookie Monster",
    category: "web",
    points: 100,
    solves: 42,
    description: "This challenge involves finding and manipulating cookies to gain access. Look for hidden values and consider how sessions are managed.",
    flag: "SUDO{c00k1e_m0nst3r_eats_all}",
    solved: false
    },
    {
    id: 2,
    title: "Buffer Overflow",
    category: "pwn",
    points: 250,
    solves: 15,
    description: "Exploit a classic buffer overflow vulnerability. Overwrite the return address to execute arbitrary code. Think about stack layouts and shellcode.",
    flag: "SUDO{bUff3r_0v3rfl0w_r0cks!}",
    solved: false
    },
    {
    id: 3,
    title: "RSA Madness",
    category: "crypto",
    points: 150,
    solves: 28,
    description: "Decipher a message encrypted with RSA. You're given the public key and ciphertext. Is there a common vulnerability or a small prime factor?",
    flag: "SUDO{cryp70_is_fun_w1th_rsa}",
    solved: false
    },
    {
    id: 4,
    title: "Secret Lockbox",
    category: "reversing",
    points: 200,
    solves: 19,
    description: "Reverse engineer a small executable to find the correct password. Look for string comparisons or mathematical operations.",
    flag: "SUDO{r3v3rs1ng_g3ts_y0u_th3_k3y}",
    solved: false
    },
    {
    id: 5,
    title: "XSS Hunter",
    category: "web",
    points: 120,
    solves: 35,
    description: "Find a Cross-Site Scripting (XSS) vulnerability and exfiltrate a cookie. Think about input sanitization and content security policies.",
    flag: "SUDO{xss_pr0t3ct10n_n33d3d}",
    solved: false
    },
    {
    id: 6,
    title: "SQL Injection",
    category: "web",
    points: 180,
    solves: 22,
    description: "Bypass authentication using SQL injection. Explore common injection techniques like UNION-based or error-based attacks.",
    flag: "SUDO{sql_1nj3ct10n_master}",
    solved: false
    },
    {
    id: 7,
    title: "Format String Bug",
    category: "pwn",
    points: 300,
    solves: 10,
    description: "Exploit a format string vulnerability to leak stack data or write to arbitrary memory locations.",
    flag: "SUDO{f0rm4t_str1ng_pwn3d}",
    solved: false
    },
    {
    id: 8,
    title: "Steganography Image",
    category: "misc",
    points: 70,
    solves: 50,
    description: "There's a secret message hidden within this image. Can you find it?",
    flag: "SUDO{h1dd3n_1n_pl41n_s1ght}",
    solved: false
    }
];

// DOM Elements
const challengesGrid = document.getElementById('challengesGrid');
const filterButtons = document.querySelectorAll('.filter-btn');
const challengeModal = document.getElementById('challengeModal');
const closeModalBtn = document.getElementById('closeModal');
const modalChallengeTitle = document.getElementById('modalChallengeTitle');
const modalChallengeCategory = document.getElementById('modalChallengeCategory');
const modalChallengePoints = document.getElementById('modalChallengePoints');
const modalChallengeSolves = document.getElementById('modalChallengeSolves');
const modalChallengeDescription = document.getElementById('modalChallengeDescription');
const flagInputSection = document.getElementById('flagInputSection');
const flagInput = document.getElementById('flagInput');
const submitFlagBtn = document.getElementById('submitFlagBtn');
const modalMessageBox = document.getElementById('modalMessageBox'); // Consolidated message box
const messageBoxTitle = document.getElementById('messageBoxTitle');
const messageBoxText = document.getElementById('messageBoxText');


let activeChallengeId = null;

// Render Challenges
function renderChallenges(challengesToRender) {
    challengesGrid.innerHTML = '';
    challengesToRender.forEach(challenge => {
    const card = document.createElement('div');
    card.className = 'challenge-card';
    card.dataset.id = challenge.id;
    if (challenge.solved) {
        card.classList.add('solved');
    }
    card.innerHTML = `
        <span class="category">${challenge.category.toUpperCase()}</span>
        <h3>${challenge.title}</h3>
        <div class="points">${challenge.points} POINTS</div>
        <div>${challenge.solves} SOLVES</div>
        ${challenge.solved ? '<div class="solved-indicator">SOLVED</div>' : ''}
    `;
    challengesGrid.appendChild(card);
    });
}

// Set active challenge (for highlighting)
function setActiveChallenge(id) {
    activeChallengeId = id;
    document.querySelectorAll('.challenge-card').forEach(card => {
    if (parseInt(card.dataset.id) === id) {
        card.classList.add('active');
        card.classList.remove('dimmed');
    } else {
        card.classList.remove('active');
        card.classList.add('dimmed');
    }
    });
}

// Show Challenge Details Modal
function showChallengeDetails(challenge) {
    modalChallengeTitle.textContent = challenge.title;
    modalChallengeCategory.textContent = challenge.category.toUpperCase();
    modalChallengePoints.textContent = challenge.points;
    modalChallengeSolves.textContent = challenge.solves;
    modalChallengeDescription.textContent = challenge.description;
    flagInput.value = ''; // Clear previous flag input

    // Reset message box and show/hide sections based on solved status
    modalMessageBox.classList.remove('visible', 'success', 'error');
    if (challenge.solved) {
    flagInputSection.style.display = 'none';
    messageBoxTitle.textContent = "CONGRATULATIONS!";
    messageBoxText.textContent = "You've successfully solved this challenge. Well done, hacker! 🎉";
    modalMessageBox.classList.add('visible', 'success');
    } else {
    flagInputSection.style.display = 'flex';
    }

    challengeModal.classList.add('visible');
}

// Hide Challenge Details Modal
function hideChallengeDetails() {
    challengeModal.classList.remove('visible');
    document.querySelectorAll('.challenge-card').forEach(card => {
    card.classList.remove('active', 'dimmed');
    });
    // Ensure message box is hidden and input section is shown for next open
    modalMessageBox.classList.remove('visible', 'success', 'error');
    flagInputSection.style.display = 'flex';
}

// Initialize
renderChallenges(challenges);

// Event Listener for Challenge Card Clicks (to open modal)
challengesGrid.addEventListener('click', (e) => {
    const card = e.target.closest('.challenge-card');
    if (card) {
    const challengeId = parseInt(card.dataset.id);
    const selectedChallenge = challenges.find(c => c.id === challengeId);
    if (selectedChallenge) {
        setActiveChallenge(challengeId);
        showChallengeDetails(selectedChallenge);
    }
    }
});

// Event Listener for closing modal
closeModalBtn.addEventListener('click', hideChallengeDetails);
challengeModal.addEventListener('click', (e) => {
    if (e.target === challengeModal) {
    hideChallengeDetails();
    }
});

// Flag Submission Logic
submitFlagBtn.addEventListener('click', () => {
    const enteredFlag = flagInput.value.trim();
    const currentChallenge = challenges.find(c => c.id === activeChallengeId);

    // Clear any previous messages
    modalMessageBox.classList.remove('visible', 'success', 'error');

    if (currentChallenge) {
    if (enteredFlag === currentChallenge.flag) {
        // Correct Flag!
        currentChallenge.solved = true;
        currentChallenge.solves++;
        modalChallengeSolves.textContent = currentChallenge.solves;

        flagInputSection.style.display = 'none';
        messageBoxTitle.textContent = "CONGRATULATIONS!";
        messageBoxText.textContent = "You've successfully solved this challenge. Well done, hacker! 🎉";
        modalMessageBox.classList.add('visible', 'success');

        // Re-render challenges to update the card status
        const activeFilterBtn = document.querySelector('.filter-btn.active');
        const currentCategory = activeFilterBtn ? activeFilterBtn.dataset.category : 'all';
        if (currentCategory === 'all') {
        renderChallenges(challenges);
        } else {
        renderChallenges(challenges.filter(c => c.category === currentCategory));
        }
        setActiveChallenge(activeChallengeId); // Re-apply active/dimmed state

    }else {
        // Incorrect Flag
        flagInput.value = ''; // Clear input on incorrect attempt
        messageBoxTitle.textContent = "FLAG INCORRECT!";
        messageBoxText.textContent = "That's not quite right. Keep trying, you'll get it! 🧐";
        modalMessageBox.classList.add('visible', 'error');
    }
    }
});

// Filter functionality
filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
    filterButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const category = btn.dataset.category;
    if (category === 'all') {
        renderChallenges(challenges);
    } else {
        renderChallenges(challenges.filter(c => c.category === category));
    }
    hideChallengeDetails();
    document.querySelectorAll('.challenge-card').forEach(card => {
        card.classList.remove('active', 'dimmed');
    });
    activeChallengeId = null;
    });
});
