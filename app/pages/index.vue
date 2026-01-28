<script setup lang="ts">
import { LucideChefHat, LucideUtensils, LucidePartyPopper, LucideMapPin, LucideArrowRight } from 'lucide-vue-next'
import gsap from 'gsap'
import type { HomepageContent } from '~/composables/usePageContent'

const heroTitle = ref(null)
const { getHomepageContent } = usePageContent()
const content = ref<HomepageContent | null>(null)
const { settings } = useGlobalSettings()

// Dynamic button URLs with fallbacks
const btnExplore = computed(() => settings.value?.icons?.btn_explore || '/images/btnExplore.svg')
const btnLearnMore = computed(() => settings.value?.icons?.btn_learn_more || '/images/btnLearnMoreAboutUs.svg')

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

// Hero video computed properties
const heroVideoType = computed(() => content.value?.hero_section?.video_type || 'image')
const heroYoutubeId = computed(() => content.value?.hero_section?.youtube_id || '')
const heroWistiaId = computed(() => content.value?.hero_section?.wistia_id || '')
const heroVideoUrl = computed(() => content.value?.hero_section?.video_url || '')
const heroBackgroundStyle = computed(() => {
  // Only show background image if video_type is 'image' or not set
  if (heroVideoType.value === 'image' && content.value?.hero_section?.bg) {
    return {
      backgroundImage: `url('${content.value.hero_section.bg}')`,
      backgroundSize: 'cover',
      backgroundPosition: 'center'
    }
  }
  return {}
})

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
  <div class="overflow-hidden">
    <!-- Loading state -->
    <div v-if="!content" class="d-flex justify-content-center align-items-center min-vh-100">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>
    
    <!-- Main content -->
    <template v-else>
    <!-- Hero Section -->
    <section id="hero" class="container-fluid d-flex align-items-center v-90 px-0 w-100">
      <div class="d-flex flex-row row w-100">
        <!-- Hero media container (image, YouTube or MP4 video) with job search form -->
        <div id="marr" class="col-lg-6 col-md-6 p-0 min-vh-100 position-relative hero-media-container"
          :style="heroBackgroundStyle"
          :class="{ 'has-video': heroVideoType !== 'image' }">
          
          <!-- YouTube Video Background -->
          <div v-if="heroVideoType === 'youtube' && heroYoutubeId" class="hero-video-wrapper">
            <iframe
              :src="`https://www.youtube.com/embed/${heroYoutubeId}?autoplay=1&mute=1&loop=1&playlist=${heroYoutubeId}&controls=0&showinfo=0&modestbranding=1&rel=0&playsinline=1`"
              frameborder="0"
              allow="autoplay; encrypted-media"
              allowfullscreen
              class="hero-youtube-iframe"
            ></iframe>
          </div>

          <!-- Wistia Video Background -->
          <div v-else-if="heroVideoType === 'wistia' && heroWistiaId" class="hero-video-wrapper">
            <iframe
              :src="`https://fast.wistia.net/embed/iframe/${heroWistiaId}?autoPlay=true&silentAutoPlay=true&muted=true&endVideoBehavior=loop&controlsVisibleOnLoad=false&playbar=false&fullscreenButton=false&playButton=false&settingsControl=false&volumeControl=false&smallPlayButton=false&fitStrategy=cover`"
              frameborder="0"
              allow="autoplay; fullscreen"
              allowfullscreen
              class="hero-wistia-iframe"
            ></iframe>
          </div>

          <!-- MP4 Video Background -->
          <video 
            v-else-if="heroVideoType === 'mp4' && heroVideoUrl" 
            class="hero-video-mp4"
            autoplay 
            muted 
            loop 
            playsinline
          >
            <source :src="heroVideoUrl" type="video/mp4">
          </video>
         
          <!-- Job Search Form -->
          <div class="job-search-form-wrapper">
            <FormsJobSearchForm />
          </div>
        </div>
        <!-- right: Text -->
        <div id="blusy" class="col-lg-6 col-md-6 p-4 d-flex flex-wrap
          flex-column  justify-content-center align-items-start min-vh-90">
          <div class="txtwrapper">
            <h1 id="heroTitle" ref="heroTitle" class="font-heading display-1 fw-bold lh-1 text-brand-dark m-0">
              {{ content.hero_section?.title?.line_1 }}
            </h1>
             <h1 id="heroTitleAlt" ref="heroTitle" class="font-heading display-1 fw-bold lh-1 text-brand-dark m-0">
              {{ content.hero_section?.title?.line_2 }}
            </h1>
            <p class="position-relative d-inline-block m-0 recoleta mb-4 mt-4">
              {{ content.hero_section?.title?.line_3 }}
            </p>
          </div>

          <NuxtLink to="#mapPreview" aria-label="Contactez-nous" class="d-inline-block mt-4 m-0 sanga">
            <NuxtImg id="btnExplore" :src="btnExplore" alt="Contact" />
          </NuxtLink>
        </div>
      </div>
    </section>
    <section id="presentationEatIsFamily" class="py-5 bg-white">
      <div id="tromp" class="container">
        <div class="kemiseba" v-html="content.intro_section?.texte"></div>
        <NuxtLink to="/about" aria-label="En savoir plus sur nous" class="mt-4 d-inline-block mt-4">
          <NuxtImg :src="btnLearnMore" alt="En savoir plus sur nous" />
        </NuxtLink>
      </div>

    </section>
    <!-- Map Preview Section - Explore Where We Operate -->
    <HomeExploreSection />

    <!-- Services Section -->
    <section id="services" class="py-5 bg-white">
      <div class="container-fluid m-0 p-0">
        <div class="text-left mb-5 p-4">
          <h2 class="font-heading display-4 fw-bold">
            {{ content.services_section?.title?.line_1 }}<br />
            <span class="position-relative d-inline-block">
              {{ content.services_section?.title?.highlight }}
              <span class="position-absolute bottom-0 start-0 w-100 bg-brand-blue highlight-bar"></span>
            </span> {{ content.services_section?.title?.line_2 }}
          </h2>
        </div>

        <div class="prod">
          <div class="service-wrapper d-flex">
            <div class="row m-0 p-0">
              <div :id="`service-${i}`" v-for="(service, i) in content.services_section?.services || []" :key="i"
                class="service d-flex row  m-0 p-0">
                <div class="col-lg-6 col-md-6 lefun p-2">
                  <div
                    :style="{ backgroundImage: service.thumbnail ? `url('${service.thumbnail}')` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }"
                    class="rounded w-100 h-100 "></div>
                </div>
                <div class="col-lg-6 col-md-6 rounded p-2 service-desc">
                  <div class="innerService rounded p-2">
                    <h3 class="recoleta preserve-lines">{{ service.title }}</h3>
                    <div class="recoleta mt-4 mb-4 preserve-lines"><p v-html="service.description"></p></div>
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
        <div class="kemiseba-alt" v-html="content.cta_section?.description"></div>
      </div>
    </section>

    <!-- Food Gallery Section -->
    <GalleryGrid :images="content.gallery_section?.images || []" />
    <!-- sustainable service  -->
    <section id="sustan" class="py-5 bg-white">
      <!-- une loop qui affiche les services durables -->
      <h3 class="font-header center text-center">{{ content.sustainable_service_title }}</h3>
      <div id="brik" class="row mx-auto ">
        <div :id="`sustainable-service-${index}`" v-for="(service, index) in content.sustainable_service || []" :key="index"
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
        <TwoColumnText :title="content.beautiful?.title" :text="content.beautiful?.text" />
        <div class="row mt-4">
          <img :src="content.beautiful?.image" alt="Beautiful Moments We've Helped Create"
            class="img-fluid mx-auto d-block">
        </div>
        <div id="lasalle" class="row mt-4">
          <div :id="`example-${index}`" v-for="(example, index) in content.examples || []" :key="index"
            class="col-md-6 example">
            <div class="inner-example">
              <h4>{{ example.title }}</h4>
              <p>{{ example.texte }}</p>
              <NuxtLink :to="example.link">
                <NuxtImg :src="example.btn" :alt="example.title" />
              </NuxtLink>

            </div>

          </div>
        </div>
      </div>
    </section>
    <!-- clients -->
    <PartnersSection :title="content.partners_title" :partners="(content.partners || []).map(p => ({ ...p, name: p.alt }))" />
    <!--  ready to make an impact -->
    <HomepageCTA
      :image="content.homepageCTA?.image"
      :title="content.homepageCTA?.title"
      :description="content.homepageCTA?.description"
      :link="content.homepageCTA?.link"
      :button-image="content.homepageCTA?.button"
      :additional-text="content.homepageCTA?.additionalText"
      :button-image2="content.homepageCTA?.button2"
    />
    </template>
  </div>
