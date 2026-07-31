function applyTheme(dark) {
    document.documentElement.classList.toggle('light', !dark);
}
export function useTheme() {
    const isDark = ref(false);
    const toggle = () => {
        isDark.value = !isDark.value;
        localStorage.setItem('gms-theme', isDark.value ? 'dark' : 'light');
        applyTheme(isDark.value);
    };
    const setTheme = (dark) => {
        isDark.value = dark;
        localStorage.setItem('gms-theme', dark ? 'dark' : 'light');
        applyTheme(dark);
    };
    // Accessibility: announce theme change for screen readers
    const announceThemeChange = () => {
        const announcement = document.createElement('div');
        announcement.setAttribute('role', 'status');
        announcement.setAttribute('aria-live', 'polite');
        announcement.textContent = `Theme set to ${isDark.value ? 'dark' : 'light'}`;
        document.body.appendChild(announcement);
        setTimeout(() => announcement.remove(), 1000);
    };
    // Public API
    return {
        isDark,
        toggle,
        setTheme,
        announceThemeChange
    };
}
