<script setup lang="ts">
import type { LocationsData, Venue } from '~/composables/useLocations'

const { getLocations } = useLocations()
const locationsData = ref<LocationsData | null>(null)
const { settings } = useGlobalSettings()

// Dynamic icon URLs with fallbacks
const iconMap = computed(() => settings.value?.icons?.icon_map || '/images/mapIcon.svg')
const iconCalendar = computed(() => settings.value?.icons?.icon_calendar || '/images/iconCal.svg')
const iconInfo = computed(() => settings.value?.icons?.icon_info || '/images/iconInfo.svg')
const iconShop = computed(() => settings.value?.icons?.icon_shop || '/images/iconShop.svg')
const iconFood = computed(() => settings.value?.icons?.icon_food || '/images/iconFood.svg')
const btnJoinNow = computed(() => settings.value?.icons?.btn_join_now || '/images/joinNowBtn.svg')

const activeVenueFilter = ref<string | undefined>(undefined)
const selectedVenue = ref<Venue | null>(null)
const activeTab = ref<'overview' | 'shops' | 'menus'>('overview')

// Load locations data
onMounted(async () => {
  locationsData.value = await getLocations()
})

// Shops carousel state
const shopsPage = ref(0)
const SHOPS_PER_PAGE = 3

// Menu pagination state
const menuPage = ref(0)
const MENU_ITEMS_PER_PAGE = 4

const toggleVenueFilter = (filterId: string) => {
  selectedVenue.value = null
  if (activeVenueFilter.value === filterId) {
    activeVenueFilter.value = undefined
  } else {
    activeVenueFilter.value = filterId
  }
}

const handleVenueClick = (venue: Venue) => {
  selectedVenue.value = venue
  activeTab.value = 'overview'
  shopsPage.value = 0
  menuPage.value = 0
}

const closeVenueDetails = () => {
  selectedVenue.value = null
}

const setActiveTab = (tab: 'overview' | 'shops' | 'menus') => {
  activeTab.value = tab
}

// Shops carousel computed
const totalShopsPages = computed(() => {
  if (!selectedVenue.value?.shops) return 0
  return Math.ceil(selectedVenue.value.shops.length / SHOPS_PER_PAGE)
})

const currentShops = computed(() => {
  if (!selectedVenue.value?.shops) return []
  const start = shopsPage.value * SHOPS_PER_PAGE
  return selectedVenue.value.shops.slice(start, start + SHOPS_PER_PAGE)
})

const nextShopsPage = () => {
  if (shopsPage.value < totalShopsPages.value - 1) {
    shopsPage.value++
  }
}

const prevShopsPage = () => {
  if (shopsPage.value > 0) {
    shopsPage.value--
  }
}

// Menu pagination computed
const totalMenuPages = computed(() => {
  if (!selectedVenue.value?.menu_items) return 0
  return Math.ceil(selectedVenue.value.menu_items.length / MENU_ITEMS_PER_PAGE)
})

const currentMenuItems = computed(() => {
  if (!selectedVenue.value?.menu_items) return []
  const start = menuPage.value * MENU_ITEMS_PER_PAGE
  return selectedVenue.value.menu_items.slice(start, start + MENU_ITEMS_PER_PAGE)
})

const nextMenuPage = () => {
  if (menuPage.value < totalMenuPages.value - 1) {
    menuPage.value++
  }
}

const prevMenuPage = () => {
  if (menuPage.value > 0) {
    menuPage.value--
  }
}

const goToMenuPage = (page: number) => {
  menuPage.value = page
}
</script>

