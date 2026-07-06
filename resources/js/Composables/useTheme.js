import { ref, watch, onMounted } from 'vue';

const isDark = ref(true);
const isInitialized = ref(false);

function applyTheme(dark) {
    document.documentElement.classList.toggle('light', !dark);
}

export function useTheme() {
    function toggle() {
        isDark.value = !isDark.value;
        localStorage.setItem('gms-theme', isDark.value ? 'dark' : 'light');
        applyTheme(isDark.value);
    }

    function setTheme(dark) {
        isDark.value = dark;
        localStorage.setItem('gms-theme', dark ? 'dark' : 'light');
        applyTheme(dark);
    }

    onMounted(() => {
        if (isInitialized.value) return;
        isInitialized.value = true;

        const stored = localStorage.getItem('gms-theme');
        if (stored) {
            isDark.value = stored === 'dark';
        } else {
            isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        applyTheme(isDark.value);

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem('gms-theme') === null) {
                isDark.value = e.matches;
                applyTheme(e.matches);
            }
        });
    });

    return { isDark, toggle, setTheme };
}
