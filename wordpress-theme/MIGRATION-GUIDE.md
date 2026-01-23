# Guide de Migration - JSON statique vers WordPress

Ce guide vous aide à migrer votre application Nuxt.js depuis l'API JSON statique vers le thème WordPress avec API REST.

## 📋 Vue d'ensemble

### Avant (JSON statique)
```
Application Nuxt → Fichiers JSON (/public/api/*.json)
```

### Après (WordPress)
```
Application Nuxt → WordPress REST API → Base de données WordPress
```

## 🔄 Étapes de migration

### Étape 1 : Installation du thème WordPress

1. **Installer WordPress** sur votre serveur
   ```bash
   # Téléchargez WordPress
   wget https://wordpress.org/latest.zip
   unzip latest.zip
   
   # Configurez la base de données dans wp-config.php
   ```

2. **Installer le thème**
   ```bash
   cd wp-content/themes/
   # Copiez le dossier wordpress-theme et renommez-le en eatisfamily
   ```

3. **Activer le thème**
   - Connectez-vous à l'admin WordPress
   - Apparence > Thèmes > Activez "Eat Is Family"

4. **Configurer les permaliens**
   - Réglages > Permaliens > "Nom de l'article"
   - Enregistrer

### Étape 2 : Import des données

#### Option A : Import automatique (Recommandé)

1. Placez vos fichiers JSON dans `/public/api/` à la racine de WordPress
2. Éditez `import-data.php` et changez la clé secrète
3. Visitez : `https://votresite.com/wp-content/themes/eatisfamily/import-data.php?import_eatisfamily_data=VOTRE_CLE`
4. Attendez la fin de l'import
5. **Supprimez immédiatement** `import-data.php`

#### Option B : Import manuel

Créez les posts manuellement dans WordPress pour chaque Custom Post Type.

### Étape 3 : Mise à jour de l'application Nuxt

