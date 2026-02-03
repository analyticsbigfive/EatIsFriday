<template>
  <div class="apply-activities-page">
    <!-- Loading Screen -->
    <LoadingScreen v-if="!content" />
    
    <template v-else>
    <section v-if="content?.page_hero" id="future" class="page-hero">
      <div class="container-fluid p-0 mt-0 text-center">
        <h1 class="mt-4 mb-4">{{ content.page_hero.title }}</h1>
        <img
          v-if="content.page_hero.image?.src"
          :src="content.page_hero.image.src"
          :alt="content.page_hero.image.alt"
          class="neil"
        />
      </div>
    </section>
    <section v-if="content?.section2" id="mouf" class="booba mb-4">
      <div
        id="denzel"
        class="container d-flex flex-column justify-content-center align-items-center"
      >
        <h1
          class="preserve-lines text-center"
          v-html="content.section2.text"
        ></h1>
        <div
          id="rohff"
          class="d-flex justify-content-center align-items-center text-center gap-4 flex-wrap"
        >
          <NuxtLink
            v-if="content.section2.btn1"
            :to="content.section2.link1 || '#'"
          >
            <img :src="content.section2.btn1" alt="Button 1" />
          </NuxtLink>
          <NuxtLink
            v-if="content.section2.btn2"
            :to="content.section2.link2 || '#'"
          >
            <img :src="content.section2.btn2" alt="Button 2" />
          </NuxtLink>
        </div>
      </div>
    </section>
    <section v-if="activities" class="explore-section mt-4">
      <HomeExploreSection />
    </section>

    <section class="mb-5" v-if="activities && content?.textedelapage">
      <div id="baks" class="container p-0 mx-auto d-flex flex-column">
        <h3 class="font-header">{{ content.textedelapage.title }}</h3>
        <div id="viber" class="row  d-flex flex-wrap">
          <div id="ludacris" class="col-12 col-lg-6 col-md-6 pt-0 pb-0">
            <p v-html="content.textedelapage.subtitle"></p>
            <p v-html="content.textedelapage.description"></p>
            <NuxtLink :to="content.textedelapage.link">
              <img :src="content.textedelapage.btn" />
            </NuxtLink>
          </div>
          <div class="col-12 col-lg-6 col-md-6  p-0 m-0">
            <img
              :src="content.textedelapage.image"
              alt="Services illustration"
              class="img-fluid rounded"
            />
          </div>
        </div>
      </div>
    </section>
    <section
      v-if="content?.weHelpWith"
      id="weHelpwith"
      class="container mx-auto mb-5"
      :style="{ '--bg-image': `url(${content.weHelpWith.image})` }"
    >
      <img :src="content.weHelpWith.image" alt="" class="img-fluid weHelpWith-image" />
      <div id="seeAboutYa">
        <h3>{{ content.weHelpWith.title }}</h3>
        <ul>
          <li
            v-for="(item, index) in content.weHelpWith.items"
            :key="index"
            v-html="item"
          ></li>
        </ul>
        <p>{{ content.weHelpWith.sous }}</p>
      </div>
    </section>
    <section class="mt-4">
      <!-- parteners  -->
      <PartnersSection
        v-if="homepageContent"
        :title="homepageContent.partners_title"
        :partners="
          (homepageContent.partners || []).map((p: any) => ({
            ...p,
            name: p.alt,
          }))
        "
      />
      <!-- gallery grid 1 - Activities specific -->
      <GalleryGrid
        v-if="activitiesGalleryImages.length > 0"
        :images="activitiesGalleryImages"
      />
      <!--  ready to make an impact -->
     <!--  <HomepageCTA
        v-if="homepageContent?.homepageCTA"
        :image="homepageContent.homepageCTA.image"
        :title="homepageContent.homepageCTA.title"
        :description="homepageContent.homepageCTA.description"
        :link="homepageContent.homepageCTA.link"
        :button-image="homepageContent.homepageCTA.button"
        :additional-text="homepageContent.homepageCTA.additionalText"
        :button-image2="homepageContent.homepageCTA.button2"
      /> -->
    <section v-if="siteContent?.about?.vision" id="vision" class="my-5">
      <div id="inver" class="container-fluid">
        <h3 class="font-header text-align-center text-center d-flex justify-content-center align-items-center"> Le souci du détail fait la différence </h3>
        <!-- Consulting section from pages-content -->
        <!-- <div v-if="pageContent?.consulting" class="consulting-section text-center p-4 cta-block d-flex flex-column justify-content-center align-items-center text-center p-4 mx-auto mt-4"> 
          <p v-html="pageContent.consulting.description" class="preserve-lines mb-4"></p>
          <NuxtLink 
            v-if="pageContent.consulting.cta?.link" 
            :to="pageContent.consulting.cta.link"
            class="mof"
          >
            <img 
              v-if="pageContent.consulting.cta?.button" 
              :src="pageContent.consulting.cta.button" 
              :alt="pageContent.consulting.cta?.text || 'Nous contacter'"
              class="img-fluid"
            />
            <span v-else>{{ pageContent.consulting.cta?.text || 'Nous contacter' }}</span>
          </NuxtLink>
        </div> -->
             <HomepageCTA
      v-if="homepageContent?.homepageCTA"
      :image="homepageContent.homepageCTA.image"
      :title="homepageContent.homepageCTA.title"
      :description="homepageContent.homepageCTA.description"
      :link="homepageContent.homepageCTA.link"
      :button-image="homepageContent.homepageCTA.button"
      :additional-text="homepageContent.homepageCTA.additionalText"
      :button-image2="homepageContent.homepageCTA.button2"
    />
      </div>
    </section>
   
      <!-- gallery grid 2 - Activities specific -->
     <!--  <GalleryGrid
        v-if="activitiesGalleryImages2.length > 0"
        :images="activitiesGalleryImages2"
      /> -->
    </section>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
