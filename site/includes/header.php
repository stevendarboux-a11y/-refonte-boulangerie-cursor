<?php

declare(strict_types=1);

$titre = $titre ?? 'Boulangerie Lefèvre';
$description = $description ?? '';
$page = $page ?? '';

$liens = [
    'accueil' => ['href' => 'index.php', 'label' => 'Accueil'],
    'produits' => ['href' => 'produits.php', 'label' => 'Nos produits'],
    'contact' => ['href' => 'contact.php', 'label' => 'Contact'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titre) ?></title>
<?php if ($description !== ''): ?>
  <meta name="description" content="<?= e($description) ?>">
<?php endif; ?>
  <link rel="stylesheet" href="css/app.css">
</head>
<body class="flex min-h-dvh flex-col bg-creme font-sans text-base leading-relaxed text-encre">
  <a href="#contenu" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-creme focus:px-3 focus:py-2 focus:text-encre">Aller au contenu</a>
  <header class="bg-brun text-white">
    <div class="mx-auto max-w-5xl px-4 py-6 text-center">
      <p class="font-serif text-3xl sm:text-4xl">
        <a href="index.php" class="text-white decoration-or underline-offset-4">Boulangerie Lefèvre</a>
      </p>
      <p class="mt-1 text-or">Artisan boulanger depuis 1987 — Lyon 3e</p>
    </div>
    <nav aria-label="Navigation principale" class="border-t border-white/20">
      <ul class="mx-auto flex max-w-5xl flex-wrap justify-center gap-x-8 gap-y-2 px-4 py-3">
<?php foreach ($liens as $cle => $lien): ?>
        <li>
          <a href="<?= e($lien['href']) ?>" class="text-white decoration-or underline-offset-4"<?= $page === $cle ? ' aria-current="page"' : '' ?>><?= e($lien['label']) ?></a>
        </li>
<?php endforeach; ?>
      </ul>
    </nav>
  </header>
  <main id="contenu" class="mx-auto w-full max-w-5xl flex-1 px-4 py-8">
