<script setup lang="ts">
import { computed } from 'vue'

const { getBlogPosts } = useBlog()

// Charger les articles depuis le serveur
const { data: posts } = await useAsyncData('blog-posts', () => getBlogPosts())

// Les 2 premiers posts sont "featured", les autres dans la grille
const featuredPosts = computed(() => posts.value?.slice(0, 2) || [])
const allPosts = computed(() => posts.value?.slice(2) || [])
</script>

<template>
  <div class="blog-page">
    <!-- Hero Section -->
    <section class="blog-hero">
      <div class="container">
        <h1 class="hero-title">
          <span class="title-black">Insightful Stories From<br>The Kitchen</span>
        </h1>
      </div>
    </section>

    <!-- Most Recent Insights -->
    <section class="recent-insights">
      <div class="container">
        <h2 class="section-title">Most Recent Insights</h2>

        <!-- Featured Post 1 - Image Left -->
        <article v-if="featuredPosts[0]" class="featured-post">
          <div class="post-image">
            <NuxtLink :to="`/blog/${featuredPosts[0].slug}`">
              <img :src="featuredPosts[0].featured_media" :alt="featuredPosts[0].title.rendered" />
            </NuxtLink>
          </div>
          <div class="post-content d-flex align-items-center flex-wrap flex-row">
            <h3 class="post-title">
              <NuxtLink :to="`/blog/${featuredPosts[0].slug}`">
                {{ featuredPosts[0].title.rendered }}
              </NuxtLink>
            </h3>
            <p class="post-excerpt">{{ featuredPosts[0].excerpt.rendered }}</p>
            <NuxtLink :to="`/blog/${featuredPosts[0].slug}`" class="bg-transparent border-0 p-0 m-0">
              <NuxtImg src="/images/btnReadMore.svg" alt="Read more" width="247"/>
            </NuxtLink>
          </div>
        </article>

        <!-- Featured Post 2 - Image Right -->
        <!--<article v-if="featuredPosts[1]" class="featured-post reverse">-->
        <article v-if="featuredPosts[1]" class="featured-post second">
          <div class="post-image">
            <NuxtLink :to="`/blog/${featuredPosts[1].slug}`">
              <img :src="featuredPosts[1].featured_media" :alt="featuredPosts[1].title.rendered" />
            </NuxtLink>
          </div>
          <div class="post-content d-flex align-items-center flex-wrap flex-row">
            <h3 class="post-title">
              <NuxtLink :to="`/blog/${featuredPosts[1].slug}`">
                {{ featuredPosts[1].title.rendered }}
              </NuxtLink>
            </h3>
            <p class="post-excerpt">{{ featuredPosts[1].excerpt.rendered }}</p>
            <NuxtLink :to="`/blog/${featuredPosts[1].slug}`" class="bg-transparent border-0 p-0 m-0">
              <NuxtImg src="/images/btnReadMore.svg" alt="Read more" width="247"/>
            </NuxtLink>
          </div>
        </article>
      </div>
    </section>

    <!-- Explore All Insights -->
    <section class="all-insights">
      <div class="container">
        <h2 class="section-title">Explore All Insight</h2>

        <div class="posts-grid">
          <article v-for="post in allPosts" :key="post.id" class="post-card">
            <div class="card-image">
              <NuxtLink :to="`/blog/${post.slug}`">
                <img :src="post.featured_media" :alt="post.title.rendered" />
              </NuxtLink>
            </div>
            <div class="card-content">
              <h3 class="card-title">
                <NuxtLink :to="`/blog/${post.slug}`">
                  {{ post.title.rendered }}
                </NuxtLink>
              </h3>
              <p class="card-excerpt">{{ post.excerpt.rendered }}</p>
              <NuxtLink :to="`/blog/${post.slug}`" class="read-more-btn">
                Read more
              </NuxtLink>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.blog-page {
  padding-top:8em;
  min-height: 100vh;
  background: #fff;
}

