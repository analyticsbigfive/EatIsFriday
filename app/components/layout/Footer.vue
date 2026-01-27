<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-section">
          <div v-if="content" class="titlewrapper d-flex align-items-center justify-content-start mb-4">
            <img v-if="content.logoFooter" :src="content.logoFooter" alt="Logo" />
            <h3 class="mb-0">{{ content.brand_name }}</h3>
          </div>
          <p v-if="content">{{ content.brand_description }}</p>
          <p v-if="content">{{ content.contact_email }}</p>
          <p v-if="content">{{ content.contact_phone }}</p>
          <ul class="d-flex flex-row gap-2">
            <li v-for="(item, index) in content?.social_links || []" :key="index" class="social-links">
              <a :href="item.url" target="_blank" rel="noopener noreferrer">
                <img v-if="item.icon" :src="item.icon" :alt="item.name" width="40" height="40" />
              </a>
            </li>
          </ul>
        </div>
        <div class="footer-section">
          <h3 class="mb-4" v-if="content">{{ content.company_title }}</h3>
          <ul>
            <li v-for="(item, index) in content?.company_links || []" :key="index">
              <NuxtLink :to="item.to">{{ item.text }}</NuxtLink>
            </li>
          </ul>
        </div>
        <div class="footer-section">
          <h3 class="mb-4"
          v-if="content">{{ content.policy_title }}</h3>
          <ul>
            <li v-for="(item, index) in content?.policy_links || []" :key="index">
              <NuxtLink :to="item.to">{{ item.text }}</NuxtLink>
            </li>
          </ul>
        </div>
      </div>
      <div class="cpyright">
        <div v-if="content">{{ content.copyright_template }}</div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const { getFooterContent } = usePageContent()
const content = ref<any>(null)

onMounted(async () => {
  content.value = await getFooterContent()
})
</script>

<style scoped lang="scss">
.footer {
  background: black;
  color: white;
  padding: 3rem 0 1rem;
  margin-top: 4rem;
  li{
    
  font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.5;
  letter-spacing: normal;
  text-align: left;
  color: #fff !important;
  a{
    color: #fff !important;
  }
 }
 h3 {
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #fff;
}
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}



.footer-section h4 {
  font-size: 1.125rem;
  margin-bottom: 1rem;
  color: #a0aec0;
}

.footer-section p {
  font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 16px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.5;
  letter-spacing: normal;
  text-align: left;
  color: #fff;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section li {
  margin-bottom: 0.5rem;
  color: #cbd5e0;
}

.footer-section a {
  color: #cbd5e0;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section a:hover {
  color: #667eea;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 1.5rem;
  text-align: center;
  color: #a0aec0;
}

@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr;
  }
}
.cpyright {
  padding-top: 1rem;
  border-top:1px solid rgba(255, 255, 255, 1);
  text-align: center;
  font-family: FONTSPRINGDEMO-Recoleta;
  font-size: 14px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.43;
  letter-spacing: normal;
  text-align: center;
  color: #fff;
}
</style>
