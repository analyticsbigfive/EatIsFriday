# Changelog - Eat Is Family WordPress Theme

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Versionnement Sémantique](https://semver.org/lang/fr/).

## [1.0.0] - 2026-01-23

### Ajouté
- Version initiale du thème WordPress
- Enregistrement de 4 Custom Post Types (Activity, Event, Job, Venue)
- 14 endpoints REST API complets
- Interface admin personnalisée avec meta boxes
- Colonnes personnalisées dans les listes admin
- Script d'import automatique des données JSON
- Support CORS pour les requêtes cross-origin
- Templates personnalisés pour Activities et Jobs
- Archive template pour Activities
- Documentation complète (README, QUICK-START, API-REFERENCE)
- Fichier .htaccess.example avec optimisations
- Support des images mises en avant
- Validation et sanitization des données
- Notice d'activation du thème

### Endpoints API
- `GET /eatisfamily/v1/activities` - Liste des activités
- `GET /eatisfamily/v1/activities/{slug}` - Activité par slug
- `GET /eatisfamily/v1/blog-posts` - Liste des articles
- `GET /eatisfamily/v1/blog-posts/{slug}` - Article par slug
- `GET /eatisfamily/v1/events` - Liste des événements
- `GET /eatisfamily/v1/events/{id}` - Événement par ID
- `GET /eatisfamily/v1/jobs` - Liste des emplois (avec filtres)
- `GET /eatisfamily/v1/jobs/{slug}` - Emploi par slug
- `GET /eatisfamily/v1/venues` - Liste des lieux avec métadonnées
- `GET /eatisfamily/v1/venues/{id}` - Lieu par ID
- `GET /eatisfamily/v1/site-content` - Contenu global du site
- `GET /eatisfamily/v1/pages-content` - Contenu des pages

### Custom Post Types
- **Activity** avec 9 champs personnalisés
- **Event** avec 3 champs personnalisés
- **Job** avec 6 champs personnalisés
- **Venue** avec 8 champs personnalisés

### Documentation
- README.md (guide complet, 500+ lignes)
- QUICK-START.md (installation rapide)
- API-REFERENCE.md (référence API complète)
- FILES-STRUCTURE.md (structure des fichiers)
- CHANGELOG.md (ce fichier)

### Compatibilité
- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+

## [Unreleased]

### À venir dans les futures versions

#### Version 1.1.0 (Planifié)
- [ ] Endpoint POST pour créer des activités via API
- [ ] Authentification JWT pour les endpoints protégés
- [ ] Endpoint pour les réservations d'activités
- [ ] Webhooks pour notifications
- [ ] Cache API intégré
- [ ] Rate limiting natif

#### Version 1.2.0 (Planifié)
- [ ] Dashboard analytics
- [ ] Export de données en CSV
- [ ] Import CSV pour données bulk
- [ ] Multi-langue (WPML/Polylang support)
- [ ] Templates Gutenberg blocks

#### Version 2.0.0 (Futur)
- [ ] Système de réservation complet
- [ ] Paiement en ligne intégré
- [ ] Gestion des utilisateurs avancée
- [ ] Notifications email automatiques
- [ ] API v2 avec GraphQL

### Améliorations envisagées
- [ ] Tests unitaires PHP
- [ ] Tests d'intégration API
- [ ] CI/CD pipeline
- [ ] Docker configuration
- [ ] Performance monitoring
- [ ] SEO optimization automatique
- [ ] Sitemap XML automatique

### Bugs connus
Aucun bug connu à ce jour. Rapportez les bugs à hello@eatisfamily.fr

## Notes de migration

### Depuis JSON statique vers WordPress

Si vous migrez depuis l'API JSON statique vers WordPress:

1. **Mettre à jour nuxt.config.ts**
   ```typescript
   runtimeConfig: {
     public: {
       // Ancien
       // apiBase: '/api'
       
       // Nouveau
       apiBase: 'https://votresite.com/wp-json/eatisfamily/v1'
     }
   }
   ```

2. **Les composables restent identiques**
   - Pas de changement dans la structure de code
   - Seule l'URL de base change
   - Les réponses JSON sont compatibles

3. **Migration des données**
   - Utilisez `import-data.php` pour importer automatiquement
   - Ou importez manuellement via l'admin WordPress

4. **URLs des images**
   - Les images doivent être téléversées dans WordPress
   - Ou pointez vers des URLs complètes

## Support et contribution

### Rapporter un bug
1. Vérifiez que le bug n'existe pas déjà
2. Créez une issue sur GitHub avec:
   - Version de WordPress
   - Version de PHP
   - Description du problème
   - Steps to reproduce
   - Expected vs actual behavior

### Demander une fonctionnalité
1. Créez une issue avec le tag "enhancement"
2. Décrivez le cas d'usage
3. Expliquez les bénéfices attendus

### Contribuer
1. Fork le repository
2. Créez une branche pour votre feature
3. Committez vos changements
4. Ouvrez une Pull Request

## Remerciements

- Équipe Eat Is Family
- Contributeurs WordPress
- Communauté open source

## Licence

GPL-2.0-or-later - voir LICENSE pour détails

---

**Dernière mise à jour:** 23 janvier 2026  
**Mainteneur:** Eat Is Family Team  
**Contact:** hello@eatisfamily.fr
