# 🚨 FIX CRITIQUE - Blog Posts JSON

## Problème identifié

Le composant `BlogCard.vue` utilise des champs qui **n'existent pas** dans `blog-posts.json`, ce qui cause des erreurs potentielles.

### Champs manquants
- `author.name`
- `author.avatar`
- `reading_time`
- `categories[]`

---

## Solutions possibles

### ✅ Solution 1 (Recommandée) - Rendre les champs optionnels

**Avantages:** Pas besoin de modifier le JSON, compatibilité arrière
**Inconvénients:** Moins d'informations affichées

**Modifications à faire:**

#### 1. Mettre à jour l'interface TypeScript

```typescript
// app/composables/useBlog.ts
export interface BlogPost {
    id: number
    slug: string
    title: {
        rendered: string
    }
    excerpt: {
        rendered: string
    }
    content: {
        rendered: string
    }
    date: string
    featured_media: string
    // Nouveaux champs optionnels
    author?: {
        name: string
        avatar: string
    }
    reading_time?: string
    categories?: Array<{
        id: number
        name: string
    }>
}
```

#### 2. Modifier BlogCard.vue pour gérer les champs optionnels

```vue
<template>
  <NuxtLink :to="`/blog/${post.slug}`" class="blog-card">
    <div class="card-image">
      <img :src="post.featured_media" :alt="post.title.rendered" loading="lazy" />
    </div>
    <div class="card-content">
      <div class="post-meta">
        <!-- Afficher seulement si author existe -->
        <span v-if="post.author" class="author">
          <img :src="post.author.avatar" :alt="post.author.name" class="author-avatar" />
          {{ post.author.name }}
        </span>
        <span class="date">{{ formatDate(post.date) }}</span>
        <span v-if="post.reading_time" class="reading-time">{{ post.reading_time }}</span>
      </div>
      <h3>{{ post.title.rendered }}</h3>
      <div class="excerpt" v-html="post.excerpt.rendered"></div>
      <!-- Afficher seulement si categories existe -->
      <div v-if="post.categories && post.categories.length > 0" class="categories">
        <span v-for="category in post.categories" :key="category.id" class="category-tag">
          {{ category.name }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
```

---

### 🔧 Solution 2 - Ajouter les champs au JSON

**Avantages:** Interface complète, meilleure UX
**Inconvénients:** Modification de tous les posts existants

**Exemple de structure JSON mise à jour:**

```json
[
  {
    "id": 1,
    "slug": "top-10-french-cheeses-you-must-try",
    "title": {
      "rendered": "Top 10 French Cheeses You Must Try"
    },
    "excerpt": {
      "rendered": "Discover the finest French cheeses..."
    },
    "content": {
      "rendered": "<p>France is home to over 400 varieties..."
    },
    "date": "2025-12-20T10:00:00",
    "featured_media": "https://images.unsplash.com/photo-1452195100486-9cc805987862?w=800",
    "author": {
      "name": "Chef Jean Dupont",
      "avatar": "/images/avatars/chef-jean.jpg"
    },
    "reading_time": "5 min read",
    "categories": [
      { "id": 1, "name": "Cheese" },
      { "id": 2, "name": "French Cuisine" }
    ]
  }
]
```

---

### 🗑️ Solution 3 - Simplifier BlogCard.vue

**Avantages:** Pas de champs manquants, code plus simple
**Inconvénients:** Moins d'informations pour l'utilisateur

**Supprimer complètement ces sections:**
- Bloc author
- reading_time
- categories

---

## ⚡ Action recommandée

**Étape 1:** Appliquer la **Solution 1** (champs optionnels) immédiatement pour éviter les crashs

**Étape 2:** Décider si vous voulez :
- Garder l'interface simple (Solution 3)
- Enrichir le JSON avec les métadonnées (Solution 2)

**Étape 3:** Tester la page `/blog` après modification

---

## 🧪 Tests à effectuer

```bash
# 1. Vérifier que la page blog charge sans erreur
npm run dev
# Naviguer vers http://localhost:3000/blog

# 2. Vérifier la console pour les erreurs
# Ouvrir DevTools → Console

# 3. Vérifier que les cartes s'affichent correctement
# Les champs manquants ne doivent pas apparaître

# 4. Vérifier qu'un article individuel fonctionne
# Cliquer sur "Read more" sur un article
```

---

## 📋 Checklist de correction

- [ ] Modifier `app/composables/useBlog.ts` - Ajouter champs optionnels
- [ ] Modifier `app/components/cards/BlogCard.vue` - Ajouter conditions `v-if`
- [ ] Tester la page `/blog`
- [ ] Tester un article individuel `/blog/[slug]`
- [ ] Vérifier qu'il n'y a pas d'erreurs dans la console
- [ ] Documenter les champs optionnels dans le README
