<script setup lang="ts">
import { LucideBriefcase, LucideMapPin, LucideArrowRight } from 'lucide-vue-next'
import type { JobWithVenue } from '~/composables/useJobs'
import type { Venue } from '~/composables/useVenues'

const selectedJobTitle = ref('')
const selectedSite = ref('')
const showJobTitleDropdown = ref(false)
const showSiteDropdown = ref(false)

const { getJobsWithVenues } = useJobs()
const { getVenues } = useVenues()
const { getFormsContent } = usePageContent()
const { settings } = useGlobalSettings()

// Dynamic icon URL with fallback
const btnSearchForm = computed(() => settings.value?.icons?.btn_search_form || '/images/btnSearchForm.svg')

const jobs = ref<JobWithVenue[]>([])
const venues = ref<Venue[]>([])
const pending = ref(true)
const formLabels = ref<any>(null)

// Default labels (fallback)
const defaultLabels = {
  title: 'Trouvez le poste qui vous correspond',
  subtitle: 'Découvrez plus de 100 offres d’emploi partout en France',
  job_title_placeholder: 'Titre de poste',
  site_placeholder: 'Sites',
  all_jobs_label: 'Tous les titres de poste',
  all_sites_label: 'Tous les sites',
  loading_text: 'Chargement...'
}

// Computed labels with fallback
const labels = computed(() => ({
  title: formLabels.value?.title || defaultLabels.title,
  subtitle: formLabels.value?.subtitle || defaultLabels.subtitle,
  job_title_placeholder: formLabels.value?.job_title_placeholder || defaultLabels.job_title_placeholder,
  site_placeholder: formLabels.value?.site_placeholder || defaultLabels.site_placeholder,
  all_jobs_label: formLabels.value?.all_jobs_label || defaultLabels.all_jobs_label,
  all_sites_label: formLabels.value?.all_sites_label || defaultLabels.all_sites_label,
  loading_text: formLabels.value?.loading_text || defaultLabels.loading_text
}))

onMounted(async () => {
  const [fetchedJobs, fetchedVenues, formsContent] = await Promise.all([
    getJobsWithVenues(),
    getVenues(),
    getFormsContent()
  ])
  jobs.value = fetchedJobs || []
  venues.value = fetchedVenues || []
  formLabels.value = formsContent?.job_search || null
  pending.value = false
})

const getJobTitle = (job: JobWithVenue) => {
  return typeof job.title === 'string' ? job.title : job.title?.rendered || ''
}

const jobsWithTitles = computed(() => {
  if (!jobs.value) return []
  return jobs.value
})

const uniqueJobTitles = computed(() => {
  if (!jobs.value) return []
  const titles = jobs.value.map(job => getJobTitle(job))
  return [...new Set(titles)].filter(Boolean)
})

// Get unique venues from jobs that have venues
const uniqueSites = computed(() => {
  if (!jobs.value) return []
  const venueLocations = jobs.value
    .filter(job => job.venue)
    .map(job => job.venue!.location)
    .filter(Boolean)
  return [...new Set(venueLocations)]
})

const navigateToJob = (job: JobWithVenue) => {
  showJobTitleDropdown.value = false
  // Navigate to careers page with search filter for the job title
  const title = getJobTitle(job)
  navigateTo(`/careers?search=${encodeURIComponent(title)}`)
}

const selectSiteAndSearch = (site: string) => {
  selectedSite.value = site
  showSiteDropdown.value = false
  // Navigate to careers page filtered by venue/location
  navigateTo(`/careers?venue=${encodeURIComponent(site)}`)
}

const handleSearch = () => {
  const query = new URLSearchParams()
  if (selectedJobTitle.value) {
    query.set('search', selectedJobTitle.value)
  }
  if (selectedSite.value) {
    query.set('venue', selectedSite.value)
  }
  navigateTo(`/careers${query.toString() ? '?' + query.toString() : ''}`)
}

const toggleJobTitleDropdown = () => {
  showJobTitleDropdown.value = !showJobTitleDropdown.value
  showSiteDropdown.value = false
}

const toggleSiteDropdown = () => {
  showSiteDropdown.value = !showSiteDropdown.value
  showJobTitleDropdown.value = false
}

const closeDropdowns = (e: Event) => {
  const target = e.target as HTMLElement
  if (!target.closest('.job-search-form')) {
    showJobTitleDropdown.value = false
    showSiteDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})
</script>

