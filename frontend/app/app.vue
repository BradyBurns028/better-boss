<script lang="ts">
export default defineNuxtComponent({
    name: 'Root',
})
</script>
<template>
    <div>
        <Toast />
        <NuxtLayout>
            <NuxtPage />
        </NuxtLayout>
    </div>
</template>