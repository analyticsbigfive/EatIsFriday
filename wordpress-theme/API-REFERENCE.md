# API Reference - Eat Is Family WordPress Theme

## Base URL
```
https://votresite.com/wp-json/eatisfamily/v1
```

## Authentication
Tous les endpoints sont publics par défaut. Pour ajouter l'authentification, modifiez `permission_callback` dans functions.php.

---

## Activities

### GET /activities
Récupère la liste de toutes les activités.

**Response:**
```json
[
  {
    "id": 1,
    "slug": "cooking-workshop-italian-cuisine",
    "title": {
      "rendered": "Italian Cuisine Cooking Workshop"
    },
    "description": "Learn to prepare authentic Italian dishes...",
    "content": {
      "rendered": "<p>Join us for an immersive...</p>"
    },
    "date": "2026-01-15T18:00:00",
    "location": "Culinary Studio, 123 Rue de la Cuisine, Paris",
    "capacity": 12,
    "available_spots": 5,
    "category": "Cooking",
    "price": "€85",
    "duration": "3 hours",
    "featured_media": "https://...",
    "status": "open"
  }
]
```

### GET /activities/{slug}
Récupère une activité par son slug.

**Parameters:**
- `slug` (string, required): Le slug de l'activité

**Example:**
```bash
GET /activities/cooking-workshop-italian-cuisine
```

**Response:** Objet activity unique (même structure que ci-dessus)

**Error Response (404):**
```json
{
  "code": "not_found",
  "message": "Activity not found",
  "data": {
    "status": 404
  }
}
```

---

## Blog Posts

### GET /blog-posts
Récupère la liste de tous les articles de blog.

**Response:**
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
      "rendered": "<p>France is home to over 400 varieties...</p>"
    },
    "date": "2025-12-20T10:00:00",
    "featured_media": "https://..."
  }
]
```

### GET /blog-posts/{slug}
Récupère un article par son slug.

**Parameters:**
- `slug` (string, required): Le slug de l'article

**Example:**
```bash
GET /blog-posts/top-10-french-cheeses-you-must-try
```

---

## Events

### GET /events
Récupère la liste de tous les événements, triés par `event_order`.

**Response:**
```json
[
  {
    "id": 1,
    "title": "The Adidas Arena",
    "image": "/images/vap.jpg",
    "description": "We are proud to announce our partnership...",
    "event_type": "Venue Partnership"
  }
]
```

### GET /events/{id}
Récupère un événement par son ID.

**Parameters:**
- `id` (integer, required): L'ID de l'événement

**Example:**
```bash
GET /events/1
```

---

## Jobs

### GET /jobs
Récupère la liste de toutes les offres d'emploi.

**Query Parameters (optional):**
- `department` (string): Filtrer par département
- `venue_id` (string): Filtrer par lieu

**Examples:**
```bash
GET /jobs
GET /jobs?department=Culinary
GET /jobs?venue_id=stade-jean-bouin
GET /jobs?department=Culinary&venue_id=stade-jean-bouin
```

**Response:**
```json
[
  {
    "id": 1,
    "slug": "head-chef-vip-hospitality-stade-jean-bouin",
    "title": {
      "rendered": "Head Chef – VIP Hospitality"
    },
    "excerpt": {
      "rendered": "Lead the culinary experience..."
    },
    "content": {
      "rendered": "<p>We are looking for...</p>"
    },
    "venue_id": "stade-jean-bouin",
    "department": "Culinary",
    "job_type": "Full-time",
    "salary": "$150 - $200 / hour",
    "requirements": [
      "5+ years experience in high-volume fine dining",
      "Strong leadership and team management skills"
    ],
    "benefits": [
      "Work on major events in an iconic venue",
      "Competitive compensation"
    ],
    "featured_media": "https://..."
  }
]
```

### GET /jobs/{slug}
Récupère une offre d'emploi par son slug.

**Parameters:**
- `slug` (string, required): Le slug de l'offre

**Example:**
```bash
GET /jobs/head-chef-vip-hospitality-stade-jean-bouin
```

---

## Venues

### GET /venues
Récupère tous les lieux avec métadonnées complètes.

**Response:**
```json
{
  "metadata": {
    "title": "Explore Where We Operate",
    "description": "Tap any marker on the map...",
    "filter_label": "Click to filter by an event type"
  },
  "event_types": [
    {
      "id": "stadium",
      "name": "Stadium",
      "image": "/images/stadium.png"
    },
    {
      "id": "festival",
      "name": "Festival",
      "image": "/images/festival.png"
    }
  ],
  "stats": [
    {
      "value": "250+",
      "label": "Food & Beverage Events in 2024"
    },
    {
      "value": "300,000",
      "label": "People fed in 2024"
    }
  ],
  "venues": [
    {
      "id": "stade-jean-bouin",
      "name": "Stade Jean Bouin",
      "location": "Paris, France",
      "city": "Paris",
      "country": "France",
      "type": "stadium",
      "lat": 48.8427,
      "lng": 2.2531,
      "description": "A historic stadium...",
      "capacity": 20000,
      "amenities": ["VIP Suites", "Press Box"],
      "image": "https://..."
    }
  ]
}
```

### GET /venues/{id}
Récupère un lieu par son identifiant (slug).

**Parameters:**
- `id` (string, required): L'identifiant du lieu (slug)

**Example:**
```bash
GET /venues/stade-jean-bouin
```

**Response:** Objet venue unique

---

## Site Content

### GET /site-content
Récupère tout le contenu global du site (infos site, contact, SEO, etc.).

**Response:**
```json
{
  "site": {
    "name": "Eat Is Family",
    "tagline": "Celebrate Food, Every Friday and Beyond",
    "description": "Your destination for culinary experiences...",
    "seo": {
      "default_title": "Eat Is Family - Event Catering...",
      "default_description": "Eat Is Family delivers exceptional...",
      "keywords": "event catering, stadium catering...",
      "og_image": "/images/og-default.jpg"
    },
    "contact": {
      "email": "hello@eatisfamily.fr",
      "phone": "+33 1 23 5 67 89"
    },
    "social": {
      "facebook": "https://facebook.com/eatisfamily",
      "instagram": "https://instagram.com/eatisfamily",
      "twitter": "https://twitter.com/eatisfamily",
      "linkedin": "https://linkedin.com/company/eatisfamily"
    }
  },
  "about": {
    "seo": { /* SEO data for about page */ },
    "hero": { /* Hero section data */ },
    "intro_section": { /* Intro section data */ }
  },
  "homepage": { /* Homepage specific data */ }
}
```

---

## Pages Content

### GET /pages-content
Récupère le contenu structuré de toutes les pages du site.

**Response:**
```json
{
  "homepage": { /* Homepage content */ },
  "about": { /* About page content */ },
  "contact": { /* Contact page content */ }
}
```

---

## Error Responses

### 404 Not Found
```json
{
  "code": "not_found",
  "message": "Resource not found",
  "data": {
    "status": 404
  }
}
```

### 400 Bad Request
```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): slug",
  "data": {
    "status": 400,
    "params": {
      "slug": "Invalid slug format"
    }
  }
}
```

### 500 Internal Server Error
```json
{
  "code": "internal_error",
  "message": "An error occurred",
  "data": {
    "status": 500
  }
}
```

---

## CORS Headers

Le thème ajoute automatiquement ces headers CORS :
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

Pour restreindre l'origine, modifiez `eatisfamily_add_cors_headers()` dans functions.php.

---

## Rate Limiting

Aucun rate limiting n'est implémenté par défaut. Pour ajouter du rate limiting, installez un plugin comme:
- WP REST API Controller
- Limit Login Attempts Reloaded

Ou implémentez votre propre logique dans `functions.php`.

---

## Caching

Pour améliorer les performances, utilisez :
1. **Plugin de cache WordPress** : WP Super Cache, W3 Total Cache
2. **Cache REST API** : REST API Cache
3. **CDN** : Cloudflare, CloudFront

---

## Testing with cURL

### Get all activities
```bash
curl -X GET https://votresite.com/wp-json/eatisfamily/v1/activities
```

### Get specific activity
```bash
curl -X GET https://votresite.com/wp-json/eatisfamily/v1/activities/cooking-workshop-italian-cuisine
```

### Get filtered jobs
```bash
curl -X GET "https://votresite.com/wp-json/eatisfamily/v1/jobs?department=Culinary"
```

### With authentication (if enabled)
```bash
curl -X GET https://votresite.com/wp-json/eatisfamily/v1/activities \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## JavaScript/TypeScript Examples

