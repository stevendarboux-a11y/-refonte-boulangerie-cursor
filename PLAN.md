# Plan de refonte — Boulangerie Lefèvre

Refonte de `legacy-site/` en PHP 8 et Tailwind CSS v4 dans `site/`, en reprenant prix, horaires, adresse et actus, avec un formulaire qui enregistre vraiment les messages. Ne pas modifier `legacy-site/`.

## 1. Arborescence

```
site/
  src/app.css                 source Tailwind (tokens + @import)
  public/                     racine du serveur PHP
    index.php                 accueil
    produits.php              tarifs
    contact.php               coordonnées + traitement POST du formulaire
    css/app.css               CSS compilé, versionné (le site tourne sans npm)
  includes/
    bootstrap.php             session, erreurs masquées, e(), jeton CSRF
    header.php                doctype, head, lien d'évitement, header, nav
    footer.php                ferme main, pied de page, JSON-LD
    ouverture.php             est_ouvert() d'après les horaires
  data/
    boutique.php              nom, adresse, téléphone, e-mail, délai 48 h
    horaires.php              plages par jour
    actus.php                 les trois actualités
    produits.php              pains, viennoiseries, pâtisseries
    messages/.gitkeep         dossier des messages (hors web)
    messages/messages.jsonl   créé à l'exécution, ignoré par git
```

Rôles :

