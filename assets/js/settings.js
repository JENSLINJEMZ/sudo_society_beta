
// --- Global State (for demonstration) ---
let userSettings = {
    profile: {
        username: "hacker1337",
        email: "hacker@sudo.society",
        bio: "CTF enthusiast specializing in web exploitation and cryptography. Top 100 player.",
        country: "US",
        avatar: "https://i.imgur.com/JqYeSzn.png"
    },
    security: {
        twoFactorEnabled: true,
        backupCodesGenerated: false,
        connectedDevices: [
            { id: 'macbook', name: 'MacBook Pro', details: 'macOS 14.0 | Chrome 116 | 192.168.1.1', current: true },
            { id: 'iphone', name: 'iPhone 14 Pro', details: 'iOS 16.6 | Safari | Last active 3 days ago', current: false }
        ]
    },
    privacy: {
        showLeaderboard: true,
        allowDMs: true,
        showEmail: false,
        showCountryFlag: true,
        activityVisibility: "public"
    },
    appearance: {
        theme: "dark",
        codeTheme: "monokai",
        enableAnimations: true,
        reduceMotion: false,
        fontSize: 16
    },
    integrations: {
        githubConnected: false,
        discordConnected: true,
        webhookUrl: "",
        apiKey: "sk_live_1234567890abcdef"
    },
    billing: {
        subscriptionStatus: "Premium Member (Active)",
        nextBillingDate: "November 15, 2023",
        cardName: "",
        cardNumber: "",
        cardExpiry: "",
        cardCVC: ""
    }
};

// --- Custom Modal Elements ---
const modalOverlay = document.getElementById('customModalOverlay');
const modalCard = document.getElementById('customModalCard');
const modalHeader = document.getElementById('customModalHeader');
const modalMessage = document.getElementById('customModalMessage');
const modalInput = document.getElementById('customModalInput');
const modalButtons = document.getElementById('customModalButtons');

let currentModalResolve = null; // Used to resolve promises for confirm/prompt

/**
 * Shows a custom alert modal.
 * @param {string} message - The message to display.
 * @param {string} [title='Sudo Society'] - The title of the alert.
 * @param {string} [buttonText='OK'] - The text for the close button.
 */
function showAlert(message, title = 'Sudo Society CTF', buttonText = 'OK') {
    modalHeader.textContent = title;
    modalMessage.textContent = message;
    modalInput.style.display = 'none'; // Hide input for alerts
    modalInput.value = ''; // Clear input value

    modalButtons.innerHTML = ''; // Clear previous buttons
    const okButton = document.createElement('button');
    okButton.textContent = buttonText;
    okButton.classList.add('custom-modal-btn', 'primary');
    okButton.addEventListener('click', hideModal, { once: true }); // Ensure it's removed after one click
    modalButtons.appendChild(okButton);

    modalOverlay.classList.add('show');
    setTimeout(() => okButton.focus(), 300); // Focus button after animation
}

/**
 * Shows a custom confirmation modal.
 * @param {string} message - The confirmation message.
 * @param {string} [title='Confirm Action'] - The title of the confirmation.
 * @param {string} [confirmText='Yes'] - Text for the confirm button.
 * @param {string} [cancelText='No'] - Text for the cancel button.
 * @returns {Promise<boolean>} Resolves with true if confirmed, false if cancelled.
 */
