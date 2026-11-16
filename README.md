# Boulangerie Lefèvre

## Présentation

Site fictif d’une boulangerie à Lyon, refait avec Cursor. L’original de 2011 est dans `legacy-site/` : trois pages HTML en tableaux (`index.html`, `produits.html`, `contact.html`) et quelques lignes de CSS. La refonte est dans `site/`. `legacy-site/` reste la référence et n’est pas modifié.

Les prix, horaires, adresse et actualités viennent de l’ancien site. Les accents manquants sont corrigés (« Bienvenue à », « Lefèvre », « équipe »). Le détail des problèmes est dans `AUDIT.md`, le détail de la refonte dans `PLAN.md`.

## Avant / après

| Problème dans `legacy-site/` | Solution dans `site/` |
| --- | --- |
| Le formulaire affiche un succès alors que rien n’est envoyé. Les champs partent en GET dans l’URL. | `contact.php` reçoit un POST, valide les champs, enregistre le message, puis redirige. Les valeurs saisies ne restent pas dans l’adresse. |
| Mise en page dans un tableau de 900 px, sans viewport. Sur un téléphone, la page entière est réduite. | HTML5, mobile d’abord, balise viewport. Les colonnes s’empilent, les champs prennent toute la largeur. |
| La promo du dimanche est un `<marquee>` qui défile sans pause. | Paragraphe fixe sur l’accueil. |
| Pas de doctype. Balises `font` et `center`. Langue et encodage non déclarés. | `<!DOCTYPE html>`, `lang="fr"`, `charset=utf-8`, régions `header`, `nav`, `main`, `footer`. |
| Les champs n’ont pas d’étiquette. Les erreurs passent par `alert()`. | Chaque champ a un `label`. L’erreur s’affiche à côté du champ. |
| Menu blanc sur orange (3,63:1). Liens non soulignés, presque illisibles au survol. | Brun `#4A2C1A` et crème `#FFF8EE`. Texte au-dessus de 4,5:1. Liens soulignés. |
| Tableaux de prix : cellules en gras, pas de légende. Un lecteur d’écran ne relie pas le prix au produit. | `<caption>` et `<th scope="col">` pour les pains, viennoiseries et pâtisseries. |
| En-tête, menu et pied copiés dans chaque fichier. Le téléphone manque au pied de la page contact. | `header.php` et `footer.php` inclus partout. Le pied (adresse et téléphone) est le même sur les trois pages. |
| Titres sans le nom de la boutique. Adresse et horaires seulement en texte libre. | `title` et meta description par page. JSON-LD `Bakery` : adresse, téléphone, horaires. |
| Trois images demandées (`devanture`, `vitrine`, `plan`) absentes du dossier, sans `alt`. | Aucune image ajoutée. L’adresse est dans un `address`, le téléphone dans un lien `tel:`. |

## Stack et choix techniques

PHP 8, sans framework. Le site a trois pages et un formulaire : les includes et le traitement POST suffisent.

Tailwind CSS v4, compilé avec la CLI. Le thème (couleurs, polices) est dans `site/src/app.css`, via `@theme`. Le fichier servi est `site/public/css/app.css`, versionné : ouvrir le site ne demande que PHP.

Polices déjà présentes sur la machine (`font-serif` pour les titres, `font-sans` pour le texte). Aucun fichier de police à télécharger.

Le contenu est dans `site/data/` : chaque fichier PHP retourne un tableau (`boutique.php`, `horaires.php`, `actus.php`, `produits.php`). Les pages affichent ces tableaux au lieu de recopier les prix et l’adresse.

`site/includes/ouverture.php` indique si la boutique est ouverte, d’après les horaires et le fuseau `Europe/Paris`. Lundi fermé. Mardi–samedi 6 h 30–19 h 30. Dimanche 7 h–13 h.

```
site/
  src/app.css          source Tailwind
  public/              index.php, produits.php, contact.php, css/app.css
  includes/            bootstrap, en-tête, pied, statut ouvert/fermé
  data/                contenu et messages reçus
```

