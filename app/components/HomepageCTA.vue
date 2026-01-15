<script setup lang="ts">
interface Props {
  image: string
  title: string
  description: string
  link: string
  buttonImage: string
  additionalText?: string
  buttonImage2?: string
}

defineProps<Props>()
</script>

<template>
  <section id="ready-to-make-an-impact" class="py-5 bg-white position-relative">
    <div class="container-fluid position-relative d-flex justify-content-center align-items-center">
      <img :src="image" alt="" />
      <div id="leforme" class="position-absolute d-flex flex-column justify-content-center align-items-center text-center p-4 mx-auto">
        <h2>{{ title }}</h2>
        <p class="preserve-lines" v-html="description"></p>
        <nuxt-link :to="link">
          <nuxt-img :src="buttonImage" :alt="title"></nuxt-img>
        </nuxt-link>
      </div>
    </div>
    <CtaBlock
      v-if="additionalText && buttonImage2"
      :text="additionalText"
      :link="link"
      :button-image="buttonImage2"
      :button-alt="title"
    />
  </section>
</template>

<style scoped lang="scss">
.preserve-lines {
  white-space: pre-line;
}

#leforme {
  max-width: 1098px;
  max-height: 400px;
  width: 100vw;
  height: 100vh;
  border-radius: 20px;
  border: solid 3px #ed2e52;
  background-color: #f83156;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  margin: auto !important;
  bottom: 0;

  h2 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 50px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: center;
    color: #fff;
    margin-bottom: 20px;
  }

  p {
    font-family: FONTSPRINGDEMO-RecoletaAlt;
    font-size: 18px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.56;
    letter-spacing: normal;
    text-align: center;
    color: #fff;
    margin-bottom: 20px;
  }
}

/* Effets de survol pour les boutons */
a:has(img),
nuxt-link:has(img) {
  display: inline-block;
  transition: transform 0.3s ease, filter 0.3s ease;

  img {
    transition: transform 0.3s ease, filter 0.3s ease, box-shadow 0.3s ease, border-radius 0.3s ease;
    border-radius: 50px;
  }

  &:hover {
    transform: translateY(-4px);

    img {
      filter: brightness(1.1);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      border-radius: 60px;
    }
  }

  &:active {
    transform: translateY(-2px) scale(0.98);
  }
}
</style>
