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

<style scoped lang="scss">
.cta-block {
  max-width: 80vw;
  margin: 4em auto;
  a{
  width: 279px;
  height: 70px;
    img{
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
  }
  p {
    font-family: FONTSPRINGDEMO-RecoletaMedium;
    font-size: 34px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.59;
    letter-spacing: normal;
    text-align: center;
    color: #000;
  }
}

@media (max-width: 768px) {
  .cta-block {
    max-width: 100%;
    padding: 0 20px;

    p {
      font-size: 24px;
    }
  }
}
</style>
