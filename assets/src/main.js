import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import './style.css';

// Import pages
import BlogList from './pages/BlogList.vue';
import SinglePost from './pages/SinglePost.vue';
import Login from './pages/Login.vue';
import Dashboard from './pages/Dashboard.vue';

// Router configuration
const routes = [
  {
    path: '/',
    name: 'BlogList',
    component: BlogList,
  },
  {
    path: '/post/:id',
    name: 'SinglePost',
    component: SinglePost,
    props: true,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
];

// Determine base path based on context
const getBasePath = () => {
  if (window.wpHeadlessBlog?.isAdmin) {
    return '/wp-admin/admin.php?page=wp-headless-blog';
  }
  return '/wp-headless-blog';
};

const router = createRouter({
  history: createWebHistory(getBasePath()),
  routes,
});

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
  const authService = window.wpHeadlessBlogAuth;
  
  if (to.meta.requiresAuth && !authService?.isAuthenticated()) {
    next({ name: 'Login', query: { redirect: to.fullPath } });
  } else if (to.meta.requiresGuest && authService?.isAuthenticated()) {
    next({ name: 'BlogList' });
  } else {
    next();
  }
});

// Create Vue app
const app = createApp(App);

app.use(router);

// Make WordPress data available globally
app.config.globalProperties.$wp = window.wpHeadlessBlog || {};

app.mount('#wp-headless-blog-app');

