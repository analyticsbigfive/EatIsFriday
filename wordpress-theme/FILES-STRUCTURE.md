# Thème WordPress Eat Is Family - Structure des Fichiers

## 📁 Structure complète du thème

```
wordpress-theme/
│
├── 📄 style.css                    # Fichier principal du thème (obligatoire)
├── 📄 functions.php                # Fonctions principales et endpoints API
├── 📄 index.php                    # Template principal avec documentation API
├── 📄 header.php                   # En-tête du site
├── 📄 footer.php                   # Pied de page du site
│
├── 📄 single-activity.php          # Template pour afficher une activité
├── 📄 single-job.php               # Template pour afficher une offre d'emploi
├── 📄 archive-activity.php         # Archive des activités
│
├── 📄 import-data.php              # Script d'import des données JSON
│
├── 📄 .htaccess.example            # Configuration Apache exemple
│
├── 📂 inc/
│   └── 📄 admin.php                # Personnalisation de l'admin WordPress
│
└── 📂 Documentation/
    ├── 📄 README.md                # Documentation complète du thème
    ├── 📄 QUICK-START.md           # Guide d'installation rapide
    ├── 📄 API-REFERENCE.md         # Référence complète de l'API
    └── 📄 FILES-STRUCTURE.md       # Ce fichier
```

## 📋 Description des fichiers

### Fichiers principaux du thème

#### style.css
- **Type:** CSS
- **Obligatoire:** Oui
- **Description:** En-tête du thème avec métadonnées + styles de base
- **Contient:**
  - Informations du thème (nom, auteur, version)
  - Reset CSS basique
  - Styles généraux

#### functions.php
- **Type:** PHP
- **Obligatoire:** Oui
- **Description:** Cœur du thème - toute la logique backend
- **Contient:**
  - Enregistrement des Custom Post Types (Activity, Event, Job, Venue)
  - Enregistrement des routes REST API
  - Fonctions de callback pour chaque endpoint
  - Fonctions de formatage des données
  - Configuration CORS
  - Support du thème (thumbnails, menus, etc.)

#### index.php
- **Type:** PHP Template
- **Obligatoire:** Oui
- **Description:** Page d'accueil affichant la documentation des API
- **Contient:**
  - Liste de tous les endpoints disponibles
  - URLs cliquables pour tester les API

#### header.php
- **Type:** PHP Template
- **Description:** En-tête HTML du site
- **Contient:**
  - Balises meta
  - wp_head() hook
  - Structure HTML de l'en-tête

#### footer.php
- **Type:** PHP Template
- **Description:** Pied de page HTML
- **Contient:**
  - Copyright
  - wp_footer() hook
  - Fermeture des balises HTML

### Templates personnalisés

#### single-activity.php
- **Type:** PHP Template
- **Description:** Affiche une activité individuelle
- **Utilise:** Meta fields personnalisés (date, location, price, etc.)
- **URL:** /activity/slug-here/

#### single-job.php
- **Type:** PHP Template
- **Description:** Affiche une offre d'emploi individuelle
- **Utilise:** Meta fields (department, salary, requirements, benefits)
- **URL:** /job/slug-here/

#### archive-activity.php
- **Type:** PHP Template
- **Description:** Liste toutes les activités
- **Fonctionnalités:** Grid layout, pagination
- **URL:** /activities/

### Scripts et outils

#### import-data.php
- **Type:** PHP Script
- **Description:** Import automatique des données JSON
- **Sécurité:** Protégé par clé secrète
- **Utilisation:** Visitez l'URL avec le paramètre secret
- **⚠️ Important:** À supprimer après utilisation

#### .htaccess.example
- **Type:** Apache Configuration
- **Description:** Configuration serveur recommandée
- **Contient:**
  - Règles de réécriture WordPress
  - Headers CORS
  - Compression Gzip
  - Caching navigateur
  - Headers de sécurité

### Fichiers d'administration

#### inc/admin.php
- **Type:** PHP
- **Description:** Personnalisation de l'interface admin WordPress
- **Contient:**
  - Colonnes personnalisées dans les listes admin
  - Meta boxes pour les champs personnalisés
  - Interface utilisateur améliorée
  - Notices d'activation du thème

### Documentation

#### README.md
- **Type:** Markdown
- **Taille:** ~500 lignes
- **Description:** Documentation complète du thème
- **Sections:**
  - Installation détaillée
  - Configuration
  - Liste de tous les endpoints API
  - Custom Post Types
  - Champs personnalisés
  - Import de données
  - Dépannage
  - Personnalisation

