<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay } from 'swiper/modules'
import 'swiper/css'

interface Partner {
  logo: string
  alt: string
}

defineProps<{
  partners: Partner[]
}>()
</script>

<template>
  <div class="partners-carousel">
    <Swiper
      :modules="[Autoplay]"
      :slides-per-view="2"
      :space-between="30"
      :loop="true"
      :autoplay="{
        delay: 0,
        disableOnInteraction: false,
      }"
      :speed="3000"
      :allow-touch-move="false"
      :breakpoints="{
        640: {
          slidesPerView: 3,
          spaceBetween: 40,
        },
        768: {
          slidesPerView: 4,
          spaceBetween: 40,
        },
        1024: {
          slidesPerView: 5,
          spaceBetween: 50,
        },
        1280: {
          slidesPerView: 6,
          spaceBetween: 50,
        },
      }"
      class="partners-swiper"
    >
      <SwiperSlide v-for="(partner, index) in partners" :key="index">
        <div class="partner-slide">
          <img :src="partner.logo" :alt="partner.alt" class="partner-logo" />
        </div>
      </SwiperSlide>
      <!-- Duplicate slides for seamless loop -->
      <SwiperSlide v-for="(partner, index) in partners" :key="`dup-${index}`">
        <div class="partner-slide">
          <img :src="partner.logo" :alt="partner.alt" class="partner-logo" />
        </div>
      </SwiperSlide>
    </Swiper>
  </div>
</template>
