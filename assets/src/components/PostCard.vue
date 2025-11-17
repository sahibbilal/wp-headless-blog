<template>
  <article
    class="card cursor-pointer hover:shadow-lg transition-shadow duration-200"
    @click="$emit('click')"
  >
    <!-- Featured Image -->
    <div
      v-if="post._embedded?.['wp:featuredmedia']?.[0]?.source_url"
      class="mb-4 -mx-6 -mt-6 rounded-t-lg overflow-hidden"
    >
      <img
        :src="post._embedded['wp:featuredmedia'][0].source_url"
        :alt="post.title.rendered"
        class="w-full h-48 object-cover"
      />
    </div>

    <!-- Post Content -->
    <h2 class="text-xl font-bold mb-2 line-clamp-2">
      {{ post.title.rendered }}
    </h2>

    <div
      class="text-gray-600 mb-4 line-clamp-3"
      v-html="post.excerpt.rendered"
    ></div>

    <!-- Post Meta -->
    <div class="flex items-center justify-between text-sm text-gray-500">
      <div class="flex items-center gap-4">
        <span v-if="post.date">
          {{ formatDate(post.date) }}
        </span>
        <span v-if="post._embedded?.author?.[0]">
          {{ post._embedded.author[0].name }}
        </span>
      </div>
      
      <div v-if="post._embedded?.['wp:term']?.[0]?.length" class="flex gap-2">
        <span
          v-for="category in post._embedded['wp:term'][0].slice(0, 2)"
          :key="category.id"
          class="bg-primary-100 text-primary-800 px-2 py-1 rounded text-xs"
        >
          {{ category.name }}
        </span>
      </div>
    </div>
  </article>
</template>

<script setup>
defineProps({
  post: {
    type: Object,
    required: true,
  },
});

defineEmits(['click']);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

