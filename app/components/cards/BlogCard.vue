<template>
  <NuxtLink :to="`/blog/${post.slug}`" class="blog-card">
    <div class="card-image">
      <img :src="post.featured_media" :alt="post.title.rendered" loading="lazy" />
    </div>
    <div class="card-content">
      <div class="post-meta">
        <span v-if="post.author" class="author">
          <img :src="post.author.avatar" :alt="post.author.name" class="author-avatar" />
          {{ post.author.name }}
        </span>
        <span class="date">{{ formatDate(post.date) }}</span>
        <span v-if="post.reading_time" class="reading-time">{{ post.reading_time }}</span>
      </div>
      <h3>{{ post.title.rendered }}</h3>
      <div class="excerpt" v-html="post.excerpt.rendered"></div>
      <div v-if="post.categories && post.categories.length > 0" class="categories">
        <span v-for="category in post.categories" :key="category.id" class="category-tag">
          {{ category.name }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { BlogPost } from '~/composables/useBlog'

defineProps<{
  post: BlogPost
}>()

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<style scoped>
.blog-card {
  display: block;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  text-decoration: none;
  color: inherit;
}

.blog-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.card-image {
  height: 240px;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.blog-card:hover .card-image img {
  transform: scale(1.05);
}

.card-content {
  padding: 1.5rem;
}

.post-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
  font-size: 0.875rem;
  color: #718096;
  flex-wrap: wrap;
}

.author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.author-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
}

.card-content h3 {
  font-size: 1.375rem;
  margin-bottom: 0.75rem;
  color: #2d3748;
  line-height: 1.4;
}

.excerpt {
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.excerpt :deep(p) {
  margin: 0;
}

.categories {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.category-tag {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>
