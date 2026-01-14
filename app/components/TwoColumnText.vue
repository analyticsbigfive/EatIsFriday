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

<style scoped lang="scss">
.two-column-text {
  .damsnt {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 50px;
    font-weight: 800;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.2;
    letter-spacing: normal;
    color: #000b0f;
  }

  .kemi {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 18px;
    font-weight: normal;
    line-height: 1.8;
    color: #333;
  }
}

@media (max-width: 768px) {
  .two-column-text {
    flex-direction: column;

    .damsnt {
      font-size: 32px;
      margin-bottom: 1rem;
    }

    .kemi {
      font-size: 16px;
    }
  }
}
</style>
