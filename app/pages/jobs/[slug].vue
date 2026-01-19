<script setup lang="ts">
import { LucideMapPin, LucideBriefcase, LucideDollarSign, LucideCalendar, LucideCheck, LucideStar, LucideArrowLeft, LucideShare2, LucideHeart } from 'lucide-vue-next'
import type { Job } from '~/composables/useJobs'

// IMMEDIATE LOG - should appear when script loads
console.log('!!!!! [slug].vue SCRIPT SETUP EXECUTING !!!!!')

const route = useRoute()
const { getJobBySlug, getJobs } = useJobs()

const job = ref<Job | null>(null)
const relatedJobs = ref<Job[]>([])
const isLoading = ref(true)
const showApplyModal = ref(false)

// Add click listener to entire document to debug
if (typeof window !== 'undefined') {
  console.log('Adding global click listener...')
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement
    console.log('CLICK DETECTED on:', target.tagName, target.className, target.textContent?.substring(0, 30))
  })
}

onMounted(async () => {
  console.log('=== [slug].vue PAGE MOUNTED ===')
  alert('Page [slug].vue mounted!') // This should show a popup!
  const slug = route.params.slug as string
  console.log('Loading job with slug:', slug)
  job.value = await getJobBySlug(slug)
  console.log('Job loaded:', job.value?.title)

  // Get related jobs (same job_type, excluding current)
  const allJobs = await getJobs()
  if (allJobs && job.value) {
    relatedJobs.value = allJobs
      .filter(j => j.id !== job.value!.id && j.job_type === job.value!.job_type)
      .slice(0, 3)
  }

  isLoading.value = false
})

const getJobTitle = (j: Job) => {
  return typeof j.title === 'string' ? j.title : j.title?.rendered || ''
}

const getJobContent = (j: Job) => {
  return typeof j.content === 'string' ? j.content : j.content?.rendered || ''
}

const getJobExcerpt = (j: Job) => {
  return typeof j.excerpt === 'string' ? j.excerpt : j.excerpt?.rendered || ''
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
}

const openApplyModal = () => {
  console.log('========== OPEN APPLY MODAL ==========')
  console.log('showApplyModal BEFORE:', showApplyModal.value)
  showApplyModal.value = true
  console.log('showApplyModal AFTER:', showApplyModal.value)
  console.log('=======================================')
}

// Debug watcher
watch(showApplyModal, (newVal) => {
  console.log('>>> showApplyModal CHANGED TO:', newVal)
})

const closeApplyModal = () => {
  console.log('closeApplyModal called')
  showApplyModal.value = false
}

useHead(() => ({
  title: job.value ? `${getJobTitle(job.value)} - Careers | Eat Is Family` : 'Job Details',
  meta: [
    { name: 'description', content: job.value ? getJobExcerpt(job.value) : '' }
  ]
}))
</script>

