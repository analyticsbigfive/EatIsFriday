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

// Debug: watch for changes
watch(jobs, (newJobs) => {
  console.log('Jobs data changed:', newJobs)
  console.log('Jobs length:', newJobs?.length)
}, { immediate: true })

watch(pending, (isPending) => {
  console.log('Pending state:', isPending)
}, { immediate: true })

watch(error, (err) => {
  console.log('Error:', err)
}, { immediate: true })

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
  console.log('Computing uniqueSites, jobs.value:', jobs.value)
  if (!jobs.value) return []
  const sites = jobs.value.map(job => job.location).filter(Boolean)
  const unique = [...new Set(sites)]
  console.log('Unique sites:', unique)
  return unique
})

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
  console.log('Toggle job title dropdown, current:', showJobTitleDropdown.value)
  showJobTitleDropdown.value = !showJobTitleDropdown.value
  showSiteDropdown.value = false
  console.log('After toggle:', showJobTitleDropdown.value)
}

const toggleSiteDropdown = () => {
  console.log('Toggle site dropdown, current:', showSiteDropdown.value)
  showSiteDropdown.value = !showSiteDropdown.value
  showJobTitleDropdown.value = false
  console.log('After toggle:', showSiteDropdown.value)
}

const closeDropdowns = (e: Event) => {
  const target = e.target as HTMLElement
  // Ne fermer que si on clique en dehors du formulaire entier
  if (!target.closest('.job-search-form')) {
    console.log('Closing dropdowns - clicked outside form')
    showJobTitleDropdown.value = false
    showSiteDropdown.value = false
  }
}

onMounted(() => {
  console.log('JobSearchForm mounted')
  console.log('Initial jobs.value:', jobs.value)
  console.log('Initial pending:', pending.value)
  console.log('Initial error:', error.value)
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
      <p style="font-size: 11px; color: green;">Debug: {{ uniqueJobTitles.length }} titles, {{ uniqueSites.length }} sites loaded</p>
    </div>

    <div class="form-fields">
      <!-- Job Title Dropdown -->
      <div class="field-wrapper">
        <div
          class="select-field"
          :class="{ 'is-open': showJobTitleDropdown, 'has-value': selectedJobTitle }"
          @click.stop="toggleJobTitleDropdown"
        >
          <LucideBriefcase class="field-icon" />
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
              @click="selectedJobTitle = ''; showJobTitleDropdown = false"
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
          <LucideMapPin class="field-icon" />
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
              @click="selectedSite = ''; showSiteDropdown = false"
            >
              All sites
            </button>
            <button
              v-for="site in uniqueSites"
              :key="site"
              class="dropdown-item"
              :class="{ 'active': selectedSite === site }"
              @click="selectedSite = site; showSiteDropdown = false"
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
        <LucideArrowRight class="search-icon" />
      </button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.job-search-form {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-radius: 1.5rem;
  padding: 2.5rem;
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow:
    0 8px 32px rgba(0, 0, 0, 0.1),
    0 2px 8px rgba(0, 0, 0, 0.05);
  max-width: 520px;
  width: 100%;
}

.form-header {
  margin-bottom: 2rem;
}

.form-title {
  font-family: var(--font-heading);
  font-size: 2rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0 0 0.5rem 0;
  line-height: 1.2;
}

.form-subtitle {
  font-family: var(--font-body);
  font-size: 1rem;
  color: rgba(26, 26, 26, 0.7);
  margin: 0;
  line-height: 1.5;
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
  background: rgba(255, 255, 255, 0.9);
  border: 1.5px solid rgba(255, 255, 255, 0.8);
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 56px;

  &:hover {
    border-color: rgba(255, 77, 109, 0.3);
    background: white;
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
  color: rgba(26, 26, 26, 0.5);
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
  border: 2px solid var(--brand-dark, #1a1a1a);
  overflow: hidden;
  z-index: 9999;
  max-height: 240px;
  overflow-y: auto;

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
  width: 56px;
  height: 56px;
  background: var(--brand-pink);
  border: none;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;

  &:hover {
    background: darken(#FF4D6D, 8%);
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(255, 77, 109, 0.4);
  }

  &:active {
    transform: scale(0.98);
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
