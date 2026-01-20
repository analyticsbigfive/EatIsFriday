<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { LucideSearch, LucideMapPin, LucideChevronDown, LucideChevronLeft, LucideChevronRight } from 'lucide-vue-next'
import type { CareersContent } from '~/composables/usePageContent'
import type { Job } from '~/composables/useJobs'
import type { LocationsData, Venue } from '~/composables/useLocations'

const route = useRoute()
const { getCareersContent } = usePageContent()
const { getJobs } = useJobs()
const { getLocations } = useLocations()

const content = ref<CareersContent | null>(null)
const allJobs = ref<Job[]>([])
const locationsData = ref<LocationsData | null>(null)

const searchQuery = ref('')
const selectedJobType = ref('')
const selectedVenue = ref('')
const showJobTypeDropdown = ref(false)
const showVenueDropdown = ref(false)

// Pagination
const currentPage = ref(1)
const itemsPerPage = 6

// Find the venue matching the selected location
const activeVenue = computed(() => {
  if (!selectedVenue.value || !locationsData.value?.map_venues) return null
  return locationsData.value.map_venues.find(venue =>
    venue.location === selectedVenue.value ||
    venue.name.toLowerCase().includes(selectedVenue.value.toLowerCase()) ||
    selectedVenue.value.toLowerCase().includes(venue.name.toLowerCase())
  ) || null
})

onMounted(async () => {
  content.value = await getCareersContent()
  locationsData.value = await getLocations()
  const fetchedJobs = await getJobs()
  if (fetchedJobs) {
    allJobs.value = fetchedJobs
  }
  if (content.value) {
    selectedJobType.value = content.value.search_section.job_types[0] || ''
  }

  // Apply URL query parameters
  if (route.query.venue) {
    selectedVenue.value = route.query.venue as string
  }
  if (route.query.search) {
    searchQuery.value = route.query.search as string
  }
  if (route.query.type) {
    selectedJobType.value = route.query.type as string
  }
})

// Extraire toutes les venues uniques des jobs
const venueOptions = computed(() => {
  const locations = new Set<string>()
  allJobs.value.forEach(job => {
    if (job.location) {
      locations.add(job.location)
    }
  })
  return ['All Sites', ...Array.from(locations)]
})

// Helper pour obtenir le titre du job
const getJobTitle = (job: Job) => {
  return typeof job.title === 'string' ? job.title : job.title?.rendered || ''
}

// Helper pour obtenir l'extrait du job
const getJobExcerpt = (job: Job) => {
  return typeof job.excerpt === 'string' ? job.excerpt : job.excerpt?.rendered || ''
}

