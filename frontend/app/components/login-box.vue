<script lang="ts">
export default {
  data() {
    return {
      username: '', // holds the input value for username
      password: ''  // holds the input value for password
    }
  },
  methods: {
    // handles the login action
    handleLogin() {
      // redirects to '/dashboard'
      this.$router.push('/dashboard')
    }
  }
}
</script>
<template>
  <!-- outer container -->
  <div class="min-h-screen flex items-start justify-center bg-gray-50 pt-20 px-4">
    <div class="max-w-md w-full bg-gray-200 p-8 rounded-lg">
      <!-- login form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- username field -->
        <div>
          <label for="username" class="block text-sm font-medium text-black">Username:</label>
          <input id="username" v-model="username" type="text" required class="w-full px-3 py-2 border rounded-md" placeholder="Enter your username"/>
        </div>
        <!-- password field -->
        <div>
          <label for="password" class="block text-sm font-medium text-black">Password:</label>
          <input id="password" v-model="password" type="password" required class="w-full px-3 py-2 border rounded-md" placeholder="Enter your password"/>
        </div>
        <!-- submit button -->
        <button type="submit" class="w-full py-2 px-4 rounded-md bg-blue-600 text-white hover:bg-blue-700">Sign In
        </button>
      </form>
    </div>
  </div>
</template>

