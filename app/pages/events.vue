<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Event } from '~/composables/useEvents'
const { getSiteContent } = useSiteContent()
const { getContentByPath, getHomepageContent } = usePageContent()
const homepageContent = ref<any>(null)
const siteContent = ref<any>(null)
const pageContent = ref<any>(null)
const content = ref<any>(null)
const { getEvents } = useEvents()
const events = ref<Event[]>([])
const loading = ref(true)

// Computed property for events gallery 1 with fallback
const eventsGalleryImages = computed(() => {
  // Try events-specific gallery first, then fallback to about.gallery_section2
  return siteContent.value?.about?.events_gallery?.images
    || siteContent.value?.about?.gallery_section2?.images
    || []
})

// Computed property for events gallery 2
const eventsGalleryImages2 = computed(() => {
  return siteContent.value?.about?.events_gallery_2?.images || []
})

onMounted(async () => {
  // Charger le contenu de la page
  content.value = await getContentByPath('events')
  // Charger le site content pour la galerie
  siteContent.value = await getSiteContent()
  //charger le contenu pour les différents composant
  homepageContent.value = await getHomepageContent()
  // Charger les événements depuis l'API WordPress
  try {
    const data = await getEvents()
    if (data) {
      events.value = data
    }
    console.log('Events loaded:', data)
  } catch (error) {
    console.error('Error loading events:', error)
  }

  loading.value = false
})

// Helper pour valider si un titre SEO est valide (pas une URL)
const isValidSeoTitle = (title: string | undefined) => {
  if (!title) return false
  return !title.includes('http') && !title.includes('wp-content') && !title.includes('themes')
}

useHead(() => ({
  title: isValidSeoTitle(content.value?.seo?.title) ? content.value.seo.title : 'Events - Eat Is Family',
  meta: [
    { name: 'description', content: content.value?.seo?.description || 'Discover our food events and culinary experiences across France.' }
  ]
}))
</script>

<template>
  <div class="events-page">
    <!-- Loading Screen -->
    <LoadingScreen v-if="loading" />
    
    <template v-else>
    <!-- Hero Section -->
    <section v-if="content" class="page-hero">
      <div class="container d-flex">
        <div class="loris">
          <h1>{{ content.page_hero.title }}</h1>
          <p class="subtitle" v-html="content.page_hero.subtitle"></p>
          <NuxtLink v-if="content.page_hero.link" :to="content.page_hero.link" class="mt-4">
            <nuxt-img :src="content.page_hero.btn" />
          </NuxtLink>
        </div>

      </div>
    </section>

    <!-- Intro Section -->
    <section v-if="content?.section2" class="intro-section">
      <div class="container">
        <p class="intro-text preserve-lines" v-html="content.section2"></p>
      </div>
    </section>

    <!-- Events List Section -->
    <section class="events-section">
      <div class="container">
        <div v-if="content?.eventslist" class="section-header">
          <p class="section-description preserve-lines" >{{ content.eventslist.description }}</p>
        </div>

        <div v-if="loading" class="loading">
          Loading events...
        </div>

        <div v-else-if="events.length > 0">
          <!-- First half of events (odd) -->
          <div class="events-grid">
            <CardsEventCard v-for="(event, index) in events.slice(0, Math.ceil(events.length / 2))" :key="event.id"
              :event="event" :color-index="index" />
          </div>

          <!-- Gallery component in the middle - uses events-specific gallery with fallback -->
          <GalleryGrid v-if="eventsGalleryImages.length > 0"
            :images="eventsGalleryImages" />
          <!-- Second half of events (even) -->
          <div class="events-grid">
            <CardsEventCard v-for="(event, index) in events.slice(Math.ceil(events.length / 2))" :key="event.id"
              :event="event" :color-index="index + Math.ceil(events.length / 2)" />
          </div>
        </div>

        <div v-else class="no-events">
          No events found. ({{ events.length }} events)
        </div>
      </div>
    </section>
    <section class="mt-4">
      <PartnersSection v-if="homepageContent" :title="homepageContent.partners_title"
        :partners="(homepageContent.partners || []).map((p: any) => ({ ...p, name: p.alt }))" />
    </section>
   <!--  <section v-if="eventsGalleryImages2.length > 0" class="mt-4">
      <GalleryGrid :images="eventsGalleryImages2" />
    </section> -->
    </template>
  </div>
</template>

<style scoped lang="scss">
.events-page {
  min-height: 100vh;
}

.page-hero {
  background: url('/images/image4.jpg') center/cover no-repeat;
  height: 90vh;
  margin: 0 0 0 0;
  justify-content: flex-end;
  display: flex;
  align-items: flex-end;

  a {
    width: 250px;
    height: 70px;

    img {
      width: 250px;
      height: 70px;
    }
  }

  .loris {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    background: url("/images/loris.svg");
    background-size: cover;
    background-repeat: no-repeat;
    max-width: 915px;
    max-height: 472px;
    padding: 3em;
    height: 100vh;
    width: 100vw;
  }

  h1 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 75px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.2;
    letter-spacing: normal;
    text-align: left;
    color: #000;
    z-index:1;
    position: relative;
    &::before {
      z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/decoHeaderBg.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 1vh;
      left: -1vw;
    }
  }

  .subtitle {
    font-family: FONTSPRINGDEMO-Recoleta;
    font-size: 18px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.44;
    letter-spacing: normal;
    text-align: left;
    color: #161616;
  }

}

.intro-section {
  padding: 4rem 0;
  background-color: white;
  background-image: url('/images/vectorBgAbout.svg');
  background-repeat: no-repeat;
  background-position: center;
  background-size: contain;

  .container {
    max-width: 1100px;
    margin: 0 auto;
  }

  .intro-text {
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

.events-section {
  padding: 4rem 0;

  .section-header {
    text-align: center;
    margin-bottom: 3rem;
  }

  .section-description {
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
}

.events-grid {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.loading,
.no-events {
  text-align: center;
  padding: 4rem 0;
  font-size: 1.25rem;
  color: #718096;
}
</style>
