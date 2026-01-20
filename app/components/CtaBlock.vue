<template>
  <div class="cta-block d-flex flex-column justify-content-center align-items-center text-center p-4 mx-auto mt-4">
    <p v-if="text" v-html="text" class="preservelines"></p>
    <nuxt-link v-if="link && buttonImage" :to="link">
      <nuxt-img :src="buttonImage" :alt="buttonAlt || 'CTA Button'" />
    </nuxt-link>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  text?: string
  link?: string
  buttonImage?: string
  buttonAlt?: string
}>()
</script>
