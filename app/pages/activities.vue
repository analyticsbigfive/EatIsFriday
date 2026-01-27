<template>
  <div class="activities-page">
    <!-- Hero Section -->
    <section v-if="content?.page_hero" class="hero-section" :style="{ backgroundImage: `url('${content.page_hero.image.src}')` }">
      <div class="hero-overlay">
        <div class="container">
          <h1 class="font-heading display-3 fw-bold text-white preserve-lines">{{ content.page_hero.title }}</h1>
        </div>
      </div>
    </section>

    <!-- Section 2 - Introduction -->
    <section v-if="content?.section2" class="py-5 bg-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-8 mx-auto text-center">
            <p class="lead preserve-lines mb-4">{{ content.section2.text }}</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
              <NuxtLink :to="content.section2.link1">
                <NuxtImg :src="content.section2.btn1" alt="Apply for Jobs" />
              </NuxtLink>
              <NuxtLink :to="content.section2.link2">
                <NuxtImg :src="content.section2.btn2" alt="Explore Map" />
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 3 - We Help With -->
    <section v-if="content?.section3" class="py-5 bg-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <h2 class="font-heading display-5 fw-bold mb-4">{{ content.section3.title }}</h2>
            <ul class="help-list">
              <li v-for="(item, index) in content.section3.liste" :key="index">
                <span class="check-icon">✓</span>
                {{ item }}
              </li>
            </ul>
            <p class="mt-4 text-muted fst-italic">{{ content.section3.sous }}</p>
          </div>
          <div class="col-lg-6">
            <NuxtImg :src="content.section3.image" alt="Our Services" class="img-fluid rounded shadow" />
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content Section -->
    <section v-if="content?.textedelapage" class="py-5 bg-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <NuxtImg :src="content.textedelapage.image" alt="Consulting" class="img-fluid rounded shadow" />
          </div>
          <div class="col-lg-6">
            <h2 class="font-heading display-5 fw-bold mb-3">{{ content.textedelapage.title }}</h2>
            <p class="lead text-muted mb-4">{{ content.textedelapage.subtitle }}</p>
            <p class="preserve-lines mb-4">{{ content.textedelapage.description }}</p>
            <NuxtLink :to="content.textedelapage.link">
              <NuxtImg :src="content.textedelapage.btn" alt="Contact Us" />
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-brand-dark text-white">
      <div class="container text-center">
        <h2 class="font-heading display-5 fw-bold mb-4">Ready to Transform Your Operations?</h2>
        <p class="lead mb-4">Let's discuss how we can help optimize your food service business.</p>
        <NuxtLink to="/contact" class="btn btn-light btn-lg px-5">Get in Touch</NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const { getPageContent } = usePageContent()
const content = ref<any>(null)

onMounted(async () => {
  const pageContent = await getPageContent()
  content.value = pageContent?.apply_activities
})

useHead({
  title: 'Activities & Consulting Services - Eat Is Family',
  meta: [
    { name: 'description', content: 'Discover our consulting services for restaurants, event venues, and food businesses. We help optimize workflows, scale operations, and launch new concepts.' }
  ]
})
</script>

<style scoped lang="scss">
.activities-page {
  .hero-section {
    min-height: 50vh;
    background-size: cover;
    background-position: center;
    position: relative;

    .hero-overlay {
      background: rgba(0, 0, 0, 0.5);
      min-height: 50vh;
      display: flex;
      align-items: center;
      padding: 4rem 0;
    }
  }

  .preserve-lines {
    white-space: pre-line;
  }

  .help-list {
    list-style: none;
    padding: 0;

    li {
      padding: 0.75rem 0;
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      font-size: 1.1rem;

      .check-icon {
        color: #4CAF50;
        font-weight: bold;
        flex-shrink: 0;
      }
    }
  }

  .bg-brand-dark {
    background-color: #1a1a1a;
  }
}
</style>
