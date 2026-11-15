<?php

declare(strict_types=1);

function est_ouvert(DateTimeImmutable $maintenant, array $horaires): bool
{
    $jour = (int) $maintenant->format('N');
    $plage = $horaires['jours'][$jour] ?? null;
    if (!is_array($plage)) {
        return false;
    }

    $heure = $maintenant->format('H:i');

    return $heure >= $plage[0] && $heure < $plage[1];
}
