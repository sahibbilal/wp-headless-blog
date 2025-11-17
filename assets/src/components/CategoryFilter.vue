<template>
  <div>
    <select
      v-model="selectedCategory"
      @change="handleChange"
      class="input"
    >
      <option :value="null">All Categories</option>
      <option
        v-for="category in categories"
        :key="category.id"
        :value="category.id"
      >
        {{ category.name }} ({{ category.count }})
      </option>
    </select>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { categoriesApi } from '../services/api';

const emit = defineEmits(['filter']);

const categories = ref([]);
const selectedCategory = ref(null);
const loading = ref(false);

const fetchCategories = async () => {
  loading.value = true;
  try {
    const response = await categoriesApi.getCategories({ per_page: 100 });
    categories.value = response.data.filter(cat => cat.count > 0);
  } catch (error) {
    console.error('Error fetching categories:', error);
  } finally {
    loading.value = false;
  }
};

const handleChange = () => {
  emit('filter', selectedCategory.value);
};

onMounted(() => {
  fetchCategories();
});
</script>

