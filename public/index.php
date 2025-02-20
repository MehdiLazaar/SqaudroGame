<?php
session_start();

use src\PlateauSquadro;
use src\ActionSquadro;
use src\PieceSquadro;
use src\SquadroUIGenerator;

require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/ActionSquadro.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';

// Initialisation du plateau si la session n'existe pas
if (!isset($_SESSION['plateau'])) {
    $plateau = new PlateauSquadro();
    $_SESSION['plateau'] = $plateau->toJson();
    $_SESSION['joueur'] = PieceSquadro::BLANC;
} else {
    try {
        $plateau = PlateauSquadro::fromJson($_SESSION['plateau']);
    } catch (Exception $e) {
        echo SquadroUIGenerator::pageDErreur("Erreur : Impossible de récupérer le plateau ! " . $e->getMessage());
        session_destroy();
        exit;
    }
}

$joueur = $_SESSION['joueur'];
$actionSquadro = new ActionSquadro($plateau);

// Si l'état de confirmation n'est pas actif, on affiche directement la page de jeu
if (!isset($_SESSION['confirmation']) || !$_SESSION['confirmation']) {
    echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueur);
    exit;
}

// Si l'utilisateur a sélectionné une pièce et est en phase de confirmation
if (isset($_SESSION['pieceChoisi'])) {
    $x = $_SESSION['pieceChoisi']['x'];
    $y = $_SESSION['pieceChoisi']['y'];
    echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau, $x, $y);
    exit;
}

// En cas d'erreur inattendue, retour au jeu
echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueur);
exit;