function showConfirm(message, title = 'Confirm Action', confirmText = 'Yes', cancelText = 'No') {
    return new Promise(resolve => {
        currentModalResolve = resolve; // Store resolve function for later use

        modalHeader.textContent = title;
        modalMessage.textContent = message;
        modalInput.style.display = 'none'; // Hide input for confirms
        modalInput.value = ''; // Clear input value

        modalButtons.innerHTML = ''; // Clear previous buttons

        const confirmButton = document.createElement('button');
        confirmButton.textContent = confirmText;
        confirmButton.classList.add('custom-modal-btn', 'primary');
        confirmButton.addEventListener('click', () => {
            hideModal();
            currentModalResolve(true); // Resolve with true for confirm
        }, { once: true });

        const cancelButton = document.createElement('button');
        cancelButton.textContent = cancelText;
        cancelButton.classList.add('custom-modal-btn', 'secondary');
        cancelButton.addEventListener('click', () => {
            hideModal();
            currentModalResolve(false); // Resolve with false for cancel
        }, { once: true });

        modalButtons.appendChild(confirmButton);
        modalButtons.appendChild(cancelButton);

        modalOverlay.classList.add('show');
        setTimeout(() => confirmButton.focus(), 300); // Focus confirm button
    });
}

/**
 * Shows a custom prompt modal with an input field.
 * @param {string} message - The prompt message.
 * @param {string} [title='Input Required'] - The title of the prompt.
 * @param {string} [placeholder=''] - Placeholder text for the input.
 * @returns {Promise<string|null>} Resolves with the input value, or null if cancelled.
 */
function showPrompt(message, title = 'Input Required', placeholder = '') {
    return new Promise(resolve => {
        currentModalResolve = resolve;

        modalHeader.textContent = title;
        modalMessage.textContent = message;
        modalInput.style.display = 'block'; // Show input for prompts
        modalInput.placeholder = placeholder;
        modalInput.value = ''; // Clear input value
        modalInput.focus(); // Focus the input field

        modalButtons.innerHTML = '';

        const okButton = document.createElement('button');
        okButton.textContent = 'OK';
        okButton.classList.add('custom-modal-btn', 'primary');
        okButton.addEventListener('click', () => {
            hideModal();
            currentModalResolve(modalInput.value); // Resolve with input value
        }, { once: true });

        const cancelButton = document.createElement('button');
        cancelButton.textContent = 'Cancel';
        cancelButton.classList.add('custom-modal-btn', 'secondary');
        cancelButton.addEventListener('click', () => {
            hideModal();
            currentModalResolve(null); // Resolve with null for cancel
        }, { once: true });

        modalButtons.appendChild(okButton);
        modalButtons.appendChild(cancelButton);

        modalOverlay.classList.add('show');
        setTimeout(() => modalInput.focus(), 300); // Ensure input is focused after animation
    });
}

/**
 * Hides the currently active custom modal.
 */
function hideModal() {
    modalOverlay.classList.remove('show');
    // Ensure any previous event listeners on buttons are cleaned up
    modalButtons.innerHTML = '';
    modalInput.value = ''; // Clear input for next use
    // If there's an unresolved promise from a confirm/prompt, resolve it as false/null on external close
    if (currentModalResolve) {
        currentModalResolve(null); // Treat as cancelled if not explicitly confirmed/denied
        currentModalResolve = null;
    }
}

// Close modal when clicking outside of it
modalOverlay.addEventListener('click', (event) => {
    if (event.target === modalOverlay) {
        hideModal();
    }
});

// Close modal when ESC key is pressed
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modalOverlay.classList.contains('show')) {
        hideModal();
    }
});


