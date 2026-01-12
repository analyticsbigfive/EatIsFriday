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
        <div id="marr" class="col-lg-6 col-md-6 p-0 min-vh-100"
          :style="{ position: 'relative', backgroundImage: `url('${content.hero_section.bg}')`, backgroundSize: 'cover', backgroundPosition: 'center' }">
          <span></span>
        </div>
        <!-- right: Text -->
        <div id="blusy" class="col-lg-6 col-md-6 p-4 d-flex flex-wrap
          flex-column  justify-content-center align-items-start min-vh-90">
          <div class="txtwrapper">
            <h1 id="heroTitle" ref="heroTitle" class="font-heading display-1 fw-bold lh-1 text-brand-dark m-0">
              {{ content.hero_section.title.line_1 }}
              {{ content.hero_section.title.line_2 }}
            </h1>
            <p class="position-relative d-inline-block m-0 recoleta mb-4 mt-4">
              {{ content.hero_section.title.line_3 }}
            </p>
          </div>

          <NuxtLink to="/contact" aria-label="Contactez-nous" class="d-inline-block mt-4 m-0 sanga">
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
              {{ content.locations_section.title.line_1 }}<br />
              <span class="position-relative d-inline-block">
                {{ content.locations_section.title.line_2_highlight }}
                <span class="position-absolute bottom-0 start-0 w-100 bg-brand-lime highlight-bar"></span>
              </span>
            </h2>
            <p class="text-muted font-body mb-4 lh-lg">
              {{ content.locations_section.description }}
            </p>

            <ul class="list-unstyled d-flex flex-column gap-3">
              <li v-for="loc in content.locations_section.locations" :key="loc.name"
                class="d-flex align-items-center justify-content-between p-3 bg-white border-organic location-item">
                <div class="d-flex align-items-center gap-3">
                  <div
                    class="location-icon rounded-circle bg-brand-pink d-flex align-items-center justify-content-center border border-2 border-dark">
                    <LucideMapPin class="text-white" style="width: 1.25rem; height: 1.25rem;" />
                  </div>
                  <span class="fw-bold fs-5">{{ loc.name }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="text-brand-pink fw-bold">{{ loc.count }} {{ content.locations_section.jobs_suffix
                    }}</span>
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
      <div class="container-fluid m-0 p-0">
        <div class="text-left mb-5 p-4">
          <h2 class="font-heading display-4 fw-bold">
            {{ content.services_section.title.line_1 }}<br />
            <span class="position-relative d-inline-block">
              {{ content.services_section.title.highlight }}
              <span class="position-absolute bottom-0 start-0 w-100 bg-brand-blue highlight-bar"></span>
            </span> {{ content.services_section.title.line_2 }}
          </h2>
        </div>

        <div class="prod">
          <div class="service-wrapper d-flex">
            <div class="row m-0 p-0">
              <div :id="`service-${i}`" v-for="(service, i) in content.services_section.services" :key="i"
                class="service d-flex row  m-0 p-0">
                <div class="col-lg-6 col-md-6 lefun p-2">
                  <div
                    :style="{ backgroundImage: service.thumbnail ? `url('${service.thumbnail}')` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }"
                    class="rounded w-100 h-100 "></div>
                </div>
                <div class="col-lg-6 col-md-6 rounded p-2 service-desc">
                  <div class="innerService rounded p-2">
                    <h3 class="recoleta preserve-lines">{{ service.title }}</h3>
                    <p class="recoleta mt-4 mb-4 preserve-lines">{{ service.description }}</p>
                    <NuxtLink :to="service.linkTo">
                      <NuxtImg :src="service.btnImage" alt="En savoir plus sur nos services" />
                    </NuxtLink>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-white">
      <div class="container">
        <div class="kemiseba-alt" v-html="content.cta_section.description"></div>
      </div>
    </section>

    <!-- Food Gallery Section -->
    <section class="py-5 bg-white">
      <div class="container">
        <div class="gallery-grid">
          <!-- Grande image à gauche -->
          <div class="gallery-item-large">
            <div class="gallery-item-wrapper">
              <NuxtImg :src="content.gallery_section.images[0].src" class="w-100 gallery-img"
                :alt="content.gallery_section.images[0].alt" />
            </div>
          </div>

          <!-- 2 images à droite -->
          <div class="gallery-items-right">
            <div class="gallery-item-small" v-for="(image, i) in content.gallery_section.images.slice(1)" :key="i">
              <div class="gallery-item-wrapper">
                <NuxtImg :src="image.src" class="w-100 gallery-img" :alt="image.alt" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- sustainable service  -->
    <section id="sustan" class="py-5 bg-white">
      <!-- une loop qui affiche les services durables -->
      <h3 class="font-header center text-center">{{ content.sustainable_service_title }}</h3>
      <div id="brik" class="row mx-auto ">
        <div :id="`sustainable-service-${index}`" v-for="(service, index) in content.sustainable_service" :key="index"
          class="sustainable-service-item col-md-4 text-center p-4">
          <div class="inner">
            <img :src="service.icone" :alt="service.title" />
            <h4>{{ service.title }}</h4>
            <p>{{ service.description }}</p>
          </div>
        </div>
      </div>
    </section>
    <!-- concession powering great public events  -->
    <section id="beautiful-moments" class="py-5 bg-white">
      <div class="container-fluid">
        <div class="row">
          <div class="col">
            <h3 class="damsnt" v-html="content.beautiful.title"></h3>
          </div>
          <div class="col">
            <p class="kemi" v-html="content.beautiful.text"></p>
          </div>
        </div>
        <div class="row mt-4">
          <img :src="content.beautiful.image" alt="Beautiful Moments We’ve Helped Create"
            class="img-fluid mx-auto d-block">
        </div>
        <div id="lasalle" class="row mt-4">
          <div :id="`example-${index}`" v-for="(example, index) in content.examples" :key="index" class="col-md-6 example">
            <div class="inner-example">
            <h4>{{ example.title }}</h4>
            <p>{{ example.texte }}</p>
            <nuxt-link :to="example.link">
              <nuxt-img :src="example.btn" :alt="example.title"></nuxt-img>
            </nuxt-link>

            </div>

          </div>
        </div>
      </div>
    </section>
<section class="py-5 bg-white"></section>
  </div>
</template>

<style scoped lang="scss">
/* Préserve les retours à la ligne du JSON */
.preserve-lines {
  white-space: pre-line;
}

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

/* Gallery Grid Layout */
.gallery-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  min-height: 600px;
}

