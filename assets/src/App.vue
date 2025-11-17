<template>
  <div id="app" class="min-h-screen">
    <header v-if="!isAdmin" class="bg-white shadow-sm">
      <nav class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <router-link to="/" class="text-2xl font-bold text-primary-600">
            {{ siteName }}
          </router-link>
          <div class="flex items-center gap-4">
            <router-link
              v-for="link in navLinks"
              :key="link.path"
              :to="link.path"
              class="text-gray-700 hover:text-primary-600 transition-colors"
            >
              {{ link.label }}
            </router-link>
            <router-link
              v-if="!isAuthenticated"
              to="/login"
              class="btn btn-primary"
            >
              Login
            </router-link>
            <div v-else class="flex items-center gap-4">
              <span class="text-gray-700">{{ currentUser?.name }}</span>
              <button @click="handleLogout" class="btn btn-secondary">
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <main :class="{ 'container mx-auto px-4 py-8': !isAdmin, 'p-8': isAdmin }">
      <router-view />
    </main>

    <footer v-if="!isAdmin" class="bg-gray-800 text-white mt-12">
      <div class="container mx-auto px-4 py-8">
        <p class="text-center">
          &copy; {{ new Date().getFullYear() }} {{ siteName }}. All rights reserved.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from './composables/useAuth';

const router = useRouter();
const { isAuthenticated, currentUser, logout } = useAuth();

const siteName = ref('Headless Blog');
const isAdmin = ref(window.wpHeadlessBlog?.isAdmin || false);

const navLinks = computed(() => {
  if (isAdmin.value) {
    return [];
  }
  return [
    { path: '/', label: 'Home' },
    { path: '/dashboard', label: 'Dashboard' },
  ];
});

const handleLogout = () => {
  logout();
  router.push('/');
};

onMounted(() => {
  if (window.wpHeadlessBlog?.siteName) {
    siteName.value = window.wpHeadlessBlog.siteName;
  }
});
</script>

