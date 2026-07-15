
// Enhanced glitch effect
const glitchText = document.querySelector('.glitch-main');

function randomGlitch() {
    if (Math.random() > 0.7) {
    glitchText.style.transform = `translate(${(Math.random() - 0.5) * 10}px, ${(Math.random() - 0.5) * 5}px)`;
    glitchText.style.opacity = '0.8';
    
    setTimeout(() => {
        glitchText.style.transform = 'translate(0, 0)';
        glitchText.style.opacity = '1';
    }, 100);
    }
}

setInterval(randomGlitch, 2000);

// Button hover effect
const ctaButton = document.getElementById('registerBtn');

ctaButton.addEventListener('mouseenter', () => {
    ctaButton.style.boxShadow = '0 0 30px rgba(0, 255, 136, 0.8)';
});

ctaButton.addEventListener('mouseleave', () => {
    ctaButton.style.boxShadow = '0 0 20px rgba(0, 255, 136, 0.5)';
});

ctaButton.addEventListener('click', () => {
    ctaButton.textContent = 'ACCESS GRANTED';
    ctaButton.style.background = 'linear-gradient(45deg, var(--neon-green), #00ff00)';
    window.location.href = "register.html"
    setTimeout(() => {
    ctaButton.textContent = 'REGISTER NOW';
    ctaButton.style.background = 'linear-gradient(45deg, var(--neon-green), var(--neon-blue))';
    }, 2000);
});

// Countdown timer
function updateCountdown() {
    const eventDate = new Date('2023-12-31T00:00:00').getTime();
    const now = new Date().getTime();
    const distance = eventDate - now;

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById('days').textContent = days.toString().padStart(2, '0');
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');

    // Glitch effect when under 1 hour
    if (distance < 3600000) {
    const countdownItems = document.querySelectorAll('.countdown-number');
    countdownItems.forEach(item => {
        if (Math.random() > 0.7) {
        item.style.color = `hsl(${Math.random() * 120}, 100%, 50%)`;
        setTimeout(() => {
            item.style.color = 'var(--neon-green)';
        }, 100);
        }
    });
    }
}

setInterval(updateCountdown, 1000);
updateCountdown();

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href')).scrollIntoView({
        behavior: 'smooth'
    });
    });
});
