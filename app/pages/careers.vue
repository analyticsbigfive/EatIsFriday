<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { LucideSearch, LucideMapPin, LucideChevronLeft, LucideChevronRight } from 'lucide-vue-next'
import type { CareersContent } from '~/composables/usePageContent'
import type { JobWithVenue } from '~/composables/useJobs'
import type { Venue } from '~/composables/useVenues'

const route = useRoute()
const { getCareersContent } = usePageContent()
const { getJobsWithVenues, getJobVenueOptions } = useJobs()
const { getVenues } = useVenues()

const content = ref<CareersContent | null>(null)
const allJobs = ref<JobWithVenue[]>([])
const allVenues = ref<Venue[]>([])

const searchQuery = ref('')
const selectedJobType = ref('')
const selectedVenueId = ref('')
const showJobTypeDropdown = ref(false)
const showVenueDropdown = ref(false)

// Pagination
const currentPage = ref(1)
const itemsPerPage = 6

// Find the venue matching the selected venue ID
const activeVenue = computed(() => {
  if (!selectedVenueId.value || !allVenues.value.length) return null
  return allVenues.value.find(venue => venue.id === selectedVenueId.value) || null
})

onMounted(async () => {
  content.value = await getCareersContent()
  const fetchedVenues = await getVenues()
  if (fetchedVenues) {
    allVenues.value = fetchedVenues
  }
  const fetchedJobs = await getJobsWithVenues()
  if (fetchedJobs) {
    allJobs.value = fetchedJobs
  }
  if (content.value?.search_section?.job_types) {
    selectedJobType.value = content.value.search_section.job_types[0] || ''
  }

  // Apply URL query parameters
  if (route.query.venue) {
    // Try to find venue by name or ID from query
    const queryVenue = route.query.venue as string
    const matchedVenue = allVenues.value.find(v =>
      v.id === queryVenue ||
      v.location.toLowerCase().includes(queryVenue.toLowerCase()) ||
      v.name.toLowerCase().includes(queryVenue.toLowerCase())
    )
    if (matchedVenue) {
      selectedVenueId.value = matchedVenue.id
    }
  }
  if (route.query.search) {
    searchQuery.value = route.query.search as string
  }
  if (route.query.type) {
    selectedJobType.value = route.query.type as string
  }
})

// Label pour "All Sites" depuis le JSON
const allSitesLabel = computed(() => {
  return content.value?.search_section.all_sites_label || 'All Sites'
})

// Extraire toutes les venues uniques des jobs
const venueOptions = computed(() => {
  const venueIds = new Set<string>()
  allJobs.value.forEach(job => {
    if (job.venue_id) {
      venueIds.add(job.venue_id)
    }
  })
  // Get venue objects for display
  const venues = Array.from(venueIds)
    .map(id => allVenues.value.find(v => v.id === id))
    .filter((v): v is Venue => v !== undefined)
  return venues
})

// Helper pour obtenir le titre du job
const getJobTitle = (job: JobWithVenue) => {
  return typeof job.title === 'string' ? job.title : job.title?.rendered || ''
}

// Helper pour obtenir l'extrait du job
const getJobExcerpt = (job: JobWithVenue) => {
  return typeof job.excerpt === 'string' ? job.excerpt : job.excerpt?.rendered || ''
}

// Helper pour obtenir le nom de la venue du job
const getJobVenueName = (job: JobWithVenue) => {
  return job.venue?.name || 'Various Locations'
}

// Helper pour obtenir la location de la venue du job
const getJobVenueLocation = (job: JobWithVenue) => {
  return job.venue?.location || ''
}

