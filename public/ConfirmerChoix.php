<?php
// ConfirmerChoix.php

require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';
require_once __DIR__ . '/../src/ActionSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';

use src\PieceSquadro;
use src\SquadroUIGenerator;
use src\ActionSquadro;
use src\PieceSquadroUI;

session_start();

// Redirection si aucune pièce n'est sélectionnée
if (!isset($_SESSION['pieceSelectionnee'])) {
    header("Location: ChoisirPiece.php");
    exit();
}

$plateau = $_SESSION['plateau'];
$pieceSelectionnee = $_SESSION['pieceSelectionnee'];
$x = $pieceSelectionnee['x'];
$y = $pieceSelectionnee['y'];
$joueurActif = $_SESSION['joueurActif'];

// Gérer la confirmation du déplacement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer'])) {
    $confirmation = $_POST['confirmer'];

    if ($confirmation === 'oui') {
        // Effectuer le déplacement
        $action = new ActionSquadro($plateau);
        try {
            $action->jouerPiece($x, $y, $joueurActif);
            unset($_SESSION['pieceSelectionnee']); // Effacer la pièce sélectionnée après le déplacement
            $_SESSION['joueurActif'] = $joueurActif === PieceSquadro::BLANC ? PieceSquadro::NOIR : PieceSquadro::BLANC;
            header("Location: ChoisirPiece.php");
            exit();
        } catch (Exception $e) {
            echo "Erreur lors du déplacement : " . htmlspecialchars($e->getMessage());
        }
    } elseif ($confirmation === 'non') {
        // Annuler le choix
        unset($_SESSION['pieceSelectionnee']);
        header("Location: ChoisirPiece.php");
        exit();
    }
}

// Afficher la page de confirmation
echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau, $x, $y);

// Ajouter un lien pour annuler directement
echo '<p><a href="AnnulerChoix.php">Annuler le choix</a></p>';