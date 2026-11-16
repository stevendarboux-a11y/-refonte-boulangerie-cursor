<?php

declare(strict_types=1);

$boutique = require __DIR__ . '/../data/boutique.php';
$horaires = require __DIR__ . '/../data/horaires.php';

$jours_schema = [
    1 => 'https://schema.org/Monday',
    2 => 'https://schema.org/Tuesday',
    3 => 'https://schema.org/Wednesday',
    4 => 'https://schema.org/Thursday',
    5 => 'https://schema.org/Friday',
    6 => 'https://schema.org/Saturday',
    7 => 'https://schema.org/Sunday',
];

$plages = [];
foreach ($horaires['jours'] as $numero => $plage) {
    if (!is_array($plage) || !isset($jours_schema[$numero], $plage[0], $plage[1])) {
        continue;
    }

    $cle = $plage[0] . "\0" . $plage[1];
    $plages[$cle]['opens'] = $plage[0];
    $plages[$cle]['closes'] = $plage[1];
    $plages[$cle]['dayOfWeek'][] = $jours_schema[$numero];
}

$ouvertures = [];
foreach ($plages as $plage) {
    $jours = $plage['dayOfWeek'];
    $ouvertures[] = [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => count($jours) === 1 ? $jours[0] : $jours,
        'opens' => $plage['opens'],
        'closes' => $plage['closes'],
    ];
}

$fiche = [
    '@context' => 'https://schema.org',
    '@type' => 'Bakery',
    'name' => $boutique['nom'],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $boutique['adresse'],
        'postalCode' => $boutique['cp'],
        'addressLocality' => $boutique['ville'],
    ],
    'telephone' => $boutique['telephone_lien'],
    'openingHoursSpecification' => $ouvertures,
];

$json_ld = json_encode(
    $fiche,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_PRETTY_PRINT
);
?>
  </main>
  <footer class="mt-auto w-full shrink-0 bg-brun text-white">
    <address class="mx-auto max-w-5xl px-4 py-4 text-center not-italic">
      © <?= e($boutique['nom']) ?> —
      <?= e($boutique['adresse']) ?>, <?= e($boutique['cp']) ?> <?= e($boutique['ville']) ?> —
      <span class="whitespace-nowrap">Tél. : <a class="text-white decoration-or underline-offset-4" href="tel:<?= e($boutique['telephone_lien']) ?>"><?= e($boutique['telephone']) ?></a></span>
    </address>
  </footer>
<?php if (is_string($json_ld)): ?>
  <script type="application/ld+json">
<?= $json_ld ?>
  </script>
<?php endif; ?>
</body>
</html>
