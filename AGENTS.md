# Boulangerie Lefèvre : refonte

Refonte du site `legacy-site/` (2011, HTML en tableaux) en PHP 8 + Tailwind CSS, dans `site/`.
Site fictif, réalisé pour pratiquer Cursor.

## Lancer le projet
```bash
php -S localhost:8000 -t site/public    # puis ouvrir http://localhost:8000
```
Tailwind (à lancer à la racine du dépôt, pendant le travail sur les styles) :
```bash
npm run css:watch    # recompile site/public/css/app.css à chaque changement
npm run css:build    # une fois, CSS minifié
```

## Arborescence
- `legacy-site/` : ancien site, référence du « avant ». Ne pas modifier.
- `site/public/` : pages accessibles par le navigateur (index.php, produits.php, contact.php) et ressources (CSS, images).
- `site/includes/` : morceaux communs inclus dans chaque page (en-tête, menu, pied de page).
- `site/data/` : contenu (produits, horaires, actus) et messages reçus par le formulaire.

## Documents
- `AUDIT.md` : problèmes de l'ancien site.
- `PLAN.md` : plan de la refonte.
- `.cursor/rules/` : règles appliquées par l'agent.