// Helper pour formater la date relative
const getPostedTime = (dateString?: string) => {
  if (!dateString) return 'Recently'
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
    const venueName = getJobVenueName(job)
    const venueLocation = getJobVenueLocation(job)

    const matchesSearch = !searchQuery.value ||
      title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      excerpt.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      venueName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      venueLocation.toLowerCase().includes(searchQuery.value.toLowerCase())

    // Normaliser les types de job pour la comparaison
    const normalizedJobType = job.job_type.toLowerCase().replace('-', ' ')
    const normalizedSelectedType = selectedJobType.value.toLowerCase().replace('-', ' ')

    const matchesType = selectedJobType.value === content.value!.search_section.job_types[0] ||
      normalizedJobType.includes(normalizedSelectedType)

    // Filtre par venue_id depuis le dropdown
    const matchesVenueFilter = selectedVenueId.value === '' ||
      job.venue_id === selectedVenueId.value

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
watch([searchQuery, selectedJobType, selectedVenueId], () => {
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
        <div class="careers-hero-container d-flex row">
          <div class="careers-hero-content col-7">
            <div class="careers-hero-inner">
              <h1 class="careers-hero-title">
                {{ content.hero_default?.title_line_1 }}<br />
                {{ content.hero_default?.title_line_2 }}<br />
              </h1>
              
            </div>
          </div>
          <div class="careers-hero-image col-5">
            <img :src="content.hero_default?.image" alt="Join our team"  />
          </div>
        </div>
        <div id="limam" class="container d-flex flex-column justify-content-center text-center flex-wrap">
          <h3> {{ content.join_box?.title }}</h3>
              <p class="kougar">
                {{ content.join_box?.description }}
              </p>
        </div>
      </section>

      <!-- ========================================== -->
      <!-- Hero Section WITH venue (When job/venue selected) -->
      <!-- ========================================== -->
      <section v-else class="hero-section has-venue">
        <!-- Background with venue image -->
        <div class="hero-background" :style="{ backgroundImage: `url('${activeVenue.image}')` }">
          <div class="hero-overlay"></div>
        </div>
        <p class="careers-hero-subtitle">
          {{ content.hero_with_venue?.subtitle }}
        </p><!-- 
        <div class="careers-hero-stats">
          <div class="stat-box">
            <span class="stat-number">{{ allJobs.length }}</span>
            <span class="stat-label">{{ content.hero_with_venue?.stats?.open_positions_label }}</span>
          </div>
          <div class="stat-box">
            <span class="stat-number">{{ venueOptions.length - 1 }}</span>
            <span class="stat-label">{{ content.hero_with_venue?.stats?.locations_label }}</span>
          </div>
        </div> -->
        <!-- Hero Content -->
        <div class="container hero-content">
          <span class="hero-tag">{{ content.hero_with_venue?.tag }}</span>
          <h1 class="hero-title">
            {{ content.hero_with_venue?.title_prefix }} {{ activeVenue.name }}
          </h1>
          <p class="hero-subtitle">
            <LucideMapPin style="width: 1.25rem; height: 1.25rem;" /> {{ activeVenue.location }}
            <span class="subtitle-divider">•</span>
            {{ filteredJobs.length }} Open Position{{ filteredJobs.length !== 1 ? 's' : '' }}
          </p>
        </div>
      </section>

      <!-- Search & Filter Bar -->
      <section class="search-bar-section position-relative d-flex flex-column" :class="{ 'has-active-venue': activeVenue }">
        <div id="rallah" class="p-3 d-flex flex-column flex-md-row gap-3">
          <!-- Search Input -->
          <div class="flex-grow-1 position-relative musuc">
            <div id="neilcruz" class="d-flex align-items-center gap-3 px-3">
              <LucideSearch class="text-white opacity-75" style="width: 1.25rem; height: 1.25rem;" />
              <input v-model="searchQuery" type="text" :placeholder="content.search_section?.search_placeholder || 'Search jobs...'"
                class="bg-transparent text-white border-0 flex-grow-1 py-2 font-body search-input" />
            </div>
          </div>

          <!-- Venue Dropdown -->
          <div class="position-relative musuc">
            <button @click="showVenueDropdown = !showVenueDropdown; showJobTypeDropdown = false"
              class="border-start border-white border-opacity-25 px-4 py-2 d-flex align-items-center gap-3 text-white w-100 w-md-auto justify-content-between dropdown-btn">
              <LucideMapPin style="width: 1rem; height: 1rem;" class="opacity-75" />
              <span>{{ activeVenue?.name || allSitesLabel }}</span>
              <img src="/images/chevronDown.svg" alt="chevron" class="chevron-icon" :class="{ 'rotated': showVenueDropdown }" />
            </button>

        <!-- Venue Dropdown Menu -->
        <Transition enter-active-class="transition-fade-in" leave-active-class="transition-fade-out">
          <div v-if="showVenueDropdown"
            class="position-absolute top-100 end-0 mt-2 bg-white border-organic shadow-organic-lg dropdown-menu-custom dropdown-menu-venue">
            <!-- All Sites option -->
            <button
              @click="selectedVenueId = ''; showVenueDropdown = false"
              :class="['w-100 text-start px-3 py-2 border-0 fw-medium dropdown-item-custom', !selectedVenueId ? 'active' : '']">
              {{ allSitesLabel }}
            </button>
            <!-- Venue options -->
            <button v-for="venue in venueOptions" :key="venue.id"
              @click="selectedVenueId = venue.id; showVenueDropdown = false"
              :class="['w-100 text-start px-3 py-2 border-0 fw-medium dropdown-item-custom', selectedVenueId === venue.id ? 'active' : '']">
              {{ venue.name }} - {{ venue.location }}
            </button>
          </div>
        </Transition>
          </div>

          <!-- Job Type Dropdown -->
          <div class="position-relative musuc">
        <button @click="showJobTypeDropdown = !showJobTypeDropdown; showVenueDropdown = false"
          class="border-start border-white border-opacity-25 px-4 py-2 d-flex align-items-center gap-3 text-white w-100 w-md-auto justify-content-between dropdown-btn">
          <span>{{ selectedJobType || 'All Job Types' }}</span>
          <img src="/images/chevronDown.svg" alt="chevron" class="chevron-icon" :class="{ 'rotated': showJobTypeDropdown }" />
        </button>
        <!-- Dropdown Menu -->
        <Transition enter-active-class="transition-fade-in" leave-active-class="transition-fade-out">
          <div v-if="showJobTypeDropdown"
            class="position-absolute top-100 end-0 mt-2 bg-white border-organic shadow-organic-lg dropdown-menu-custom">
            <button v-for="type in content.search_section?.job_types || []" :key="type"
          @click="selectedJobType = type; showJobTypeDropdown = false" :class="[
            'w-100 text-start px-3 py-2 border-0 fw-medium dropdown-item-custom',
            selectedJobType === type ? 'active' : ''
          ]">
          {{ type }}
            </button>
          </div>
        </Transition>
          </div>
        </div>
        <hr>
        <p id="bananasleep">
            {{ filteredJobs.length }} {{ filteredJobs.length === 1 ? content.job_listing?.positions_available_singular :
              content.job_listing?.positions_available_plural }} {{ content.job_listing?.positions_available_suffix }}
          </p>
      </section>

      <!-- Job Grid -->
      <section id="jobgrid" class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
          
          <p v-if="totalPages > 1" class="text-muted small mb-0">
            Page {{ currentPage }} of {{ totalPages }}
          </p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-4">
          <div v-for="job in paginatedJobs" :key="job.id" class="col">
            <div class="bg-white border-organic p-4 h-100 job-card">
              <div class="d-flex flex-column h-100 justify-content-between">
                <!-- Header -->
                <div id="jobcardheader">
                  <h3 class="font-heading fw-bold fs-5 lh-sm mb-1">{{ getJobTitle(job) }}</h3>
                  <p class="job-date small text-muted fw-medium mb-3">{{ content.job_listing?.posted_prefix }} {{
                    getPostedTime(job.date) }}</p>

                  <!-- Tags Row -->
                  <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="tag-blue">{{ job.department }}</span>
                   <!-- <span class="tag-outline d-flex align-items-center gap-1">
                      <LucideMapPin style="width: 0.75rem; height: 0.75rem;" /> {{ getJobVenueLocation(job) }}
                    </span>-->
                    <span class="tag-lime d-flex align-items-center gap-1">
                      <nuxt-img src="/images/streamline-emojis_briefcase.png" alt="briefcase icon" width="16" height="16" />
                      {{ job.job_type }}
                    </span>
                    <span class="tag-yellow d-flex align-items-center gap-1">
                      <nuxt-img src="/images/streamline-emojis_moneybag.svg" alt="money bag icon" width="16" height="16" />
                      {{ job.salary }}
                    </span>
                  </div>

                  <!-- Description -->
                  <p class="small text-muted mb-4 line-clamp-3 font-body lh-lg">
                    {{ getJobExcerpt(job) }}
                  </p>
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-3 mt-auto">
                  <NuxtLink class="matiti":to="`/jobs/${job.slug}`">
                    <nuxt-img src="/images/btnApplu.svg"></nuxt-img>
                  </NuxtLink>
                  <NuxtLink class="matiti" :to="`/jobs/${job.slug}`">
                    <nuxt-img src="/images/btnVieu.svg"></nuxt-img>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Results -->
        <div v-if="filteredJobs.length === 0" class="text-center py-5 bg-white border-organic">
          <p class="fs-5 text-muted mb-2">{{ content.no_results?.title }}</p>
          <p class="text-secondary mb-3">{{ content.no_results?.description }}</p>
          <button
            @click="searchQuery = ''; selectedJobType = content.search_section?.job_types?.[0] || ''; selectedVenueId = ''"
            class="text-brand-pink fw-bold btn btn-link text-decoration-none">
            {{ content.no_results?.clear_filters_button }}
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="d-flex justify-content-center align-items-center gap-2 mt-5">
          <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="pagination-btn"
            :class="{ 'disabled': currentPage === 1 }">
            <LucideChevronLeft style="width: 1.25rem; height: 1.25rem;" />
          </button>

          <template v-for="page in totalPages" :key="page">
            <button v-if="page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)"
              @click="goToPage(page)" class="pagination-btn" :class="{ 'active': currentPage === page }">
              {{ page }}
            </button>
            <span v-else-if="page === currentPage - 2 || page === currentPage + 2" class="pagination-dots">...</span>
          </template>

          <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" class="pagination-btn"
            :class="{ 'disabled': currentPage === totalPages }">
            <LucideChevronRight style="width: 1.25rem; height: 1.25rem;" />
          </button>
        </div>
      </section>

      <!-- CTA Section -->
      <section id="mahamad" class="py-5 text-center">
        <div class="container">
          <h2 class="font-heading display-5 fw-bold text-white mb-4">
            {{ content.cta_section?.title }}
          </h2>
          <p class="text-secondary mx-auto mb-4 font-body" style="max-width: 42rem;">
            {{ content.cta_section?.description }}
          </p>
          <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <!-- <button class="btn-lime fs-5 px-5 py-3">
              {{ content.cta_section?.explore_venues_button }}
            </button>
            <button class="btn-secondary fs-5 px-5 py-3">
              {{ content.cta_section?.general_application_button }}
            </button> -->
            <NuxtLink to="/careers">
              <NuxtImg src="/images/btnDiscoverAndApply.svg" width="316" />
            </NuxtLink>
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
+
<style scoped lang="scss">
/* ============================================
   CAREERS HERO DEFAULT (No venue selected)
   ============================================ */
.careers-hero-default {
  background-color: #FFF;
  padding-top: 6rem;
  margin:4em auto;
  
}

.careers-hero-container {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  background: url(/images/dida.svg);
  background-repeat: no-repeat;
  background-size:cover;
  max-width: 1400px;
  margin: 0 auto;
  max-height: 400px;
  height: 100vh;
  width: 100vw;
  margin: 1em auto;
}

.careers-hero-image {
  position: relative;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow: hidden;
}

.careers-hero-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
  max-width:340px;
  max-height:340px;
}

.careers-hero-content {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  margin:0 !important;
  
}

.careers-hero-inner {
  width:100%;
  margin:0;
}

.careers-hero-title {
  font-family: var(--font-heading), 'Recoleta', serif;
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 600;
  line-height: 1.1;
  color: #1a1a1a;
  margin: 0 0 1.5rem;
  z-index:0;
  position:relative;
  &::before{
   content:"" ;
    background:url(/images/decoHeaderBg.svg);
    width:400px;
  height:80px;
    position:absolute;
    z-index:-1;
    
  }
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
  min-height: 80vh;
}

.hero-section.has-venue {
  padding:0em !important;
  max-height:710px !important;
  min-height:0px !important;
  height:100vh !important;
  display:flex !important;
  flex-direction:column !important;
  align-items:center !important;
  justify-content:center !important;
}
  

.hero-section.has-venue .hero-background {
  background: none;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  justify-content: center;
}

.hero-section.has-venue .hero-background::before {
  display: none;
}

.hero-section.has-venue .hero-overlay {
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.7) 100%);
}

