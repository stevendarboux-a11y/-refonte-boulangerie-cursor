<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$boutique = require __DIR__ . '/../data/boutique.php';

$titre = 'Nous contacter';
$page = 'contact';

$objets = ['Commande de gâteau', 'Question', 'Autre'];

require __DIR__ . '/../includes/header.php';
?>
<h1 class="font-serif text-3xl text-brun">Nous contacter</h1>
<div class="mt-8 grid grid-cols-1 items-start gap-10 md:grid-cols-[minmax(0,18rem)_minmax(0,1fr)]">
  <address class="not-italic break-words">
    <p><?= e($boutique['adresse']) ?><br><?= e($boutique['cp']) ?> <?= e($boutique['ville']) ?></p>
    <p class="mt-4">Tél. : <a href="tel:<?= e($boutique['telephone_lien']) ?>"><?= e($boutique['telephone']) ?></a></p>
    <p class="mt-2">E-mail : <a href="mailto:<?= e($boutique['email']) ?>"><?= e($boutique['email']) ?></a></p>
  </address>

  <form method="post" action="contact.php" class="min-w-0 max-w-xl space-y-4">
    <p>Le nom, l'e-mail et le message sont enregistrés dans un fichier de la boutique, seulement pour répondre à la demande, et ne sont pas publiés.</p>

    <div>
      <label for="nom" class="mb-1 block font-semibold">Nom</label>
      <input id="nom" name="nom" type="text" autocomplete="name" maxlength="100" required class="w-full border border-brun bg-white px-3 py-2 text-encre">
    </div>

    <div>
      <label for="email" class="mb-1 block font-semibold">E-mail</label>
      <input id="email" name="email" type="email" autocomplete="email" maxlength="254" required class="w-full border border-brun bg-white px-3 py-2 text-encre">
    </div>

    <div>
      <label for="objet" class="mb-1 block font-semibold">Objet</label>
      <select id="objet" name="objet" class="w-full border border-brun bg-white px-3 py-2 text-encre">
<?php foreach ($objets as $objet): ?>
        <option value="<?= e($objet) ?>"><?= e($objet) ?></option>
<?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="message" class="mb-1 block font-semibold">Message</label>
      <textarea id="message" name="message" rows="6" maxlength="2000" required class="w-full border border-brun bg-white px-3 py-2 text-encre"></textarea>
    </div>

    <button type="submit" class="bg-brun px-4 py-2 font-semibold text-white">Envoyer</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php';
