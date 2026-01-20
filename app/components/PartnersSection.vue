<template>
  <section id="partners" class="py-5 bg-white">
    <h3 v-if="title" class="font-header center text-center preserve-lines">{{ title }}</h3>
    <ClientOnly>
      <PartnersCarousel :partners="partners" />
    </ClientOnly>
  </section>
</template>

<script setup lang="ts">
interface Partner {
  name: string
  logo: string
  url?: string
}

defineProps<{
  title?: string
  partners: Partner[]
}>()
</script>
