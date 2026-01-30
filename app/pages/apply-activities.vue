<template>
  <div class="apply-activities-page">
    <section v-if="content?.page_hero" id="future" class="page-hero">
      <div class="container-fluid p-0 mt-0 text-center">
        <h1 class="mt-4 mb-4">{{ content.page_hero.title }}</h1>
        <img v-if="content.page_hero.image?.src" :src="content.page_hero.image.src" :alt="content.page_hero.image.alt">
      </div>
    </section>
    <section v-if="content?.section2" id="mouf" class="booba mb-4">
      <div id="denzel" class="container d-flex flex-column justify-content-center align-items-center">
        <h1 class="preserve-lines text-center" v-html="content.section2.text"></h1>
        <div id="rohff" class="d-flex justify-content-center align-items-center text-center gap-4 flex-wrap">
          <NuxtLink v-if="content.section2.btn1" :to="content.section2.link1 || '#'">
            <img :src="content.section2.btn1" alt="Button 1">
          </NuxtLink>
          <NuxtLink v-if="content.section2.btn2" :to="content.section2.link2 || '#'">
            <img :src="content.section2.btn2" alt="Button 2">
          </NuxtLink>
        </div>
      </div>
    </section>
    <section v-if="activities" class="explore-section mt-4">
      <HomeExploreSection />
    </section>
  
    <section class="mb-5" v-if="activities && content?.textedelapage">
      <div id="baks" class="row container-fluid p-0 m-0 d-flex align-items-center">
          <h3 class="font-header">{{ content.textedelapage.title }}</h3>

        <div id="ludacris" class="col-6 p-5">
          <p v-html="content.textedelapage.subtitle"></p>
          <p v-html="content.textedelapage.description"></p>
           <NuxtLink :to="content.textedelapage.link">
            <img :src="content.textedelapage.btn">
          </NuxtLink>
        </div>
        <div class="col-6 p-0 m-0">
          <img :src="content.textedelapage.image" alt="Services illustration" class="img-fluid">
        </div>
      </div>
    </section>
<section v-if="content?.weHelpWith" id="weHelpwith">
  <img :src="content.weHelpWith.image" alt="" class="img-fluid">
  <div id="seeAboutYa">
    <h3>{{ content.weHelpWith.title }}</h3>
    <ul>
      <li v-for="(item, index) in content.weHelpWith.items" :key="index">{{ item }}</li> 
    </ul>
    <p>{{ content.weHelpWith.sous }}</p>
  </div>
</section>
    <section class="mt-4">
      <!-- parteners  -->
      <PartnersSection v-if="homepageContent" :title="homepageContent.partners_title"
        :partners="(homepageContent.partners || []).map((p: any) => ({ ...p, name: p.alt }))" />
      <!-- gallery grid -->
      <GalleryGrid v-if="siteContent?.about?.gallery_section2?.images"
        :images="siteContent.about.gallery_section2.images" />
      <!--  ready to make an impact -->
      <HomepageCTA v-if="homepageContent?.homepageCTA" :image="homepageContent.homepageCTA.image" :title="homepageContent.homepageCTA.title"
        :description="homepageContent.homepageCTA.description" :link="homepageContent.homepageCTA.link"
        :button-image="homepageContent.homepageCTA.button" :additional-text="homepageContent.homepageCTA.additionalText"
        :button-image2="homepageContent.homepageCTA.button2" />
            <GalleryGrid v-if="siteContent?.about?.gallery_section?.images"
      :images="siteContent.about.gallery_section.images" />
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
const { getSiteContent } = useSiteContent()

const { getActivities } = useActivities()
const { getContentByPath, getHomepageContent } = usePageContent()
const homepageContent = ref<any>(null)
const siteContent = ref<any>(null)
const activities = ref<any>(null)
const content = ref<any>(null)

onMounted(async () => {
  content.value = await getContentByPath('apply_activities')
  console.log('Content loaded:', content.value)
  console.log('weHelpWith exists:', content.value?.weHelpWith)
  activities.value = await getActivities()
  homepageContent.value = await getHomepageContent()
  siteContent.value = await getSiteContent()
})

useHead(() => ({
  title: content.value?.seo?.title || 'Activities - Eat Is Family',
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

#future.page-hero {
  background: white !important;
  text-align: center;
  margin: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  align-items: center;
  padding: 117px 0 0 0;
  height: 100%;

  ::before {
    content: "";
    display: block;
    background-image: url("/images/bgVector.svg");
    background-size: contain;
    width: 100%;
    height: 300px;
    background-repeat: no-repeat;
    top: 10pc;
    position: absolute;
  }

  h1 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 75px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.2;
    letter-spacing: normal;
    text-align: center;
    color: #000b0f;
    z-index: 1;
    white-space: pre-line;
    /* To handle newline characters */
    position: relative;

    &::before {
      display: none !important;
    }

    &::after {

      content: "";
      display: block;
      background-image: url(/images/line5.svg);
      background-size: contain;
      width: 450px;
      height: 100px;
      background-repeat: no-repeat;
      position: absolute;
      z-index: -1;
      margin: auto;
      right: 0;
      left: 0;
      top: 0;
    }

  }
}

#mouf {
  background: url("/images/unsplash_6vfYbDwOuMo.svg");
  background-size: cover;
  padding: 4em 0 !important;
  background-repeat: no-repeat;
  position: relative;
  min-height: 66vh;
  margin-top:-10px;
margin-bottom:4em !important;
  &:after {
    background: url("/images/concession.svg");
    content: "";
    display: block;
    position: absolute;
    width: 100%;
    right: 0;
    bottom: 0;
    width: 221px;
    height: 195px;
  }

  h1 {
    font-family: FONTSPRINGDEMO-RecoletaMedium;
    font-size: 34px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.47;
    letter-spacing: normal;
    text-align: center;
    color: #000;
    max-width: 1066px;
    padding: 60px auto;
  }
}

#rohff {
  margin-top: 15px;
  padding: 20px 0;
  
  a {
    display: inline-block;
    min-width: 150px;
    
    img {
      display: block;
      max-width: 100%;
      height: auto;
      min-height: 70px;
    }
  }
}

</style>