// Function to load settings into the form
function loadSettings() {
    // Profile
    document.getElementById('avatarPreview').src = userSettings.profile.avatar;
    document.getElementById('headerUserAvatar').src = userSettings.profile.avatar;
    document.getElementById('username').value = userSettings.profile.username;
    document.getElementById('headerUsername').textContent = userSettings.profile.username;
    document.getElementById('email').value = userSettings.profile.email;
    document.getElementById('bio').value = userSettings.profile.bio;
    document.getElementById('country').value = userSettings.profile.country;

    // Security
    document.getElementById('2faToggle').checked = userSettings.security.twoFactorEnabled;
    document.getElementById('2faStatus').textContent = userSettings.security.twoFactorEnabled ? 'Enabled' : 'Disabled';
    document.getElementById('backupCodesToggle').checked = userSettings.security.backupCodesGenerated;
    document.getElementById('backupCodesStatus').textContent = userSettings.security.backupCodesGenerated ? 'Generated' : 'Not Generated';

    // Privacy
    document.getElementById('leaderboardVisibility').checked = userSettings.privacy.showLeaderboard;
    document.getElementById('dmToggle').checked = userSettings.privacy.allowDMs;
    document.getElementById('emailVisibility').checked = userSettings.privacy.showEmail;
    document.getElementById('countryFlagVisibility').checked = userSettings.privacy.showCountryFlag;
    document.getElementById('activityVisibility').value = userSettings.privacy.activityVisibility;

    // Appearance
    document.getElementById('theme').value = userSettings.appearance.theme;
    document.getElementById('codeTheme').value = userSettings.appearance.codeTheme;
    document.getElementById('animationsToggle').checked = userSettings.appearance.enableAnimations;
    document.getElementById('reduceMotionToggle').checked = userSettings.appearance.reduceMotion;
    document.getElementById('fontSize').value = userSettings.appearance.fontSize;
    document.getElementById('currentFontSize').textContent = userSettings.appearance.fontSize + 'px';

    // Integrations
    document.getElementById('githubIntegrationToggle').checked = userSettings.integrations.githubConnected;
    document.getElementById('githubStatus').textContent = userSettings.integrations.githubConnected ? 'Connected' : 'Not Connected';
    document.getElementById('discordIntegrationToggle').checked = userSettings.integrations.discordConnected;
    document.getElementById('discordStatus').textContent = userSettings.integrations.discordConnected ? 'Connected' : 'Not Connected';
    document.getElementById('webhooks').value = userSettings.integrations.webhookUrl;
    document.getElementById('apiKey').value = userSettings.integrations.apiKey;

    // Billing
    document.getElementById('subscriptionStatus').textContent = userSettings.billing.subscriptionStatus;
    document.getElementById('nextBillingDate').textContent = userSettings.billing.nextBillingDate;
    document.getElementById('cardName').value = userSettings.billing.cardName;
    document.getElementById('cardNumber').value = userSettings.billing.cardNumber;
    document.getElementById('cardExpiry').value = userSettings.billing.cardExpiry;
    document.getElementById('cardCVC').value = userSettings.billing.cardCVC;
}

// Function to save settings (simulated)
async function saveSettings() {
    // Profile
    userSettings.profile.username = document.getElementById('username').value;
    userSettings.profile.email = document.getElementById('email').value;
    userSettings.profile.bio = document.getElementById('bio').value;
    userSettings.profile.country = document.getElementById('country').value;
    // Avatar is handled separately via event listener

    // Security
    userSettings.security.twoFactorEnabled = document.getElementById('2faToggle').checked;
    userSettings.security.backupCodesGenerated = document.getElementById('backupCodesToggle').checked;
    // Password fields are usually handled with a separate API call for security, so we'll just log
    if (document.getElementById('newPassword').value) {
        // In a real app, send new password to backend
        console.log("Password change requested (not saved on client-side).");
    }

    // Privacy
    userSettings.privacy.showLeaderboard = document.getElementById('leaderboardVisibility').checked;
    userSettings.privacy.allowDMs = document.getElementById('dmToggle').checked;
    userSettings.privacy.showEmail = document.getElementById('emailVisibility').checked;
    userSettings.privacy.showCountryFlag = document.getElementById('countryFlagVisibility').checked;
    userSettings.privacy.activityVisibility = document.getElementById('activityVisibility').value;

    // Appearance
    userSettings.appearance.theme = document.getElementById('theme').value;
    userSettings.appearance.codeTheme = document.getElementById('codeTheme').value;
    userSettings.appearance.enableAnimations = document.getElementById('animationsToggle').checked;
    userSettings.appearance.reduceMotion = document.getElementById('reduceMotionToggle').checked;
    userSettings.appearance.fontSize = parseInt(document.getElementById('fontSize').value);

    // Integrations
    userSettings.integrations.githubConnected = document.getElementById('githubIntegrationToggle').checked;
    userSettings.integrations.discordConnected = document.getElementById('discordIntegrationToggle').checked;
    userSettings.integrations.webhookUrl = document.getElementById('webhooks').value;
    // API key changes are handled separately

    // Billing
    userSettings.billing.cardName = document.getElementById('cardName').value;
    userSettings.billing.cardNumber = document.getElementById('cardNumber').value;
    userSettings.billing.cardExpiry = document.getElementById('cardExpiry').value;
    userSettings.billing.cardCVC = document.getElementById('cardCVC').value;

    // Re-render display elements that show saved state
    loadSettings(); // Re-load to update display based on new userSettings

    await showAlert('Settings saved successfully!');
    console.log("Current User Settings:", userSettings);
}

