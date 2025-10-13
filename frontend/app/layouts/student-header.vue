<!-- student header layout with organization name header and navigation -->
<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <!-- organization name header -->
    <header class="bg-blue-600 text-white text-center py-10 text-4xl font-bold">"org name"</header>
    <!-- navigation bar below the header -->
    <Nav />
    <!-- page content slot -->
    <slot />
  </div>
</template>