<template>
  <section v-if="locationsData" id="mapPreview" class="explore-section">
    <div class="explore-container">
      <!-- Map on the left -->
      <div class="explore-map">
        <ClientOnly>
          <VenueMap
            :venues="locationsData.map_venues"
            :venue-types="locationsData.venue_types"
            :active-filter="activeVenueFilter"
            @venue-clicked="handleVenueClick"
          />
        </ClientOnly>
      </div>

      <!-- Content on the right -->
      <div class="explore-content">
        <!-- Venue details when selected -->
        <div v-if="selectedVenue" class="venue-details">
          <!-- Images grid -->
          <div class="venue-images-grid">
            <div class="venue-image-wrapper">
              <span class="venue-type-badge">{{ selectedVenue.type }}</span>
              <div class="venue-image" :style="{ backgroundImage: `url('${selectedVenue.image}')` }"></div>
            </div>
            <div class="venue-image-wrapper">
              <button class="venue-close-btn" @click="closeVenueDetails">&times;</button>
              <div class="venue-image" :style="{ backgroundImage: `url('${selectedVenue.image2 || selectedVenue.image}')` }"></div>
            </div>
          </div>

          <!-- Name and logo -->
          <div class="venue-header">
            <img v-if="selectedVenue.logo" :src="selectedVenue.logo" :alt="selectedVenue.name" class="venue-logo" />
            <h3 class="venue-name">{{ selectedVenue.name }}</h3>
          </div>

          <!-- Info: Location, Capacity, Staff -->
          <div class="venue-info-row">
            <div class="venue-info-item">
              <span class="venue-info-icon"><img :src="iconMap" alt=""></span>
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

          <!-- Recent event banner -->
          <div class="venue-event-banner">
            <div class="venue-event-item">
              <span class="venue-event-label" style="text-transform: uppercase;">Evenememnts récents</span>
              <span class="venue-event-value">{{ selectedVenue.recent_event }}</span>
            </div>
            <div class="venue-event-item">
              <span class="venue-event-label">INVITÉS SERVIS</span>
              <span class="venue-event-value">{{ selectedVenue.guests_served }} Guest Served</span>
            </div>
          </div>

          <!-- Tabs -->
          <div class="venue-tabs">
            <button
            style="text-transform: uppercase;"
              class="venue-tab "
              :class="{ active: activeTab === 'overview' }"
              @click="setActiveTab('overview')"
            >
              <span class="tab-icon"><nuxt-img :src="iconInfo" alt="Overview Icon" /></span> Présentation
            </button>
            <button
              class="venue-tab"
              :class="{ active: activeTab === 'shops' }"
              @click="setActiveTab('shops')"
            >
              <span class="tab-icon"><nuxt-img :src="iconShop" alt="Shops Icon" /></span> BOUTIQUES ({{ selectedVenue.shops?.length || selectedVenue.shops_count }})
            </button>
            <button
              class="venue-tab"
              :class="{ active: activeTab === 'menus' }"
              @click="setActiveTab('menus')"
            >
              <span class="tab-icon"><nuxt-img :src="iconFood" alt="Menus Icon" /></span> MENU ({{ selectedVenue.menu_items?.length || selectedVenue.menus_count }})
            </button>
          </div>

          <!-- Tab Content: Overview -->
          <div v-if="activeTab === 'overview'" class="tab-content">
            <div class="venue-about">
              <div class="mt-0 venue-about-text" v-html="selectedVenue.description"></div>
            </div>
            <!-- Services List -->
            <div v-if="selectedVenue.services && selectedVenue.services.length > 0" class="venue-services">
              <h4 class="venue-services-title">SERVICES : </h4>
              <ul class="venue-services-list">
                <li v-for="service in selectedVenue.services" :key="service" class="venue-service-item">
                  {{ service }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Tab Content: Shops Carousel -->
          <div v-if="activeTab === 'shops'" class="tab-content">
            <div class="shops-carousel">
              <div class="shops-grid">
                <div v-for="shop in currentShops" :key="shop.id" class="shop-card">
                  <div class="shop-image-wrapper">
                    <img v-if="shop.image" :src="shop.image" :alt="shop.name" class="shop-image" />
                  </div>
                  <p class="shop-name">{{ shop.name }}</p>
                </div>
              </div>

              <!-- Carousel Navigation -->
              <div v-if="totalShopsPages > 1" class="carousel-nav">
                <button
                  class="carousel-btn prev"
                  :disabled="shopsPage === 0"
                  @click="prevShopsPage"
                >
                  ‹
                </button>
                <div class="carousel-dots">
                  <span
                    v-for="page in totalShopsPages"
                    :key="page"
                    class="carousel-dot"
                    :class="{ active: shopsPage === page - 1 }"
                    @click="shopsPage = page - 1"
                  ></span>
                </div>
                <button
                  class="carousel-btn next"
                  :disabled="shopsPage === totalShopsPages - 1"
                  @click="nextShopsPage"
                >
                  ›
                </button>
              </div>
            </div>
          </div>

          <!-- Tab Content: Menu Carousel (2 colonnes x 2 lignes) -->
          <div v-if="activeTab === 'menus'" class="tab-content">
            <div class="menus-carousel">
              <div class="menus-grid">
                <div v-for="item in currentMenuItems" :key="item.id" class="menu-card">
                  <div class="menu-image-wrapper">
                    <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.name" class="menu-image" />
                  </div>
                  <div class="menu-card-info">
                    <p class="menu-card-name">{{ item.name }}</p>
                    <p class="menu-card-description">{{ item.description }}</p>
                  </div>
                  <span class="menu-card-price">€{{ item.price }}</span>
                </div>
              </div>

              <!-- Carousel Navigation -->
              <div v-if="totalMenuPages > 1" class="carousel-nav">
                <button
                  class="carousel-btn prev"
                  :disabled="menuPage === 0"
                  @click="prevMenuPage"
                >
                  ‹
                </button>
                <div class="carousel-dots">
                  <span
                    v-for="page in totalMenuPages"
                    :key="page"
                    class="carousel-dot"
                    :class="{ active: menuPage === page - 1 }"
                    @click="goToMenuPage(page - 1)"
                  ></span>
                </div>
                <button
                  class="carousel-btn next"
                  :disabled="menuPage === totalMenuPages - 1"
                  @click="nextMenuPage"
                >
                  ›
                </button>
              </div>
            </div>
          </div>

          <!-- Join Now Button -->
          <NuxtLink
            :to="`/careers?venue=${encodeURIComponent(selectedVenue.location)}`"
            class="d-flex align-items-start justify-content-start venue-join-btn"
          >
          <nuxt-img :src="btnJoinNow" alt="Join Now Button" class="img-fluid"></nuxt-img>
          </NuxtLink>
        </div>

        <!-- Default content -->
        <template v-else>
          <h2 class="explore-title">{{ locationsData.title }}</h2>
          <p class="explore-description" v-html="locationsData.description"></p>

          <!-- Venue Type Filters -->
          <p class="filter-label">{{ locationsData.filter_label }}</p>
          <div class="venue-filters">
            <button
              v-for="venueType in locationsData.venue_types"
              :key="venueType.id"
              class="venue-filter-btn"
              :class="{ active: activeVenueFilter === venueType.id }"
              @click="toggleVenueFilter(venueType.id)"
            >
              <div class="venue-filter-image" :style="{ backgroundImage: `url('${venueType.image}')` }"></div>
              <span class="guignol">{{ venueType.name }}</span>
            </button>
          </div>

          <!-- Statistics -->
          <div class="explore-stats row gap-0">
            <div
              v-for="(stat, index) in locationsData.stats"
              :key="index"
              class="stat-item col-3 d-flex flex-column align-items-center justify-content-start"
            >
              <span class="stat-value">{{ stat.value }}</span>
              <span class="stat-label">{{ stat.label }}</span>
            </div>
          </div>
        </template>
      </div>
    </div>
  </section>
</template>

<style scoped lang="scss">
/* Explore Where We Operate Section */
.explore-section {
  background-color: #f5f5f0;
  padding: 0;
  overflow: hidden;
}

.explore-container {
  display: flex;
  flex-direction: row;
  min-height: 80vh;
}

.explore-map {
  flex: 0 0 55%;
  min-height: 80vh;
  position: relative;
}

.explore-content {
  flex: 0 0 45%;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.explore-title {
   font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 50px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #fe002f;
  margin-top: 1em;

}

.explore-description {
  margin:1em 0;
  font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.56;
  letter-spacing: normal;
  text-align: left;
  color: #000;

}

.filter-label {  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 20px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.4;
  letter-spacing: normal;
  text-align: left;
  color: #000;  margin:1em 0;

}

.venue-filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.venue-filter-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  transition: transform 0.2s ease;
}

.venue-filter-btn:hover {
  transform: translateY(-4px);
}

.venue-filter-btn.active .venue-filter-image {
  border-color: #FF4D6D;
  box-shadow: 0 4px 12px rgba(255, 77, 109, 0.3);
}

.venue-filter-image {
  width: 180px;
  height: 185px;
  border-radius: 12px;
  background-size: cover;
  background-position: center;
  background-repeat:no-repeat;
  transition: all 0.2s ease;
}



.explore-stats {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  justify-content: center;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
}

.stat-item {
  display: flex;
  flex-direction: column;
  min-width: 80px;
}

.stat-value { font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 30px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #ff004e;
  display: flex;
  align-items:center;
  justify-content:center;
}

.stat-label {
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 14px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: center;
  color: #000;
  display: flex;
  align-items:center;
  justify-content:center;
}

/* Responsive */
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

  .venue-filters {
    justify-content: center;
  }

  .explore-stats {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .venue-filter-image {
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
  height: 300px;
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
  font-family: FONTSPRINGDEMO-RecoletaSemiBold;
  font-size: 24px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 2.2;
  letter-spacing: normal;
  text-align: left;
  color: #f9375b;
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
 font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.42;
  letter-spacing: normal;
  text-align: left;
  color: #000;}

.venue-info-value{
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.42;
  letter-spacing: normal;
  text-align: left;
  color: #000;
}
.venue-info-text{
  font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.42;
  letter-spacing: normal;
  text-align: left;
  color: #000;
}

.venue-event-banner {
  display: flex;
  background:url(/images/infoContainer.svg);background-repeat:no-repeat;
  background-size:contain;
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
  position: relative;
  &::nth-of-type(2){
  &::before {
    margin-right: 6px;
    content:"";
    height:100%;
    width:1px;
    background:url(/images/divider.svg) no-repeat;
    position:absolute;
    left:0;
  }}
}

.venue-event-label {
 display: flex;
 align-items: center;
   font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 11px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.5;
  letter-spacing: 0.26px;
  text-align: left;
  color: rgba(26, 26, 26, 0.8);
}

.venue-event-label::before {
  margin-right: 6px;
  content: "";
  display:block;
    background: url(/images/iconCal.svg) no-repeat;
  width: 16px;height:16px;
}

.venue-event-item:last-child .venue-event-label::before {
  content: "";
  display:block;
    background: url(/images/iconPeople.svg) no-repeat;
  width: 16px;height:16px;  }

.venue-event-value {
   font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 13px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.25;
  letter-spacing: normal;
  text-align: left;
  color: #000;
}

.venue-tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  border-bottom: solid 1px rgba(0, 0, 0, 0.6);
  padding-bottom: 0.5rem;
}

.venue-tab {
  background: none;
  border: none;
   font-family: FONTSPRINGDEMO-RecoletaSemiBold;
  font-size: 15px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.36;
  letter-spacing: 0.34px;
  text-align: left;
  color: rgba(0, 0, 0, 0.6);
  cursor: pointer;
  padding: 0.5rem 1rem;
  transition: all 0.2s ease;

filter: alpha(opacity=50); /* internet explorer */
  -khtml-opacity: 0.5;      /* khtml, old safari */
  -moz-opacity: 0.5;       /* mozilla, netscape */
  opacity: 0.5;           /* fx, safari, opera */

}

.venue-tab.active {
  color: #FF4D6D;


       filter: alpha(opacity=100); /* internet explorer */
        -khtml-opacity: 1;      /* khtml, old safari */
        -moz-opacity: 1;       /* mozilla, netscape */
        opacity: 1;           /* fx, safari, opera */

}

.venue-tab:hover {
  color: #FF4D6D;


         filter: alpha(opacity=100); /* internet explorer */
          -khtml-opacity: 1;      /* khtml, old safari */
          -moz-opacity: 1;       /* mozilla, netscape */
          opacity: 1;           /* fx, safari, opera */

}

.tab-icon {
  font-size: 0.8rem;
}

/* Tab Content */
.tab-content {
  flex: 1;
  overflow-y: auto;
  position:relative;
}

.venue-about {
  //margin-bottom: 1.5rem;
}

.venue-about-title {
   font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: 0.26px;
  text-align: left;
  color: rgba(26, 26, 26, 0.8);
  margin-bottom: 0.5rem;
}

.venue-about-text {
   font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.8;
  letter-spacing: normal;
  text-align: left;
  color: #000;
}

/* Services List */
.venue-services {
  //margin-top: 1.5rem;
}

.venue-services-title {
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  letter-spacing: 0.26px;
  color: rgba(26, 26, 26, 0.8);
  margin-bottom: 0.75rem;
}

.venue-services-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.tab-icon{
  flex-wrap:wrap;
  display:flex;
  justify-content:center;
  img, svg, nuxt-image {
    margin-right: 16px;
    display: block;
    width: 16px;
    height: 16px;
    transition: all 0.2s ease;
  }
}

/* Changer la couleur de l'icône quand l'onglet est actif */
.venue-tab.active .tab-icon img,
.venue-tab.active .tab-icon svg,
.venue-tab.active .tab-icon nuxt-image {
  filter: brightness(0) saturate(100%) invert(62%) sepia(75%) saturate(486%) hue-rotate(329deg) brightness(104%) contrast(100%);
}

/* Changer la couleur de l'icône au survol */
.venue-tab:hover .tab-icon img,
.venue-tab:hover .tab-icon svg,
.venue-tab:hover .tab-icon nuxt-image {
  filter: brightness(0) saturate(100%) invert(62%) sepia(75%) saturate(486%) hue-rotate(329deg) brightness(104%) contrast(100%);
}
.venue-tab{

  flex-wrap:wrap;
  display:flex;
  justify-content:center;

}
.venue-service-item {
 font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 16px;
  line-height: 1.8;
  color: #000;
  padding-left: 1.5rem;
  position: relative;
  margin-bottom: 0.25rem;

  &::before {
    content: "";
    background:url(/images/listeAPuces.svg) no-repeat;
    position: absolute;
    left: 0;
    top: 0;
    bottom:0;
    width:8.49px;
    height:8.49px;
    margin:auto;
  }
}

/* Shops Carousel */
.shops-carousel {
  padding: 1rem 0;
}

.shops-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1rem;
}

.shop-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position:relative;
}

