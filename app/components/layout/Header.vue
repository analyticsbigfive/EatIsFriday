<template>
  <header v-if="content" class="header">
    <TornPaperContainer variant="white">
      <div class="container header-grid">
        <!-- Left Navigation -->

        <nav class="nav-right desktop-only">
          <NuxtLink to="/about" class="nav-link">{{ content.nav_links.about }}</NuxtLink>
          <NuxtLink to="/apply-activities" class="nav-link">{{ content.nav_links.activities }}</NuxtLink>
          <NuxtLink to="/events" class="nav-link">{{ content.nav_links.events }}</NuxtLink>
        </nav>
        <!-- Center Logo -->
        <div class="logo-container">
          <NuxtLink to="/" class="logo">
            {{ content.logo }}
          </NuxtLink>
        </div>

        <!-- Right Navigation -->
        <nav class="nav-left desktop-only">
          <NuxtLink to="/careers" class="nav-link">{{ content.nav_links.careers }}</NuxtLink>
          <NuxtLink to="/blog" class="nav-link">{{ content.nav_links.blogs }}</NuxtLink>
          <div class="contact-button-wrapper" @click="openContactModal">
            <!-- <span class="contact-label">Get in touch</span> -->
            <NuxtImg
              src="/images/btnGetInTouch.svg"
              alt="Get in touch"
              class="contact-image"
            />
          </div>
        </nav>

        <!-- Mobile Menu Toggle -->
        <div class="mobile-toggle">
          <LayoutNavigation @open-contact="openContactModal" />
        </div>
      </div>
    </TornPaperContainer>

    <LayoutContactModal :is-open="isContactModalOpen" @close="closeContactModal" />
  </header>
</template>

<script setup lang="ts">
const { getHeaderContent } = usePageContent()
const content = ref<any>(null)
const isContactModalOpen = ref(false)

onMounted(async () => {
  content.value = await getHeaderContent()
})

const openContactModal = () => {
  isContactModalOpen.value = true
}

const closeContactModal = () => {
  isContactModalOpen.value = false
}
</script>

<style scoped lang="scss">
.header {
  padding: 0;
  position: absolute;
  top: 0;
  z-index: 1000;
  left:0;
  right:0;
}

.header-grid {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  height:103px;
  background:url(/images/headerBg.png) no-repeat center;
  background-size: contain;
    a{
  font-family: FONTSPRINGDEMO-RecoletaMedium;
      font-size: 18px;
      font-weight: 500;
      font-stretch: normal;
      font-style: normal;
      line-height: normal;
      letter-spacing: normal;
      text-align: left;
      color: #0d0a00;
  }
}

.nav-left {
  display: flex;
  align-items: center;
  gap: 2rem;
  justify-content: flex-end;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 2rem;
  justify-content: flex-start;
}

.nav-link {
  font-weight: 500;
  color: var(--color-text-dark);
  transition: color 0.2s ease;
  font-family: var(--font-body);
}

.nav-link:hover {
  color: var(--color-primary-blue);
}

.contact-button-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.contact-button-wrapper:hover {
  transform: scale(1.05);
  opacity: 0.8;
}

.contact-label {
  font-weight: 500;
  color: var(--color-text-dark);
  font-family: var(--font-body);
  font-size: 0.875rem;
}

.contact-image {
  height: 68px;
}

.logo {
  font-family: var(--font-heading);
  font-size: 2rem;
  font-weight: 900;
  color: var(--color-text-dark);
  text-decoration: none;
  letter-spacing: -1px;
}

.mobile-toggle {
  display: none;
}
.contact-button-wrapper{
  position: relative;
  display: flex;align-items:center;justify-content: center;flex-direction: column;
  span{
  font-family: FONTSPRINGDEMO-RecoletaMedium;
      font-size: 18px;
      font-weight: 600;
      color:white;
      position:absolute;
      
  }
}
@media (max-width: 968px) {
  .header-grid {
    display: flex;
    justify-content: space-between;
  }

  .desktop-only {
    display: none;
  }

  .mobile-toggle {
    display: block;
  }
}
</style>