<template>
  <div class="job-detail-page">
    <!-- Loading State -->
    <div v-if="isLoading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading job details...</p>
    </div>

    <!-- Job Not Found -->
    <div v-else-if="!job" class="not-found-container">
      <h1>Job Not Found</h1>
      <p>The position you're looking for doesn't exist or has been filled.</p>
      <NuxtLink to="/jobs" class="btn-back">
        <LucideArrowLeft class="btn-icon" />
        Browse All Jobs
      </NuxtLink>
    </div>

    <!-- Job Details -->
    <template v-else>
      <!-- Hero Section -->
      <section class="job-hero">
        <div class="hero-image">
          <img :src="job.featured_media" :alt="getJobTitle(job)" />
          <div class="hero-overlay"></div>
        </div>
        <div class="container hero-content">
          <NuxtLink to="/jobs" class="back-link">
            <LucideArrowLeft class="back-icon" />
            Back to Jobs
          </NuxtLink>

          <div class="job-header">
            <span class="job-type-tag" :class="job.job_type.toLowerCase().replace('-', '')">
              {{ job.job_type }}
            </span>
            <h1 class="job-title">{{ getJobTitle(job) }}</h1>

            <div class="job-meta-grid">
              <div class="meta-card">
                <LucideMapPin class="meta-icon" />
                <div class="meta-text">
                  <span class="meta-label">Location</span>
                  <span class="meta-value">{{ job.location }}</span>
                </div>
              </div>
              <div class="meta-card">
                <LucideDollarSign class="meta-icon" />
                <div class="meta-text">
                  <span class="meta-label">Salary</span>
                  <span class="meta-value">{{ job.salary }}</span>
                </div>
              </div>
              <div class="meta-card">
                <LucideBriefcase class="meta-icon" />
                <div class="meta-text">
                  <span class="meta-label">Job Type</span>
                  <span class="meta-value">{{ job.job_type }}</span>
                </div>
              </div>
              <div class="meta-card">
                <LucideCalendar class="meta-icon" />
                <div class="meta-text">
                  <span class="meta-label">Posted</span>
                  <span class="meta-value">{{ formatDate(job.date) }}</span>
                </div>
              </div>
            </div>

            <div class="hero-actions">
              <button class="btn-apply-hero" @click="openApplyModal">
                Apply for This Position
              </button>
              <button class="btn-icon-action" aria-label="Save job">
                <LucideHeart />
              </button>
              <button class="btn-icon-action" aria-label="Share job">
                <LucideShare2 />
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Content Section -->
      <section class="job-content-section">
        <div class="container">
          <div class="content-grid">
            <!-- Main Content -->
            <div class="main-content">
              <!-- About the Position -->
              <div class="content-card">
                <h2 class="section-title">About the Position</h2>
                <div class="prose" v-html="getJobContent(job)"></div>
              </div>

              <!-- Requirements -->
              <div class="content-card">
                <h2 class="section-title">Requirements</h2>
                <ul class="requirements-list">
                  <li v-for="(req, index) in job.requirements" :key="index">
                    <div class="list-icon requirements-icon">
                      <LucideCheck />
                    </div>
                    <span>{{ req }}</span>
                  </li>
                </ul>
              </div>

              <!-- Benefits -->
              <div class="content-card">
                <h2 class="section-title">What We Offer</h2>
                <ul class="benefits-list">
                  <li v-for="(benefit, index) in job.benefits" :key="index">
                    <div class="list-icon benefits-icon">
                      <LucideStar />
                    </div>
                    <span>{{ benefit }}</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar">
              <!-- Apply Card -->
              <div id="apply" class="apply-card">
                <h3 class="apply-title">Ready to Join Us?</h3>
                <p class="apply-subtitle">Take the next step in your career journey.</p>

                <button class="btn-apply-sidebar" @click="openApplyModal">
                  Apply Now
                </button>
              </div>

              <!-- Quick Info -->
              <div class="quick-info-card">
                <h4>Quick Info</h4>
                <div class="info-item">
                  <span class="info-label">Department</span>
                  <span class="info-value">{{ job.department || 'Culinary & Hospitality' }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Experience</span>
                  <span class="info-value">See Requirements</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Remote</span>
                  <span class="info-value">{{ job.location.toLowerCase().includes('remote') ? 'Yes' : 'On-site' }}</span>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <!-- Related Jobs -->
      <section v-if="relatedJobs.length > 0" class="related-section">
        <div class="container">
          <h2 class="related-title">Similar Positions</h2>
          <div class="related-grid">
            <NuxtLink
              v-for="relatedJob in relatedJobs"
              :key="relatedJob.id"
              :to="`/jobs/${relatedJob.slug}`"
              class="related-card"
            >
              <div class="related-image">
                <img :src="relatedJob.featured_media" :alt="getJobTitle(relatedJob)" loading="lazy" />
              </div>
              <div class="related-content">
                <span class="related-type">{{ relatedJob.job_type }}</span>
                <h3>{{ getJobTitle(relatedJob) }}</h3>
                <div class="related-meta">
                  <LucideMapPin class="related-icon" />
                  <span>{{ relatedJob.location }}</span>
                </div>
              </div>
            </NuxtLink>
          </div>
        </div>
      </section>

      <!-- Bottom CTA -->
      <section class="bottom-cta">
        <div class="container">
          <div class="cta-content">
            <h2>Interested in This Role?</h2>
            <p>Don't miss this opportunity to join our team.</p>
            <button class="btn-apply-bottom" @click="openApplyModal">
              Apply Now
            </button>
          </div>
        </div>
      </section>

      <!-- Apply Modal -->
      <JobApplyModal
        :is-open="showApplyModal"
        :job-title="getJobTitle(job)"
        :job-location="job.location"
        :job-slug="job.slug"
        @close="closeApplyModal"
      />
    </template>
  </div>
</template>

<style scoped lang="scss">
.job-detail-page {
  min-height: 100vh;
  background: var(--brand-gray, #F5F5F5);
}

// Loading & Not Found
.loading-container,
.not-found-container {
  min-height: 60vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 2rem;
}

.loading-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid var(--brand-gray);
  border-top-color: var(--brand-pink);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-container p,
.not-found-container p {
  margin-top: 1rem;
  color: rgba(0, 0, 0, 0.5);
}

.not-found-container h1 {
  font-family: var(--font-heading);
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 1.5rem;
  padding: 0.875rem 1.5rem;
  background: var(--brand-pink);
  color: white;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 77, 109, 0.3);
  }
}