### Using Fetch API
```javascript
// Get all activities
fetch('https://votresite.com/wp-json/eatisfamily/v1/activities')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

// Get activity by slug
fetch('https://votresite.com/wp-json/eatisfamily/v1/activities/cooking-workshop')
  .then(response => {
    if (!response.ok) throw new Error('Not found');
    return response.json();
  })
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));
```

### Using Axios
```javascript
import axios from 'axios';

const API_BASE = 'https://votresite.com/wp-json/eatisfamily/v1';

// Get all jobs
const jobs = await axios.get(`${API_BASE}/jobs`);
console.log(jobs.data);

// Get filtered jobs
const culinaryJobs = await axios.get(`${API_BASE}/jobs`, {
  params: { department: 'Culinary' }
});
console.log(culinaryJobs.data);
```

### Nuxt 3 Composable
```typescript
// composables/useActivities.ts
export const useActivities = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getActivities = async () => {
    return await $fetch(`${apiBase}/activities`)
  }

  const getActivityBySlug = async (slug: string) => {
    return await $fetch(`${apiBase}/activities/${slug}`)
  }

  return {
    getActivities,
    getActivityBySlug
  }
}

// Usage in component
const { getActivities } = useActivities()
const activities = await getActivities()
```

---

## WordPress Integration

### Access from WordPress Theme/Plugin
```php
// Get activities
$response = wp_remote_get(rest_url('eatisfamily/v1/activities'));
$activities = json_decode(wp_remote_retrieve_body($response), true);

// Get specific activity
$slug = 'cooking-workshop';
$response = wp_remote_get(rest_url("eatisfamily/v1/activities/{$slug}"));
$activity = json_decode(wp_remote_retrieve_body($response), true);
```

---

## Version History

### v1.0.0 (Current)
- Initial release
- All endpoints implemented
- CORS support
- Custom Post Types registration
- Meta boxes for admin

---

## Support & Contact

Pour toute question sur l'API :
- Documentation complète : voir README.md
- Support : hello@eatisfamily.fr
- Repository : [GitHub URL]

---

**Last Updated:** Janvier 2026  
**API Version:** 1.0.0  
**WordPress Version:** 6.0+  
**PHP Version:** 8.0+
