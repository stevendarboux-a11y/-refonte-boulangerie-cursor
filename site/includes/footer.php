<?php

declare(strict_types=1);

$boutique = require __DIR__ . '/../data/boutique.php';
?>
  </main>
  <footer class="bg-brun text-white">
    <address class="mx-auto max-w-5xl px-4 py-4 text-center not-italic">
      © <?= e($boutique['nom']) ?> —
      <?= e($boutique['adresse']) ?>, <?= e($boutique['cp']) ?> <?= e($boutique['ville']) ?> —
      Tél. : <a class="text-white decoration-or underline-offset-4" href="tel:<?= e($boutique['telephone_lien']) ?>"><?= e($boutique['telephone']) ?></a>
    </address>
  </footer>
</body>
</html>
