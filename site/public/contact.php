<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$boutique = require __DIR__ . '/../data/boutique.php';

$titre = 'Nous contacter — ' . $boutique['nom'] . ', ' . $boutique['ville'];
$description = $boutique['nom'] . ', ' . $boutique['adresse'] . ', ' . $boutique['cp'] . ' ' . $boutique['ville'] . '. Téléphone ' . $boutique['telephone'] . '.';
$page = 'contact';

$objets = ['Commande de gâteau', 'Question', 'Autre'];
$message_succes = 'Votre message a été enregistré et sera lu pour y répondre.';

$valeurs = [
    'nom' => '',
    'email' => '',
    'objet' => '',
    'message' => '',
];
$erreurs = [];
$succes = '';

if (isset($_SESSION['contact_succes']) && is_string($_SESSION['contact_succes']) && $_SESSION['contact_succes'] !== '') {
    $succes = $_SESSION['contact_succes'];
    unset($_SESSION['contact_succes']);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $controle = $_POST['champ_controle'] ?? '';
    if (!is_string($controle) || trim($controle) !== '') {
        $_SESSION['contact_succes'] = $message_succes;
        csrf_renouveler();
        header('Location: contact.php', true, 303);
        exit;
    }

    $jeton = $_POST['csrf'] ?? null;
    if (!is_string($jeton)) {
        $jeton = null;
    }

    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $objet = $_POST['objet'] ?? '';
    $message = $_POST['message'] ?? '';

    $valeurs['nom'] = is_string($nom) ? trim($nom) : '';
    $valeurs['email'] = is_string($email) ? trim($email) : '';
    $valeurs['objet'] = is_string($objet) ? trim($objet) : '';
    $valeurs['message'] = is_string($message) ? trim($message) : '';

    if (!csrf_ok($jeton)) {
        $erreurs['formulaire'] = 'Le formulaire a expiré, envoyez-le à nouveau.';
    } else {
        if ($valeurs['nom'] === '') {
            $erreurs['nom'] = 'Indiquez votre nom.';
        } elseif (mb_strlen($valeurs['nom']) > 100) {
            $erreurs['nom'] = 'Le nom ne peut pas dépasser 100 caractères.';
        }

        if ($valeurs['email'] === '') {
            $erreurs['email'] = 'Indiquez votre e-mail.';
        } elseif (filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL) === false) {
            $erreurs['email'] = 'Cette adresse e-mail n\'est pas valide.';
        } elseif (mb_strlen($valeurs['email']) > 254) {
            $erreurs['email'] = 'L\'e-mail ne peut pas dépasser 254 caractères.';
        }

        if (!in_array($valeurs['objet'], $objets, true)) {
            $erreurs['objet'] = 'Choisissez un objet dans la liste.';
        }

        if ($valeurs['message'] === '') {
            $erreurs['message'] = 'Écrivez votre message.';
        } elseif (mb_strlen($valeurs['message']) > 2000) {
            $erreurs['message'] = 'Le message ne peut pas dépasser 2 000 caractères.';
        }

        if ($erreurs === []) {
            $ligne = json_encode(
                [
                    'date' => (new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))->format('c'),
                    'nom' => $valeurs['nom'],
                    'email' => $valeurs['email'],
                    'objet' => $valeurs['objet'],
                    'message' => $valeurs['message'],
                ],
                JSON_UNESCAPED_UNICODE
            );

            $chemin = __DIR__ . '/../data/messages/messages.jsonl';
            $ecrit = is_string($ligne) && file_put_contents($chemin, $ligne . "\n", FILE_APPEND | LOCK_EX) !== false;

            if ($ecrit) {
                $_SESSION['contact_succes'] = $message_succes;
                csrf_renouveler();
                header('Location: contact.php', true, 303);
                exit;
            }

            $erreurs['formulaire'] = 'Le message n\'a pas pu être enregistré. Réessayez plus tard.';
        }
    }
}

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
<?php if ($succes !== ''): ?>
    <p class="bg-succes-fond px-3 py-2 text-succes" role="status"><?= e($succes) ?></p>
<?php endif; ?>
<?php if (isset($erreurs['formulaire'])): ?>
    <p class="bg-erreur-fond px-3 py-2 text-erreur" role="alert"><?= e($erreurs['formulaire']) ?></p>
<?php endif; ?>
    <p>Le nom, l'e-mail et le message sont enregistrés dans un fichier de la boutique, seulement pour répondre à la demande, et ne sont pas publiés.</p>

    <div class="sr-only" aria-hidden="true">
      <label for="champ_controle">Ne pas remplir</label>
      <input id="champ_controle" name="champ_controle" type="text" tabindex="-1" autocomplete="off">
    </div>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div>
      <label for="nom" class="mb-1 block font-semibold">Nom</label>
      <input id="nom" name="nom" type="text" autocomplete="name" maxlength="100" required value="<?= e($valeurs['nom']) ?>" class="w-full border border-brun bg-white px-3 py-2 text-encre"<?= isset($erreurs['nom']) ? ' aria-invalid="true" aria-describedby="nom-erreur"' : '' ?>>
<?php if (isset($erreurs['nom'])): ?>
      <p id="nom-erreur" class="mt-1 text-erreur"><?= e($erreurs['nom']) ?></p>
<?php endif; ?>
    </div>

    <div>
      <label for="email" class="mb-1 block font-semibold">E-mail</label>
      <input id="email" name="email" type="email" autocomplete="email" maxlength="254" required value="<?= e($valeurs['email']) ?>" class="w-full border border-brun bg-white px-3 py-2 text-encre"<?= isset($erreurs['email']) ? ' aria-invalid="true" aria-describedby="email-erreur"' : '' ?>>
<?php if (isset($erreurs['email'])): ?>
      <p id="email-erreur" class="mt-1 text-erreur"><?= e($erreurs['email']) ?></p>
<?php endif; ?>
    </div>

    <div>
      <label for="objet" class="mb-1 block font-semibold">Objet</label>
      <select id="objet" name="objet" class="w-full border border-brun bg-white px-3 py-2 text-encre"<?= isset($erreurs['objet']) ? ' aria-invalid="true" aria-describedby="objet-erreur"' : '' ?>>
<?php foreach ($objets as $objet): ?>
        <option value="<?= e($objet) ?>"<?= $valeurs['objet'] === $objet ? ' selected' : '' ?>><?= e($objet) ?></option>
<?php endforeach; ?>
      </select>
<?php if (isset($erreurs['objet'])): ?>
      <p id="objet-erreur" class="mt-1 text-erreur"><?= e($erreurs['objet']) ?></p>
<?php endif; ?>
    </div>

    <div>
      <label for="message" class="mb-1 block font-semibold">Message</label>
      <textarea id="message" name="message" rows="6" maxlength="2000" required class="w-full border border-brun bg-white px-3 py-2 text-encre"<?= isset($erreurs['message']) ? ' aria-invalid="true" aria-describedby="message-erreur"' : '' ?>><?= e($valeurs['message']) ?></textarea>
<?php if (isset($erreurs['message'])): ?>
      <p id="message-erreur" class="mt-1 text-erreur"><?= e($erreurs['message']) ?></p>
<?php endif; ?>
    </div>

    <button type="submit" class="bg-brun px-4 py-2 font-semibold text-white">Envoyer</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php';
