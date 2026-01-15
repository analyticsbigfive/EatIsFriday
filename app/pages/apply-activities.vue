<template>
  <div class="apply-activities-page">
    <section v-if="content" id="future" class="page-hero">
      <div class="container-fluid p-0 mt-0 text-center">
        <h1 class="mt-4 mb-4">{{ content.page_hero.title }}</h1>
        <img :src="content.page_hero.image.src" :alt="content.page_hero.image.alt">
      </div>
    </section>
<section v-if="content" id="mouf" class="booba">
  <div id="denzel" class="container">
    <h1 class="preserve-lines">{{ content.section2.text }}</h1>
    <div id="rohff" class="d-flex justify-content-center align-items-center text-center gap-4 flex-wrap">
    <nuxtlink :to="content.section2.link1">
      <img :src="content.section2.btn1" >
    </nuxtlink>
     <nuxtlink :to="content.section2.link2">
      <img :src="content.section2.btn2" >
    </nuxtlink>
    </div>
  </div>
</section>
<section>  <HomeExploreSection />
</section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const { getActivities } = useActivities()
const { getContentByPath } = usePageContent()

const activities = ref<any>(null)
const content = ref<any>(null)

onMounted(async () => {
  content.value = await getContentByPath('apply_activities')
  activities.value = await getActivities()
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
    white-space: pre-line; /* To handle newline characters */
    position:relative;
    &::before{
      display:none !important;
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
  &:after{
    background:url("/images/concession.svg");
    content:"";
    display:block;
    position:absolute;
    width:100%;
    right:0;
    bottom:0;
    width:221px;
    height:195px;
  }
  h1{
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
  padding:60px auto;
  }
}
#rohff{
  margin-top: 15px;
  padding:20px 0;
}
</style>
