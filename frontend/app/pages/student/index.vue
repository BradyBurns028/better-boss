<script lang="ts">
definePageMeta({
    layout: 'student-header'
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