const { getSiteContent } = useSiteContent();

const { getActivities } = useActivities();
const { getContentByPath, getHomepageContent } = usePageContent();
const homepageContent = ref<any>(null);
const siteContent = ref<any>(null);
const activities = ref<any>(null);
const content = ref<any>(null);
const pageContent = ref<any>(null);

// Computed property for activities gallery 1 with fallback to about galleries
const activitiesGalleryImages = computed(() => {
  return (
    siteContent.value?.about?.activities_gallery?.images ||
    siteContent.value?.about?.gallery_section2?.images ||
    []
  );
});

// Computed property for activities gallery 2 with fallback
const activitiesGalleryImages2 = computed(() => {
  return (
    siteContent.value?.about?.activities_gallery_2?.images ||
    siteContent.value?.about?.gallery_section?.images ||
    []
  );
});

onMounted(async () => {
  content.value = await getContentByPath("apply_activities");
  pageContent.value = await getContentByPath("about");
  console.log("Content loaded:", content.value);
  console.log("weHelpWith exists:", content.value?.weHelpWith);
  console.log("pageContent consulting:", pageContent.value?.consulting);
  activities.value = await getActivities();
  homepageContent.value = await getHomepageContent();
  siteContent.value = await getSiteContent();
});

// Helper pour valider si un titre SEO est valide (pas une URL)
const isValidSeoTitle = (title: string | undefined) => {
  if (!title) return false
  return !title.includes('http') && !title.includes('wp-content') && !title.includes('themes')
}

useHead(() => ({
  title: isValidSeoTitle(content.value?.seo?.title) ? content.value.seo.title : "Activities - Eat Is Family",
  meta: [
    {
      name: "description",
      content: content.value?.seo?.meta_description || "",
    },
  ],
}));
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
#viber{
  margin-top:4em;
}
#mouf {
  background: url("/images/unsplash_6vfYbDwOuMo.svg");
  background-size: cover;
  padding: 4em 0 !important;
  background-repeat: no-repeat;
  position: relative;
  min-height: 66vh;
  margin-top: -10px;
  margin-bottom: 4em !important;
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
.neil {
  max-width: 100%;
  height: auto;
  max-height: 80vh;
  margin-bottom: 40px;
  object-fit: cover;
}

#baks{
  h3{
    max-width:800px;
  }
}

/* Section weHelpWith - Mobile background image */
#weHelpwith {
  position: relative;
  
  .weHelpWith-image {
    display: block;
  }
}

@media (max-width: 1024px) {
  #weHelpwith {
      background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 400px 20px 40px 20px;
    border-radius: 12px;
    position: relative;
    /* Overlay pour améliorer la lisibilité du texte */
  /*   &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.85);
      border-radius: 12px;
      z-index: 0;
    } */
    
    /* Cacher l'image sur mobile */
    .weHelpWith-image {
      display: none !important;
    }
    
    /* S'assurer que le contenu est au-dessus de l'overlay */
    #seeAboutYa {
      position: relative;
      z-index: 1;
    }
  }
}

</style>