.btn-icon {
  width: 1.25rem;
  height: 1.25rem;
}

// Hero Section
.job-hero {
  position: relative;
  min-height: 500px;
  display: flex;
  align-items: flex-end;
  padding-bottom: 3rem;
}

.hero-image {
  position: absolute;
  inset: 0;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.3) 0%,
    rgba(0, 0, 0, 0.7) 100%
  );
}

.hero-content {
  position: relative;
  z-index: 1;
  color: white;
  width: 100%;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 2rem;
  opacity: 0.9;
  transition: opacity 0.2s ease;

  &:hover {
    opacity: 1;
  }
}

.back-icon {
  width: 1rem;
  height: 1rem;
}

.job-header {
  max-width: 800px;
}

.job-type-tag {
  display: inline-block;
  padding: 0.375rem 1rem;
  background: var(--brand-lime);
  color: var(--brand-dark);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 50px;
  margin-bottom: 1rem;

  &.fulltime {
    background: var(--brand-lime);
  }

  &.parttime {
    background: var(--brand-blue);
  }
}

.job-title {
  font-family: var(--font-heading);
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.1;
  margin: 0 0 2rem;
}

.job-meta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.meta-card {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 0.75rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.meta-icon {
  width: 1.5rem;
  height: 1.5rem;
  color: var(--brand-lime);
  flex-shrink: 0;
}

.meta-text {
  display: flex;
  flex-direction: column;
}

.meta-label {
  font-size: 0.75rem;
  opacity: 0.7;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.meta-value {
  font-weight: 600;
  font-size: 0.9375rem;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btn-apply-hero {
  padding: 1rem 2rem;
  background: var(--brand-pink);
  color: white;
  border: 2px solid white;
  border-radius: 0.5rem;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background: white;
    color: var(--brand-pink);
    transform: translateY(-2px);
  }
}

.btn-icon-action {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  color: white;
  cursor: pointer;
  transition: all 0.2s ease;

  svg {
    width: 1.25rem;
    height: 1.25rem;
  }

  &:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
  }
}

// Content Section
.job-content-section {
  padding: 3rem 0;
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2rem;
  align-items: start;
}

.main-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.content-card {
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 1rem;
  padding: 2rem;
}

.section-title {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0 0 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--brand-gray);
}

