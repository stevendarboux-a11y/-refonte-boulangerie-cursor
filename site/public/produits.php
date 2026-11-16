<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$produits = require __DIR__ . '/../data/produits.php';

$titre = 'Nos produits';
$page = 'produits';

require __DIR__ . '/../includes/header.php';
?>
<h1 class="font-serif text-3xl text-brun">Nos produits</h1>
<div class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-3">
<?php foreach ($produits['categories'] as $categorie): ?>
  <table class="w-full border-collapse text-left">
    <caption class="mb-3 text-left font-serif text-xl text-brun"><?= e($categorie['titre']) ?></caption>
    <thead>
      <tr class="border-b-2 border-brun">
        <th scope="col" class="py-2 pr-4 font-semibold">Produit</th>
        <th scope="col" class="py-2 text-right font-semibold">Prix</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($categorie['produits'] as $produit): ?>
      <tr class="odd:bg-sable">
        <td class="py-2 pr-4"><?= e($produit['nom']) ?></td>
        <td class="whitespace-nowrap py-2 text-right"><?= e($produit['prix']) ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
<?php endforeach; ?>
</div>
<p class="mt-8"><?= e($produits['commande']) ?></p>
<?php require __DIR__ . '/../includes/footer.php';
