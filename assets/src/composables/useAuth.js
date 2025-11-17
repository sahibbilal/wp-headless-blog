import { ref, computed } from 'vue';
import { authApi } from '../services/api';

// Global auth state
const token = ref(localStorage.getItem('wp_headless_blog_token') || null);
const user = ref(
  localStorage.getItem('wp_headless_blog_user')
    ? JSON.parse(localStorage.getItem('wp_headless_blog_user'))
    : null
);

// Initialize from WordPress if available
if (window.wpHeadlessBlog?.currentUser) {
  user.value = window.wpHeadlessBlog.currentUser;
}

// Make auth service available globally
window.wpHeadlessBlogAuth = {
  isAuthenticated: () => !!token.value,
  getToken: () => token.value,
  getUser: () => user.value,
};

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value);
  const currentUser = computed(() => user.value);

  const login = async (username, password) => {
    try {
      const response = await authApi.login(username, password);
      const { token: newToken, user: newUser } = response.data;

      token.value = newToken;
      user.value = newUser;

      localStorage.setItem('wp_headless_blog_token', newToken);
      localStorage.setItem('wp_headless_blog_user', JSON.stringify(newUser));

      // Update global auth service
      window.wpHeadlessBlogAuth.isAuthenticated = () => true;
      window.wpHeadlessBlogAuth.getToken = () => newToken;
      window.wpHeadlessBlogAuth.getUser = () => newUser;

      return { success: true };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'Login failed',
      };
    }
  };

  const logout = () => {
    token.value = null;
    user.value = null;

    localStorage.removeItem('wp_headless_blog_token');
    localStorage.removeItem('wp_headless_blog_user');

    // Update global auth service
    window.wpHeadlessBlogAuth.isAuthenticated = () => false;
    window.wpHeadlessBlogAuth.getToken = () => null;
    window.wpHeadlessBlogAuth.getUser = () => null;
  };

  const checkAuth = async () => {
    if (!token.value) {
      return false;
    }

    try {
      const response = await authApi.validateToken();
      if (response.data.valid) {
        user.value = response.data.user;
        localStorage.setItem('wp_headless_blog_user', JSON.stringify(response.data.user));
        return true;
      }
    } catch (error) {
      logout();
      return false;
    }

    return false;
  };

  return {
    isAuthenticated,
    currentUser,
    login,
    logout,
    checkAuth,
  };
}

