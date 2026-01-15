<script setup lang="ts">
export interface Venue {
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
}

export interface EventType {
  id: string
  name: string
  image: string
}

export interface Stat {
  value: string
  label: string
}

export interface LocationsData {
  title: string
  description: string
  filter_label: string
  event_types: EventType[]
  stats: Stat[]
  map_venues: Venue[]
}

const { data: locationsData } = await useFetch<LocationsData>('/api/locations.json')

const activeEventFilter = ref<string | undefined>(undefined)
const selectedVenue = ref<Venue | null>(null)

const toggleEventFilter = (filterId: string) => {
  selectedVenue.value = null
  if (activeEventFilter.value === filterId) {
    activeEventFilter.value = undefined
  } else {
    activeEventFilter.value = filterId
  }
}

const handleVenueClick = (venue: Venue) => {
  selectedVenue.value = venue
}

const closeVenueDetails = () => {
  selectedVenue.value = null
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
            :active-filter="activeEventFilter"
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
            <img :src="selectedVenue.logo" :alt="selectedVenue.name" class="venue-logo" />
            <h3 class="venue-name">{{ selectedVenue.name }}</h3>
          </div>

          <!-- Info: Location, Capacity, Staff -->
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

          <!-- Recent event banner -->
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

          <!-- Tabs -->
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

        <!-- Default content -->
        <template v-else>
          <h2 class="explore-title">{{ locationsData.title }}</h2>
          <p class="explore-description">{{ locationsData.description }}</p>

          <!-- Event Type Filters -->
          <p class="filter-label">{{ locationsData.filter_label }}</p>
          <div class="event-filters">
            <button
              v-for="eventType in locationsData.event_types"
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
              v-for="(stat, index) in locationsData.stats"
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