<template>
  <div class="job-search-form">
    <div class="form-header">
      <h2 class="form-title">{{ labels.title }}</h2>
      <p class="form-subtitle">{{ labels.subtitle.replace('{count}', String(jobs?.length || 0)) }}</p>
    </div>

    <div class="form-fields">
      <!-- Job Title Dropdown -->
      <div class="field-wrapper">
        <div
          class="select-field"
          :class="{ 'is-open': showJobTitleDropdown, 'has-value': selectedJobTitle }"
          @click.stop="toggleJobTitleDropdown"
        >
          <span class="field-text">{{ selectedJobTitle || labels.job_title_placeholder }}</span>
          <svg class="chevron-icon" :class="{ 'rotated': showJobTitleDropdown }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9l6 6 6-6"/>
          </svg>
        </div>

        <div class="dropdown-menu" :class="{ 'is-visible': showJobTitleDropdown }" @click.stop>
          <div v-if="pending" class="dropdown-loading">{{ labels.loading_text }}</div>
          <template v-else>
            <button
              class="dropdown-item"
              :class="{ 'active': selectedJobTitle === '' }"
              @click="selectedJobTitle = ''; showJobTitleDropdown = false; navigateTo('/careers')"
            >
              {{ labels.all_jobs_label }}
            </button>
            <button
              v-for="job in jobsWithTitles"
              :key="job.slug"
              class="dropdown-item"
              :class="{ 'active': selectedJobTitle === getJobTitle(job) }"
              @click="navigateToJob(job)"
            >
              {{ getJobTitle(job) }}
            </button>
          </template>
        </div>
      </div>

      <!-- Site Dropdown -->
      <div class="field-wrapper">
        <div
          class="select-field"
          :class="{ 'is-open': showSiteDropdown, 'has-value': selectedSite }"
          @click.stop="toggleSiteDropdown"
        >
          <span class="field-text">{{ selectedSite || labels.site_placeholder }}</span>
          <svg class="chevron-icon" :class="{ 'rotated': showSiteDropdown }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9l6 6 6-6"/>
          </svg>
        </div>

        <div class="dropdown-menu" :class="{ 'is-visible': showSiteDropdown }" @click.stop>
          <div v-if="pending" class="dropdown-loading">{{ labels.loading_text }}</div>
          <template v-else>
            <button
              class="dropdown-item"
              :class="{ 'active': selectedSite === '' }"
              @click="selectedSite = ''; showSiteDropdown = false; navigateTo('/careers')"
            >
              {{ labels.all_sites_label }}
            </button>
            <button
              v-for="site in uniqueSites"
              :key="site"
              class="dropdown-item"
              :class="{ 'active': selectedSite === site }"
              @click="selectSiteAndSearch(site)"
            >
              {{ site }}
            </button>
          </template>
        </div>
      </div>

      <!-- Search Button -->
      <button
        class="search-button"
        @click="handleSearch"
        aria-label="Search jobs"
      >
        <img :src="btnSearchForm" alt="Search" class="search-icon" />
      </button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.job-search-form {
  padding: 31px 22px 35px;
  border-radius: 20px;
  -webkit-backdrop-filter: blur(20px);
  backdrop-filter: blur(20px);
  border: solid 3px rgba(253, 250, 248, 0.3);
  background-color: rgba(47, 47, 47, 0.4);
}

.form-header {
  margin-bottom: 2rem;
}

.form-title {
    font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 34px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #fff;
}

.form-subtitle {  font-family: FONTSPRINGDEMO-Recoleta;

   font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.44;
  letter-spacing: normal;
  text-align: left;
  color: #fff;
}

.form-fields {
  display: flex;
  gap: 0.75rem;
  align-items: flex-start;
}

.field-wrapper {
  position: relative;
  flex: 1;
}

.select-field {
         display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-radius: 30px;
    background-size: 100% 100%;
    background-repeat: no-repeat;
    cursor: pointer;
    transition: all 0.2s ease;
    max-height: 74px;
    max-width: 270px;
    min-height: 61px;
    border: 2px solid white;

  &:hover {
    border-color: var(--brand-pink);
  opacity: 0.9;
  }

  &.is-open {
    border-color: var(--brand-pink);
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 77, 109, 0.1);
  }

  &.has-value .field-text {
    color: var(--brand-dark);
  }
}

.field-icon {
  width: 1.25rem;
  height: 1.25rem;
  color: rgba(26, 26, 26, 0.5);
  flex-shrink: 0;
}

.field-text {
  flex: 1;
  font-family: var(--font-body);
  font-size: 0.9375rem;
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: rgba(255, 255, 255, 0.9);
    white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.chevron-icon {
  width: 1rem;
  height: 1rem;
  color: rgba(26, 26, 26, 0.4);
  flex-shrink: 0;
  transition: transform 0.2s ease;

  &.rotated {
    transform: rotate(180deg);
  }
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 0;
  right: 0;
  background: white;
  border-radius: 1rem;
  box-shadow:
    0 10px 40px rgba(0, 0, 0, 0.12),
    0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--brand-dark, #1a1a1a);
  overflow: hidden;
  z-index: 9999;
  max-height: 240px;
  overflow-y: auto;
width:34vw;
  // Caché par défaut
  display: none;

  // Visible quand la classe is-visible est présente
  &.is-visible {
    display: block;
  }
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 0.875rem 1.25rem;
  text-align: left;
  background: none;
  border: none;
  font-family: var(--font-body);
  font-size: 0.9375rem;
  color: var(--brand-dark);
  cursor: pointer;
  transition: all 0.15s ease;

  &:hover {
    background: rgba(255, 77, 109, 0.05);
  }

  &.active {
    background: rgba(255, 77, 109, 0.1);
    color: var(--brand-pink);
    font-weight: 500;
  }
}

.dropdown-loading {
  padding: 1rem 1.25rem;
  text-align: center;
  color: rgba(26, 26, 26, 0.5);
  font-size: 0.875rem;
}

.search-button {
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
  img{
    width: 60px;
    height: 60px;
  }
}

.search-icon {
  width: 1.5rem;
  height: 1.5rem;
  color: white;
}

// Dropdown animation
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

// Responsive
@media (max-width: 768px) {
  .job-search-form {
    padding: 1.75rem;
    border-radius: 1.25rem;
  }

  .form-title {
    font-size: 1.625rem;
  }

  .form-fields {
    flex-direction: column;
  }

  .field-wrapper {
    width: 100%;
  }

  .search-button {
    width: 100%;
    border-radius: 50px;
    height: 52px;
  }
}
</style>
