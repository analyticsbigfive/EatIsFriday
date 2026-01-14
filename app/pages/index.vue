<script setup lang="ts">
import { LucideChefHat, LucideUtensils, LucidePartyPopper, LucideMapPin, LucideArrowRight } from 'lucide-vue-next'
import gsap from 'gsap'
import type { HomepageContent } from '~/composables/usePageContent'

const heroTitle = ref(null)
const { getHomepageContent } = usePageContent()
const content = ref<HomepageContent | null>(null)
const activeEventFilter = ref<string | undefined>(undefined)
const selectedVenue = ref<{
  id: string
  name: string
  location: string
  type?: string
  lat: number
  lng: number
  image?: string
  image2?: string
  logo?: string
  capacity?: string
  staff_members?: number
  recent_event?: string
  guests_served?: string
  shops_count?: number
  menus_count?: number
  description?: string
} | null>(null)

const toggleEventFilter = (filterId: string) => {
  // Réinitialiser le lieu sélectionné
  selectedVenue.value = null

  if (activeEventFilter.value === filterId) {
    activeEventFilter.value = undefined
  } else {
    activeEventFilter.value = filterId
  }
}

const handleVenueClick = (venue: any) => {
  selectedVenue.value = venue
}

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
    <!-- Map Preview Section - Explore Where We Operate -->
    <section id="mapPreview" class="explore-section">
      <div class="explore-container">
        <!-- Map on the left -->
        <div class="explore-map">
          <ClientOnly>
            <VenueMap
              :venues="content.locations_section.map_venues.map(v => ({
                id: v.id,
                name: v.name,
                location: v.location,
                type: v.type,
                lat: v.lat,
                lng: v.lng,
                image: v.image,
                image2: v.image2,
                logo: v.logo,
                capacity: v.capacity,
                staff_members: v.staff_members,
                recent_event: v.recent_event,
                guests_served: v.guests_served,
                shops_count: v.shops_count,
                menus_count: v.menus_count,
                description: v.description
              }))"
              :active-filter="activeEventFilter"
              @venue-clicked="handleVenueClick"
            />
          </ClientOnly>
        </div>

        <!-- Content on the right -->
        <div class="explore-content">
          <!-- Affichage des détails du lieu sélectionné -->
          <div v-if="selectedVenue" class="venue-details">
            <!-- Images en haut -->
            <div class="venue-images-grid">
              <div class="venue-image-wrapper">
                <span class="venue-type-badge">{{ selectedVenue.type }}</span>
                <div class="venue-image" :style="{ backgroundImage: `url('${selectedVenue.image}')` }"></div>
              </div>
              <div class="venue-image-wrapper">
                <button class="venue-close-btn" @click="selectedVenue = null">&times;</button>
                <div class="venue-image" :style="{ backgroundImage: `url('${selectedVenue.image2 || selectedVenue.image}')` }"></div>
              </div>
            </div>

            <!-- Nom et logo -->
            <div class="venue-header">
              <img :src="selectedVenue.logo" :alt="selectedVenue.name" class="venue-logo" />
              <h3 class="venue-name">{{ selectedVenue.name }}</h3>
            </div>

            <!-- Infos: Location, Capacity, Staff -->
            <div class="venue-info-row">
              <div class="venue-info-item">
                <span class="venue-info-icon">📍</span>
                <span class="venue-info-text">{{ selectedVenue.location }}</span>
              </div>
              <div class="venue-info-item">
                <span class="venue-info-label">Capacity:</span>
                <span class="venue-info-value">{{ selectedVenue.capacity }} capacity facility</span>
              </div>
              <div class="venue-info-item">
                <span class="venue-info-label">Staff Members:</span>
                <span class="venue-info-value">{{ selectedVenue.staff_members }} staffs</span>
              </div>
            </div>

            <!-- Bannière événement récent -->
            <div class="venue-event-banner">
              <div class="venue-event-item">
                <span class="venue-event-label">RECENT EVENT</span>
                <span class="venue-event-value">{{ selectedVenue.recent_event }}</span>
              </div>
              <div class="venue-event-item">
                <span class="venue-event-label">GUEST SERVED</span>
                <span class="venue-event-value">{{ selectedVenue.guests_served }} Guest Served</span>
              </div>
            </div>

            <!-- Onglets -->
            <div class="venue-tabs">
              <button class="venue-tab active">
                <span class="tab-icon">◉</span> OVERVIEW
              </button>
              <button class="venue-tab">
                <span class="tab-icon">🏪</span> SHOPS ({{ selectedVenue.shops_count }})
              </button>
              <button class="venue-tab">
                <span class="tab-icon">🍽️</span> MENUS ({{ selectedVenue.menus_count }})
              </button>
            </div>

            <!-- Description -->
            <div class="venue-about">
              <h4 class="venue-about-title">ABOUT THIS VENUE</h4>
              <p class="venue-about-text">{{ selectedVenue.description }}</p>
            </div>
          </div>

          <!-- Contenu par défaut -->
          <template v-else>
            <h2 class="explore-title">{{ content.locations_section.title }}</h2>
            <p class="explore-description">{{ content.locations_section.description }}</p>

            <!-- Event Type Filters -->
            <p class="filter-label">{{ content.locations_section.filter_label }}</p>
            <div class="event-filters">
              <button
                v-for="eventType in content.locations_section.event_types"
                :key="eventType.id"
                class="event-filter-btn"
                :class="{ active: activeEventFilter === eventType.id }"
                @click="toggleEventFilter(eventType.id)"
              >
                <div class="event-filter-image" :style="{ backgroundImage: `url('${eventType.image}')` }"></div>
                <span class="event-filter-name">{{ eventType.name }}</span>
              </button>
            </div>

            <!-- Statistics -->
            <div class="explore-stats">
              <div
                v-for="(stat, index) in content.locations_section.stats"
                :key="index"
                class="stat-item"
              >
                <span class="stat-value">{{ stat.value }}</span>
                <span class="stat-label">{{ stat.label }}</span>
              </div>
            </div>
          </template>
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
    <GalleryGrid :images="content.gallery_section.images" />
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
        <TwoColumnText :title="content.beautiful.title" :text="content.beautiful.text" />
        <div class="row mt-4">
          <img :src="content.beautiful.image" alt="Beautiful Moments We’ve Helped Create"
            class="img-fluid mx-auto d-block">
        </div>
        <div id="lasalle" class="row mt-4">
          <div :id="`example-${index}`" v-for="(example, index) in content.examples" :key="index"
            class="col-md-6 example">
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
    <!-- clients -->
    <PartnersSection :title="content.partners_title" :partners="content.partners.map(p => ({ ...p, name: p.alt }))" />
    <!--  ready to make an impact -->
      <section id="ready-to-make-an-impact" class="py-5 bg-white position-relative">
        <div class="container-fluid position-relative d-flex justify-content-center align-items-center">
          <img :src="content.homepageCTA.image" alt=""/>
          <div id="leforme" class="position-absolute d-flex flex-column justify-content-center align-items-center text-center p-4 mx-auto">
            <h2>{{ content.homepageCTA.title }}</h2>
            <p class="preserve-lines" v-html="content.homepageCTA.description"></p>
              <nuxt-link :to="content.homepageCTA.link">
                <nuxt-img :src="content.homepageCTA.button" :alt="content.homepageCTA.title"></nuxt-img>
              </nuxt-link>
          </div>
        </div>
        <CtaBlock
          :text="content.homepageCTA.additionalText"
          :link="content.homepageCTA.link"
          :button-image="content.homepageCTA.button2"
          :button-alt="content.homepageCTA.title"
        />
      </section>
  </div>