</template>

<style scoped lang="scss">
.txtwrapper{
  max-width:800px;
}
  /* Préserve les retours à la ligne du JSON */
.preserve-lines {
  white-space: pre-line;
}

/* Effets de survol pour les boutons */
a:has(img),
.sanga,
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
#marr {
  min-height: 100vh !important;
  padding: 0 !important;
  position: relative;
}

/* Hero Video Styles */
.hero-media-container {
  overflow: hidden;
  
  &.has-video {
    background: #000;
  }
}

.hero-video-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
  pointer-events: none;
}

.hero-youtube-iframe,
.hero-wistia-iframe {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100vw;
  height: 100vh;
  min-width: 100%;
  min-height: 100%;
  transform: translate(-50%, -50%);
  object-fit: cover;
  pointer-events: none;
}

.hero-video-mp4 {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}

.hero-blur-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(255, 245, 238, 0.3) 0%,
    rgba(255, 220, 200, 0.2) 50%,
    rgba(255, 200, 180, 0.3) 100%
  );
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 1;
}

.job-search-form-wrapper {
     position: absolute;
    bottom: 0%;
    left: 0;
    margin: auto;
    z-index: 2;
    padding: 1rem;
    right: 0;
    max-width: 558px;
}

.job-search-form-placeholder {
}

@media (max-width: 768px) {
  .job-search-form-wrapper {
    position: relative;
    top: auto;
    left: auto;
    transform: none;
    width: 100%;
    max-width: none;
    padding: 2rem 1rem;
  }

  #marr {
    min-height: auto !important;
    padding: 2rem 0 !important;
  }
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
    background-image: url('/images/sus1.svg');
    transform: rotate(-356deg);

  }

}

#sustainable-service-1 {
  .inner {
    background-image: url('/images/sus2.svg');
    transform: rotate(-1deg);

  }

}

#sustainable-service-2 {
  .inner {
    background-image: url('/images/sus3.svg');
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
