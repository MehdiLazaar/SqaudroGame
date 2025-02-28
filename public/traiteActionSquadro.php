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

// Vérification de la session
if (!isset($_SESSION['plateau']) || !isset($_SESSION['joueur'])) {
    echo SquadroUIGenerator::pageDErreur("Session expirée ou non initialisée.");
    exit;
}

try {
    $plateau = PlateauSquadro::fromJson($_SESSION['plateau']);
} catch (Exception $e) {
    echo SquadroUIGenerator::pageDErreur("Erreur de récupération du plateau : " . $e->getMessage());
    session_destroy();
    exit;
}

$actionSquadro = new ActionSquadro($plateau);
$joueurActif = $_SESSION['joueur'];

// Traitement de la sélection d'une pièce
if (isset($_POST['x']) && isset($_POST['y'])) {
    $x = (int)$_POST['x'];
    $y = (int)$_POST['y'];

    if ($actionSquadro->estJouablePiece($x, $y)) {
        $_SESSION['pieceChoisi'] = ['x' => $x, 'y' => $y];
        $_SESSION['confirmation'] = true;

        // Affichage de la page de confirmation
        echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau, $x, $y);
        exit;
    } else {
        echo SquadroUIGenerator::pageDErreur("La pièce sélectionnée ne vous appartient pas ou est invalide.");
        exit;
    }
}

// Traitement de la confirmation du déplacement
if (isset($_POST['confirmer'])) {
    if ($_POST['confirmer'] === 'oui' && isset($_SESSION['pieceChoisi'])) {
        $x = $_SESSION['pieceChoisi']['x'];
        $y = $_SESSION['pieceChoisi']['y'];

        // Exécuter le déplacement de la pièce
        $actionSquadro->jouePiece($x, $y);

        // Mise à jour du plateau
        $_SESSION['plateau'] = $plateau->toJson();
    }

    // Dans tous les cas (oui ou non), on supprime les variables de confirmation
    unset($_SESSION['confirmation'], $_SESSION['pieceChoisi']);

    // Redirection vers la page de jeu
    header("Location: index.php");
    exit;
}
// 1) Gérer AnnulerChoix
if (isset($_POST['action']) && $_POST['action'] === 'annulerChoix') {
    // On oublie la pièce sélectionnée et on repasse à l'état ChoixPièce
    unset($_SESSION['confirmation'], $_SESSION['pieceChoisi']);

    // Redirection vers la page de jeu
    header("Location: index.php");
    exit;
}
// l'action rejouer
if (isset($_POST['action']) && $_POST['action'] === 'rejouer') {
    // On upprime les informations de session
    session_destroy();
    session_start();

    // On redirige vers index.php qui va ré-initialiser la partie
    header("Location: public/index.php");
    exit;
}

// Si aucune action détectée, retour au jeu
header("Location: index.php");
exit;