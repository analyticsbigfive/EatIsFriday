# 🧹 Script de nettoyage JSON - Champs inutilisés

## Actions à effectuer pour optimiser les fichiers JSON

---

## 1️⃣ Nettoyer `site-content.json`

### ❌ SUPPRIMER le bloc `home.*` complet

**Ligne à supprimer:** Tout le bloc `"home": { ... }`

**Raison:** Doublon avec `pages-content.json → homepage`, jamais utilisé

**Avant (environ ligne 35):**
```json
{
  "site": { ... },
  "home": {
    "seo": { ... },
    "hero": { ... },
    "intro": { ... },
    "features": [ ... ],
    "stats": [ ... ],
    ...
  }
}
```

**Après:**
```json
{
  "site": { ... }
}
```

**Économie:** ~200 lignes de JSON

---

## 2️⃣ Nettoyer `pages-content.json` → `homepage`

### Section hero_section - Supprimer les champs inutilisés

**Champs à SUPPRIMER:**

```json
"hero_section": {
  "bg": "...",           // ✅ GARDÉ - utilisé
  "tag": "...",          // ❌ SUPPRIMER
  "title": { ... },      // ✅ GARDÉ - utilisé
  "description": "...",  // ❌ SUPPRIMER
  "cta_primary": { ... },    // ❌ SUPPRIMER
  "cta_secondary": { ... },  // ❌ SUPPRIMER
  "images": [ ... ],         // ❌ SUPPRIMER (3 images)
  "experience_badge": { ... },  // ❌ SUPPRIMER
  "floating_badge": "..."       // ❌ SUPPRIMER
}
```

**Résultat après nettoyage:**
```json
"hero_section": {
  "bg": "images/unsplash_6vfYbDwOuMo.jpg",
  "title": {
    "line_1": "Feeding\nExperiences.\n",
    "line_2": "Serving\nMoments",
    "line_3": "From intimate gatherings..."
  }
}
```

**Économie:** ~50 lignes

---

### Section services_section - Supprimer champs inutilisés

**Champs à SUPPRIMER:**

```json
"services_section": {
  "tag": "OUR SERVICES",           // ❌ SUPPRIMER
  "title": { ... },                 // ✅ GARDÉ
  "services": [ ... ],              // ✅ GARDÉ
  "learn_more_button": "Learn more" // ❌ SUPPRIMER
}
```

**Économie:** 2 lignes

---

### Bloc _note_locations - Supprimer

```json
"_note_locations": "Venues data is now in venues.json - use useVenues() composable",
```

**Raison:** Note de développeur, pas de données

**Économie:** 1 ligne

---

## 3️⃣ Nettoyer les blocs `seo.*` dans `pages-content.json`

### Pages concernées
- `homepage.seo.*`
- `events.seo.*`
- `apply_activities.seo.*`
- Toutes les autres pages

### Vérification nécessaire

**Avant de supprimer, vérifier si utilisé dans:**
```bash
grep -r "content.*seo" app/pages/
grep -r "useHead.*seo" app/pages/
```

**Si NON utilisé dans `useHead()`:** SUPPRIMER tous les blocs `seo.*`

**Économie potentielle:** ~100-150 lignes

---

## 4️⃣ Plan d'action détaillé

### Ordre recommandé

1. **Backup avant modification**
   ```bash
   cp public/api/site-content.json public/api/site-content.json.backup
   cp public/api/pages-content.json public/api/pages-content.json.backup
   ```

2. **Nettoyer `site-content.json`**
   - Supprimer le bloc `home.*` complet
   - Sauvegarder

3. **Nettoyer `pages-content.json`**
   - Supprimer champs hero_section inutilisés
   - Supprimer `_note_locations`
   - Supprimer `services_section.tag` et `learn_more_button`
   - Sauvegarder

4. **Tester l'application**
   ```bash
   npm run dev
   ```
   - Vérifier `/` (homepage)
   - Vérifier `/events`
   - Vérifier `/apply-activities`
   - Vérifier la console pour erreurs

5. **Si tout fonctionne, vérifier les blocs SEO**
   - Chercher usage dans le code
   - Supprimer si inutilisé
   - Tester à nouveau

---

## 🧪 Tests après nettoyage

### Checklist de vérification

- [ ] La homepage charge correctement
- [ ] L'image hero s'affiche
- [ ] Les titres sont corrects
- [ ] Les sections services s'affichent
- [ ] La page events fonctionne
- [ ] La page apply-activities fonctionne
- [ ] Aucune erreur dans la console
- [ ] Aucun `undefined` visible

### Commandes de test

```bash
# 1. Lancer le serveur de dev
npm run dev

# 2. Vérifier chaque page
open http://localhost:3000
open http://localhost:3000/events
open http://localhost:3000/apply-activities
open http://localhost:3000/careers
open http://localhost:3000/about

# 3. Vérifier les logs de la console (DevTools)
# Chercher des erreurs liées à des propriétés manquantes
```

---

## 📊 Impact attendu

| Fichier | Avant | Après | Économie |
|---------|-------|-------|----------|
| `site-content.json` | ~636 lignes | ~430 lignes | **~30%** |
| `pages-content.json` | ~773 lignes | ~620 lignes | **~20%** |
| **Total** | 1409 lignes | 1050 lignes | **~25%** |

### Bénéfices

- ✅ **Performance:** Chargement initial plus rapide
- ✅ **Maintenance:** Code plus clair, moins de confusion
- ✅ **Bugs:** Moins de champs inutilisés = moins de bugs potentiels
- ✅ **Documentation:** Structure plus simple à comprendre

---

## ⚠️ Précautions

1. **Toujours faire un backup avant**
2. **Tester après chaque modification majeure**
3. **Vérifier la console pour les erreurs**
4. **Garder les backups pendant 1 semaine**

---

## 🔄 Rollback si problème

```bash
# Restaurer les fichiers originaux
cp public/api/site-content.json.backup public/api/site-content.json
cp public/api/pages-content.json.backup public/api/pages-content.json

# Relancer le serveur
npm run dev
```
