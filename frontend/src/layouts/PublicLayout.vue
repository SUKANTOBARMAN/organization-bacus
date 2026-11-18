<script setup>
import SiteHeader from '../components/layout/SiteHeader.vue'
import SiteFooter from '../components/layout/SiteFooter.vue'
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <SiteHeader />
    <main class="mx-auto w-full max-w-7xl flex-1 px-5 py-10">
      <RouterView />
    </main>
    <SiteFooter />
  </div>
</template>
