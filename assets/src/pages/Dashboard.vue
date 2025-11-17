<template>
  <div>
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-2">Dashboard</h1>
      <p class="text-gray-600">Welcome back, {{ currentUser?.name }}!</p>
    </div>

    <!-- User Info Card -->
    <div class="card mb-6">
      <h2 class="text-xl font-bold mb-4">Your Profile</h2>
      <div class="space-y-2">
        <p><strong>Name:</strong> {{ currentUser?.name }}</p>
        <p><strong>Email:</strong> {{ currentUser?.email }}</p>
        <p><strong>Roles:</strong> {{ currentUser?.roles?.join(', ') }}</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      <div class="card">
        <h3 class="text-lg font-semibold mb-2">View Blog</h3>
        <p class="text-gray-600 mb-4">Browse all published posts</p>
        <router-link to="/" class="btn btn-primary">
          Go to Blog
        </router-link>
      </div>

      <div class="card">
        <h3 class="text-lg font-semibold mb-2">WordPress Admin</h3>
        <p class="text-gray-600 mb-4">Access WordPress dashboard</p>
        <a :href="wpAdminUrl" class="btn btn-secondary" target="_blank">
          Open Admin
        </a>
      </div>

      <div class="card">
        <h3 class="text-lg font-semibold mb-2">REST API</h3>
        <p class="text-gray-600 mb-4">Explore WordPress REST API</p>
        <a :href="apiUrl" class="btn btn-secondary" target="_blank">
          View API
        </a>
      </div>
    </div>

    <!-- Recent Posts Preview -->
    <div class="card">
      <h2 class="text-xl font-bold mb-4">Recent Posts</h2>
      <div v-if="recentPostsLoading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>
      <div v-else-if="recentPosts.length > 0" class="space-y-4">
        <div
          v-for="post in recentPosts"
          :key="post.id"
          class="border-b border-gray-200 pb-4 last:border-0"
        >
          <h3 class="font-semibold text-lg mb-2">
            <router-link
              :to="`/post/${post.id}`"
              class="text-primary-600 hover:text-primary-700"
            >
              {{ post.title.rendered }}
            </router-link>
          </h3>
          <p class="text-gray-600 text-sm">{{ formatDate(post.date) }}</p>
        </div>
      </div>
      <div v-else class="text-gray-600 py-4">
        No posts found.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '../composables/useAuth';
import { postsApi } from '../services/api';

const { currentUser } = useAuth();

const recentPosts = ref([]);
const recentPostsLoading = ref(false);

const wpAdminUrl = computed(() => {
  return window.wpHeadlessBlog?.siteUrl ? `${window.wpHeadlessBlog.siteUrl}/wp-admin` : '/wp-admin';
});

const apiUrl = computed(() => {
  return window.wpHeadlessBlog?.apiUrl || '/wp-json/wp/v2/';
});

const fetchRecentPosts = async () => {
  recentPostsLoading.value = true;
  try {
    const response = await postsApi.getPosts({ per_page: 5 });
    recentPosts.value = response.data;
  } catch (error) {
    console.error('Error fetching recent posts:', error);
  } finally {
    recentPostsLoading.value = false;
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

onMounted(() => {
  fetchRecentPosts();
});
</script>

