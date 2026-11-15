# Audit — Boulangerie Lefèvre (`legacy-site`)

Le site tient sur trois pages statiques (`index.html`, `produits.html`, `contact.html`) et quatre lignes de CSS. Il n’y a ni images, ni serveur, ni envoi d’e-mail. Sur un grand écran, le texte se lit. Dès qu’on est sur mobile, avec un lecteur d’écran, ou qu’on essaie vraiment d’écrire à la boutique, le site échoue.

Huit problèmes sont critiques, le reste est important ou mineur.

## Ce que fait vraiment le formulaire

Le formulaire de `contact.html` n’a ni `action` ni `method`. Le navigateur l’envoie donc en GET vers la page elle-même. Le script ne fait que ceci :

```javascript
function envoyer(){
  if(document.f.nom.value==""){ alert("Veuillez remplir votre nom !!"); return false; }
  alert("Merci, votre message a bien été envoyé !"); return true;
}
```

Trois cas réels :

1. Le nom est vide. Une alerte bloque l’envoi. L’e-mail et le message peuvent rester vides. Un nom fait d’espaces passe, parce que la valeur n’est pas nettoyée.
2. Le nom est rempli. L’alerte dit « votre message a bien été envoyé », puis la fonction renvoie `true`. Le navigateur recharge alors `contact.html?nom=…&email=…&objet=…&msg=…`. Rien n’est transmis à la boulangerie : pas de serveur, pas de `mailto:`, pas d’enregistrement. Après le OK de l’alerte, le formulaire est de nouveau vide.
3. JavaScript est désactivé. Il n’y a plus ni contrôle ni alerte. Le navigateur fait le même GET, et le message part dans l’adresse de la page.

L’e-mail publié à côté, `contact@boulangerie-lefevre.example`, ne peut pas recevoir de courrier : `.example` est un domaine réservé (RFC 2606), il n’achemine rien. Le téléphone et le formulaire sont les seuls autres canaux, et le formulaire ne livre rien. Une commande de gâteau, annoncée comme à faire 48 h à l’avance, est perdue tout en affichant un succès.

## Critique

**Le succès du formulaire est faux.** La boutique ne reçoit aucune commande ni aucun message. Le client repart convaincu que sa demande est prise en charge. C’est le bug le plus grave du site, parce qu’il casse le seul parcours commercial (gâteaux, pièces montées, questions).

**Le nom, l’e-mail et le message se retrouvent dans l’URL.** Un envoi GET les place dans l’historique, les favoris, les journaux du serveur si la page est hébergée, et l’en-tête `Referer` si le visiteur clique ensuite vers un autre site. Un message de commande contient souvent un téléphone, une adresse ou des allergies. Ces données circulent en clair, sans que la page les réaffiche (il n’y a donc pas de faille XSS sur ce HTML statique : les paramètres ne sont jamais réécrits dans la page).

**L’adresse e-mail affichée est injoignable.** `contact@boulangerie-lefevre.example` est imprimée comme le moyen de contact direct. Le domaine `.example` ne livre aucun message. Écrire à cette adresse échoue silencieusement chez l’expéditeur, ou rebondit, selon son fournisseur.

**Les trois images sont absentes et sans texte de remplacement.** `img/devanture.jpg`, `img/vitrine.jpg` et `img/plan.gif` sont demandées, le dossier n’existe pas. Chaque page produit donc une requête en erreur et une icône d’image cassée. L’attribut `alt` manque (WCAG 1.1.1) : un lecteur d’écran annonce un nom de fichier inutile, et la devanture, la vitrine et le plan n’apportent aucune information. Le plan est le seul repère visuel pour venir au 12 rue Paul Bert.

**La langue de la page n’est pas déclarée.** `<html>` n’a pas `lang="fr"` (WCAG 3.1.1, niveau A). Un lecteur d’écran peut prononcer « boulangerie », « épeautre » ou « pralines » avec une voix anglaise. Les moteurs s’en servent aussi pour savoir à qui montrer la page.

**Le bandeau promo est un `<marquee>`.** Le texte « -20 % sur les viennoiseries le dimanche » défile en boucle, sans pause, sans arrêt, sans respect de `prefers-reduced-motion` (WCAG 2.2.2, niveau A). Les personnes sensibles au mouvement ne peuvent pas le figer. Celles qui lisent lentement n’ont pas le temps de le lire. L’élément est retiré du HTML vivant : les navigateurs le gardent par compatibilité, rien ne garantit qu’ils le gardent.