.shop-image-wrapper {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
  position:relative;
  z-index:0;
}
.shop-image-wrapper{
  position:relative;
  border-radius:10px;
}
.shop-image {
  z-index:0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  position:relative;
}

.shop-name {
 font-family: FONTSPRINGDEMO-RecoletaSemiBold;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.56;
  letter-spacing: normal;
  text-align: center;
  color: #fff;
    -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  background-color: rgba(64, 64, 64, 0.3);
  position:absolute;
  left:0;
  right:0;
  bottom:0;
  z-index:1000;
}

.carousel-nav {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.carousel-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #FF4D6D;
  background: white;
  color: #FF4D6D;
  font-size: 1.25rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;

  &:hover:not(:disabled) {
    background: #FF4D6D;
    color: white;
  }

  &:disabled {
    opacity: 0.3;
    cursor: not-allowed;
  }
}

.carousel-dots {
  display: flex;
  gap: 0.5rem;
}

.carousel-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #ddd;
  cursor: pointer;
  transition: background-color 0.2s ease;

  &.active {
    background-color: #FF4D6D;
  }

  &:hover {
    background-color: #FF4D6D;
  }
}

/* Menus Carousel (style similaire à Shops) */
.menus-carousel {
  padding: 1rem 0;
}

.menus-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1rem;
}

