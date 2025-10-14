import { ref, onMounted } from 'vue';

/**
 * Vue composable for managing application theme
 * Provides consistent theme state across all components
 * @returns {Object} - Theme functions and state
 */
export function useTheme() {
  // Theme state
  const isDark = ref(false); // Default to light theme
  
  /**
   * Load theme from localStorage
   */
  const loadTheme = () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      isDark.value = savedTheme === 'dark';
    } else {
      // Default to light theme
      isDark.value = false;
    }
    applyTheme();
  };

  /**
   * Save theme to localStorage
   */
  const saveTheme = () => {
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
  };

  /**
   * Apply theme to document
   */
  const applyTheme = () => {
    const html = document.documentElement;
    const body = document.body;
    
    if (isDark.value) {
      html.classList.add('dark');
      html.classList.add('v-theme--dark');
      html.classList.remove('v-theme--light');
      html.setAttribute('data-theme', 'dark');
      body.classList.add('dark-theme');
      body.classList.remove('light-theme');
    } else {
      html.classList.remove('dark');
      html.classList.remove('v-theme--dark');
      html.classList.add('v-theme--light');
      html.setAttribute('data-theme', 'light');
      body.classList.add('light-theme');
      body.classList.remove('dark-theme');
    }
  };

  /**
   * Toggle between light and dark theme
   */
  const toggleTheme = () => {
    isDark.value = !isDark.value;
    applyTheme();
    saveTheme();
    
    // Optional: Show a brief notification
    console.log(`Theme switched to: ${isDark.value ? 'dark' : 'light'} mode`);
  };

  /**
   * Set theme directly
   * @param {boolean} dark - Whether to use dark theme
   */
  const setTheme = (dark) => {
    isDark.value = dark;
    applyTheme();
    saveTheme();
  };

  /**
   * Get current theme name
   * @returns {string} - Current theme name ('light' or 'dark')
   */
  const getCurrentTheme = () => {
    return isDark.value ? 'dark' : 'light';
  };

  /**
   * Check if current theme is dark
   * @returns {boolean} - Whether current theme is dark
   */
  const isDarkTheme = () => {
    return isDark.value;
  };

  // Initialize theme on composable creation
  onMounted(() => {
    loadTheme();
  });

  return {
    // State
    isDark,
    
    // Methods
    loadTheme,
    saveTheme,
    applyTheme,
    toggleTheme,
    setTheme,
    getCurrentTheme,
    isDarkTheme
  };
}