// Hero Section
.blog-hero {
      background: url(/images/bBlog.svg);
    background-repeat: no-repeat;
    background-size: cover;
    max-width: 1400px;
    max-height: 405px;
    margin: 0 auto;
    height: 100vh;
    display: flex;
    align-items: center;
}

.hero-decoration.pink-blob {
  position: absolute;
  top: 0;
  left: 0;
  width: 300px;
  height: 100%;
  background: #FF6B9D;
  border-radius: 0 0 150px 0;
}

.hero-title {
  position: relative;
  z-index: 1;
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 700;
  line-height: 1.15;
  margin: 0;
  padding-left: 2rem;
}

.title-white {
  display: block;
  color: #fff;
}

.title-highlight {
  display: block;
  color: #1a1a1a;
  position: relative;

  &::before {
    content: '';
    position: absolute;
    bottom: 0.1em;
    left: 0;
    width: 100%;
    height: 0.3em;
    background: #FFDD00;
    z-index: -1;
  }
}

.title-black {
  display: block;
  color: #1a1a1a;
}

// Section Title
.section-title {
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 2.5rem;
}

// Recent Insights Section
.recent-insights {
  padding: 4rem 0;
  background: #fff;
}

.featured-post {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  .post-content{
       width: 100%;
    height: 100%;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    padding: 2em;
  }
  &:nth-of-type(1){
    .post-content{
    background:url(/images/bgFeatured1.svg);  
    }
  }
  &:nth-of-type(2){
    .post-content{
    background:url(/images/bgFeatured.svg);
      
    }
  
  }
  
  &:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }

  &.reverse {
    .post-image {
      order: 2;
    }
    .post-content {
      order: 1;
    }
  }
}

.post-image {
  border-radius: 12px;
  overflow: hidden;

  img {
    width: 100%;
    height: 390px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  &:hover img {
    transform: scale(1.03);
  }
}

.post-content {
  padding: 1rem 0;
}

.post-title {
  font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 34px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #000b0f;

  a {
    font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 34px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  text-decoration:none;
  color: #000b0f;

    &:hover {
      color: #FF4D6D;
    }
  }
}

.post-excerpt {
   font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.56;
  letter-spacing: normal;
  text-align: left;
  color: #000;
}

.read-more-btn {
  display: inline-block;
  padding: 0.75rem 1.75rem;
  background: #fff;
  color: #1a1a1a;
  font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif);
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  border: 2px solid #1a1a1a;
  border-radius: 50px;
  transition: all 0.2s ease;

  &:hover {
    background: #1a1a1a;
    color: #fff;
  }
}

// All Insights Section
.all-insights {
  padding: 4rem 0;
  background: #fff;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2.5rem;
}

.post-card {
  display: flex;
  flex-direction: column;
}

.card-image {
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1.25rem;

  img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  &:hover img {
    transform: scale(1.03);
  }
}

.card-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-title {
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: 1.25rem;
  font-weight: 700;
  line-height: 1.3;
  margin: 0 0 0.75rem;

  a {
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.2s ease;

    &:hover {
      color: #FF4D6D;
    }
  }
}

.card-excerpt {
  font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif);
  font-size: 0.875rem;
  color: #666;
  line-height: 1.6;
  margin: 0 0 1.25rem;
  flex: 1;

  // Clamp to 3 lines
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

// Responsive
@media (max-width: 992px) {
  .featured-post {
    grid-template-columns: 1fr;
    gap: 1.5rem;

    &.reverse {
      .post-image {
        order: 1;
      }
      .post-content {
        order: 2;
      }
    }
  }

  .post-image img {
    height: 250px;
  }
}

@media (max-width: 768px) {
  .blog-hero {
    padding: 3rem 0;
  }

  .hero-decoration.pink-blob {
    width: 150px;
  }

  .hero-title {
    font-size: 2rem;
    padding-left: 1rem;
  }

  .posts-grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .hero-decoration.pink-blob {
    width: 100px;
  }

  .post-image img,
  .card-image img {
    height: 200px;
  }
}
</style>
