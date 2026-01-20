<template>
  <div class="torn-paper-container pt-4" >
    <!-- Contenu -->
    <div class="torn-paper-content">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  variant?: 'white' | 'blue' | 'cream'
}>()
</script>