#### 3.1 Mettre à jour nuxt.config.ts

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      // ANCIEN - Commentez ou supprimez
      // apiBase: '/api'
      
      // NOUVEAU - URL de votre WordPress
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'https://votresite.com/wp-json/eatisfamily/v1'
    }
  }
})
```

#### 3.2 Créer un fichier .env

```env
# .env
NUXT_PUBLIC_API_BASE=https://votresite.com/wp-json/eatisfamily/v1
```

#### 3.3 Vérifier les composables

Bonne nouvelle : **Aucun changement nécessaire** dans vos composables ! 

Les endpoints WordPress retournent la même structure JSON que vos fichiers statiques.

```typescript
// composables/useActivities.ts - Reste identique !
export const useActivities = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase // Pointe maintenant vers WordPress

  const getActivities = async () => {
    return await $fetch(`${apiBase}/activities`) // Fonctionne tel quel
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

### Étape 4 : Gestion des images

#### Problème potentiel
Les images dans vos JSON pointent vers des URLs relatives ou Unsplash.

#### Solution 1 : Téléverser dans WordPress
```php
// Les images sont automatiquement téléversées par import-data.php
// Elles seront hébergées sur votre serveur WordPress
```

#### Solution 2 : Garder les URLs externes
```php
// Dans functions.php, les URLs externes sont préservées
'featured_media' => get_the_post_thumbnail_url($post->ID, 'large') ?: $external_url
```

### Étape 5 : Configuration CORS

#### Si Nuxt et WordPress sont sur le même domaine
Aucune configuration nécessaire.

#### Si Nuxt et WordPress sont sur des domaines différents

**WordPress est déjà configuré** avec les headers CORS dans `functions.php`.

Si vous avez des problèmes, vérifiez votre serveur :

**Apache (.htaccess)**
```apache
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "https://votre-app-nuxt.com"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>
```

**Nginx**
```nginx
add_header Access-Control-Allow-Origin "https://votre-app-nuxt.com";
add_header Access-Control-Allow-Methods "GET, POST, OPTIONS";
add_header Access-Control-Allow-Headers "Content-Type, Authorization";
```

### Étape 6 : Tests

#### 6.1 Tester les endpoints WordPress

```bash
# Activities
curl https://votresite.com/wp-json/eatisfamily/v1/activities

# Blog posts
curl https://votresite.com/wp-json/eatisfamily/v1/blog-posts

# Events
curl https://votresite.com/wp-json/eatisfamily/v1/events

# Jobs
curl https://votresite.com/wp-json/eatisfamily/v1/jobs

# Venues
curl https://votresite.com/wp-json/eatisfamily/v1/venues

# Site content
curl https://votresite.com/wp-json/eatisfamily/v1/site-content
```

#### 6.2 Tester l'application Nuxt

```bash
# En développement
npm run dev

# Vérifiez que les données se chargent correctement
# Testez chaque page de votre application
```

### Étape 7 : Déploiement

#### 7.1 Variables d'environnement

**Production**
```env
NUXT_PUBLIC_API_BASE=https://wordpress.votresite.com/wp-json/eatisfamily/v1
```

**Staging**
```env
NUXT_PUBLIC_API_BASE=https://staging-wordpress.votresite.com/wp-json/eatisfamily/v1
```

**Développement**
```env
NUXT_PUBLIC_API_BASE=http://localhost:8888/wp-json/eatisfamily/v1
```

#### 7.2 Build et déploiement

```bash
# Build de l'application Nuxt
npm run build

# Déployez sur votre hébergeur
npm run preview # Pour tester en local d'abord
```

## 🔍 Comparaison des structures

### Avant (JSON statique)

```
public/
└── api/
    ├── activities.json
    ├── blog-posts.json
    ├── events.json
    ├── jobs.json
    ├── venues.json
    ├── site-content.json
    └── pages-content.json
```

**Problèmes :**
- ❌ Pas de mise à jour dynamique
- ❌ Pas d'interface admin
- ❌ Modifications manuelles des fichiers
- ❌ Pas de validation des données
- ❌ Difficile à maintenir

### Après (WordPress)

```
WordPress Admin → Custom Post Types → Base de données → REST API → Nuxt App
```

**Avantages :**
- ✅ Interface admin conviviale
- ✅ Mise à jour en temps réel
- ✅ Validation des données
- ✅ Gestion des médias
- ✅ Versioning des contenus
- ✅ Multi-utilisateurs
- ✅ Backup automatique
- ✅ Recherche intégrée
- ✅ SEO friendly

## 📊 Checklist de migration

### Pré-migration
- [ ] Backup des fichiers JSON actuels
- [ ] WordPress installé et configuré
- [ ] Thème téléversé et activé
- [ ] Permalinks configurés
- [ ] Base de données créée

### Migration des données
- [ ] Fichiers JSON copiés dans `/public/api/`
- [ ] Clé secrète changée dans `import-data.php`
- [ ] Import exécuté avec succès
- [ ] `import-data.php` supprimé
- [ ] Données vérifiées dans l'admin WordPress
- [ ] Images vérifiées

### Configuration Nuxt
- [ ] `nuxt.config.ts` mis à jour
- [ ] Fichier `.env` créé
- [ ] Variables d'environnement configurées
- [ ] Composables testés
- [ ] CORS configuré si nécessaire

### Tests
- [ ] Tous les endpoints testés avec cURL
- [ ] Application Nuxt testée en dev
- [ ] Toutes les pages fonctionnent
- [ ] Images s'affichent correctement
- [ ] Pas d'erreurs CORS
- [ ] Performance acceptable

### Déploiement
- [ ] Build de production testé
- [ ] Variables d'environnement de prod configurées
- [ ] Application déployée
- [ ] Tests post-déploiement
- [ ] Monitoring activé
- [ ] Documentation mise à jour

## 🐛 Dépannage

### Les données ne s'affichent pas

**Problème :** L'application Nuxt ne charge pas les données

**Solutions :**
1. Vérifiez l'URL API dans `.env`
2. Testez l'endpoint directement dans le navigateur
3. Vérifiez la console pour les erreurs CORS
4. Vérifiez que WordPress REST API est activé

```bash
# Test rapide
curl https://votresite.com/wp-json/eatisfamily/v1/activities
```

### Erreurs CORS

**Problème :** `Access-Control-Allow-Origin` error

**Solutions :**
1. Vérifiez `functions.php` - la fonction `eatisfamily_add_cors_headers()` doit être présente
2. Testez avec `Access-Control-Allow-Origin: *` d'abord
3. Puis restreignez à votre domaine Nuxt

### Images manquantes

**Problème :** Les images ne s'affichent pas

**Solutions :**
1. Vérifiez que les images ont été téléversées dans WordPress
2. Allez dans Médias > Bibliothèque pour voir les images
3. Vérifiez les permissions du dossier `wp-content/uploads`
4. Si nécessaire, téléversez manuellement les images

### 404 sur les endpoints

**Problème :** `/wp-json/eatisfamily/v1/activities` retourne 404

**Solutions :**
1. Allez dans Réglages > Permaliens
2. Cliquez sur "Enregistrer" (sans rien changer)
3. Cela régénère les règles de réécriture
4. Testez à nouveau l'endpoint

## 🚀 Prochaines étapes

Après la migration réussie :

1. **Formez votre équipe** à utiliser l'admin WordPress
2. **Configurez les backups** automatiques
3. **Installez des plugins** de sécurité (Wordfence, iThemes Security)
4. **Configurez SSL** si pas déjà fait
5. **Activez le cache** (W3 Total Cache, WP Super Cache)
6. **Moniteur les performances** (Query Monitor, New Relic)
7. **Planifiez les mises à jour** WordPress/PHP régulières

## 📞 Support

Besoin d'aide ? Contactez hello@eatisfamily.fr

---

**Temps estimé de migration:** 2-4 heures  
**Niveau de difficulté:** Intermédiaire  
**Dernière mise à jour:** Janvier 2026
