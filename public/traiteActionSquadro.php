<?php

session_start();

use src\PlateauSquadro;
use src\ActionSquadro;
use src\PieceSquadro;
use src\SquadroUIGenerator;

require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/ActionSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';

// Si la session est invalidée ou n'existe pas encore, rediriger à la page d'accueil
if (!isset($_SESSION['plateau']) || !isset($_SESSION['joueur'])) {
    echo SquadroUIGenerator::pageDErreur("Session expirée ou non initialisée.");
    exit;
}

try {
    // Récupérer le plateau depuis la session
    $plateau = PlateauSquadro::fromJson($_SESSION['plateau']);
} catch (Exception $e) {
    echo SquadroUIGenerator::pageDErreur("Erreur de récupération du plateau de jeu : " . $e->getMessage());
    session_destroy();
    exit;
}

// Créer une instance de ActionSquadro pour gérer les actions
$actionSquadro = new ActionSquadro($plateau);
$joueurActif = $_SESSION['joueur'];

// Vérifier si une action de déplacement a été envoyée
if (isset($_POST['confirmer'])) {
    if ($_POST['confirmer'] === 'oui' && isset($_SESSION['pieceChoisi'])) {
        // Récupérer la position de la pièce choisie depuis la session
        $pieceChoisie = $_SESSION['pieceChoisi'];
        $x = $pieceChoisie['x'];
        $y = $pieceChoisie['y'];

        // Exécuter le déplacement de la pièce
        $actionSquadro->jouePiece($x, $y);

        // Mettre à jour le plateau dans la session
        $_SESSION['plateau'] = $plateau->toJson();

        // ✅ Redirection vers index.php après déplacement
        header("Location: index.php");
        exit;
    }

    // Si l'utilisateur a choisi "non", on le renvoie au jeu normalement
    header("Location: index.php");
    exit;
}


// Vérifier si l'action est de sélectionner une pièce
if (isset($_POST['x']) && isset($_POST['y'])) {
    $x = (int)$_POST['x'];
    $y = (int)$_POST['y'];

    // Vérifier si la pièce sélectionnée est bien celle du joueur actif
    if ($actionSquadro->estJouablePiece($x, $y)) {
        $_SESSION['pieceChoisi'] = ['x' => $x, 'y' => $y];

        // Afficher la page pour confirmer le déplacement
        echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau, $x, $y);
        exit;
    } else {
        // Si la pièce n'est pas jouable
        echo SquadroUIGenerator::pageDErreur("La pièce sélectionnée ne vous appartient pas ou est invalide.");
        exit;
    }
}

// Si aucune action n'a été envoyée, rediriger vers la page d'accueil du jeu
echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueurActif);
exit;
