<template>
  <div class="row two-column-text">
    <div class="col">
      <h3 v-if="title" class="damsnt" v-html="title"></h3>
    </div>
    <div class="col">
      <p v-if="text" class="kemi" v-html="text"></p>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title?: string
  text?: string
}>()
</script>
