<template>
   <!-- main container for the page -->
  <div class="min-h-screen bg-gray-50 text-gray-800 flex flex-col">
    <!-- header component at the top of the page -->
    <Header />
    <!-- navigation bar below the header -->
    <Nav />
     <!-- Main content area -->
    <main class="flex-1 flex flex-col">
      <!-- notifications section -->
      <div class="px-4 pt-4 pb-0">
        <Notifications />
      </div>
    </main>
  </div>
</template>
