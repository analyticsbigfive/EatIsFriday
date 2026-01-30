<script setup lang="ts">
// Ensure standard layout
useHead({
  titleTemplate: (titleChunk) => {
    return titleChunk ? `${titleChunk} - Eat Is Family` : 'Eat Is Family - Celebrate Food Every Day'
  },
  meta: [
    { name: 'description', content: 'Creating unforgettable culinary experiences that bring people together.' }
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap' }
  ]
})
</script>

<template>
  <div class="min-h-screen flex flex-col font-body">
    <!-- SVG Blob Mask Definition (Invisible) -->
    <svg width="0" height="0" class="absolute">
      <defs>
        <clipPath id="blob-mask-svg" clipPathUnits="objectBoundingBox">
          <path d="M0.3,0.05 C0.5,0.01,0.7,0.05,0.85,0.15 C0.95,0.25,1,0.4,0.98,0.6 C0.95,0.8,0.8,0.95,0.6,0.98 C0.4,1,0.2,0.9,0.1,0.7 C0,0.5,0.05,0.2,0.3,0.05 Z" />
        </clipPath>
      </defs>
    </svg>

    <TheHeader />
    <main class="flex-grow">
      <NuxtPage />
    </main>
    <TheFooter />
  </div>
</template>

<style lang="scss">
/* Global resets or transitions can go here if not in main.css */
.page-enter-active,
.page-leave-active {
  transition: all 0.3s;
}
.page-enter-from,
.page-leave-to {
  opacity: 0;
  filter: blur(1rem);
}


</style>
