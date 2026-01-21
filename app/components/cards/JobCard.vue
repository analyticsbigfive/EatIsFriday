<template>
  <article class="job-card">
    <div class="card-image">
      <img :src="job.featured_media" :alt="getJobTitle()" loading="lazy" />
      <span class="job-type-badge" :class="getBadgeClass()">{{ job.job_type }}</span>
    </div>
    <div class="card-content">
      <h3 class="job-title">{{ getJobTitle() }}</h3>
      <div class="job-meta">
        <span class="meta-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
            <circle cx="12" cy="10" r="3"></circle>
          </svg>
          {{ getVenueLocation() }}
        </span>
        <span class="meta-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
          </svg>
          {{ job.salary }}
        </span>
      </div>
      <p class="job-excerpt" v-html="getJobExcerpt()"></p>
      <div class="card-actions">
        <NuxtLink :to="`/jobs/${job.slug}`" class="btn-view">View Details</NuxtLink>
        <NuxtLink :to="`/jobs/${job.slug}#apply`" class="btn-apply">Apply</NuxtLink>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
import type { JobWithVenue } from '~/composables/useJobs'

const props = defineProps<{
  job: JobWithVenue
}>()

const getJobTitle = () => {
  return typeof props.job.title === 'string' ? props.job.title : props.job.title?.rendered || ''
}

const getJobExcerpt = () => {
  return typeof props.job.excerpt === 'string' ? props.job.excerpt : props.job.excerpt?.rendered || ''
}

const getVenueLocation = () => {
  return props.job.venue?.location || ''
}

const getBadgeClass = () => {
  const type = props.job.job_type.toLowerCase().replace('-', '').replace(' ', '')
  return type
}
</script>

<style scoped lang="scss">
.job-card {
  background: white;
  border: 2px solid var(--brand-dark, #1A1A1A);
  border-radius: 1rem;
  overflow: hidden;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 6px 6px 0 rgba(0, 0, 0, 1);
  }
}

.card-image {
  position: relative;
  height: 180px;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .job-card:hover & img {
    transform: scale(1.05);
  }
}

.job-type-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.375rem 0.875rem;
  background: var(--brand-dark, #1A1A1A);
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 50px;
  text-transform: uppercase;
  letter-spacing: 0.03em;

  &.fulltime {
    background: var(--brand-pink, #FF4D6D);
  }

  &.parttime {
    background: var(--brand-blue, #A0C4FF);
    color: var(--brand-dark, #1A1A1A);
  }
}

.card-content {
  padding: 1.5rem;
}

.job-title {
  font-family: var(--font-heading, serif);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--brand-dark, #1A1A1A);
  margin: 0 0 1rem;
  line-height: 1.3;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: rgba(0, 0, 0, 0.6);

  svg {
    color: var(--brand-pink, #FF4D6D);
    flex-shrink: 0;
  }
}

.job-excerpt {
  font-size: 0.9375rem;
  color: rgba(0, 0, 0, 0.7);
  line-height: 1.6;
  margin: 0 0 1.25rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;

  :deep(p) {
    margin: 0;
  }
}

.card-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-view,
.btn-apply {
  flex: 1;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-view {
  background: white;
  color: var(--brand-dark, #1A1A1A);
  border: 2px solid var(--brand-dark, #1A1A1A);

  &:hover {
    background: var(--brand-gray, #F5F5F5);
  }
}

.btn-apply {
  background: var(--brand-pink, #FF4D6D);
  color: white;
  border: 2px solid var(--brand-dark, #1A1A1A);

  &:hover {
    box-shadow: 2px 2px 0 rgba(0, 0, 0, 1);
    transform: translate(-2px, -2px);
  }
}
</style>
