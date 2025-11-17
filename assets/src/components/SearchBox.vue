<template>
  <div class="relative flex-1">
    <input
      v-model="query"
      type="text"
      placeholder="Search posts..."
      class="input pr-10"
      @input="handleInput"
      @keyup.enter="handleSearch"
    />
    <button
      @click="handleSearch"
      class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary-600"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['search']);

const query = ref('');

let searchTimeout = null;

const handleInput = () => {
  // Debounce search
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    emit('search', query.value);
  }, 500);
};

const handleSearch = () => {
  clearTimeout(searchTimeout);
  emit('search', query.value);
};
</script>

