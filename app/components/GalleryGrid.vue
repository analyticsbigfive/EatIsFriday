<template>
  <section class="py-5 bg-white gallery-section">
    <div class="container">
      <div class="gallery-grid">
        <!-- Grande image à gauche -->
        <div class="gallery-item-large" v-if="images[0]">
          <div class="gallery-item-wrapper">
            <NuxtImg :src="images[0].src" class="w-100 gallery-img" :alt="images[0].alt" />
          </div>
        </div>

        <!-- 2 images à droite -->
        <div class="gallery-items-right">
          <div class="gallery-item-small" v-for="(image, i) in images.slice(1)" :key="i">
            <div class="gallery-item-wrapper">
              <NuxtImg :src="image.src" class="w-100 gallery-img" :alt="image.alt" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
interface GalleryImage {
  src: string
  alt: string
}

defineProps<{
  images: GalleryImage[]
}>()
</script>
