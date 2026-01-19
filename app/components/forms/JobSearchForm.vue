<script setup lang="ts">
import { LucideBriefcase, LucideMapPin, LucideArrowRight } from 'lucide-vue-next'
import type { Job } from '~/composables/useJobs'

const selectedJobTitle = ref('')
const selectedSite = ref('')
const showJobTitleDropdown = ref(false)
const showSiteDropdown = ref(false)

// Use useLazyFetch for client-side data loading
const { data: jobs, pending, error } = useLazyFetch<Job[]>('/api/jobs.json', {
  default: () => [],
  server: false
})


const getJobTitle = (job: Job) => {
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

const uniqueSites = computed(() => {
  if (!jobs.value) return []
  const sites = jobs.value.map(job => job.location).filter(Boolean)
  return [...new Set(sites)]
})

const navigateToJob = (job: Job) => {
  showJobTitleDropdown.value = false
  navigateTo(`/jobs/${job.slug}`)
}

const selectSiteAndSearch = (site: string) => {
  selectedSite.value = site
  showSiteDropdown.value = false
  // Navigate to jobs page filtered by location
  navigateTo(`/jobs?location=${encodeURIComponent(site)}`)
}

const handleSearch = () => {
  const query = new URLSearchParams()
  if (selectedJobTitle.value) {
    query.set('title', selectedJobTitle.value)
  }
  if (selectedSite.value) {
    query.set('location', selectedSite.value)
  }
  navigateTo(`/jobs${query.toString() ? '?' + query.toString() : ''}`)
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
      <h2 class="form-title">Find Your Perfect Role</h2>
      <p class="form-subtitle">Explore more than {{ jobs?.length || 0 }} open positions across France</p>
    </div>

    <div class="form-fields">
      <!-- Job Title Dropdown -->
      <div class="field-wrapper">
        <div
          class="select-field"
          :class="{ 'is-open': showJobTitleDropdown, 'has-value': selectedJobTitle }"
          @click.stop="toggleJobTitleDropdown"
        >
          <span class="field-text">{{ selectedJobTitle || 'Select job title' }}</span>
          <svg class="chevron-icon" :class="{ 'rotated': showJobTitleDropdown }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9l6 6 6-6"/>
          </svg>
        </div>

        <div class="dropdown-menu" :class="{ 'is-visible': showJobTitleDropdown }" @click.stop>
          <div v-if="pending" class="dropdown-loading">Loading...</div>
          <template v-else>
            <button
              class="dropdown-item"
              :class="{ 'active': selectedJobTitle === '' }"
              @click="selectedJobTitle = ''; showJobTitleDropdown = false; navigateTo('/jobs')"
            >
              All job titles
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
          <span class="field-text">{{ selectedSite || 'Select sites' }}</span>
          <svg class="chevron-icon" :class="{ 'rotated': showSiteDropdown }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9l6 6 6-6"/>
          </svg>
        </div>

        <div class="dropdown-menu" :class="{ 'is-visible': showSiteDropdown }" @click.stop>
          <div v-if="pending" class="dropdown-loading">Loading...</div>
          <template v-else>
            <button
              class="dropdown-item"
              :class="{ 'active': selectedSite === '' }"
              @click="selectedSite = ''; showSiteDropdown = false; navigateTo('/jobs')"
            >
              All sites
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
        <img src="/images/btnSearchForm.svg" alt="Search" class="search-icon" />
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
    background: url(/images/bgInputForm.svg);
    background-size: 100% 100%;
    background-repeat: no-repeat;
    cursor: pointer;
    transition: all 0.2s ease;
    max-height: 74px;
    max-width: 270px;

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