.gallery-item-large {
  grid-row: 1 / -1;
  height: 100%;
}

.gallery-items-right {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.gallery-item-small {
  flex: 1;
}

.gallery-item-wrapper {
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  height: 100%;
}

.gallery-item-wrapper:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.gallery-img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.gallery-img:hover {
  transform: scale(1.05);
}

/* Responsive: sur mobile, passer en une seule colonne */
@media (max-width: 768px) {
  .gallery-grid {
    grid-template-columns: 1fr;
    min-height: auto;
  }

  .gallery-item-large {
    grid-row: auto;
    height: 400px;
  }

  .gallery-item-small {
    height: 250px;
  }
}

#brik {
  max-width: 1380px;
}

.sustainable-service-item {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;

  .inner {
    width: 441.43px;
    height: 437px;
    border-radius: 20px;
    background-size: contain;
    padding: 2em;
    background-repeat: no-repeat;

    h4 {
      font-family: FONTSPRINGDEMO-RecoletaBold;
      font-size: 24px;
      font-weight: normal;
      font-stretch: normal;
      font-style: normal;
      line-height: normal;
      letter-spacing: normal;
      text-align: center;
      color: #000;
      text-transform: capitalize;
      margin: 1em 0;
    }

    p {
      font-family: FONTSPRINGDEMO-Recoleta-Regular !important;
      font-size: 16px;
      font-weight: normal;
      font-stretch: normal;
      font-style: normal;
      line-height: 1.52;
      letter-spacing: normal;
      text-align: center;
      color: #000;
      margin: 1em 0;

    }
  }
}

#sustainable-service-0 {
  .inner {
    background-image: url('images/sus1.svg');
    transform: rotate(-356deg);

  }

}

#sustainable-service-1 {
  .inner {
    background-image: url('images/sus2.svg');
    transform: rotate(-1deg);

  }

}

#sustainable-service-2 {
  .inner {
    background-image: url('images/sus3.svg');
    transform: rotate(-4deg);

  }

}

#beautiful-moments {
  h3 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 50px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #000;
  }

  p {
    font-family: FONTSPRINGDEMO-Recoleta-Regular !important;
    font-size: 18px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.56;
    letter-spacing: normal;
    text-align: left;
    color: #000;
  }
}

</style>
