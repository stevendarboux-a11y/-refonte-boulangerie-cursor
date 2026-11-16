# Audit sécurité et accessibilité

Audite le dossier `site/` en lecture seule. Ne modifie aucun fichier.

## Sécurité
- Toute donnée affichée passe par `e()` (y compris dans les attributs `value`).
- Le formulaire : jeton CSRF vérifié avec `hash_equals`, honeypot, validation serveur de chaque champ, redirection après succès.
- Aucune erreur PHP affichée, aucun secret dans le code.
- Rien de sensible accessible depuis `site/public/`.

## Accessibilité (WCAG AA)
- Un seul `h1` par page, titres dans l'ordre.
- Chaque champ a un `label`, les erreurs sont reliées avec `aria-describedby`.
- Navigation complète au clavier, focus visible, lien d'évitement.
- `lang="fr"`, images avec `alt`, contrastes 4,5:1 minimum.

## Format de la réponse
Un tableau : gravité (critique / moyen / mineur), fichier:ligne, problème, preuve (la ligne de code).
Ne signale que ce que tu as vu dans le code. Si tu n'es pas sûr, écris « à vérifier ».
Termine par la liste des points vérifiés et conformes.