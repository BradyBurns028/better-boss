<script lang="ts">
definePageMeta(() => {
    const authStore = useAuthStore()
    const isAdmin = authStore.user && authStore.user.user_type === 'admin'
    return {
        layout: isAdmin ? 'default' : 'student-header'
    }
})
export default defineNuxtComponent({
    layout: 'default',
    data() {
        return {
            authStore: useAuthStore()
        }
    }
})
</script>

<template>
  <main class="flex-1 flex flex-col">
    <div class="px-4 pt-4 pb-0">
      <Notifications />
    </div>
  </main>
</template>
