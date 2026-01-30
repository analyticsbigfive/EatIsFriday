<template>
  <header v-if="content" class="header">
    <TornPaperContainer variant="white">
      <div class="container header-grid">
        <!-- Left Navigation -->
  <div class="mobile-toggle">
          <LayoutNavigation />
        </div>
        <nav class="nav-right desktop-only">
          <NuxtLink to="/about" class="nav-link" :class="{ 'nav-active': isActiveLink('/about') }">{{ content.nav_links.about }}</NuxtLink>
          <NuxtLink to="/apply-activities" class="nav-link" :class="{ 'nav-active': isActiveLink('/apply-activities') }">{{ content.nav_links.activities }}</NuxtLink>
          <NuxtLink to="/events" class="nav-link" :class="{ 'nav-active': isActiveLink('/events') }">{{ content.nav_links.events }}</NuxtLink>
        </nav>
        <!-- Center Logo -->
        <div class="logo-container">
          <NuxtLink to="/" class="logo">
            <img src="/images/imageLogo.png" alt="Logo" class="logo-image" />
          </NuxtLink>
        </div>

        <!-- Right Navigation -->
        <nav class="nav-left desktop-only">
          <NuxtLink to="/careers" class="nav-link" :class="{ 'nav-active': isActiveLink('/careers') }">{{ content.nav_links.careers }}</NuxtLink>
          <NuxtLink to="/blog" class="nav-link" :class="{ 'nav-active': isActiveLink('/blog') }">{{ content.nav_links.blogs }}</NuxtLink>
          <NuxtLink to="/contact" class="contact-button-wrapper">
            <NuxtImg
              :src="btnGetInTouch"
              alt="Get in touch"
              class="contact-image"
            />
          </NuxtLink>
        </nav>

        <!-- Mobile Menu Toggle -->
      <NuxtLink to="/contact" class="contact-button-wrapper mobile-only">
            <NuxtImg
              :src="btnGetInTouch"
              alt="Get in touch"
              class="contact-image"
            />
          </NuxtLink>
      </div>
    </TornPaperContainer>
  </header>
</template>

<script setup lang="ts">
const route = useRoute()
const { getHeaderContent } = usePageContent()
const { settings } = useGlobalSettings()
const content = ref<any>(null)

// Dynamic button URL with fallback
const btnGetInTouch = computed(() => settings.value?.icons?.btn_get_in_touch || '/images/btnGetInTouch.svg')

// Check if a link is active
const isActiveLink = (linkTo: string) => {
  if (linkTo === '/') {
    return route.path === '/'
  }
  return route.path.startsWith(linkTo)
}

onMounted(async () => {
  content.value = await getHeaderContent()
})
</script>

<style scoped lang="scss">
.header {
  padding: 0;
  position: fixed;
  top: 0;
  z-index: 1000;
  left: 0;
  right: 0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.header-grid {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  height: 103px;
  background: url(/images/headerBg.png) no-repeat center;
  background-size: cover;
  max-width: 1400px;
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
  color: #f9375b !important;
}

.nav-active {
  color: #f9375b !important;
  font-weight: 600;
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
  display: flex;
  align-items: center;
}

.logo-image {
  height: 80px;
  width: auto;
  object-fit: contain;
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
