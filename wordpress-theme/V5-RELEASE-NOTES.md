# EatIsFamily Theme v5.0 - Release Notes

## Date : 27 janvier 2026

## 🎉 Nouvelles fonctionnalités

### Interface d'administration unifiée

La version 5.0 introduit une interface d'administration WordPress complètement repensée et unifiée, accessible via le menu **"EatIsFamily"** dans le tableau de bord WordPress.

### Nouveaux menus d'administration

| Menu | Description |
|------|-------------|
| **EatIsFamily** | Dashboard principal avec vue d'ensemble |
| **Site Content** | Paramètres globaux du site (SEO, contact, réseaux sociaux) |
| **Pages Content** | Contenu de toutes les pages (Homepage, About, Contact, Careers, Events, Blog, Job Detail, Apply Activities) |
| **Forms & Labels** | Configuration de tous les formulaires |
| **Components** | Gestion du Header et Footer |
| **Partners** | Gestion des logos partenaires |
| **Services** | Gestion des services |
| **Sustainability** | Gestion de la section durabilité |
| **Gallery** | Gestion de la galerie |
| **Data Management** | Import/export des données |

---

## 📝 Section "Forms & Labels" (NOUVELLE)

Cette section permet de gérer tous les textes des formulaires depuis l'interface d'administration :

### 🔍 Job Search Form (Homepage Hero)
- **Form Title** : "Find Your Perfect Role" → Modifiable
- **Form Subtitle** : "Explore open positions across France" → Modifiable
- **Job Title Placeholder** : "Select job title" → Modifiable
- **Site Placeholder** : "Select sites" → Modifiable
- **All Jobs Label** : "All job titles" → Modifiable
- **All Sites Label** : "All sites" → Modifiable
- **Search Button** : "Search" → Modifiable
- **Loading Text** : "Loading..." → Modifiable

### 📧 Contact Form
- Labels et placeholders pour tous les champs (nom, email, sujet, message)
- Textes des boutons (Submit, Submitting)
- Messages de succès et d'erreur

### 💼 Job Application Form
- Tous les labels (First Name, Last Name, Email, Phone, Resume, Cover Letter)
- Tous les placeholders
- Messages de feedback

### 🎯 Activity Registration Form
- Labels et placeholders complets
- Support des restrictions alimentaires
- Informations additionnelles

---

## 🧩 Section "Components" (NOUVELLE)

Gestion des composants globaux :

### 🔝 Header / Navigation
- Logo Text
- Liens de navigation : About, Activities, Events, Careers, Blog, Contact

### 🔻 Footer
- Logo Footer
- Brand Name & Description
- Contact Email & Phone
- Titres des sections Company et Policy
- Copyright Text (avec support de `{year}` pour l'année actuelle)
- Texte "Back to top"

---

## 📄 Pages Content amélioré

Toutes les pages sont maintenant administrables avec onglets :

- **🏠 Homepage** : Hero, Intro, Services, CTA, Beautiful, Partners, SEO
- **ℹ️ About** : Hero, Section Titles
- **📧 Contact** : Hero, Form Section
- **💼 Careers** : Hero, Join Box
- **🎉 Events** : Hero Section
- **📝 Blog** : Index Page, Detail Page
- **📋 Job Detail** : CTA Banner, Job Description Labels, Quick Facts Labels
- **🎯 Apply Activities** : Page Hero, Page Text

---

## 📁 Fichiers modifiés/créés

| Fichier | Action | Description |
|---------|--------|-------------|
| `inc/admin-pages-v5.php` | ✨ CRÉÉ | Nouvelle interface unifiée v5 |
| `functions.php` | ✏️ MODIFIÉ | Inclusion du nouveau fichier v5 |

---

## 🔧 Compatibilité

- ✅ Compatible avec les versions précédentes (v4.x)
- ✅ Les anciens menus restent disponibles
- ✅ Les données existantes sont préservées
- ✅ Support complet du fichier `pages-content.json`

---

## 📍 Comment accéder aux nouvelles fonctionnalités

1. Connectez-vous à l'admin WordPress
2. Dans le menu latéral, cherchez **"EatIsFamily"** (icône 🍴)
3. Cliquez sur **"Forms & Labels"** pour modifier les textes du Job Search Form
4. Cliquez sur **"Components"** pour gérer Header et Footer
5. Cliquez sur **"Pages Content"** pour le contenu des pages

---

## ⚠️ Notes importantes

- Les données sont stockées dans les options WordPress (`eatisfamily_forms`, `eatisfamily_components`, `eatisfamily_pages_content`)
- Pour que les modifications soient visibles sur le site Nuxt, assurez-vous que l'API exporte correctement ces données
- Sauvegardez avant toute modification majeure

---

## 🔜 Prochaines étapes suggérées

1. Tester tous les champs de formulaire
2. Configurer l'export vers `pages-content.json` depuis les options WordPress
3. Ajouter les sections manquantes si nécessaire (Cards, Map labels, etc.)