.menu-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
}

.menu-image-wrapper {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 12px;
  overflow: hidden;
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
  position: relative;
  z-index: 0;
}

.menu-image {
  z-index: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: relative;
}

.menu-card-info {
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  background-color: rgba(64, 64, 64, 0.3);
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  padding: 0.5rem;
  border-radius: 0 0 12px 12px;
}

.menu-card-name {
  font-family: FONTSPRINGDEMO-RecoletaSemiBold;
  font-size: 16px;
  font-weight: normal;
  line-height: 1.3;
  text-align: left;
  color: #fff;
  margin: 1rem 0 1rem 0;
}

.menu-card-description {
    font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 14px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #fff;
}

.menu-card-price {
  font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 14px;
  color: #FF4D6D;
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(255, 255, 255, 0.95);
  padding: 4px 8px;
  border-radius: 8px;
  z-index: 1001;
}

/* Join Now Button */
.venue-join-btn {
  border-top:1px solid #000;
  padding:1rem 0;   
    width: 100%;
    height: 104px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 20px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.4;
    letter-spacing: normal;
    text-align: center;
    color: #fff;
    text-decoration: none;
    margin: 2rem auto 1rem auto;
    transition: all 0.2s ease;
    img{
      width:287px;
      height:60px;
    }
}

/* Mobile responsiveness for tabs */
@media (max-width: 768px) {
  .shops-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .menus-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }

  .venue-tabs {
    flex-wrap: wrap;
  }

  .venue-tab {
    font-size: 13px;
    padding: 0.4rem 0.75rem;
  }

  .menu-card-name {
    font-size: 14px;
  }

  .menu-card-description {
    font-size: 10px;
    -webkit-line-clamp: 2;
  }

  .menu-card-price {
    font-size: 12px;
    padding: 3px 6px;
  }
}

@media (max-width: 480px) {
  .shops-grid {
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
  }

  .menus-grid {
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
  }

  .menu-card-info {
    padding: 0.35rem;
  }

  .menu-card-name {
    font-size: 12px;
  }

  .menu-card-description {
    font-size: 9px;
    -webkit-line-clamp: 1;
  }

  .menu-card-price {
    font-size: 11px;
    padding: 2px 5px;
  }
}
.guignol{
  display:flex;
  align-items:center;
  justify-content:center;
  width: 100%;
  height: 47px;
  padding: 8px 54px 11px 55px;
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  background-color: rgba(45, 45, 45, 0.2);
    font-family: FONTSPRINGDEMO-RecoletaSemiBold;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.56;
  letter-spacing: normal;
  text-align: left;
  color: #fff6f0;
  position:absolute;
  bottom:0;
  left:0;
  right:0;
  margin:auto;
  z-index:1;  

}
.venue-filter-btn{
  position:relative;
  overflow:hidden;
  border-radius:10px;
}
</style>
