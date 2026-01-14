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

<style scoped lang="scss">
.gallery-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.gallery-item-large {
  grid-row: span 2;
}

.gallery-items-right {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.gallery-item-wrapper {
  border-radius: 20px;
  overflow: hidden;
  height: 100%;
}

.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;

  &:hover {
    transform: scale(1.05);
  }
}

.gallery-item-large .gallery-item-wrapper {
  height: 100%;
}

.gallery-item-small .gallery-item-wrapper {
  height: 100%;
}

@media (max-width: 768px) {
  .gallery-grid {
    grid-template-columns: 1fr;
  }

  .gallery-item-large {
    grid-row: span 1;
  }

  .gallery-items-right {
    flex-direction: row;
  }

  .gallery-item-small {
    flex: 1;
  }
}
</style>