</template>

<style scoped lang="scss">
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
#leforme{
  max-width: 1098px;
  max-height: 400px;  
  width:100vw;
  height:100vh;
    border-radius: 20px;
  border: solid 3px #ed2e52;
  background-color: #f83156;
  position: absolute;
  top:0;
  left:0;
  right: 0;
  margin:auto !important;
  bottom:0;
  h2{
      font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 50px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: center;
  color: #fff;
  margin-bottom:20px;

  }
  p{  font-family: FONTSPRINGDEMO-RecoletaAlt;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.56;
  letter-spacing: normal;
  text-align: center;
  color: #fff;


  margin-bottom:20px;
}
}


/* Explore Where We Operate Section */
.explore-section {
  background-color: #f5f5f0;
  padding: 0;
  overflow: hidden;
}

.explore-container {
  display: flex;
  flex-direction: row;
  min-height: 500px;
}

.explore-map {
  flex: 0 0 55%;
  min-height: 500px;
  position: relative;
}

.explore-content {
  flex: 0 0 45%;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  background-color: #f5f5f0;
  overflow-y: auto;
  max-height: 500px;
}

.explore-title {
  font-family: FONTSPRINGDEMO-RecoletaBold, Georgia, serif;
  font-size: 2.5rem;
  font-weight: bold;
  color: #FF4D6D;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.explore-description {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.95rem;
  color: #333;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.filter-label {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.85rem;
  color: #666;
  margin-bottom: 1rem;
  font-weight: 500;
}

