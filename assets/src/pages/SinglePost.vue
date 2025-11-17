<template>
  <div>
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      <p class="mt-4 text-gray-600">Loading post...</p>
    </div>

    <!-- Post Content -->
    <article v-else-if="post" class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <button
        @click="$router.push('/')"
        class="mb-6 text-primary-600 hover:text-primary-700 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Blog
      </button>

      <!-- Post Header -->
      <header class="mb-8">
        <h1 class="text-4xl font-bold mb-4">{{ post.title.rendered }}</h1>
        
        <div class="flex items-center gap-4 text-gray-600 mb-4">
          <span v-if="post._embedded?.author?.[0]">
            By {{ post._embedded.author[0].name }}
          </span>
          <span v-if="post.date">
            {{ formatDate(post.date) }}
          </span>
          <span v-if="post._embedded?.['wp:term']?.[0]?.length">
            <span
              v-for="category in post._embedded['wp:term'][0]"
              :key="category.id"
              class="inline-block bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-sm mr-2"
            >
              {{ category.name }}
            </span>
          </span>
        </div>

        <!-- Featured Image -->
        <img
          v-if="post._embedded?.['wp:featuredmedia']?.[0]?.source_url"
          :src="post._embedded['wp:featuredmedia'][0].source_url"
          :alt="post.title.rendered"
          class="w-full h-96 object-cover rounded-lg mb-8"
        />
      </header>

      <!-- Post Content -->
      <div
        class="prose prose-lg max-w-none"
        v-html="post.content.rendered"
      ></div>

      <!-- Post Footer -->
      <footer class="mt-12 pt-8 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <button
            @click="$router.push('/')"
            class="btn btn-secondary"
          >
            ← Back to Blog
          </button>
          
          <div v-if="post._embedded?.author?.[0]" class="flex items-center gap-4">
            <img
              v-if="post._embedded.author[0].avatar_urls?.[96]"
              :src="post._embedded.author[0].avatar_urls[96]"
              :alt="post._embedded.author[0].name"
              class="w-12 h-12 rounded-full"
            />
            <div>
              <p class="font-semibold">{{ post._embedded.author[0].name }}</p>
              <p class="text-sm text-gray-600">{{ post._embedded.author[0].description || 'Author' }}</p>
            </div>
          </div>
        </div>
      </footer>
    </article>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <p class="text-red-600 text-lg">Post not found.</p>
      <button
        @click="$router.push('/')"
        class="btn btn-primary mt-4"
      >
        Back to Blog
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { postsApi } from '../services/api';

const route = useRoute();
const post = ref(null);
const loading = ref(true);

const fetchPost = async () => {
  try {
    const response = await postsApi.getPost(route.params.id);
    post.value = response.data;
    
    // Update page title
    document.title = `${post.value.title.rendered} - Headless Blog`;
  } catch (error) {
    console.error('Error fetching post:', error);
  } finally {
    loading.value = false;
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
  fetchPost();
});
</script>

<style scoped>
.prose {
  @apply text-gray-800;
}

.prose h2 {
  @apply text-2xl font-bold mt-8 mb-4;
}

.prose h3 {
  @apply text-xl font-bold mt-6 mb-3;
}

.prose p {
  @apply mb-4 leading-relaxed;
}

.prose a {
  @apply text-primary-600 hover:text-primary-700 underline;
}

.prose img {
  @apply rounded-lg my-6;
}

.prose ul, .prose ol {
  @apply mb-4 ml-6;
}

.prose li {
  @apply mb-2;
}

.prose blockquote {
  @apply border-l-4 border-primary-500 pl-4 italic my-6;
}
</style>

