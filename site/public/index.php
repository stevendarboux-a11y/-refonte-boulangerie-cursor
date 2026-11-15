<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/ouverture.php';

$boutique = require __DIR__ . '/../data/boutique.php';
$horaires = require __DIR__ . '/../data/horaires.php';
$actus = require __DIR__ . '/../data/actus.php';

$titre = $boutique['nom'];
$page = 'accueil';

$maintenant = new DateTimeImmutable('now', new DateTimeZone($horaires['fuseau']));
$ouvert = est_ouvert($maintenant, $horaires);

require __DIR__ . '/../includes/header.php';
?>
<div class="grid grid-cols-1 items-start gap-6 md:grid-cols-[minmax(0,1fr)_minmax(0,2.2fr)_minmax(0,1fr)]">
  <section aria-labelledby="titre-accueil" class="min-w-0 md:col-start-2 md:row-start-1">
    <h1 id="titre-accueil" class="font-serif text-3xl text-brun">Boulangerie artisanale à Lyon 3e, depuis 1987</h1>
    <p class="mt-4">Bienvenue à la boulangerie Lefèvre. Nous faisons du pain depuis <?= e($boutique['depuis']) ?> avec des farines de la région. Tout est fait maison chaque matin par notre équipe.</p>
    <p class="mt-4">Venez découvrir nos baguettes tradition, nos pains spéciaux, nos viennoiseries et nos pâtisseries. Nous faisons aussi les gâteaux d'anniversaire et les pièces montées sur commande (<?= e($boutique['delai_commande']) ?>).</p>
  </section>

  <section aria-labelledby="titre-horaires" class="min-w-0 bg-sable p-4 md:col-start-1 md:row-start-1">
    <h2 id="titre-horaires" class="font-serif text-xl text-brun">Horaires</h2>
    <ul class="mt-3 space-y-1">
<?php foreach ($horaires['libelles'] as $ligne): ?>
      <li><?= e($ligne) ?></li>
<?php endforeach; ?>
    </ul>
    <p class="mt-4">La boulangerie est <?= $ouvert ? 'ouverte' : 'fermée' ?>.</p>
    <p class="mt-4 bg-promo px-3 py-2">−20 % sur les viennoiseries le dimanche</p>
  </section>

  <section aria-labelledby="titre-actus" class="min-w-0 bg-sable p-4 md:col-start-3 md:row-start-1">
    <h2 id="titre-actus" class="font-serif text-xl text-brun">Actualités</h2>
    <ul class="mt-3 space-y-4">
<?php foreach ($actus as $actu): ?>
      <li><strong><?= e($actu['date']) ?></strong> — <?= e($actu['texte']) ?></li>
<?php endforeach; ?>
    </ul>
  </section>
</div>
<?php require __DIR__ . '/../includes/footer.php';
