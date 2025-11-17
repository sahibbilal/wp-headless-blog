<template>
  <div class="max-w-md mx-auto">
    <div class="card">
      <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Error Message -->
        <div
          v-if="error"
          class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"
        >
          {{ error }}
        </div>

        <!-- Username -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
            Username
          </label>
          <input
            id="username"
            v-model="username"
            type="text"
            class="input"
            required
            autocomplete="username"
          />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Password
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            class="input"
            required
            autocomplete="current-password"
          />
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="btn btn-primary w-full"
          :disabled="loading"
        >
          <span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <!-- Success Message -->
      <div
        v-if="success"
        class="mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg"
      >
        Login successful! Redirecting...
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const route = useRoute();
const { login } = useAuth();

const username = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const success = ref(false);

const handleLogin = async () => {
  error.value = '';
  loading.value = true;

  const result = await login(username.value, password.value);

  if (result.success) {
    success.value = true;
    
    // Redirect to dashboard or requested page
    const redirect = route.query.redirect || '/dashboard';
    setTimeout(() => {
      router.push(redirect);
    }, 1000);
  } else {
    error.value = result.error || 'Login failed. Please try again.';
    loading.value = false;
  }
};
</script>

