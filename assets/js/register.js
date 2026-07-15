
// Form validation
const registerForm = document.getElementById('registerForm');
const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const terms = document.getElementById('terms');

registerForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    
    // Validate username
    if (username.value.length < 4 || username.value.length > 16) {
    username.parentElement.classList.add('error');
    isValid = false;
    } else {
    username.parentElement.classList.remove('error');
    username.parentElement.classList.add('success');
    }
    
    // Validate email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
    email.parentElement.classList.add('error');
    isValid = false;
    } else {
    email.parentElement.classList.remove('error');
    email.parentElement.classList.add('success');
    }
    
    // Validate password
    if (password.value.length < 8) {
    password.parentElement.classList.add('error');
    isValid = false;
    } else {
    password.parentElement.classList.remove('error');
    password.parentElement.classList.add('success');
    }
    
    // Validate password match
    if (password.value !== confirmPassword.value) {
    confirmPassword.parentElement.classList.add('error');
    isValid = false;
    } else {
    confirmPassword.parentElement.classList.remove('error');
    confirmPassword.parentElement.classList.add('success');
    }
    
    // Validate terms
    if (!terms.checked) {
    terms.parentElement.style.color = 'var(--neon-pink)';
    isValid = false;
    } else {
    terms.parentElement.style.color = 'rgba(192, 252, 204, 0.8)';
    }
    
    // If form is valid, submit it
    if (isValid) {
    // Simulate form submission
    const submitBtn = document.querySelector('.submit-btn');
    submitBtn.textContent = 'CREATING ACCOUNT...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        submitBtn.textContent = 'ACCESS GRANTED!';
        submitBtn.style.background = 'linear-gradient(45deg, var(--neon-green), #00ff00)';
        
        // Redirect to dashboard after successful registration
        setTimeout(() => {
        window.location.href = 'dashboard.html';
        }, 1000);
    }, 1500);
    }
});

// Input validation on blur
username.addEventListener('blur', function() {
    if (this.value.length < 4 || this.value.length > 16) {
    this.parentElement.classList.add('error');
    } else {
    this.parentElement.classList.remove('error');
    this.parentElement.classList.add('success');
    }
});

email.addEventListener('blur', function() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(this.value)) {
    this.parentElement.classList.add('error');
    } else {
    this.parentElement.classList.remove('error');
    this.parentElement.classList.add('success');
    }
});

password.addEventListener('blur', function() {
    if (this.value.length < 8) {
    this.parentElement.classList.add('error');
    } else {
    this.parentElement.classList.remove('error');
    this.parentElement.classList.add('success');
    }
});

confirmPassword.addEventListener('blur', function() {
    if (this.value !== password.value) {
    this.parentElement.classList.add('error');
    } else {
    this.parentElement.classList.remove('error');
    this.parentElement.classList.add('success');
    }
});

// Button hover effect
const submitBtn = document.querySelector('.submit-btn');

submitBtn.addEventListener('mouseenter', () => {
    submitBtn.style.boxShadow = '0 0 30px rgba(0, 255, 136, 0.8)';
});

submitBtn.addEventListener('mouseleave', () => {
    submitBtn.style.boxShadow = '0 0 20px rgba(0, 255, 136, 0.5)';
});