// --- Navigation and Scrolling ---
document.querySelectorAll('.settings-menu a').forEach(link => {
    link.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.settings-menu a').forEach(nav => nav.classList.remove('active'));
    this.classList.add('active');
    const targetId = this.getAttribute('href');
    document.querySelector(targetId).scrollIntoView({
        behavior: 'smooth'
    });
    });
});

// --- Avatar Upload ---
document.getElementById('uploadAvatarBtn').addEventListener('click', () => {
    document.getElementById('avatarInput').click();
});

document.getElementById('avatarInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
    const reader = new FileReader();
    reader.onload = async function(e) {
        document.getElementById('avatarPreview').src = e.target.result;
        document.getElementById('headerUserAvatar').src = e.target.result; // Update header avatar
        userSettings.profile.avatar = e.target.result; // Update in global state
        await showAlert('Avatar uploaded successfully!');
    };
    reader.readAsDataURL(file);
    }
});

document.getElementById('removeAvatarBtn').addEventListener('click', async () => {
    const defaultAvatar = "https://i.imgur.com/JqYeSzn.png"; // Or a placeholder image
    document.getElementById('avatarPreview').src = defaultAvatar;
    document.getElementById('headerUserAvatar').src = defaultAvatar;
    userSettings.profile.avatar = defaultAvatar;
    await showAlert('Avatar removed!');
});

// --- Toggle Switches ---
document.getElementById('2faToggle').addEventListener('change', async function() {
    document.getElementById('2faStatus').textContent = this.checked ? 'Enabled' : 'Disabled';
    await showAlert(`Two-Factor Authentication: ${this.checked ? 'Enabled' : 'Disabled'}.`);
    console.log('Two-Factor Authentication:', this.checked ? 'Enabled' : 'Disabled');
});

document.getElementById('backupCodesToggle').addEventListener('change', async function() {
    document.getElementById('backupCodesStatus').textContent = this.checked ? 'Generated' : 'Not Generated';
    await showAlert(`Backup codes ${this.checked ? 'generated' : 'deactivated'}!`);
    console.log('Backup Codes:', this.checked ? 'Generated' : 'Not Generated');
});

document.getElementById('leaderboardVisibility').addEventListener('change', function() {
    console.log('Show profile on leaderboards:', this.checked);
});

document.getElementById('dmToggle').addEventListener('change', function() {
    console.log('Allow direct messages:', this.checked);
});

document.getElementById('emailVisibility').addEventListener('change', function() {
    console.log('Show email to other members:', this.checked);
});

document.getElementById('countryFlagVisibility').addEventListener('change', function() {
    console.log('Show country flag on profile:', this.checked);
});

document.getElementById('animationsToggle').addEventListener('change', async function() {
    console.log('Enable animations and effects:', this.checked);
    await showAlert(`Animations ${this.checked ? 'enabled' : 'disabled'}. Reload may be required for full effect.`);
});

document.getElementById('reduceMotionToggle').addEventListener('change', async function() {
    console.log('Reduce motion (accessibility mode):', this.checked);
    await showAlert(`Reduce motion ${this.checked ? 'enabled' : 'disabled'}.`);
});

