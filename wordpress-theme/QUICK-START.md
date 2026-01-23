# Guide d'Installation Rapide - Thème WordPress Eat Is Family

## Installation en 5 minutes

### Étape 1 : Téléverser le thème
```bash
# Via FTP ou SFTP
Téléversez le dossier 'wordpress-theme' dans /wp-content/themes/
Renommez-le en 'eatisfamily'
```

Ou via l'administration WordPress :
1. Compressez le dossier `wordpress-theme` en .zip
2. Allez dans `Apparence > Thèmes > Ajouter`
3. Cliquez sur "Téléverser un thème"
4. Sélectionnez le fichier .zip

### Étape 2 : Activer le thème
1. Allez dans `Apparence > Thèmes`
2. Activez "Eat Is Family"

### Étape 3 : Configurer les permaliens
1. Allez dans `Réglages > Permaliens`
2. Sélectionnez "Nom de l'article"
3. Cliquez sur "Enregistrer les modifications"

### Étape 4 : Vérifier les Custom Post Types
Après activation, vous devriez voir dans le menu admin :
- Activities
- Events
- Jobs
- Venues
- Articles (posts par défaut)

### Étape 5 : Importer les données (optionnel)

#### Option A : Import automatique via script

1. Copiez vos fichiers JSON dans le dossier `public/api/` à la racine de WordPress :
   ```
   /public/api/activities.json
   /public/api/blog-posts.json
   /public/api/events.json
   /public/api/jobs.json
   /public/api/venues.json
   /public/api/site-content.json
   /public/api/pages-content.json
   ```

2. Éditez `import-data.php` et changez la clé secrète :
   ```php
   define('IMPORT_SECRET_KEY', 'votre_cle_secrete_ici');
   ```

3. Visitez : `https://votresite.com/wp-content/themes/eatisfamily/import-data.php?import_eatisfamily_data=votre_cle_secrete_ici`

4. **IMPORTANT** : Supprimez le fichier `import-data.php` après l'import

#### Option B : Import manuel

1. Créez les posts manuellement via l'admin WordPress
2. Remplissez les champs personnalisés pour chaque post

## Test des Endpoints API

Une fois le thème activé, testez vos endpoints :

```bash
# Activities
curl https://votresite.com/wp-json/eatisfamily/v1/activities

# Blog Posts
curl https://votresite.com/wp-json/eatisfamily/v1/blog-posts

# Events
curl https://votresite.com/wp-json/eatisfamily/v1/events

# Jobs
curl https://votresite.com/wp-json/eatisfamily/v1/jobs

# Venues
curl https://votresite.com/wp-json/eatisfamily/v1/venues

# Site Content
curl https://votresite.com/wp-json/eatisfamily/v1/site-content
```

## Configuration des Champs Personnalisés

### Pour Activities :
Lors de la création/édition d'une activité, ajoutez ces champs personnalisés :
- `description` (texte)
- `activity_date` (date au format ISO: 2026-01-15T18:00:00)
- `location` (texte)
- `capacity` (nombre)
- `available_spots` (nombre)
- `category` (texte)
- `price` (texte, ex: "€85")
- `duration` (texte, ex: "3 hours")
- `status` (texte: "open", "closed", ou "full")

### Pour Jobs :
- `venue_id` (texte)
- `department` (texte)
- `job_type` (texte)
- `salary` (texte)
- `requirements` (JSON array, ex: ["5+ years", "Leadership skills"])
- `benefits` (JSON array, ex: ["Competitive pay", "Health insurance"])

### Pour Venues :
- `location` (texte)
- `city` (texte)
- `country` (texte)
- `venue_type` (texte: "stadium", "festival", "arena")
- `latitude` (nombre décimal)
- `longitude` (nombre décimal)
- `capacity` (nombre)
- `amenities` (JSON array)

### Pour Events :
- `event_type` (texte)
- `event_order` (nombre pour l'ordre d'affichage)
- `image` (URL de l'image si différente de l'image mise en avant)

## Plugin Recommandés

Pour faciliter la gestion des champs personnalisés, installez :

1. **Advanced Custom Fields (ACF)** - Gratuit
   - Facilite la création et gestion des champs personnalisés
   - Interface intuitive dans l'admin

2. **WP All Import** - Pour import en masse (optionnel)
   - Import de données depuis CSV/JSON
   - Mapping automatique des champs

3. **REST API Security** (optionnel)
   - Ajoute de la sécurité aux endpoints
   - Rate limiting

## Utilisation des APIs dans votre application Nuxt

Mettez à jour votre fichier `nuxt.config.ts` :

```typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: 'https://votresite.com/wp-json/eatisfamily/v1'
    }
  }
})
```

Mettez à jour vos composables pour pointer vers WordPress :

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
```

## Dépannage Rapide

### Les endpoints retournent 404
```bash
# Solution : Rafraîchir les permaliens
1. Allez dans Réglages > Permaliens
2. Cliquez sur "Enregistrer" (sans rien changer)
```

### Les Custom Post Types n'apparaissent pas
```bash
# Solution : Vérifier que le thème est bien activé
1. Apparence > Thèmes
2. Vérifiez que "Eat Is Family" est actif
3. Si problème persiste, désactivez puis réactivez le thème
```

### Les images ne s'affichent pas
```bash
# Solution : Vérifier les permissions
chmod 755 wp-content/uploads
```

### CORS errors
```bash
# Les headers CORS sont déjà configurés dans functions.php
# Si vous avez encore des erreurs, vérifiez :
1. Que votre serveur autorise les headers CORS
2. Ajoutez dans .htaccess si nécessaire :
   Header set Access-Control-Allow-Origin "*"
```

## Structure des URLs

Après configuration :
- Activities : `/activities/` ou `/activity/slug-here/`
- Jobs : `/jobs/` ou `/job/slug-here/`
- Events : `/events/` ou `/event/slug-here/`
- Venues : `/venues/` ou `/venue/slug-here/`
- Blog : `/blog/` ou `/slug-here/`

## Support

Pour toute question :
- Consultez le README.md complet
- Vérifiez les logs WordPress (WP_DEBUG)
- Contactez l'équipe de développement

## Checklist Post-Installation

- [ ] Thème activé
- [ ] Permaliens configurés
- [ ] Custom Post Types visibles dans l'admin
- [ ] Données importées (si applicable)
- [ ] API endpoints testés et fonctionnels
- [ ] Images mises en avant configurées
- [ ] Champs personnalisés ajoutés
- [ ] Import script supprimé (si utilisé)
- [ ] Application Nuxt mise à jour pour pointer vers WordPress
- [ ] Tests end-to-end effectués

---

**Durée estimée** : 10-15 minutes  
**Niveau** : Intermédiaire  
**Prérequis** : Accès admin WordPress, connaissances de base WordPress
