<template>
  <div class="apply-jobs-page">
    <!-- Loading Screen -->
    <LoadingScreen v-if="!content" />
    
    <template v-else>
    <section v-if="content" class="page-hero">
      <div class="container">
        <h1>{{ content.page_hero.title }}</h1>
        <p>{{ content.page_hero.subtitle }}</p>
      </div>
    </section>

    <section class="page-content">
      <div class="container">
        <div v-if="jobs" class="grid grid-2">
          <JobCard v-for="job in jobs" :key="job.id" :job="job" />
        </div>
        <div v-else-if="content" class="loading">{{ content.loading_text }}</div>
      </div>
    </section>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const { getJobs } = useJobs()
const { getContentByPath } = usePageContent()

const jobs = ref<any>(null)
const content = ref<any>(null)

onMounted(async () => {
  content.value = await getContentByPath('apply_jobs')
  jobs.value = await getJobs()
})

// Helper pour valider si un titre SEO est valide (pas une URL)
const isValidSeoTitle = (title: string | undefined) => {
  if (!title) return false
  return !title.includes('http') && !title.includes('wp-content') && !title.includes('themes')
}

useHead(() => ({
  title: isValidSeoTitle(content.value?.seo?.title) ? content.value.seo.title : 'Job Opportunities - Eat Is Family',
  meta: [
    { name: 'description', content: content.value?.seo?.meta_description || '' }
  ]
}))
</script>

<style scoped lang="scss">
.loading {
  text-align: center;
  padding: 4rem 0;
  font-size: 1.25rem;
  color: #718096;
}
</style>