.prose {
  color: rgba(0, 0, 0, 0.75);
  line-height: 1.8;

  :deep(p) {
    margin: 0 0 1rem;

    &:last-child {
      margin-bottom: 0;
    }
  }
}

.requirements-list,
.benefits-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;

  li {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    color: rgba(0, 0, 0, 0.75);
    line-height: 1.5;
  }
}

.list-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 0.125rem;

  svg {
    width: 14px;
    height: 14px;
  }
}

.requirements-icon {
  background: var(--brand-lime);
  color: var(--brand-dark);
}

.benefits-icon {
  background: var(--brand-pink);
  color: white;
}

// Sidebar
.sidebar {
  position: sticky;
  top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.apply-card {
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 4px 4px 0 var(--brand-pink);
}

.apply-title {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0 0 0.5rem;
}

.apply-subtitle {
  color: rgba(0, 0, 0, 0.6);
  margin: 0 0 1.5rem;
}

.btn-apply-sidebar {
  width: 100%;
  padding: 1rem 2rem;
  background: var(--brand-pink);
  color: white;
  border: 2px solid var(--brand-dark);
  border-radius: 0.75rem;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background: var(--brand-lime);
    color: var(--brand-dark);
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--brand-dark);
  }
}

.quick-info-card {
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 1rem;
  padding: 1.5rem;

  h4 {
    font-family: var(--font-heading);
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 1rem;
    color: var(--brand-dark);
  }
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--brand-gray);

  &:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
}

.info-label {
  color: rgba(0, 0, 0, 0.5);
  font-size: 0.875rem;
}

.info-value {
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--brand-dark);
}

// Related Section
.related-section {
  padding: 4rem 0;
  background: white;
  border-top: 2px solid var(--brand-dark);
}

.related-title {
  font-family: var(--font-heading);
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0 0 2rem;
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.related-card {
  display: flex;
  background: var(--brand-gray);
  border: 2px solid var(--brand-dark);
  border-radius: 0.75rem;
  overflow: hidden;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 4px 4px 0 rgba(0, 0, 0, 1);
  }
}

.related-image {
  width: 120px;
  flex-shrink: 0;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.related-content {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.related-type {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--brand-pink);
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.related-content h3 {
  font-family: var(--font-heading);
  font-size: 1rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0.25rem 0 0.5rem;
  line-height: 1.3;
}

.related-meta {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.8125rem;
  color: rgba(0, 0, 0, 0.6);
}

.related-icon {
  width: 0.875rem;
  height: 0.875rem;
}

// Bottom CTA
.bottom-cta {
  background: var(--brand-dark);
  padding: 4rem 0;
}

.cta-content {
  text-align: center;
  max-width: 500px;
  margin: 0 auto;
}

.cta-content h2 {
  font-family: var(--font-heading);
  font-size: 2rem;
  color: white;
  margin: 0 0 0.5rem;
}

.cta-content p {
  color: rgba(255, 255, 255, 0.7);
  margin: 0 0 1.5rem;
}

.btn-apply-bottom {
  padding: 1rem 3rem;
  background: var(--brand-lime);
  color: var(--brand-dark);
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    box-shadow: 4px 4px 0 rgba(255, 255, 255, 0.3);
    transform: translate(-2px, -2px);
  }
}

// Responsive
@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: static;
  }
}

@media (max-width: 768px) {
  .job-hero {
    min-height: 400px;
  }

  .job-title {
    font-size: 2rem;
  }

  .job-meta-grid {
    grid-template-columns: 1fr;
  }

  .hero-actions {
    flex-wrap: wrap;
  }

  .btn-apply-hero {
    width: 100%;
    order: 1;
  }

  .content-card {
    padding: 1.5rem;
  }

  .related-card {
    flex-direction: column;
  }

  .related-image {
    width: 100%;
    height: 140px;
  }
}
</style>