document.getElementById('githubIntegrationToggle').addEventListener('change', async function() {
    document.getElementById('githubStatus').textContent = this.checked ? 'Connected' : 'Not Connected';
    await showAlert(`GitHub integration ${this.checked ? 'enabled' : 'disabled'}.`);
    console.log('GitHub Integration:', this.checked ? 'Connected' : 'Not Connected');
});

document.getElementById('discordIntegrationToggle').addEventListener('change', async function() {
    document.getElementById('discordStatus').textContent = this.checked ? 'Connected' : 'Not Connected';
    await showAlert(`Discord integration ${this.checked ? 'enabled' : 'disabled'}.`);
    console.log('Discord Integration:', this.checked ? 'Connected' : 'Not Connected');
});


// --- Range Input for Font Size ---
document.getElementById('fontSize').addEventListener('input', function() {
    document.getElementById('currentFontSize').textContent = this.value + 'px';
    // document.body.style.fontSize = `${this.value}px`; // This would affect all text sizes
});

// --- Button Actions ---
document.getElementById('connectGitHubBtn').addEventListener('click', async () => {
    await showAlert('Attempting to connect to GitHub (simulated OAuth flow)...');
    // In a real app: window.location.href to GitHub OAuth endpoint
    userSettings.integrations.githubConnected = true;
    document.getElementById('githubIntegrationToggle').checked = true;
    document.getElementById('githubStatus').textContent = 'Connected';
});

document.getElementById('disconnectDiscordBtn').addEventListener('click', async () => {
    const confirmed = await showConfirm('Are you sure you want to disconnect Discord?', 'Disconnect Integration');
    if (confirmed) {
        await showAlert('Discord disconnected.');
        userSettings.integrations.discordConnected = false;
        document.getElementById('discordIntegrationToggle').checked = false;
        document.getElementById('discordStatus').textContent = 'Not Connected';
    } else {
        await showAlert('Discord disconnection cancelled.');
    }
});

document.getElementById('copyApiKeyBtn').addEventListener('click', async () => {
    const apiKeyInput = document.getElementById('apiKey');
    apiKeyInput.select();
    apiKeyInput.setSelectionRange(0, 99999); /* For mobile devices */
    try {
    document.execCommand('copy');
    await showAlert('API Key copied to clipboard!');
    } catch (err) {
    console.error('Failed to copy API Key:', err);
    await showAlert('Failed to copy API Key. Please copy manually.');
    }
});

document.getElementById('regenerateApiKeyBtn').addEventListener('click', async () => {
    const confirmed = await showConfirm('Are you sure you want to regenerate your API Key? This will invalidate the old one.', 'Regenerate API Key');
    if (confirmed) {
    const newKey = 'sk_live_' + Math.random().toString(36).substring(2, 18);
    document.getElementById('apiKey').value = newKey;
    userSettings.integrations.apiKey = newKey;
    await showAlert('New API Key generated!');
    } else {
    await showAlert('API Key regeneration cancelled.');
    }
});

document.getElementById('requestDataExportBtn').addEventListener('click', async () => {
    await showAlert('Your data export request has been submitted. You will receive an email when it\'s ready (simulated).');
});

document.getElementById('updatePaymentBtn').addEventListener('click', async () => {
    const cardName = document.getElementById('cardName').value;
    const cardNumber = document.getElementById('cardNumber').value;
    // Basic validation
    if (!cardName || !cardNumber) {
        await showAlert('Please fill in card details.', 'Validation Error', 'Got It');
        return;
    }
    await showAlert('Payment method updated (simulated)!');
    console.log('Updated Payment Info:', {
        name: cardName,
        number: cardNumber.replace(/.(?=.{4})/g, '*'), // Mask most of the number
        expiry: document.getElementById('cardExpiry').value,
        cvc: document.getElementById('cardCVC').value
    });
});