.event-filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.event-filter-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  transition: transform 0.2s ease;
}

.event-filter-btn:hover {
  transform: translateY(-4px);
}

.event-filter-btn.active .event-filter-image {
  border-color: #FF4D6D;
  box-shadow: 0 4px 12px rgba(255, 77, 109, 0.3);
}

.event-filter-image {
  width: 100px;
  height: 70px;
  border-radius: 12px;
  background-size: cover;
  background-position: center;
  border: 3px solid transparent;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.event-filter-name {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.85rem;
  color: #333;
  margin-top: 0.5rem;
  font-weight: 500;
}

.explore-stats {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
}

.stat-item {
  display: flex;
  flex-direction: column;
  min-width: 80px;
}

.stat-value {
  font-family: FONTSPRINGDEMO-RecoletaBold, Georgia, serif;
  font-size: 1.5rem;
  font-weight: bold;
  color: #FF4D6D;
  line-height: 1.2;
}

.stat-label {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.7rem;
  color: #666;
  line-height: 1.3;
  max-width: 100px;
}

/* Responsive pour la section Explore */
@media (max-width: 992px) {
  .explore-container {
    flex-direction: column;
  }

  .explore-map {
    flex: none;
    height: 350px;
    min-height: 350px;
  }

  .explore-content {
    flex: none;
    padding: 2rem 1.5rem;
  }

  .explore-title {
    font-size: 2rem;
  }

  .event-filters {
    justify-content: center;
  }

  .explore-stats {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .event-filter-image {
    width: 80px;
    height: 55px;
  }

  .explore-stats {
    gap: 1rem;
  }

  .stat-item {
    min-width: 70px;
  }

  .stat-value {
    font-size: 1.25rem;
  }
}

/* Venue Details Styles */
.venue-details {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 0;
  overflow-y: auto;
}

.venue-images-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.venue-image-wrapper {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
}

.venue-image {
  width: 100%;
  height: 120px;
  background-size: cover;
  background-position: center;
}

.venue-type-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background-color: rgba(255, 255, 255, 0.95);
  color: #333;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.65rem;
  font-weight: 600;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 4px;
  z-index: 2;
}

.venue-type-badge::before {
  content: '🏟️';
  font-size: 0.7rem;
}

.venue-close-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.95);
  border: none;
  font-size: 1.2rem;
  color: #333;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  transition: background-color 0.2s ease;
}

.venue-close-btn:hover {
  background-color: #FF4D6D;
  color: #fff;
}

.venue-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.venue-logo {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid #eee;
  object-fit: contain;
}

.venue-name {
  font-family: FONTSPRINGDEMO-RecoletaBold, Georgia, serif;
  font-size: 1.25rem;
  color: #FF4D6D;
  margin: 0;
  text-transform: uppercase;
}

.venue-info-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem 1rem;
  margin-bottom: 1rem;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.75rem;
}

.venue-info-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.venue-info-icon {
  font-size: 0.8rem;
}

.venue-info-label {
  color: #666;
}

.venue-info-value {
  color: #333;
  font-weight: 500;
}

.venue-event-banner {
  display: flex;
  background-color: #d4e5ff;
  border-radius: 8px;
  padding: 0.75rem;
  margin-bottom: 1rem;
  gap: 1rem;
}

.venue-event-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.venue-event-label {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.6rem;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.venue-event-label::before {
  content: '📅';
  font-size: 0.7rem;
}

.venue-event-item:last-child .venue-event-label::before {
  content: '👥';
}

.venue-event-value {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.8rem;
  font-weight: 500;
  color: #333;
}

.venue-tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.venue-tab {
  background: none;
  border: none;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  color: #999;
  cursor: pointer;
  padding: 0.25rem 0;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  transition: color 0.2s ease;
}

.venue-tab.active {
  color: #FF4D6D;
}

.venue-tab:hover {
  color: #FF4D6D;
}

.tab-icon {
  font-size: 0.8rem;
}

.venue-about {
  flex: 1;
}

.venue-about-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  color: #333;
  margin: 0 0 0.5rem 0;
  text-transform: uppercase;
}

.venue-about-text {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.8rem;
  color: #666;
  line-height: 1.5;
  margin: 0;
}
</style>
