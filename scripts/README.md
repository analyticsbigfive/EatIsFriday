# Scripts - Eat Is Family

Ce dossier contient les scripts utilitaires pour le projet.

## 📋 Scripts disponibles

### validate-json.ts

Script de validation TypeScript pour vérifier la structure et l'intégrité des fichiers JSON.

#### Utilisation

```bash
# Méthode 1 : Via npm (recommandé)
npm run validate:json

# Méthode 2 : Directement avec npx
npx tsx scripts/validate-json.ts
```

#### Fonctionnalités

- ✅ **Validation syntaxique JSON** - Détecte les erreurs de syntaxe
- ✅ **Détection des clés dupliquées** - Trouve les clés JSON en double
- ✅ **Validation des structures** - Vérifie que les objets ont les champs requis
- ✅ **Rapport visuel** - Affiche un tableau ASCII avec les résultats
- ✅ **Code de sortie** - Exit code 0 si valide, 1 sinon (parfait pour CI/CD)

#### Fichiers validés

| Fichier | Validation de structure |
|---------|------------------------|
| `blog-posts.json` | ✅ Vérifie id, slug, title, excerpt, content, date, featured_media |
| `activities.json` | ✅ Vérifie id, slug, title, description, date, location, capacity, category |
| `events.json` | ✅ Vérifie id, title, image, description, event_type |
| `jobs.json` | ✅ Vérifie id, slug, title, venue_id, department, job_type, requirements, benefits |
| `pages-content.json` | ✅ Validation JSON uniquement |
| `site-content.json` | ✅ Validation JSON uniquement |
| `venues.json` | ✅ Validation JSON uniquement |

#### Exemple de sortie

```
🔍 Validation des fichiers JSON...

┌────────────────────────┬────────┬─────────┬──────────┬──────────┐
│ Fichier                │ Taille │ Statut  │ Erreurs  │ Warnings │
├────────────────────────┼────────┼─────────┼──────────┼──────────┤
│ blog-posts.json        │ 9.3K   │ ✅ OK   │ 0        │ 0        │
│ activities.json        │ 5.4K   │ ✅ OK   │ 0        │ 0        │
│ events.json            │ 2.8K   │ ✅ OK   │ 0        │ 0        │
│ jobs.json              │ 26.3K  │ ✅ OK   │ 0        │ 0        │
│ pages-content.json     │ 31.6K  │ ✅ OK   │ 0        │ 0        │
│ site-content.json      │ 27.7K  │ ✅ OK   │ 0        │ 0        │
│ venues.json            │ 18.2K  │ ✅ OK   │ 0        │ 0        │
└────────────────────────┴────────┴─────────┴──────────┴──────────┘

📊 Total: 7/7 fichiers valides
   Erreurs: 0
   Warnings: 0

✅ Tous les fichiers JSON sont valides!
```

#### Intégration CI/CD

Ajoutez la validation JSON à votre pipeline GitHub Actions :

```yaml
# .github/workflows/validate.yml
name: Validate JSON
on: [push, pull_request]

jobs:
  validate:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with:
          node-version: '18'
      - run: npm install -g tsx
      - run: npm run validate:json
```

#### Pre-commit hook

Pour valider automatiquement avant chaque commit :

```bash
# Installation de husky
npm install --save-dev husky
npx husky init

# Créer le hook
echo "npm run validate:json" > .husky/pre-commit
chmod +x .husky/pre-commit
```

---

## 🔧 Développement

### Ajouter un nouveau script

1. Créer le fichier dans `/scripts/`
2. Utiliser TypeScript pour le typage
3. Ajouter une commande npm dans `package.json` :

```json
{
  "scripts": {
    "mon-script": "npx tsx scripts/mon-script.ts"
  }
}
```

### Conventions

- ✅ Utiliser TypeScript (`.ts`)
- ✅ Ajouter une description en en-tête
- ✅ Exporter les fonctions principales
- ✅ Gérer les codes de sortie (0 = succès, 1 = erreur)
- ✅ Afficher des messages clairs et visuels

---

## 📚 Documentation associée

- [JSON_OPTIMIZATION_COMPLETE.md](../JSON_OPTIMIZATION_COMPLETE.md) - Résumé complet de l'optimisation JSON
- [JSON_AUDIT_REPORT.md](../JSON_AUDIT_REPORT.md) - Rapport d'audit initial
- [CLEANUP_JSON_SCRIPT.md](../CLEANUP_JSON_SCRIPT.md) - Guide de nettoyage

---

**Dernière mise à jour :** $(date +"%Y-%m-%d")
