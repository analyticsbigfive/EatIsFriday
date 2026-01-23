# Thème WordPress Eat Is Family

Template WordPress personnalisé avec des endpoints REST API pour l'application Eat Is Family.

## 📋 Table des matières

- [Installation](#installation)
- [Configuration](#configuration)
- [Endpoints API](#endpoints-api)
- [Custom Post Types](#custom-post-types)
- [Import des données](#import-des-données)
- [Structure des données](#structure-des-données)

## 🚀 Installation

### Prérequis
- WordPress 6.0 ou supérieur
- PHP 8.0 ou supérieur
- MySQL 5.7+ ou MariaDB 10.3+

### Étapes d'installation

1. **Télécharger le thème**
   ```bash
   cd wp-content/themes/
   git clone [url-du-repo] eatisfamily
   ```
   Ou téléversez le dossier `wordpress-theme` et renommez-le en `eatisfamily`.

2. **Activer le thème**
   - Connectez-vous à l'administration WordPress
   - Allez dans `Apparence > Thèmes`
   - Activez le thème "Eat Is Family"

3. **Configurer les permaliens**
   - Allez dans `Réglages > Permaliens`
   - Sélectionnez "Nom de l'article" ou une structure personnalisée
   - Enregistrez les modifications

4. **Vérifier les endpoints**
   - Visitez : `https://votresite.com/wp-json/eatisfamily/v1/`
   - Vous devriez voir les routes disponibles

## ⚙️ Configuration

### Activation des Custom Post Types

Le thème enregistre automatiquement les Custom Post Types suivants :
- **Activities** (`activity`)
- **Events** (`event`)
- **Jobs** (`job`)
- **Venues** (`venue`)

Ces CPT sont accessibles dans l'administration WordPress après activation du thème.

### Configuration CORS

Les headers CORS sont automatiquement ajoutés pour permettre les requêtes cross-origin. Pour restreindre l'accès, modifiez la fonction `eatisfamily_add_cors_headers()` dans `functions.php`.

## 📡 Endpoints API

Tous les endpoints sont disponibles sous le namespace `eatisfamily/v1`.

### Activities

**Liste toutes les activités**
```
GET /wp-json/eatisfamily/v1/activities
```

**Récupérer une activité par slug**
```
GET /wp-json/eatisfamily/v1/activities/{slug}
```

Exemple : `/wp-json/eatisfamily/v1/activities/cooking-workshop-italian-cuisine`

### Blog Posts

**Liste tous les articles**
```
GET /wp-json/eatisfamily/v1/blog-posts
```

**Récupérer un article par slug**
```
GET /wp-json/eatisfamily/v1/blog-posts/{slug}
```

### Events

**Liste tous les événements**
```
GET /wp-json/eatisfamily/v1/events
```

**Récupérer un événement par ID**
```
GET /wp-json/eatisfamily/v1/events/{id}
```

### Jobs

**Liste toutes les offres d'emploi**
```
GET /wp-json/eatisfamily/v1/jobs
```

Paramètres de filtrage disponibles :
- `department` : Filtrer par département
- `venue_id` : Filtrer par lieu

Exemple : `/wp-json/eatisfamily/v1/jobs?department=Culinary`

**Récupérer une offre par slug**
```
GET /wp-json/eatisfamily/v1/jobs/{slug}
```

### Venues

**Récupérer tous les lieux avec métadonnées**
```
GET /wp-json/eatisfamily/v1/venues
```

Retourne :
- `metadata` : Titre, description, labels
- `event_types` : Types d'événements
- `stats` : Statistiques du site
- `venues` : Liste des lieux

**Récupérer un lieu par ID**
```
GET /wp-json/eatisfamily/v1/venues/{id}
```

### Site Content

**Récupérer le contenu global du site**
```
GET /wp-json/eatisfamily/v1/site-content
```

Contient :
- Informations du site
- Contact et réseaux sociaux
- SEO metadata
- Contenu des pages principales

### Pages Content

**Récupérer le contenu des pages**
```
GET /wp-json/eatisfamily/v1/pages-content
```

## 📝 Custom Post Types

### Activity

**Champs personnalisés (Custom Fields) :**
- `description` : Description courte
- `activity_date` : Date de l'activité (format ISO 8601)
- `location` : Lieu
- `capacity` : Capacité totale (nombre)
- `available_spots` : Places disponibles (nombre)
- `category` : Catégorie
- `price` : Prix
- `duration` : Durée
- `status` : Statut (open, closed, full)

### Event

**Champs personnalisés :**
- `image` : URL de l'image (si différente de l'image mise en avant)
- `event_type` : Type d'événement
- `event_order` : Ordre d'affichage (nombre)

### Job

**Champs personnalisés :**
- `venue_id` : Identifiant du lieu
- `department` : Département
- `job_type` : Type de contrat
- `salary` : Salaire
- `requirements` : Exigences (JSON array)
- `benefits` : Avantages (JSON array)

Exemple de `requirements` (stocker en JSON) :
```json
["5+ years experience", "Strong leadership skills", "HACCP certification"]
```

### Venue

**Champs personnalisés :**
- `location` : Adresse complète
- `city` : Ville
- `country` : Pays
- `venue_type` : Type (stadium, festival, arena)
- `latitude` : Latitude (float)
- `longitude` : Longitude (float)
- `capacity` : Capacité (nombre)
- `amenities` : Équipements (JSON array)

## 📊 Import des données

### Méthode 1 : Via l'administration WordPress

1. Créez manuellement les posts dans chaque Custom Post Type
2. Remplissez les champs personnalisés via l'interface
3. Ajoutez les images mises en avant

### Méthode 2 : Via plugin d'import

Utilisez un plugin comme **WP All Import** ou **Advanced Custom Fields** pour importer les données depuis vos fichiers JSON.

### Méthode 3 : Script PHP personnalisé

Un fichier `import-data.php` est inclus dans le dossier du thème. Voir ci-dessous.

### Utilisation du script d'import

1. Placez vos fichiers JSON dans `wp-content/uploads/import/`
2. Accédez à : `https://votresite.com/?import_eatisfamily_data=1`
3. Les données seront importées automatiquement
4. **IMPORTANT** : Supprimez ou commentez le code d'import après utilisation pour des raisons de sécurité

## 🔧 Options du thème

### Configurer les métadonnées des venues

Dans l'admin WordPress, utilisez un plugin comme **Advanced Custom Fields** ou ajoutez via le code :

```php
update_option('eatisfamily_venues_metadata', array(
    'title' => 'Explore Where We Operate',
    'description' => 'Description...',
    'filter_label' => 'Click to filter by an event type',
));
```

### Configurer les types d'événements

```php
update_option('eatisfamily_event_types', array(
    array('id' => 'stadium', 'name' => 'Stadium', 'image' => '/images/stadium.png'),
    array('id' => 'festival', 'name' => 'Festival', 'image' => '/images/festival.png'),
));
```

### Configurer les statistiques

```php
update_option('eatisfamily_stats', array(
    array('value' => '250+', 'label' => 'Food & Beverage Events in 2024'),
    array('value' => '300,000', 'label' => 'People fed in 2024'),
));
```

### Configurer le contenu du site

```php
update_option('eatisfamily_site_content', array(
    'site' => array(
        'name' => 'Eat Is Family',
        'tagline' => 'Celebrate Food',
        // ...
    ),
));
```

## 🔒 Sécurité

### Permissions API

Par défaut, tous les endpoints sont publics (`permission_callback' => '__return_true'`). Pour ajouter de l'authentification :

```php
register_rest_route($namespace, '/activities', array(
    'methods' => 'GET',
    'callback' => 'eatisfamily_get_activities',
    'permission_callback' => function() {
        return current_user_can('read');
    },
));
```

### Rate Limiting

Considérez l'ajout d'un plugin de rate limiting pour protéger vos APIs contre les abus.

## 🎨 Personnalisation

### Modifier le format de sortie

Éditez les fonctions `eatisfamily_format_*()` dans `functions.php` pour personnaliser la structure JSON retournée.

### Ajouter des endpoints

Exemple pour ajouter un endpoint personnalisé :

```php
register_rest_route('eatisfamily/v1', '/custom-endpoint', array(
    'methods' => 'GET',
    'callback' => 'your_callback_function',
    'permission_callback' => '__return_true',
));
```

## 📚 Structure des données

### Format Activity
```json
{
  "id": 1,
  "slug": "cooking-workshop",
  "title": {"rendered": "Cooking Workshop"},
  "description": "Short description",
  "content": {"rendered": "<p>Full HTML content</p>"},
  "date": "2026-01-15T18:00:00",
  "location": "Paris",
  "capacity": 12,
  "available_spots": 5,
  "category": "Cooking",
  "price": "€85",
  "duration": "3 hours",
  "featured_media": "https://...",
  "status": "open"
}
```

### Format Job
```json
{
  "id": 1,
  "slug": "head-chef",
  "title": {"rendered": "Head Chef"},
  "excerpt": {"rendered": "Short description"},
  "content": {"rendered": "<p>Full description</p>"},
  "venue_id": "stade-jean-bouin",
  "department": "Culinary",
  "job_type": "Full-time",
  "salary": "$150 - $200 / hour",
  "requirements": ["5+ years experience", "..."],
  "benefits": ["Competitive pay", "..."],
  "featured_media": "https://..."
}
```

## 🐛 Dépannage

### Les permaliens ne fonctionnent pas
1. Allez dans `Réglages > Permaliens`
2. Cliquez sur "Enregistrer les modifications" sans rien changer
3. Vérifiez que le fichier `.htaccess` est accessible en écriture

### Les endpoints retournent 404
1. Assurez-vous que le thème est activé
2. Rafraîchissez les permaliens (voir ci-dessus)
3. Vérifiez que `rest_api_init` est bien appelé

### Les images ne s'affichent pas
1. Vérifiez que les images mises en avant sont définies
2. Utilisez des URLs complètes pour les images
3. Configurez correctement les permissions du dossier `uploads`

## 📄 Licence

GPL v2 ou ultérieur

## 👥 Support

Pour toute question ou problème, contactez l'équipe de développement Eat Is Family.

---

**Version** : 1.0.0  
**Dernière mise à jour** : Janvier 2026