**La page est un tableau de 900 px, sans viewport.** `width="900"`, colonnes de 200 / 500 / 200 px, images en 480, 400 et 350 px, zone de texte en `cols="50"`. Il n’y a pas de `<meta name="viewport">`. Sur un téléphone, le navigateur réduit la page entière pour faire tenir 900 px : le texte devient minuscule, il faut pincer pour zoomer, et rien ne se réempile. Au zoom 200 % ou à 320 px de large, le contenu dépasse de l’écran au lieu de passer à la ligne (WCAG 1.4.10). Les trois pages ont le même gabarit.

**Les champs n’ont pas d’étiquette associée.** « Nom : », « Email : », « Objet : » et « Message : » sont du texte posé à côté des champs, sans `<label for>` ni `aria-label`. Un lecteur d’écran annonce « zone de texte » sans dire ce qu’il faut y mettre (WCAG 1.3.1 et 4.1.2). Cliquer sur le mot « Nom » ne place pas le curseur dans le champ. Il manque aussi `autocomplete` (WCAG 1.3.5) : le navigateur ne peut pas proposer le nom et l’e-mail déjà connus.

## Important

**Le menu blanc sur orange ne passe pas le contraste, et le survol le rend illisible.** Blanc `#FFFFFF` sur `#D2691E` donne 3,63:1. Le texte normal en a besoin de 4,5:1 (WCAG 1.4.3). Les liens sont en gras mais à la taille par défaut, donc ce seuil s’applique. Au survol, `a:hover { color: red !important; }` donne rouge sur ce même orange, soit 1,10:1 : le lien disparaît presque. Le `!important` empêche un style utilisateur de corriger cette couleur. Le blanc et le jaune du bandeau marron, eux, passent (7,10:1 et 6,61:1). Le texte noir sur le crème aussi.

**Le document déclenche le mode quirks, avec le HTML de mise en page de 1998.** Aucune page n’a `<!DOCTYPE html>`. Le navigateur utilise alors l’ancien algorithme de calcul des largeurs et des marges : le même CSS ne se comporte pas comme en HTML5. La structure visuelle repose sur `<center>`, `<font face="Comic Sans MS" size="7">`, `bgcolor`, `align`, `valign`, `cellpadding` et un tableau qui englobe toute la page. Ces éléments décrivent l’apparence, pas le contenu. Un outil d’assistance ne distingue pas l’en-tête, le menu, le contenu et le pied. Comic Sans via `face` est en plus souvent absente sur macOS et Linux : la règle CSS `Times New Roman` prend le relais, au hasard de la machine.

**L’encodage n’est pas déclaré.** Il n’y a pas de `<meta charset="utf-8">`. Le fichier est en UTF-8 (les « é » sont corrects dedans). Sans déclaration, un serveur ou un vieux navigateur peut l’interpréter autrement, et « Lefèvre » ou « pâtisseries » s’affichent cassés.

**Les titres et les métadonnées SEO sont trop pauvres.** L’accueil s’intitule « Boulangerie Lefèvre - Bienvenue !!! ». Les pages intérieures s’intitulent « Contact » et « Nos produits », sans le nom de la boutique ni la ville. Il n’y a pas de meta description, pas d’URL canonique, pas de favicon. Google peut indexer le texte, qui est bien dans le HTML, mais le résultat de recherche ne dit pas « boulangerie artisanale, Lyon 3e ».

**L’adresse, les horaires et le téléphone ne sont pas structurés.** Ils sont écrits en texte libre, ce qui est déjà utile. Il manque un balisage `address`, un lien `tel:`, et des données `LocalBusiness` / `OpeningHoursSpecification`. Pour une boutique au 12 rue Paul Bert, 69003, c’est ce qui alimente le bloc local (horaires, itinéraire, appel). Les actus (« 01/12 », « 15/09 », « 01/06 ») n’ont pas d’année. La mention « Site mis à jour le 12/03/2011 » date l’ensemble de quinze ans. Un moteur et un client en déduisent que les prix et la fermeture d’août ne sont plus sûrs.

**Les tableaux de prix n’ont pas de vrais en-têtes.** Utiliser un `<table>` pour les pains, viennoiseries et pâtisseries est le bon choix. Les cellules d’en-tête sont des `<td><b>Produit</b></td>` au lieu de `<th scope="col">`. Il n’y a pas de `<caption>`. Un lecteur d’écran ne peut pas annoncer « Prix, 1,30 € » en lien avec « Baguette tradition » (WCAG 1.3.1). Le grand tableau qui met la page en colonnes, lui, ne devrait pas être un tableau : ce n’est pas une donnée.

