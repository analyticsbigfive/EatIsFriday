<script setup lang="ts">
import { LucideChefHat, LucideUtensils, LucidePartyPopper, LucideMapPin, LucideArrowRight } from 'lucide-vue-next'
import gsap from 'gsap'
import type { HomepageContent } from '~/composables/usePageContent'

const heroTitle = ref(null)
const { getHomepageContent } = usePageContent()
const content = ref<HomepageContent | null>(null)

// Icon mapping
const iconMap: Record<string, any> = {
  'chef-hat': LucideChefHat,
  'utensils': LucideUtensils,
  'party-popper': LucidePartyPopper
}

// Color mapping
const bgColorMap: Record<string, string> = {
  'pink': 'bg-brand-pink',
  'lime': 'bg-brand-lime',
  'yellow': 'bg-brand-yellow'
}

const textColorMap: Record<string, string> = {
  'white': 'text-white',
  'dark': 'text-brand-dark'
}

onMounted(async () => {
  content.value = await getHomepageContent()
  
  if (heroTitle.value && content.value) {
    gsap.from(heroTitle.value, {
      y: 60,
      opacity: 0,
      duration: 1,
      ease: 'power4.out',
      delay: 0.2
    })
  }
})
</script>

<template>
  <div class="overflow-hidden" v-if="content">
    <!-- Hero Section -->
    <section id="hero" class="container-fluid d-flex align-items-center v-90 px-0 w-100">
       <div class="d-flex flex-row row w-100">
            <!-- image de la nourriture -->
      <div id="marr" class="col-lg-6 col-md-6 p-0 min-vh-100" :style="{ position: 'relative', backgroundImage: `url('${content.hero_section.bg}')`, backgroundSize: 'cover', backgroundPosition: 'center' }">
        <span></span>
       </div>
        <!-- right: Text -->
          <div id="blusy" class="col-lg-6 col-md-6 p-4 d-flex flex-wrap
          flex-column  justify-content-center align-items-start min-vh-90">
            
              <div class="txtwrapper">
                <h1 id="heroTitle" ref="heroTitle" class="font-heading display-1 fw-bold lh-1 text-brand-dark m-0">
                {{ content.hero_section.title.line_1 }}
            </h1>
              <p class="position-relative d-inline-block m-0 recoleta mb-4 mt-4">
                {{ content.hero_section.title.line_2 }}
              </p>
              </div>
          
            <NuxtLink to="/contact" aria-label="Contactez-nous" class="d-inline-block mt-4 m-0">
              <NuxtImg id="btnExplore" src="/images/btnExplore.svg" alt="Contact" />
            </NuxtLink>
          </div>
        </div>
    </section>
<section id="presentationEatIsFamily" class="py-5 bg-white">
<div id="tromp" class="container">
  <div class="kemiseba" v-html="content.intro_section.texte"></div>
  <NuxtLink to="/about" aria-label="En savoir plus sur nous" class="mt-4 d-inline-block mt-4">
    <NuxtImg src="/images/btnLearnMoreAboutUs.svg" alt="En savoir plus sur nous" />
  </NuxtLink>
</div>

