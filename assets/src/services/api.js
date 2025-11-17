import axios from 'axios';

// Get WordPress configuration from global
const wpConfig = window.wpHeadlessBlog || {};

// Create axios instance
const api = axios.create({
  baseURL: wpConfig.apiUrl || '/wp-json/wp/v2/',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('wp_headless_blog_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Add nonce for WordPress REST API
    if (wpConfig.nonce) {
      config.headers['X-WP-Nonce'] = wpConfig.nonce;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('wp_headless_blog_token');
      localStorage.removeItem('wp_headless_blog_user');
      
      // Redirect to login if not already there
      if (window.location.pathname !== '/wp-headless-blog/login') {
        window.location.href = '/wp-headless-blog/login';
      }
    }
    return Promise.reject(error);
  }
);

// API methods
export const postsApi = {
  /**
   * Get posts
   */
  getPosts(params = {}) {
    return api.get('posts', { params });
  },
  
  /**
   * Get single post
   */
  getPost(id) {
    return api.get(`posts/${id}`);
  },
  
  /**
   * Search posts
   */
  searchPosts(query, params = {}) {
    return api.get('posts', {
      params: {
        search: query,
        ...params,
      },
    });
  },
  
  /**
   * Get posts by category
   */
  getPostsByCategory(categoryId, params = {}) {
    return api.get('posts', {
      params: {
        categories: categoryId,
        ...params,
      },
    });
  },
};

export const categoriesApi = {
  /**
   * Get categories
   */
  getCategories(params = {}) {
    return api.get('categories', { params });
  },
};

export const authApi = {
  /**
   * Login
   */
  login(username, password) {
    const jwtEndpoint = wpConfig.jwtEndpoint || '/wp-json/wp-headless-blog/v1/auth/token';
    return axios.post(jwtEndpoint, {
      username,
      password,
    });
  },
  
  /**
   * Get current user
   */
  getMe() {
    return api.get('/wp-headless-blog/v1/auth/me');
  },
  
  /**
   * Validate token
   */
  validateToken() {
    return api.get('/wp-headless-blog/v1/auth/validate');
  },
};

export default api;

