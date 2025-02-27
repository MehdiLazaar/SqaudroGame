<?php
require_once 'env/db.php';
require_once 'skel/PDOSquadro.php';
require_once __DIR__ . '/src/JoueurSquadro.php';
require_once __DIR__ . '/src/PartieSquadro.php';

session_start();

use Squadro\PDOSquadro;
use src\JoueurSquadro;
use src\PartieSquadro;

PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);

if (!($_SESSION['player'] instanceof JoueurSquadro)) {
    header('Location: login.php');
    exit();
}

$player = $_SESSION['player'];

// Créer une nouvelle partie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newGame'])) {
    // Créer une nouvelle partie
    $newPartie = new PartieSquadro($player);

    // Convertir la partie en JSON
    $jsonPartie = $newPartie->toJson($newPartie->getPartieID());

    // Créer la partie avec le statut "initialized"
    PDOSquadro::createPartieSquadro($player->getNomJoueur(), $jsonPartie);

    // Sauvegarder l'ID de la partie dans la session
    $_SESSION['partieSquadro'] = $jsonPartie; // Stocke la partie dans la session

    // Rafraîchir la page pour voir la mise à jour
    header("Location: index.php");
    exit();
}

// Ajouter le deuxième joueur à la partie
if (isset($_GET['partieSquadro'])) {
    $partieId = $_GET['partieSquadro'];
    $partie = PDOSquadro::getPartieSquadroById($partieId);

    if ($partie && $partie->gameStatus === 'initialized') {
        // Ajouter le deuxième joueur à la partie
        PDOSquadro::addPlayerToPartieSquadro($player->getNomJoueur(), $partie->toJson(), $partieId);

        // Mettre à jour la partie avec le statut 'inProgress' et enregistrer
        PDOSquadro::savePartieSquadro('inProgress', $partie->toJson(), $partieId);

        // Mettre à jour la session avec la partie en cours
        $_SESSION['partieSquadro'] = $partie->toJson();

        // Rafraîchir la page pour voir la mise à jour
        header("Location: index.php");
        exit();
    }
}

// Charger les parties disponibles (en attente et en cours)
$parties = PDOSquadro::getAllPartieSquadroByPlayerName($player->getNomJoueur());
$partiesEnAttente = array_filter($parties, fn($p) => $p->gameStatus === 'initialized');
$partiesEnCours = array_filter($parties, fn($p) => $p->gameStatus === 'inProgress');
$partiesTerminees = array_filter($parties, fn($p) => $p->gameStatus === 'finished');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squadro - Accueil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';
        }
    </script>
</head>
<body>

<section class="section">
    <div class="container">
        <h1 class="title">Bienvenue, <?= htmlspecialchars($player->getNomJoueur()) ?></h1>

        <form method="POST">
            <button class="button is-primary" type="submit" name="newGame">Créer une nouvelle partie</button>
        </form>

        <div class="tabs is-centered">
            <ul>
                <li class="is-active"><a href="#" onclick="showTab('partiesEnAttente')">Parties en attente</a></li>
                <li><a href="#" onclick="showTab('partiesEnCours')">Parties en cours</a></li>
                <li><a href="#" onclick="showTab('partiesTerminees')">Parties terminées</a></li>
            </ul>
        </div>

        <div id="partiesEnAttente" class="tab-content">
            <h2 class="subtitle">Parties en attente</h2>
            <?php if (empty($partiesEnAttente)) : ?>
                <p>Aucune partie en attente.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($partiesEnAttente as $partie) : ?>
                        <li class="box">
                            Partie #<?= $partie->getPartieID() ?> - Joueur actif : <?= $partie->getNomJoueurActif($partie->getJoueurActif()->getNomJoueur()) ?>
                            <a href="index.php?gameId=<?= $partie->getPartieID() ?>" class="button is-link is-small ml-2">Rejoindre</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div id="partiesEnCours" class="tab-content" style="display:none;">
            <h2 class="subtitle">Parties en cours</h2>
            <?php if (empty($partiesEnCours)) : ?>
                <p>Aucune partie en cours.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($partiesEnCours as $partie) : ?>
                        <li class="box">Partie #<?= $partie->getPartieID() ?> - Statut : En cours</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div id="partiesTerminees" class="tab-content" style="display:none;">
            <h2 class="subtitle">Parties terminées</h2>
            <?php if (empty($partiesTerminees)) : ?>
                <p>Aucune partie terminée.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($partiesTerminees as $partie) : ?>
                        <li class="box">Partie #<?= $partie->getPartieID() ?> - Statut : Terminé</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    showTab('partiesEnAttente');
</script>

</body>
</html>