**Il n’y a pas de lien d’évitement ni de régions.** Pas de `<header>`, `<nav>`, `<main>`, `<footer>`, pas de « aller au contenu » (WCAG 2.4.1). Le menu ne fait que trois liens, donc le détour reste court, mais il est répété sur chaque page et invisible pour qui navigue par régions.

**La validation ignore presque tout, et seulement dans le navigateur.** Seul le nom vide est refusé. L’e-mail est un `<input type="text">` : `pas-un-email` est accepté. Le message peut être vide. Rien ne limite la longueur : un long texte est collé dans l’URL, que les navigateurs tronquent souvent autour de quelques milliers de caractères, sans prévenir. Comme le contrôle est dans `onsubmit`, il disparaît dès que le JavaScript ne tourne pas. Il n’existe de toute façon aucun contrôle serveur, puisqu’il n’y a pas de serveur.

**Les erreurs passent par `alert()`.** La boîte bloque la page, ne reste pas affichée à côté du champ, et ne survit pas au rechargement (WCAG 3.3.1). Après le faux succès, il ne reste aucune trace de ce qui a été « envoyé ».

**Aucune information n’accompagne la collecte du nom et de l’e-mail.** Le formulaire demande des données personnelles, y compris un message libre, sans dire qui les reçoit, pourquoi, ni combien de temps elles sont gardées. Pour une boutique en France, cette information est due aux personnes (RGPD), même sur un petit formulaire de commande.

**Les coordonnées ne sont pas les mêmes partout.** L’accueil et les produits affichent le téléphone dans le pied de page. La page contact l’enlève du pied et le met dans le texte, avec l’e-mail. Le nom est « Lefèvre » dans le titre et « Lefevre » dans le premier paragraphe. Cette variation brouille le nom, l’adresse et le téléphone que Google rapproche pour une fiche locale.

**Les liens n’ont pas de soulignement.** `a { text-decoration: none; }` sur tout le site. Dans le bandeau orange, seule la couleur les distingue, et cette couleur est déjà trop faible. Au survol ils deviennent presque invisibles, et sur un écran tactile il n’y a pas de survol du tout.

**Les images cassées pèsent sur l’affichage, le reste est léger.** Le HTML et le CSS font quelques kilo-octets, la police est système, il n’y a pas de police web. Le coût réel vient des trois requêtes image en 404, des images sans `height` (le navigateur ne réserve pas la place, le texte saute quand l’erreur se dessine), du `<marquee>` qui redessine en continu, et du script d’horloge dans le `<head>` qui attend `onload` pour écrire l’heure.

## Mineur

**L’horloge ne tourne pas.** `heure()` écrit une seule fois `6h5` plutôt que `6h05`, dans un `<span id="h">` vide tant que le script n’a pas tourné. Ce n’est pas une zone live : un lecteur d’écran ne la voit pas se remplir. L’information utile, ce sont les horaires d’ouverture déjà écrits à côté.

**Le texte a des fautes et une mention illisible.** « Bienvenue a la boulangerie », « mis a jour », « equipe » sans accent. La date de mise à jour est en `<font size="1">`, autour de 10 px. Rien de bloquant, mais la page d’accueil est la vitrine du savoir-faire.

**Le titre d’accueil se termine par « !!! ».** Les lecteurs d’écran lisent la ponctuation. Le mot « Bienvenue » ne dit pas ce qu’on trouve sur la page. « Contact » et « Nos produits » décrivent le sujet, sans le nom du commerce.

**Il n’y a pas de partage social ni de sitemap.** Pas d’Open Graph, pas de `robots.txt`, pas de sitemap. Sur trois pages reliées par le menu, un sitemap change peu. Un aperçu correct quand on colle l’adresse dans un message, en revanche, manque.

**L’en-tête, le menu et le pied sont copiés trois fois.** Toute correction (téléphone, lien, contraste) est à refaire à la main dans chaque fichier. C’est la raison pour laquelle le pied de la page contact a déjà divergé.

**Le numéro `04 78 00 00 00` a la forme d’un numéro d’exemple.** L’indicatif 04 78 correspond bien à Lyon. La suite `00 00 00` est le genre de valeur qu’on met dans une maquette. À confirmer avant de le republier : un lien `tel:` vers un numéro mort ferait échouer le seul canal qui reste une fois le formulaire et l’e-mail écartés.

## Ce qui est déjà solide

Le contenu utile est du vrai texte : prix, horaires, adresse, téléphone, délai de 48 h. Il ne faudra pas le réinventer. Chaque page a un seul `h1`. Les liens du menu ont un intitulé clair. Les tableaux de tarifs sont la bonne structure, à condition d’en faire des vrais en-têtes. Le contraste du bandeau marron et du texte sur fond crème est suffisant. Le poids des pages est très faible.