document.getElementById('viewBillingHistoryBtn').addEventListener('click', async () => {
    await showAlert('Redirecting to billing history (simulated)...');
    // In a real app: window.open('billing-history.html', '_blank');
    console.log('Simulating redirection to billing history.');
});

document.querySelectorAll('.device-btn.logout').forEach(button => {
    button.addEventListener('click', async function() {
        const deviceId = this.dataset.deviceId;
        const confirmed = await showConfirm(`Are you sure you want to log out from this device (${deviceId})?`, 'Logout Device');
        if (confirmed) {
            await showAlert(`Logged out from ${deviceId} (simulated).`);
            // In a real app, send a request to invalidate the session for this device
            this.closest('.device-item').remove(); // Remove from UI
        } else {
            await showAlert('Logout cancelled.');
        }
    });
});

document.getElementById('deactivateAccountBtn').addEventListener('click', async () => {
    const confirmed = await showConfirm('Are you sure you want to deactivate your account? You can reactivate it later.', 'Deactivate Account');
    if (confirmed) {
        await showAlert('Account deactivated (simulated). You will be logged out.');
        // In a real app: window.location.href = 'logout.html';
        console.log('Simulating account deactivation and logout.');
    } else {
        await showAlert('Account deactivation cancelled.');
    }
});

document.getElementById('deleteAllDataBtn').addEventListener('click', async () => {
    const confirmedFirst = await showConfirm('WARNING: Are you absolutely sure you want to permanently erase ALL your data? This cannot be undone.', 'PERMANENT DATA DELETION');
    if (confirmedFirst) {
        const confirmText = await showPrompt('This action is irreversible. Type "DELETE MY DATA" to confirm:', 'Final Confirmation', 'DELETE MY DATA');
        if (confirmText === 'DELETE MY DATA') {
            await showAlert('All your data has been permanently erased (simulated).');
            // In a real app: Trigger a backend process to delete data
            // window.location.href = 'goodbye.html';
            console.log('Simulating permanent data deletion.');
        } else {
            await showAlert('Data deletion cancelled or incorrect confirmation.');
        }
    } else {
        await showAlert('Data deletion cancelled.');
    }
});

document.getElementById('deleteAccountBtn').addEventListener('click', async () => {
    const confirmedFirst = await showConfirm('WARNING: Are you absolutely sure you want to permanently delete your account? This cannot be undone.', 'PERMANENT ACCOUNT DELETION');
    if (confirmedFirst) {
        const confirmText = await showPrompt('This action is irreversible. Type "DELETE MY ACCOUNT" to confirm:', 'Final Confirmation', 'DELETE MY ACCOUNT');
        if (confirmText === 'DELETE MY ACCOUNT') {
            await showAlert('Your account has been permanently deleted (simulated).');
            // In a real app: Trigger a backend process to delete the account
            // window.location.href = 'goodbye.html';
            console.log('Simulating permanent account deletion.');
        } else {
            await showAlert('Account deletion cancelled or incorrect confirmation.');
        }
    } else {
        await showAlert('Account deletion cancelled.');
    }
});

// --- Save and Discard Changes ---
document.getElementById('saveChangesBtn').addEventListener('click', saveSettings);

document.getElementById('discardChangesBtn').addEventListener('click', async () => {
    const confirmed = await showConfirm('Are you sure you want to discard all unsaved changes?', 'Discard Changes');
    if (confirmed) {
        loadSettings(); // Reload settings from the initial state
        await showAlert('Changes discarded!');
    } else {
        await showAlert('Discard changes cancelled.');
    }
});

// --- Initial Load ---
document.addEventListener('DOMContentLoaded', loadSettings);

// Dummy toggleDropdown function for user-profile click (if it's intended to do something)
function toggleDropdown() {
    console.log("User profile clicked! (Dropdown functionality not implemented)");
    // await showAlert("Imagine a dropdown menu appearing here!");
}