Les messages sont ajoutés, une ligne JSON par envoi, dans `site/data/messages/messages.jsonl`. Ce dossier est hors de `public/` : le serveur PHP (`-t site/public`) ne le sert pas. Le fichier est ignoré par git.

## Sécurité

`site/includes/bootstrap.php` définit `e()`, qui passe toute donnée affichée par `htmlspecialchars()` (guillemets inclus, UTF-8). Les erreurs PHP sont journalisées (`display_errors` à 0). Le cookie de session est `HttpOnly` et `SameSite=Lax` (`Secure` si la page est en HTTPS).

Le formulaire est en POST. Un jeton CSRF est stocké en session et comparé avec `hash_equals`. Après un enregistrement, le jeton est renouvelé et le navigateur est renvoyé vers `contact.php` (code 303) : le message de succès est en session.

Contrôles serveur, après `trim` : nom obligatoire (100 caractères), e-mail obligatoire (`filter_var`, 254 caractères), objet parmi les trois valeurs proposées, message obligatoire (2 000 caractères). Les attributs HTML (`required`, `maxlength`, `type="email"`) ne font que guider la saisie.

Un champ honeypot (`champ_controle`), masqué et retiré de l’ordre de tabulation, sert de filtre anti-spam. S’il contient du texte, la page affiche le même succès qu’un envoi réel et n’écrit rien.

Chaque ligne enregistrée contient la date (fuseau `Europe/Paris`), le nom, l’e-mail, l’objet et le message. Si l’écriture échoue, la page le dit et n’affiche pas de succès. À côté du formulaire, une phrase indique que ces données servent à répondre à la demande et ne sont pas publiées.

## Accessibilité

Objectif WCAG AA.

- Un seul `h1` par page. Lien « Aller au contenu ». Menu avec `aria-current="page"` sur la page courante.
- Chaque champ a un `label`. En cas d’erreur : `aria-invalid` et `aria-describedby` vers le texte d’erreur. Pas de boîte `alert()`.
- Focus visible (anneau brun et crème) sur les liens, boutons et champs. Liens soulignés. Aucun texte qui défile.
- Contrastes du texte courant, seuil 4,5:1 : encre `#2B2118` sur crème `#FFF8EE` (14,93:1), blanc sur brun `#4A2C1A` (12,60:1), liens `#6B3A12` sur crème (8,89:1). Erreur et succès ont leur propre paire texte / fond (7,14:1 et 8,11:1).
- Tableaux de tarifs avec légende et en-têtes de colonne. La page tient sans défilement horizontal à 320 px.

## Lancer le projet

PHP 8. Depuis la racine du dépôt :

```bash
php -S localhost:8000 -t site/public
```

Puis ouvrir http://localhost:8000.

Node sert seulement à recompiler le CSS, à la racine du dépôt :

```bash
npm install
npm run css:watch    # recompile site/public/css/app.css à chaque changement
npm run css:build    # une fois, CSS minifié
```

## Utilisation de Cursor

**Règles.** `.cursor/rules/projet.mdc` est appliqué à chaque modification (`alwaysApply`). Le fichier fixe la stack, le HTML sémantique, le responsive, le niveau WCAG AA, les contrôles du formulaire, et le fait de laisser `legacy-site/` intact.

**`AGENTS.md`.** Contexte du dépôt pour l’agent : objectif de la refonte, commandes pour lancer le site et Tailwind, rôle de `legacy-site/`, `site/public/`, `site/includes/` et `site/data/`, pointeurs vers `AUDIT.md`, `PLAN.md` et `.cursor/rules/`.

**Mode Plan.** Il sert à écrire le plan avant de modifier le code. Ici, ce plan est `PLAN.md` : arborescence, couleurs, formulaire, puis une fonctionnalité par étape.

**Commande `/audit`.** Définie dans `.cursor/commands/audit.md`. Elle relit `site/` sans rien modifier et répond par un tableau : gravité (critique, moyen, mineur), fichier et ligne, problème, extrait de code. Les points déjà conformes sont listés à la fin.
