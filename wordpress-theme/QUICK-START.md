# 🚀 Guide d'Installation Rapide - WordPress Theme v2.0

## Installation WordPress (5 minutes)

### Étape 1 : Téléverser le thème
1. Téléchargez `wordpress-theme-v2.0.zip`
2. Dans WordPress admin, allez dans **Apparence > Thèmes**
3. Cliquez sur **Ajouter** puis **Téléverser un thème**
4. Sélectionnez le fichier ZIP et cliquez sur **Installer maintenant**

### Étape 2 : Activer le thème
1. Après l'installation, cliquez sur **Activer**
2. Le thème est maintenant actif

### Étape 3 : Configurer les permaliens
⚠️ **IMPORTANT** - Nécessaire pour que les endpoints API fonctionnent
1. Allez dans **Réglages > Permaliens**
2. Sélectionnez **Nom de l'article**
3. Cliquez sur **Enregistrer les modifications**

### Étape 4 : Vérifier les Custom Post Types
Vous devriez maintenant voir dans le menu admin:
- ✅ Activities
- ✅ Events
- ✅ Jobs
- ✅ Venues
- ✅ Timeline
- ✅ Site Content (nouveau menu)

### Étape 5 : Tester l'API
Ouvrez dans votre navigateur:
```
https://votre-site.com/wp-json/eatisfamily/v1/activities
```

Vous devriez voir une réponse JSON (même si vide au début).

---

## Premier Contenu (10 minutes)

### Créer une Activité
1. Allez dans **Activities > Ajouter**
2. Remplissez:
   - Titre: "Italian Cooking Workshop"
   - Contenu: "Learn to prepare authentic Italian dishes..."
   - Image mise en avant
   - Date: Sélectionnez une date/heure
   - Localisation: "Paris, France"
   - Prix: "€85"
   - Capacité: 12
3. Cliquez sur **Publier**

### Créer un Job
1. Allez dans **Jobs > Ajouter**
2. Remplissez:
   - Titre: "Head Chef – VIP Hospitality"
   - Contenu: Description du poste
   - Venue: Sélectionnez dans la liste
   - Département: "Culinary"
   - Requirements: Ajoutez des items
3. Cliquez sur **Publier**

### Créer un Venue
1. Allez dans **Venues > Ajouter**
2. Remplissez:
   - Titre: "Stade Jean Bouin"
   - Contenu: Description du lieu
   - Localisation: "Paris, France"
   - Coordonnées GPS: Lat/Long
3. Cliquez sur **Publier**

### Configurer Site Content
1. Allez dans **Site Content > Site Content**
2. Remplissez:
   - Nom du site: "Eat Is Family"
   - Email: votre@email.com
   - Réseaux sociaux: URLs
3. Cliquez sur **Enregistrer**

### Configurer Pages Content
1. Allez dans **Site Content > Pages Content**
2. Remplissez les différents onglets:
   - Homepage: Hero title, CTA, etc.
   - About: Hero, intro
   - Contact: Formulaire
3. Cliquez sur **Enregistrer**

---

## Configuration Nuxt.js (5 minutes)

### Étape 1 : Mettre à jour nuxt.config.ts
```typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: 'https://votre-wordpress.com/wp-json/eatisfamily/v1',
      useLocalFallback: false,
    }
  }
})
```

### Étape 2 : Créer un fichier .env
```env
NUXT_PUBLIC_API_BASE=https://votre-wordpress.com/wp-json/eatisfamily/v1
```

### Étape 3 : Tester
```bash
npm run dev
```

Visitez votre site Nuxt et vérifiez que les données s'affichent.

---

## Import de Données Existantes (Optionnel)

Si vous avez des fichiers JSON existants:

### Étape 1 : Copier les fichiers JSON
Copiez vos fichiers JSON dans `/wp-content/themes/eatisfamily/data/`:
- `activities.json`
- `events.json`
- `jobs.json`
- `venues.json`
- `blog-posts.json`
- `site-content.json`
- `pages-content.json`

### Étape 2 : Importer via Admin
1. Allez dans **Site Content > Data Management**
2. Cochez les contenus à importer
3. Cliquez sur **Import Selected Data**
4. Attendez la confirmation

⚠️ **Note:** L'import ne remplacera pas le contenu existant, il n'ajoutera que les nouveaux éléments.

---

## Vérification Finale

### ✅ Checklist Post-Installation

- [ ] Thème activé
- [ ] Permaliens configurés
- [ ] Custom Post Types visibles
- [ ] Au moins 1 contenu créé par type
- [ ] Site Content configuré
- [ ] Pages Content configuré
- [ ] API testée dans le navigateur
- [ ] Nuxt.js configuré
- [ ] Application Nuxt démarrée
- [ ] Données s'affichent dans Nuxt

### 🧪 Tests API

```bash
# Activities
curl https://votre-site.com/wp-json/eatisfamily/v1/activities

# Jobs
curl https://votre-site.com/wp-json/eatisfamily/v1/jobs

# Venues
curl https://votre-site.com/wp-json/eatisfamily/v1/venues

# Site Content
curl https://votre-site.com/wp-json/eatisfamily/v1/site-content

# Pages Content
curl https://votre-site.com/wp-json/eatisfamily/v1/pages-content
```

Toutes les requêtes doivent retourner du JSON valide.

---

## Problèmes Courants

### ❌ Endpoints retournent 404
**Solution:** Allez dans Réglages > Permaliens et cliquez sur "Enregistrer" (sans rien changer)

### ❌ Erreur CORS dans Nuxt
**Solution:** Les headers CORS sont déjà configurés. Si le problème persiste, vérifiez que l'URL WordPress dans nuxt.config.ts est correcte.

### ❌ Images ne s'affichent pas
**Solution:** Vérifiez les permissions du dossier `wp-content/uploads` (doit être 755)

### ❌ Meta boxes ne s'affichent pas
**Solution:** Vérifiez que les fichiers `inc/meta-boxes.php` et `inc/admin-pages.php` existent et sont bien inclus dans `functions.php`

---

## 📚 Documentation Complète

Pour plus de détails, consultez:
- `README.md` - Guide complet
- `NUXT-INTEGRATION.md` - Intégration Nuxt détaillée
- `CHANGELOG.md` - Historique des versions
- `V2-RELEASE-NOTES.md` - Notes de version

---

## 🆘 Support

Besoin d'aide ?
- **Email:** hello@eatisfamily.fr
- **Check logs:** `wp-content/debug.log`
- **Test API:** Utilisez curl ou Postman

---

**Durée totale:** ~20 minutes  
**Niveau:** Intermédiaire  
**Version:** 2.0.0  
**Dernière mise à jour:** 27 janvier 2026