// Helper pour formater la date relative
const getPostedTime = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now.getTime() - date.getTime()
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  
  if (diffHours < 1) return 'Just now'
  if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`
  if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

const filteredJobs = computed(() => {
  if (!content.value) return []
  return allJobs.value.filter(job => {
    const title = getJobTitle(job)
    const excerpt = getJobExcerpt(job)

    const matchesSearch = !searchQuery.value ||
                          title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          excerpt.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          job.location.toLowerCase().includes(searchQuery.value.toLowerCase())

    // Normaliser les types de job pour la comparaison
    const normalizedJobType = job.job_type.toLowerCase().replace('-', ' ')
    const normalizedSelectedType = selectedJobType.value.toLowerCase().replace('-', ' ')
    
    const matchesType = selectedJobType.value === content.value!.search_section.job_types[0] ||
                        normalizedJobType.includes(normalizedSelectedType)

    // Filtre par venue/location depuis le dropdown
    const matchesVenueFilter = selectedVenue.value === '' ||
                               selectedVenue.value === 'All Sites' ||
                               job.location.toLowerCase().includes(selectedVenue.value.toLowerCase())

    return matchesSearch && matchesType && matchesVenueFilter
  })
})

// Pagination computed
const totalPages = computed(() => Math.ceil(filteredJobs.value.length / itemsPerPage))

const paginatedJobs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredJobs.value.slice(start, end)
})

// Reset to page 1 when filters change
watch([searchQuery, selectedJobType, selectedVenue], () => {
  currentPage.value = 1
})

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}
</script>

<template>
  <div class="careers-page">
    <div v-if="content" class="min-vh-100 bg-brand-gray">
    
    <!-- ========================================== -->
    <!-- Hero Section WITHOUT venue (Default View) -->
    <!-- ========================================== -->
    <section v-if="!activeVenue" class="careers-hero-default">
      <div class="careers-hero-container">
        <!-- Left: Image -->
        <div class="careers-hero-image">
          <img src="/images/enteteSansJob.jpg" alt="Join our team" />
        </div>
        <!-- Right: Content -->
        <div class="careers-hero-content">
          <div class="careers-hero-inner">
            <h1 class="careers-hero-title">
              Join France's<br/>
              leading catering<br/>
              company
            </h1>
            <p class="careers-hero-subtitle">
              Discover exciting opportunities in hospitality & culinary excellence
            </p>
            <div class="careers-hero-stats">
              <div class="stat-box">
                <span class="stat-number">{{ allJobs.length }}</span>
                <span class="stat-label">Open Positions</span>
              </div>
              <div class="stat-box">
                <span class="stat-number">{{ venueOptions.length - 1 }}</span>
                <span class="stat-label">Locations</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- Hero Section WITH venue (When job/venue selected) -->
    <!-- ========================================== -->
    <section v-else class="hero-section has-venue">
      <!-- Background with venue image -->
      <div
        class="hero-background"
        :style="{ backgroundImage: `url('${activeVenue.image}')` }"
      >
        <div class="hero-overlay"></div>
      </div>

      <!-- Hero Content -->
      <div class="container hero-content">
        <span class="hero-tag">Now Hiring</span>
        <h1 class="hero-title">
          Join Our Team At {{ activeVenue.name }}
        </h1>
        <p class="hero-subtitle">
          <LucideMapPin class="subtitle-icon" /> {{ activeVenue.location }}
          <span class="subtitle-divider">•</span>
          {{ filteredJobs.length }} Open Position{{ filteredJobs.length !== 1 ? 's' : '' }}
        </p>
      </div>
    </section>

    <!-- Search & Filter Bar -->
    <section class="container search-bar-section position-relative">
      <div class="bg-brand-dark border-organic p-3 d-flex flex-column flex-md-row gap-3 shadow-organic">
        <!-- Search Input -->
        <div class="flex-grow-1 position-relative">
          <div class="d-flex align-items-center gap-3 px-3">
            <LucideSearch class="text-white opacity-75" style="width: 1.25rem; height: 1.25rem;" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search Job title and category here"
              class="bg-transparent text-white border-0 flex-grow-1 py-2 font-body search-input"
            />
          </div>
        </div>

        <!-- Venue Dropdown -->
        <div class="position-relative">
          <button
            @click="showVenueDropdown = !showVenueDropdown; showJobTypeDropdown = false"
            class="bg-brand-dark border-start border-white border-opacity-25 px-4 py-2 d-flex align-items-center gap-3 text-white w-100 w-md-auto justify-content-between dropdown-btn"
          >
            <LucideMapPin style="width: 1rem; height: 1rem;" class="opacity-75" />
            <span>{{ selectedVenue || 'All Sites' }}</span>
            <LucideChevronDown style="width: 1rem; height: 1rem;" :class="{ 'rotate-180': showVenueDropdown }" />
          </button>

          <!-- Venue Dropdown Menu -->
          <Transition
            enter-active-class="transition-fade-in"
            leave-active-class="transition-fade-out"
          >
            <div v-if="showVenueDropdown" class="position-absolute top-100 end-0 mt-2 bg-white border-organic shadow-organic-lg dropdown-menu-custom dropdown-menu-venue">
              <button
                v-for="venue in venueOptions"
                :key="venue"
                @click="selectedVenue = venue === 'All Sites' ? '' : venue; showVenueDropdown = false"
                :class="[
                  'w-100 text-start px-3 py-2 border-0 fw-medium dropdown-item-custom',
                  (selectedVenue === venue || (venue === 'All Sites' && !selectedVenue)) ? 'active' : ''
                ]"
              >
                {{ venue }}
              </button>
            </div>
          </Transition>
        </div>

        <!-- Job Type Dropdown -->
        <div class="position-relative">
          <button
            @click="showJobTypeDropdown = !showJobTypeDropdown; showVenueDropdown = false"
            class="bg-brand-dark border-start border-white border-opacity-25 px-4 py-2 d-flex align-items-center gap-3 text-white w-100 w-md-auto justify-content-between dropdown-btn"
          >
            <span>{{ selectedJobType || 'All Job Types' }}</span>
            <LucideChevronDown style="width: 1rem; height: 1rem;" :class="{ 'rotate-180': showJobTypeDropdown }" />
          </button>

          <!-- Dropdown Menu -->
          <Transition
            enter-active-class="transition-fade-in"
            leave-active-class="transition-fade-out"
          >
            <div v-if="showJobTypeDropdown" class="position-absolute top-100 end-0 mt-2 bg-white border-organic shadow-organic-lg dropdown-menu-custom">
              <button
                v-for="type in content.search_section.job_types"
                :key="type"
                @click="selectedJobType = type; showJobTypeDropdown = false"
                :class="[
                  'w-100 text-start px-3 py-2 border-0 fw-medium dropdown-item-custom',
                  selectedJobType === type ? 'active' : ''
                ]"
              >
                {{ type }}
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </section>

    <!-- Job Grid -->
    <section class="container py-5">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="font-heading fs-4 fw-bold">
          {{ filteredJobs.length }} {{ filteredJobs.length === 1 ? content.job_listing.positions_available_singular : content.job_listing.positions_available_plural }} {{ content.job_listing.positions_available_suffix }}
        </h2>
        <p v-if="totalPages > 1" class="text-muted small mb-0">
          Page {{ currentPage }} of {{ totalPages }}
        </p>
      </div>

      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <div
          v-for="job in paginatedJobs"
          :key="job.id"
          class="col"
        >
          <div class="bg-white border-organic p-4 h-100 job-card">
            <div class="d-flex flex-column h-100 justify-content-between">
              <!-- Header -->
              <div>
                <h3 class="font-heading fw-bold fs-5 lh-sm mb-1">{{ getJobTitle(job) }}</h3>
                <p class="small text-muted fw-medium mb-3">{{ content.job_listing.posted_prefix }} {{ getPostedTime(job.date) }}</p>

                <!-- Tags Row -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                  <span class="tag-blue">🍳 {{ job.department }}</span>
                  <span class="tag-outline d-flex align-items-center gap-1">
                    <LucideMapPin style="width: 0.75rem; height: 0.75rem;" /> {{ job.location }}
                  </span>
                </div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                  <span class="tag-lime d-flex align-items-center gap-1">
                    {{ job.job_type }}
                  </span>
                  <span class="tag-yellow d-flex align-items-center gap-1">
                    <span class="small">💰</span> {{ job.salary }}
                  </span>
                </div>

                <!-- Description -->
                <p class="small text-muted mb-4 line-clamp-3 font-body lh-lg">
                  {{ getJobExcerpt(job) }}
                </p>
              </div>

              <!-- Buttons -->
              <div class="d-flex gap-3 mt-auto">
                <NuxtLink :to="`/jobs/${job.slug}`" class="btn-primary flex-grow-1 text-center small">
                  {{ content.job_listing.apply_button }}
                </NuxtLink>
                <NuxtLink :to="`/jobs/${job.slug}`" class="btn-secondary flex-grow-1 text-center small">
                  {{ content.job_listing.view_details_button }}
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-if="filteredJobs.length === 0" class="text-center py-5 bg-white border-organic">
        <p class="fs-5 text-muted mb-2">{{ content.no_results.title }}</p>
        <p class="text-secondary mb-3">{{ content.no_results.description }}</p>
        <button @click="searchQuery = ''; selectedJobType = content.search_section.job_types[0] || ''; selectedVenue = ''" class="text-brand-pink fw-bold btn btn-link text-decoration-none">
          {{ content.no_results.clear_filters_button }}
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="d-flex justify-content-center align-items-center gap-2 mt-5">
        <button 
          @click="goToPage(currentPage - 1)" 
          :disabled="currentPage === 1"
          class="pagination-btn"
          :class="{ 'disabled': currentPage === 1 }"
        >
          <LucideChevronLeft style="width: 1.25rem; height: 1.25rem;" />
        </button>
        
        <template v-for="page in totalPages" :key="page">
          <button 
            v-if="page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)"
            @click="goToPage(page)"
            class="pagination-btn"
            :class="{ 'active': currentPage === page }"
          >
            {{ page }}
          </button>
          <span v-else-if="page === currentPage - 2 || page === currentPage + 2" class="pagination-dots">...</span>
        </template>
        
        <button 
          @click="goToPage(currentPage + 1)" 
          :disabled="currentPage === totalPages"
          class="pagination-btn"
          :class="{ 'disabled': currentPage === totalPages }"
        >
          <LucideChevronRight style="width: 1.25rem; height: 1.25rem;" />
        </button>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-brand-dark py-5 text-center">
      <div class="container">
        <h2 class="font-heading display-5 fw-bold text-white mb-4">
          {{ content.cta_section.title }}
        </h2>
        <p class="text-secondary mx-auto mb-4 font-body" style="max-width: 42rem;">
          {{ content.cta_section.description }}
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
          <button class="btn-lime fs-5 px-5 py-3">
            {{ content.cta_section.explore_venues_button }}
          </button>
          <button class="btn-secondary fs-5 px-5 py-3">
            {{ content.cta_section.general_application_button }}
          </button>
        </div>
      </div>
    </section>
  </div>

  <!-- Loading state -->
  <div v-else class="min-vh-100 bg-brand-gray d-flex align-items-center justify-content-center">
    <div class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
</div>
</template>

<style scoped>
/* ============================================
   CAREERS HERO DEFAULT (No venue selected)
   ============================================ */
.careers-hero-default {
  background-color: #F5F5F0;
  padding-top: 6rem;
}

.careers-hero-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 70vh;
  max-width: 1400px;
  margin: 0 auto;
}

.careers-hero-image {
  position: relative;
  overflow: hidden;
}

.careers-hero-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.careers-hero-content {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  background-color: #F5F5F0;
}

.careers-hero-inner {
  max-width: 520px;
}

.careers-hero-title {
  font-family: var(--font-heading), 'Recoleta', serif;
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 600;
  line-height: 1.1;
  color: #1a1a1a;
  margin: 0 0 1.5rem;
}

.careers-hero-subtitle {
  font-size: 1.125rem;
  color: #555;
  line-height: 1.6;
  margin: 0 0 2.5rem;
  max-width: 380px;
}

.careers-hero-stats {
  display: flex;
  gap: 1.5rem;
}

.careers-hero-stats .stat-box {
  background: #1a1a1a;
  color: white;
  padding: 1.5rem 2rem;
  border-radius: 0.75rem;
  text-align: center;
  min-width: 140px;
}

.careers-hero-stats .stat-number {
  display: block;
  font-family: var(--font-heading), 'Recoleta', serif;
  font-size: 2.5rem;
  font-weight: 700;
  line-height: 1;
  color: #c8f560;
}

.careers-hero-stats .stat-label {
  display: block;
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.8);
  margin-top: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Responsive: Tablet & Mobile */
@media (max-width: 991px) {
  .careers-hero-container {
    grid-template-columns: 1fr;
    min-height: auto;
  }

  .careers-hero-image {
    height: 50vh;
    min-height: 300px;
  }

  .careers-hero-content {
    padding: 3rem 1.5rem;
  }

  .careers-hero-title {
    font-size: 2.25rem;
  }

  .careers-hero-stats {
    flex-direction: column;
    gap: 1rem;
  }

  .careers-hero-stats .stat-box {
    min-width: auto;
    width: 100%;
  }
}

/* ============================================
   Dynamic Hero Section (with venue)
   ============================================ */
/* Dynamic Hero Section */
.hero-section {
  position: relative;
  padding: 8rem 0 6rem;
  overflow: hidden;
}

.hero-section.has-venue {
  padding: 10rem 0 6rem;
}

.hero-section.has-venue .hero-background {
  background: none;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.hero-section.has-venue .hero-background::before {
  display: none;
}

.hero-section.has-venue .hero-overlay {
  background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.7) 100%);
}

.hero-section.has-venue .hero-title {
  font-size: 2.75rem;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}

.hero-background {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #8B5CF6 0%, #7c3aed 100%);
}

.hero-background::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url('/images/bgIntro.jpg');
  background-size: cover;
  background-position: center;
  opacity: 0.15;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  color: white;
}

.hero-tag {
  padding:1em;
  border-radius: 30px;
  background-color: #93cbff;
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 14px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: 0.68px;
  text-align: left;
  color: #000;
}

.hero-title {
  font-family: var(--font-heading);
  font-size: 3.5rem;
  font-weight: 700;
  margin: 0 0 1rem;
  line-height: 1.1;
}

.hero-subtitle {
  font-size: 1.25rem;
  opacity: 0.9;
  margin: 0 0 2.5rem;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.subtitle-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.subtitle-divider {
  margin: 0 0.25rem;
  opacity: 0.6;
}

.hero-stats {
  display: inline-flex;
  align-items: center;
  gap: 2rem;
  padding: 1.5rem 2.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 1rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-family: var(--font-heading);
  font-size: 2.5rem;
  font-weight: 700;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.8;
  margin-top: 0.25rem;
}

.stat-divider {
  width: 1px;
  height: 3rem;
  background: rgba(255, 255, 255, 0.3);
}

.search-bar-section {
  margin-top: 2rem;
  margin-bottom: 2rem;
  z-index: 20;
}

/* Adjust search bar when venue is active (overlapping hero) */
.has-venue ~ .search-bar-section {
  margin-top: -1.5rem;
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.search-input:focus {
  outline: none;
}

.dropdown-btn {
  transition: background-color 0.3s ease;
}

.dropdown-btn:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.rotate-180 {
  transform: rotate(180deg);
}

.dropdown-menu-custom {
  width: 12rem;
  z-index: 30;
}

.dropdown-menu-venue {
  width: 16rem;
  max-height: 300px;
  overflow-y: auto;
}

.dropdown-item-custom {
  background: transparent;
  transition: background-color 0.3s ease;
}

.dropdown-item-custom:hover {
  background-color: rgba(200, 245, 96, 0.3);
}

.dropdown-item-custom.active {
  background-color: rgba(200, 245, 96, 0.5);
}

.job-card {
  transition: all 0.3s ease;
}

.job-card:hover {
  box-shadow: 4px 4px 0 rgba(0, 0, 0, 1);
}

/* Pagination styles */
.pagination-btn {
  width: 40px;
  height: 40px;
  border: 2px solid #1a1a1a;
  background: #fff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(.disabled) {
  background: var(--brand-lime, #C8F560);
}

.pagination-btn.active {
  background: var(--brand-pink, #FF4D6D);
  color: #fff;
  border-color: var(--brand-pink, #FF4D6D);
}

.pagination-btn.disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.pagination-dots {
  padding: 0 0.5rem;
  color: #666;
}

.transition-fade-in {
  animation: fadeIn 0.2s ease-out;
}

.transition-fade-out {
  animation: fadeOut 0.15s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes fadeOut {
  from {
    opacity: 1;
    transform: scale(1);
  }
  to {
    opacity: 0;
    transform: scale(0.95);
  }
}

/* Responsive styles */
@media (max-width: 768px) {
  .hero-section {
    padding: 6rem 0 5rem;
  }

  .hero-section.has-venue {
    padding: 7rem 0 4rem;
  }

  .hero-section.has-venue .hero-title {
    font-size: 1.75rem;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .hero-subtitle {
    font-size: 1rem;
    flex-wrap: wrap;
  }

  .hero-stats {
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem 2rem;
  }

  .stat-divider {
    width: 3rem;
    height: 1px;
  }
}
</style>