#### QUICK-START.md
- **Type:** Markdown
- **Description:** Guide d'installation en 5 minutes
- **Sections:**
  - Installation rapide
  - Configuration minimale
  - Test des endpoints
  - Checklist post-installation

#### API-REFERENCE.md
- **Type:** Markdown
- **Description:** Documentation complète de l'API REST
- **Sections:**
  - Référence de chaque endpoint
  - Paramètres de requête
  - Exemples de réponses JSON
  - Codes d'erreur
  - Exemples cURL
  - Exemples JavaScript/TypeScript
  - Exemples Nuxt 3

#### FILES-STRUCTURE.md
- **Type:** Markdown
- **Description:** Ce fichier - vue d'ensemble de la structure

## 🔧 Fonctionnalités implémentées

### Custom Post Types (CPT)
1. **Activity** - Activités culinaires
2. **Event** - Événements
3. **Job** - Offres d'emploi
4. **Venue** - Lieux

### REST API Endpoints

#### Activities
- `GET /wp-json/eatisfamily/v1/activities`
- `GET /wp-json/eatisfamily/v1/activities/{slug}`

#### Blog Posts
- `GET /wp-json/eatisfamily/v1/blog-posts`
- `GET /wp-json/eatisfamily/v1/blog-posts/{slug}`

#### Events
- `GET /wp-json/eatisfamily/v1/events`
- `GET /wp-json/eatisfamily/v1/events/{id}`

#### Jobs
- `GET /wp-json/eatisfamily/v1/jobs`
- `GET /wp-json/eatisfamily/v1/jobs/{slug}`
- Paramètres: `?department=X&venue_id=Y`

#### Venues
- `GET /wp-json/eatisfamily/v1/venues`
- `GET /wp-json/eatisfamily/v1/venues/{id}`

#### Site Content
- `GET /wp-json/eatisfamily/v1/site-content`

#### Pages Content
- `GET /wp-json/eatisfamily/v1/pages-content`

### Champs personnalisés

#### Activity
- description, activity_date, location, capacity
- available_spots, category, price, duration, status

#### Job
- venue_id, department, job_type, salary
- requirements (JSON), benefits (JSON)

#### Venue
- location, city, country, venue_type
- latitude, longitude, capacity, amenities (JSON)

#### Event
- event_type, event_order, image

### Fonctionnalités admin

- Colonnes personnalisées dans les listes
- Meta boxes avec interface intuitive
- Validation des champs
- Notice d'activation
- Support des images mises en avant

### Sécurité

- CORS configuré
- Nonces pour les formulaires
- Sanitization des données
- Vérification des permissions
- Protection des fichiers sensibles

## 📊 Statistiques du thème

- **Nombre total de fichiers:** 14
- **Lignes de code PHP:** ~1500+
- **Endpoints API:** 14
- **Custom Post Types:** 4
- **Templates:** 6
- **Documentation:** 4 fichiers (3000+ lignes)

## 🚀 Pour commencer

1. **Installation:** Voir QUICK-START.md
2. **Configuration:** Voir README.md
3. **Référence API:** Voir API-REFERENCE.md
4. **Structure:** Ce fichier

## 📦 Compatibilité

- **WordPress:** 6.0+
- **PHP:** 8.0+
- **MySQL:** 5.7+ / MariaDB 10.3+
- **Serveur:** Apache (avec mod_rewrite) ou Nginx

## 🔄 Versions

- **Version actuelle:** 1.0.0
- **Date de création:** Janvier 2026
- **Auteur:** Eat Is Family

## 📝 Notes importantes

### Fichiers à ne PAS modifier directement
- `functions.php` (sauf pour personnalisation avancée)
- Custom Post Types (risque de perte de données)

### Fichiers à personnaliser
- `style.css` (design)
- Templates PHP (affichage)
- `inc/admin.php` (interface admin)

### Fichiers à supprimer après usage
- `import-data.php` (après import des données)
- `.htaccess.example` (après copie en .htaccess)

## 🔗 Intégration avec Nuxt

Pour utiliser ces APIs dans votre application Nuxt existante, mettez à jour vos composables pour pointer vers les nouveaux endpoints WordPress au lieu des fichiers JSON statiques.

**Exemple:**
```typescript
// Avant (JSON statique)
const apiBase = '/api'

// Après (WordPress)
const apiBase = 'https://votresite.com/wp-json/eatisfamily/v1'
```

## 📞 Support

Pour toute question sur les fichiers :
- Consultez la documentation appropriée
- Vérifiez les commentaires dans le code
- Contactez : hello@eatisfamily.fr

---

**Date de génération:** Janvier 2026  
**Version du thème:** 1.0.0  
**Statut:** Production Ready ✅