- **Pages** (`site/public/*.php`) : chacune charge le bootstrap, fixe `$titre` et `$description`, inclut l'en-tête, écrit un seul `h1` dans `<main id="contenu">`, inclut le pied. Liens : `index.php`, `produits.php`, `contact.php`.
- **`bootstrap.php`** : `session_start` (cookie `HttpOnly`, `SameSite=Lax`), `display_errors` à 0, journal dans le terminal de `php -S`. Fonction `e()` = `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. Jeton CSRF en session (`random_bytes`).
- **`header.php` / `footer.php`** : le nom dans l'en-tête est un lien, pas un `h1`. Menu : Accueil, Nos produits, Contact, avec `aria-current="page"`. Pied identique partout : 12 rue Paul Bert, 69003 Lyon, et le téléphone (la page contact de l'ancien site l'avait retiré).
- **`data/*.php`** : chaque fichier `return` un tableau. Les pages ne recopient pas les prix ni l'adresse.
- **Messages** : hors de `public/`, donc non servis par `php -S localhost:8000 -t site/public`.

Contenu repris tel quel, accents corrigés : « Bienvenue à », « Lefèvre » partout, « équipe ». Prix, horaires, trois actus (dates sans année : `01/12`, `15/09`, `01/06`), adresse, `04 78 00 00 00`, `contact@boulangerie-lefevre.example`, délai de 48 h, « Gâteaux sur commande : nous appeler. »

La mention « Site mis à jour le 12/03/2011 » n'est pas reprise : sur un site refait en 2026, elle donnerait l'impression d'un site abandonné.

Les fichiers `img/devanture.jpg`, `img/vitrine.jpg` et `img/plan.gif` n'existent pas : aucune balise `img`, aucune photo inventée. L'adresse dans un `<address>` et un lien `tel:+33478000000` remplacent le plan.

La fermeture « du 1er au 21 août » reste le texte de l'actualité. Le statut ouvert / fermé suit seulement les horaires de la semaine, fuseau `Europe/Paris`. Lundi fermé. Mardi–samedi 6h30 inclus à 19h30 exclu. Dimanche 7h inclus à 13h exclu. La promo « −20 % sur les viennoiseries le dimanche » est un paragraphe fixe sur l'accueil, visible en permanence.

## 2. Direction visuelle

Proche du brun et du crème d'origine. L'orange `#D2691E` (blanc dessus : 3,63:1) est abandonné. Comic Sans et Times sont remplacés par des polices déjà présentes sur la machine : pas de CDN, pas de fichier de police.

- Titres : Georgia, Iowan Old Style, Palatino, serif (`font-serif`)
- Texte : system-ui, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif (`font-sans`)

Couleurs (ratio mesuré, texte normal, seuil 4,5:1) :

- Crème `#FFF8EE` fond de page. Encre `#2B2118` dessus : **14,93:1**
- Sable `#F3E6D0` colonnes horaires et actus. Encre dessus : **12,78:1**
- Brun `#4A2C1A` en-tête, menu, pied. Blanc `#FFFFFF` dessus : **12,60:1**. Or `#F0D9A8` (sous-titre « depuis 1987 ») dessus : **9,12:1**
- Liens `#6B3A12` sur crème, soulignés : **8,89:1**
- Bandeau promo `#F6E7C1`, encre : **12,84:1**
- Erreur `#9B1C1C` sur `#FDECEC` : **7,14:1**. Succès `#14532D` sur `#E8F5EC` : **8,11:1**

Focus visible sur fond clair et sur fond brun : double anneau (crème + brun). Liens soulignés. Pas de texte animé.

Mise en page mobile d'abord. À 320 px le menu (trois liens) passe à la ligne, les trois colonnes de l'accueil s'empilent, les tableaux tiennent en deux colonnes (produit, prix), les champs du formulaire font toute la largeur. Vérifier aussi 375, 768 et 1280 px.

## 3. Tailwind CLI

Tailwind **v4** (`tailwindcss` et `@tailwindcss/cli`), en dépendance de développement. Pas de `tailwind.config.js` : le thème est dans le CSS.

À la racine du dépôt :

```bash
npm install -D tailwindcss @tailwindcss/cli
```

`site/src/app.css` :

```css
@import "tailwindcss";
@source "../public";
@source "../includes";

@theme {
  --font-sans: system-ui, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-serif: Georgia, "Iowan Old Style", Palatino, "Palatino Linotype", serif;
  --color-creme: #FFF8EE;
  --color-sable: #F3E6D0;
  --color-promo: #F6E7C1;
  --color-encre: #2B2118;
  --color-brun: #4A2C1A;
  --color-lien: #6B3A12;
  --color-or: #F0D9A8;
  --color-erreur: #9B1C1C;
  --color-erreur-fond: #FDECEC;
  --color-succes: #14532D;
  --color-succes-fond: #E8F5EC;
}
```

Scripts dans `package.json` :

```bash
npm run css:watch   # npx @tailwindcss/cli -i site/src/app.css -o site/public/css/app.css --watch
npm run css:build   # même commande avec --minify
```

`@source` force le scan des fichiers PHP. Classes utiles : `bg-creme`, `text-encre`, `bg-brun`, `font-serif`. Le CSS compilé est commité. `node_modules/` est déjà dans `.gitignore`.

Lancer le site : `php -S localhost:8000 -t site/public`.

## 4. Formulaire de contact

`contact.php` reçoit le POST. Champs repris de l'ancien formulaire : nom, e-mail, objet (Commande de gâteau, Question, Autre), message. Chaque champ a un `<label for>`, `autocomplete` sur le nom et l'e-mail, `maxlength` en confort. Les erreurs sont des paragraphes à côté du champ (`aria-invalid`, `aria-describedby`), jamais `alert()`.

Contrôles serveur, dans cet ordre :

1. **Honeypot** : champ texte `champ_controle` dans un `<div aria-hidden="true">` caché visuellement. `aria-hidden` est sur le conteneur, pas sur le champ (un champ focusable ne doit pas porter `aria-hidden`). Le champ garde `tabindex="-1"` et `autocomplete="off"`. S'il contient quelque chose, afficher le même succès que pour un envoi réel, et n'écrire aucun fichier.
2. **CSRF** : champ caché comparé à `$_SESSION['csrf']` avec `hash_equals`. Échec : « Le formulaire a expiré, envoyez-le à nouveau. » Nouveau jeton après un succès.
3. **Champs** : `trim`. Nom obligatoire, 100 caractères max, refuser une chaîne vide ou faite d'espaces. E-mail obligatoire, `filter_var(..., FILTER_VALIDATE_EMAIL)`, 254 max. Objet dans la liste des trois valeurs. Message obligatoire, 2 000 caractères max (`mb_strlen`).
4. **Écriture** : une ligne JSON (`JSON_UNESCAPED_UNICODE`) ajoutée à `site/data/messages/messages.jsonl` avec `FILE_APPEND | LOCK_EX`. Contenu : date ISO, nom, e-mail, objet, message. Pas d'adresse IP. Si l'écriture échoue, message générique et pas de succès.
5. **PRG** : en cas de succès, redirection vers `contact.php` et message en session : le message a été enregistré et sera lu pour y répondre. Les valeurs saisies ne repassent pas dans l'URL.

Phrase à côté du formulaire : le nom, l'e-mail et le message sont enregistrés dans un fichier de la boutique, seulement pour répondre à la demande, et ne sont pas publiés.

`.gitignore` : `site/data/messages/*` et exception `!site/data/messages/.gitkeep`.

## 5. Étapes, un commit chacune

Messages au format Conventional Commits, en français.

1. **`chore: installer Tailwind et poser les couleurs`** — `package.json`, `package-lock.json`, `site/src/app.css`, CSS compilé, commande dans `AGENTS.md`. Page vide minimale pour vérifier que le CSS est servi.
2. **`feat: gabarit commun accessible`** — bootstrap, en-tête, menu, pied, lien d'évitement, focus, `lang="fr"`, charset, viewport. L'accueil n'a encore que le titre.
3. **`feat: données boutique, horaires, actus et produits`** — les quatre fichiers de `site/data/` et `est_ouvert()` dans `site/includes/ouverture.php`. Pas encore d'affichage.
4. **`feat: page d'accueil`** — texte corrigé, horaires, statut ouvert ou fermé, promo fixe, trois actus. Colonnes empilées sur mobile.
5. **`feat: page des produits`** — trois tableaux avec `<caption>` et `<th scope="col">`, prix exacts, phrase sur les gâteaux.
6. **`feat: page contact et formulaire`** — adresse, téléphone, e-mail, formulaire étiqueté, sans traitement (le POST ne fait encore rien d'utile : cette étape reste visuelle).
7. **`feat: enregistrer les messages de contact`** — validation, CSRF, honeypot, JSONL, erreurs à côté des champs, redirection. Ignorer le fichier de messages.
8. **`feat: titres, descriptions et fiche locale`** — `<title>` et meta description par page (nom de la boutique et Lyon), JSON-LD `Bakery` : adresse, téléphone, horaires. Pas d'Open Graph ni de sitemap.
