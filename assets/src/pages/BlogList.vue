<template>
  <div>
    <div class="mb-8">
      <h1 class="text-4xl font-bold mb-4">Blog Posts</h1>
      
      <!-- Search and Filters -->
      <div class="flex flex-col md:flex-row gap-4 mb-6">
        <SearchBox @search="handleSearch" />
        <CategoryFilter @filter="handleCategoryFilter" />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && posts.length === 0" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      <p class="mt-4 text-gray-600">Loading posts...</p>
    </div>

    <!-- Posts Grid -->
    <div v-else-if="posts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <PostCard
        v-for="post in posts"
        :key="post.id"
        :post="post"
        @click="goToPost(post.id)"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <p class="text-gray-600 text-lg">No posts found.</p>
    </div>

    <!-- Load More Button -->
    <div v-if="hasMore && !loading" class="text-center mt-8">
      <button
        @click="loadMore"
        class="btn btn-primary"
        :disabled="loading"
      >
        Load More
      </button>
    </div>

    <!-- Loading More Indicator -->
    <div v-if="loading && posts.length > 0" class="text-center py-4">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { postsApi } from '../services/api';
import PostCard from '../components/PostCard.vue';
import SearchBox from '../components/SearchBox.vue';
import CategoryFilter from '../components/CategoryFilter.vue';

const router = useRouter();

const posts = ref([]);
const loading = ref(false);
const page = ref(1);
const hasMore = ref(true);
const searchQuery = ref('');
const categoryFilter = ref(null);

const fetchPosts = async (reset = false) => {
  if (loading.value) return;

  loading.value = true;

  try {
    const params = {
      page: reset ? 1 : page.value,
      per_page: 12,
      _embed: true,
    };

    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    if (categoryFilter.value) {
      params.categories = categoryFilter.value;
    }

    const response = await postsApi.getPosts(params);
    const newPosts = response.data;

    if (reset) {
      posts.value = newPosts;
      page.value = 2;
    } else {
      posts.value = [...posts.value, ...newPosts];
      page.value += 1;
    }

    // Check if there are more posts
    const totalPages = parseInt(response.headers['x-wp-totalpages'] || '1');
    hasMore.value = page.value <= totalPages;
  } catch (error) {
    console.error('Error fetching posts:', error);
  } finally {
    loading.value = false;
  }
};

const loadMore = () => {
  if (!loading.value && hasMore.value) {
    fetchPosts(false);
  }
};

const handleSearch = (query) => {
  searchQuery.value = query;
  page.value = 1;
  hasMore.value = true;
  fetchPosts(true);
};

const handleCategoryFilter = (categoryId) => {
  categoryFilter.value = categoryId;
  page.value = 1;
  hasMore.value = true;
  fetchPosts(true);
};

const goToPost = (id) => {
  router.push(`/post/${id}`);
};

// Infinite scroll
let scrollHandler = null;

onMounted(() => {
  fetchPosts(true);

  // Infinite scroll
  scrollHandler = () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= documentHeight - 200) {
      loadMore();
    }
  };

  window.addEventListener('scroll', scrollHandler);
});

onUnmounted(() => {
  if (scrollHandler) {
    window.removeEventListener('scroll', scrollHandler);
  }
});
</script>

