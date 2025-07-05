// Dark Mode Toggle Functionality
class DarkModeToggle {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    init() {
        // Set initial theme
        this.setTheme(this.theme);
        
        // Create toggle button
        this.createToggleButton();
        
        // Add event listener
        this.addEventListeners();
    }

    createToggleButton() {
        // Check if toggle already exists
        if (document.querySelector('.theme-toggle')) {
            return;
        }

        const toggle = document.createElement('button');
        toggle.className = 'theme-toggle';
        toggle.setAttribute('aria-label', 'Toggle dark mode');
        toggle.innerHTML = `
            <span class="sun-icon">☀️</span>
            <span class="moon-icon">🌙</span>
        `;
        
        document.body.appendChild(toggle);
    }

    addEventListeners() {
        const toggle = document.querySelector('.theme-toggle');
        if (toggle) {
            toggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }

        // Listen for theme changes from other tabs/windows
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                this.setTheme(e.newValue || 'light');
            }
        });
    }

    toggleTheme() {
        const newTheme = this.theme === 'light' ? 'dark' : 'light';
        this.setTheme(newTheme);
        this.saveTheme(newTheme);
    }

    setTheme(theme) {
        this.theme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        
        // Update meta theme-color for mobile browsers
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', theme === 'dark' ? '#1a1a2e' : '#667eea');
        }
    }

    saveTheme(theme) {
        localStorage.setItem('theme', theme);
    }

    getTheme() {
        return this.theme;
    }
}

// Initialize dark mode when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new DarkModeToggle();
});

// Also initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new DarkModeToggle();
    });
} else {
    new DarkModeToggle();
}

// Export for use in other scripts
window.DarkModeToggle = DarkModeToggle; 