.hero-section.has-venue .hero-title {
  font-size: 2.75rem;
  max-width: 700px;
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
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.3) 100%);
}

.hero-content {
  position: relative;
  z-index: 1;
  color: white;
  text-align: left;
  justify-content: center;
  display: flex;
  flex-wrap: wrap;
  flex-direction: column;
  align-items: flex-start;
}

.hero-tag {
  padding: 1em;
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
  margin-bottom: 2em;
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
     /* margin-top: 2rem; */
    /* margin-bottom: 2rem; */
    z-index: 20;
    max-width: 1400px;
    background: url(/images/le24.svg);
    max-height: 263px;
    background-repeat: no-repeat;
    background-size: cover;
    padding: 0em;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    width: 100vw;
    height: 100vh;
    &.has-active-venue {
        margin-top: -9.5rem !important;
        backdrop-filter: blur(20px);
        border: solid 3px rgba(253, 250, 248, 0.2);
        background-color: rgba(47, 47, 47, 0.4);
        background-image: none;
        border-radius: 21px;
        padding: 0em;
        height:150px !important;
        .dropdown-menu-custom{
            backdrop-filter: blur(20px);
        border: solid 3px rgba(253, 250, 248, 0.2);
        background-color: rgba(47, 47, 47, 0.93) !important;
        }
        #neilcruz svg{
          color:white !important;

        }
        .search-input::placeholder{
          
            font-family: FONTSPRINGDEMO-RecoletaMedium;
          font-size: 18px;
          font-weight: normal;
          font-stretch: normal;
          font-style: normal;
          line-height: normal;
          letter-spacing: normal;
          text-align: left;
          color: #fff !important;
        }

      #bananasleep{
      display:none !important;
      }
      #rallah{
  
        height: 72px;
        padding: 0 !important;
        margin:  27px 0 0 0;
          input, button{
            font-family: FONTSPRINGDEMO-RecoletaMedium;
          font-size: 18px;
          font-weight: normal;
          font-stretch: normal;
          font-style: normal;
          line-height: normal;
          letter-spacing: normal;
          text-align: left;
          color: #fff !important;
          }
      }
  .musuc{
  background:none !important;
  border:2px white solid !important;
  border-radius:10px !important;
  }
    }
}

