<?php
require_once 'env/db.php';
require_once 'skel/PDOSquadro.php';
require_once __DIR__ . '/src/JoueurSquadro.php';
require_once __DIR__ . '/src/PartieSquadro.php';
require_once __DIR__ . '/src/SquadroUIGenerator.php';

use Squadro\PDOSquadro;
use src\JoueurSquadro;
use src\PartieSquadro;
use src\SquadroUIGenerator;

session_start();

// Vérifie qu'un joueur est connecté, sinon redirige vers login.php
if (!isset($_SESSION['player']) || !($_SESSION['player'] instanceof JoueurSquadro)) {
    header('Location: login.php');
    exit();
}

$player = $_SESSION['player'];

// Initialisation de la connexion à la BDD
PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);

// Récupère toutes les parties
$allParties = PDOSquadro::getAllPartieSquadro();

$partiesEnAttente = [];
$partiesEnCours   = [];
$partiesTerminees = [];

// Tri des parties selon leur statut
foreach ($allParties as $partie) {
    switch ($partie->gameStatus) {
        case 'initialized':
            $partiesEnAttente[] = $partie;
            break;
        case 'waitingForPlayer':
            $partiesEnCours[] = $partie;
            break;
        case 'finished':
            $partiesTerminees[] = $partie;
            break;
    }
}

$errorMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newGame'])) {
        // Création d'une nouvelle partie
        $newPartie = new PartieSquadro($player);
        $partieId = PDOSquadro::createPartieSquadro($player->getNomJoueur(), $newPartie->toJson(0));
        if ($partieId > 0) {
            $newPartie->setPartieID($partieId);
            $_SESSION['partieSquadro'] = $newPartie;
            header("Location: index.php");
            exit();
        } else {
            $errorMsg = "Une erreur est survenue lors de la création de la partie.";
        }
    } elseif (isset($_POST['logout'])) {
        // Déconnexion
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Traitement pour rejoindre une partie
if (isset($_GET['partieSquadro'])) {
    $partieId = $_GET['partieSquadro'];
    $partieTrouvee = null;
    foreach ($partiesEnAttente as $partie) {
        if ($partie->getPartieID() == $partieId) {
            $partieTrouvee = $partie;
            break;
        }
    }
    if ($partieTrouvee && $partieTrouvee->gameStatus === 'initialized') {
        try {
            PDOSquadro::addPlayerToPartieSquadro($player->getNomJoueur(), $partieId);
            $updatedPartie = PDOSquadro::getPartieSquadroById($partieId);
            $_SESSION['partieSquadro'] = $updatedPartie;
            header("Location: index.php");
            exit();
        } catch (\Exception $e) {
            $errorMsg = "Erreur : " . $e->getMessage();
        }
    } else {
        $errorMsg = "La partie que vous essayez de rejoindre n'est pas disponible ou a déjà commencé.";
    }
}

// Génération du HTML via la fonction intoBalise

// Construction de la section head
$headContent  = SquadroUIGenerator::intoBalise("meta", "", ["charset" => "UTF-8"]);
$headContent .= SquadroUIGenerator::intoBalise("meta", "", ["name" => "viewport", "content" => "width=device-width, initial-scale=1"]);
$headContent .= SquadroUIGenerator::intoBalise("title", "Squadro - Accueil");
$headContent .= SquadroUIGenerator::intoBalise("link", "", ["rel" => "stylesheet", "href" => "https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css"]);
$head = SquadroUIGenerator::intoBalise("head", $headContent);

// Construction du contenu du body
$bodyContent = "";

// Titre de bienvenue
$bodyContent .= SquadroUIGenerator::intoBalise("h1", "Bienvenue, " . htmlspecialchars($player->getNomJoueur()), ["class" => "title"]);

// Formulaire pour créer une nouvelle partie
$newGameButton = SquadroUIGenerator::intoBalise("button", "Créer une nouvelle partie", [
    "class" => "button is-primary",
    "type"  => "submit",
    "name"  => "newGame"
]);
$formNewGame  = SquadroUIGenerator::intoBalise("form", $newGameButton, [
    "method" => "post",
    "action" => "index.php",
    "class"  => "mb-4"
]);
$bodyContent .= $formNewGame;

// Message d'erreur éventuel
if (!empty($errorMsg)) {
    $bodyContent .= SquadroUIGenerator::intoBalise("div", htmlspecialchars($errorMsg), ["class" => "notification is-danger"]);
}

// Colonnes pour afficher les parties

// Colonne des parties en attente
$listeAttente = "";
foreach ($partiesEnAttente as $partie) {
    $item = "Joueur actif : " . $partie->getNomJoueurActif($partie->getJoueurActif()->getNomJoueur());
    $link = SquadroUIGenerator::intoBalise("a", "Rejoindre", [
        "href"  => "index.php?partieSquadro=" . $partie->getPartieID(),
        "class" => "button is-link is-small ml-2"
    ]);
    $listeAttente .= SquadroUIGenerator::intoBalise("li", $item . " " . $link, ["class" => "box"]);
}
$colAttente = SquadroUIGenerator::intoBalise("div",
    SquadroUIGenerator::intoBalise("h2", "Parties en attente", ["class" => "subtitle"]) .
    SquadroUIGenerator::intoBalise("ul", $listeAttente),
    ["class" => "column"]
);

// Colonne des parties en cours
$listeCours = "";
foreach ($partiesEnCours as $partie) {
    $item = "Partie #" . $partie->getPartieID() . " - Statut : " . $partie->gameStatus;
    $link = SquadroUIGenerator::intoBalise("a", "Accéder au plateau", [
        "href"  => "public/index.php?partieSquadro=" . $partie->getPartieID(),
        "class" => "button is-link is-small ml-2"
    ]);
    $listeCours .= SquadroUIGenerator::intoBalise("li", $item . " " . $link, ["class" => "box"]);
}
$colCours = SquadroUIGenerator::intoBalise("div",
    SquadroUIGenerator::intoBalise("h2", "Parties en cours", ["class" => "subtitle"]) .
    SquadroUIGenerator::intoBalise("ul", $listeCours),
    ["class" => "column"]
);

// Colonne des parties terminées
$listeTerminees = "";
foreach ($partiesTerminees as $partie) {
    $item = "Partie #" . $partie->getPartieID() . " - Statut : Terminé";
    $listeTerminees .= SquadroUIGenerator::intoBalise("li", $item, ["class" => "box"]);
}
$colTerminees = SquadroUIGenerator::intoBalise("div",
    SquadroUIGenerator::intoBalise("h2", "Parties terminées", ["class" => "subtitle"]) .
    SquadroUIGenerator::intoBalise("ul", $listeTerminees),
    ["class" => "column"]
);

// Assemblage des colonnes
$columns = SquadroUIGenerator::intoBalise("div", $colAttente . $colCours . $colTerminees, ["class" => "columns"]);
$bodyContent .= $columns;

// Formulaire de déconnexion
$logoutButton = SquadroUIGenerator::intoBalise("button", "Se déconnecter", [
    "class" => "button is-danger",
    "type"  => "submit",
    "name"  => "logout"
]);
$formLogout  = SquadroUIGenerator::intoBalise("form", $logoutButton, [
    "method" => "post",
    "action" => "index.php"
]);
$bodyContent .= $formLogout;

// Encapsulation dans une section et un container
$container = SquadroUIGenerator::intoBalise("div", $bodyContent, ["class" => "container"]);
$section   = SquadroUIGenerator::intoBalise("section", $container, ["class" => "section"]);
$body      = SquadroUIGenerator::intoBalise("body", $section);

// Assemblage complet de la page
$html = "<!DOCTYPE html>\n" . SquadroUIGenerator::intoBalise("html", $head . $body);

// Affichage de la page
echo $html;