</section>
    <!-- Map Preview Section -->
    <section class="py-5 bg-brand-gray overflow-hidden">
      <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-center gap-5">
          <div class="col-lg-4">
            <span class="tag-blue mb-3 d-inline-block">{{ content.locations_section.tag }}</span>
            <h2 class="font-heading display-4 fw-bold mb-4">
              {{ content.locations_section.title.line_1 }}<br/>
              <span class="position-relative d-inline-block">
                {{ content.locations_section.title.line_2_highlight }}
                <span class="position-absolute bottom-0 start-0 w-100 bg-brand-lime highlight-bar"></span>
              </span>
            </h2>
            <p class="text-muted font-body mb-4 lh-lg">
              {{ content.locations_section.description }}
            </p>

            <ul class="list-unstyled d-flex flex-column gap-3">
              <li v-for="loc in content.locations_section.locations" :key="loc.name" class="d-flex align-items-center justify-content-between p-3 bg-white border-organic location-item">
                <div class="d-flex align-items-center gap-3">
                  <div class="location-icon rounded-circle bg-brand-pink d-flex align-items-center justify-content-center border border-2 border-dark">
                    <LucideMapPin class="text-white" style="width: 1.25rem; height: 1.25rem;" />
                  </div>
                  <span class="fw-bold fs-5">{{ loc.name }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="text-brand-pink fw-bold">{{ loc.count }} {{ content.locations_section.jobs_suffix }}</span>
                  <LucideArrowRight class="text-secondary" style="width: 1rem; height: 1rem;" />
                </div>
              </li>
            </ul>
          </div>

          <div class="col-lg-8 map-container">
            <div class="w-100 h-100 border-organic shadow-organic overflow-hidden">
              <ClientOnly>
                <VenueMap :venues="content.locations_section.map_venues.map(v => ({
                  id: v.id,
                  name: v.name,
                  location: v.location,
                  lat: v.lat,
                  lng: v.lng,
                  openPositions: v.open_positions
                }))" />
              </ClientOnly>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-white">
      <div class="container">
        <div class="text-left mb-5">
          <h2 class="font-heading display-4 fw-bold">
            {{ content.services_section.title.line_1 }}<br/>
            <span class="position-relative d-inline-block">
              {{ content.services_section.title.highlight }}
              <span class="position-absolute bottom-0 start-0 w-100 bg-brand-blue highlight-bar"></span>
            </span> {{ content.services_section.title.line_2 }}
          </h2>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
          <div
            v-for="(service, i) in content.services_section.services"
            :key="i"
            class="col"
          >
            <div :class="['card-organic position-relative overflow-hidden h-100 service-card', bgColorMap[service.bg_color || 'pink'], textColorMap[service.text_color || 'white']]">
              <component :is="iconMap[service.icon]" class="service-icon mb-4" style="width: 3rem; height: 3rem;" />
              <h3 class="font-heading fs-3 fw-bold mb-3">{{ service.title }}</h3>
              <p class="opacity-75 font-body">{{ service.description }}</p>

              <div class="service-circle position-absolute rounded-circle"></div>

              <button class="mt-4 d-flex align-items-center gap-2 fw-bold border-0 bg-transparent service-btn">
                {{ content.services_section.learn_more_button }} <LucideArrowRight style="width: 1rem; height: 1rem;" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Partners Section -->
    <section class="py-5 bg-brand-gray border-top border-bottom border-dark border-2">
      <div class="container">
        <p class="text-center font-heading fs-4 mb-4">
          {{ content.partners_section.intro_text }}
        </p>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-5">
          <div v-for="(partner, i) in content.partners_section.partners" :key="i" class="fs-2 font-heading fw-bold text-secondary partner-item">
            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" style="height: 3rem;" />
            <span v-else>{{ partner.name }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-brand-pink text-white text-center position-relative overflow-hidden">
      <div class="position-absolute top-0 start-0 end-0 bottom-0 opacity-10">
        <div class="position-absolute top-0 cta-circle-1 border border-2 border-white rounded-circle"></div>
        <div class="position-absolute bottom-0 cta-circle-2 border border-2 border-white rounded-circle"></div>
      </div>

      <div class="container position-relative" style="z-index: 10;">
        <h2 class="font-heading display-4 fw-bold mb-4">{{ content.cta_section.title }}</h2>
        <p class="text-white-50 mx-auto mb-5 fs-5 font-body" style="max-width: 42rem;">
          {{ content.cta_section.description }}
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
          <BaseButton :to="content.cta_section.cta_primary.link" variant="lime" size="lg">{{ content.cta_section.cta_primary.text }}</BaseButton>
          <BaseButton :to="content.cta_section.cta_secondary.link" variant="secondary" size="lg">{{ content.cta_section.cta_secondary.text }}</BaseButton>
        </div>
      </div>
    </section>

    <!-- Food Gallery Section -->
    <section class="py-5 bg-brand-dark">
      <div class="container">
        <div class="row row-cols-2 row-cols-md-4 g-3">
          <div v-for="(image, i) in content.gallery_section.images" :key="i" class="col">
            <div class="border-organic overflow-hidden gallery-item">
              <NuxtImg
                :src="image.src"
                class="w-100 h-100 object-fit-cover gallery-img"
                :alt="image.alt"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.tag-pink {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 700;
  border: 2px solid black;
  background-color: var(--brand-pink, #FF4D6D);
  color: white;
  border-radius: 50px 10px 45px 10px / 10px 45px 10px 50px;
}

.min-vh-90 {
  min-height: 90vh;
}

.highlight-bar {
  height: 0.75rem;
  z-index: -1;
}

.location-icon {
  width: 2.5rem;
  height: 2.5rem;
}

.location-item {
  cursor: pointer;
  transition: all 0.3s ease;
}

.location-item:hover {
  box-shadow: 4px 4px 0 rgba(0, 0, 0, 1);
}

.map-container {
  height: 500px;
}

.service-card:hover .service-icon {
  transform: scale(1.1);
}

.service-icon {
  transition: transform 0.3s ease;
}

.service-circle {
  width: 8rem;
  height: 8rem;
  background: rgba(0, 0, 0, 0.1);
  bottom: -2.5rem;
  right: -2.5rem;
  transition: transform 0.5s ease;
}

.service-card:hover .service-circle {
  transform: scale(1.5);
}

.service-btn {
  color: inherit;
  transition: gap 0.3s ease;
}

.service-card:hover .service-btn {
  gap: 1rem !important;
}

.partner-item {
  cursor: pointer;
  transition: color 0.3s ease;
}

.partner-item:hover {
  color: var(--brand-dark, #0d0a00) !important;
}

.cta-circle-1 {
  width: 20rem;
  height: 20rem;
  left: 25%;
}

.cta-circle-2 {
  width: 15rem;
  height: 15rem;
  right: 25%;
}

.gallery-item {
  aspect-ratio: 1;
}

.gallery-img {
  transition: transform 0.5s ease;
}

.gallery-img:hover {
  transform: scale(1.1);
}
</style>