/* Adjust search bar when venue is active (overlapping hero) */
.has-venue~.search-bar-section {
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

/* Chevron icon animations */
.chevron-icon {
  width: 1rem;
  height: 1rem;
  transition: transform 0.3s ease;
}

.chevron-icon.rotated {
  transform: rotate(180deg);
}

.dropdown-btn:hover .chevron-icon {
  transform: translateY(3px);
}

.dropdown-btn:hover .chevron-icon.rotated {
  transform: rotate(180deg) translateY(3px);
}

.dropdown-menu-custom {
  width: 100%;
  z-index: 30;
}

.dropdown-menu-venue {
  width: 100%;
  min-height: 300px !important;
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
  #limam{
    width:100vw;
    max-width:1400px;
    padding:4em 0 0 0 ;
    h3{
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
    p{
        font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.84;
  letter-spacing: normal;
  text-align: left;
  color: #000;
    }
  }
  .musuc{
    background: url(/images/searcgBgContainer.svg);
    width: 100vw !important;
    height: 100vh !important;
    max-width: 427px;
    max-height: 74px !important;
    border: none;
    background-repeat: no-repeat !important;
    background-size: contain !important;
    display: flex;
    align-items: center;
    justify-content: center;

    input {
        font-family: FONTSPRINGDEMO-RecoletaMedium;
        font-size: 18px;
        font-weight: normal;
        font-stretch: normal;
        font-style: normal;
        line-height: 74px;
        letter-spacing: normal;
        text-align: left;
        color: rgba(0, 0, 0, 0.8) !important;
        height: 100%;

        &::placeholder {
            font-family: FONTSPRINGDEMO-RecoletaMedium;
            font-size: 18px;
            color: rgba(0, 0, 0, 0.5) !important;
        }
    }
    button{
      background:none;
      border:none;
      width:100%;
      height:100%;   
        font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: rgba(0, 0, 0, 0.8) !important;
  text-align: left;
  
    }
  }
#neilcruz{
  width:100%;
  svg{
  color:black !important;
  }
}
#bananasleep{       width: 94%;
    padding: 1em 0em 0em 0em;
    position: relative;
    font-family: FONTSPRINGDEMO-RecoletaMedium;
    font-size: 18px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.84;
    letter-spacing: normal;
    text-align: left;
    color: rgba(0, 0, 0, 0.8) !important;
    margin: 0;
    &::before{
         content: "";
    background: url(/images/lineUnderSearchBar.svg);
    width: 100%;
    height: 1px;
    position: absolute;
    top: -13px;
    left: 0;
    background-repeat: no-repeat;
    right: 0;
    margin: auto;
    background-position: center;
    padding: 1em 0 0 0;
    }
}
  #jobgrid{
  max-width:1400px;
  width:100%;
  padding:2em 4em !important;
  border-radius: 20px;
  background-color: #fff6f0;
  }

  #jobcardheader{
    p{
        font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.52 !important;
  letter-spacing: normal;
  text-align: left;
  color: #000 !important;
    }
    .job-date{
      font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.52;
  letter-spacing: normal;
  text-align: left;
  color: rgba(0, 0, 0, 0.7);
  display:block;
  margin:1em auto;
    }
    .tag-blue{
      font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.52;
  letter-spacing: normal;
  text-align: left;
  color: rgba(0, 0, 0, 0.7);
    }
  }
  .matiti{
    width:100vw;
    height:100vh;
      max-width: 298px;
      max-height: 64px;
    img{
      width:100%;
      
    }
  }
#mahamad{
    background: url(/images/ctaBgCareers.svg);
    background-repeat: no-repeat;
    background-size: contain;
    padding: 6em 0 !important;
    margin: 4em auto 0 auto;
    text-align: center;
    max-width: 1400px;
    width:100%;
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
    }
    p{
        font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.84;
  letter-spacing: normal;
  text-align: center;
  color: #fff !important;
    }
}
</style>
