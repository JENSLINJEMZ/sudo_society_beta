
const forgotPasswordForm = document.getElementById('forgotPasswordForm');
const usernameEmailInput = document.getElementById('username-email');
const resetBtn = document.getElementById('resetBtn');

forgotPasswordForm.addEventListener('submit', function(e) {
    e.preventDefault();
    let isValid = true;

    if (usernameEmailInput.value.trim() === '') {
    usernameEmailInput.parentElement.classList.add('error');
    isValid = false;
    } else {
    usernameEmailInput.parentElement.classList.remove('error');
    usernameEmailInput.parentElement.classList.add('success');
    }

    if (isValid) {
    resetBtn.textContent = 'SENDING LINK...';
    resetBtn.disabled = true;

    // Simulate sending reset link
    setTimeout(() => {
        resetBtn.textContent = 'RESET LINK SENT';
        resetBtn.style.background = 'linear-gradient(45deg, var(--neon-green), #00cc00)'; // Ensure success feedback is green
        // Optionally redirect after a short delay
        setTimeout(() => {
        alert('A password reset link has been sent to your email address. Please check your inbox (and spam folder).');
        window.location.href = 'login.html'; // Redirect back to login
        }, 1500);
    }, 2000);
    }
});

usernameEmailInput.addEventListener('blur', function() {
    if (this.value.trim() === '') {
    this.parentElement.classList.add('error');
    } else {
    this.parentElement.classList.remove('error');
    this.parentElement.classList.add('success');
    }
});
