<?php
// test_squadrouigenerator.php

// Inclure les fichiers nécessaires avec le bon chemin relatif
require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';
require_once __DIR__ . '/../src/ActionSquadro.php';

use src\PieceSquadro;
use src\PlateauSquadro;
use src\SquadroUIGenerator;
use src\ActionSquadro;

// Démarrer la session
session_start();

// Initialiser le plateau et le joueur actif si la session est vide
if (!isset($_SESSION['plateau'])) {
    $_SESSION['plateau'] = new PlateauSquadro();
    $_SESSION['joueurActif'] = PieceSquadro::BLANC; // Le joueur blanc commence
}

$plateau = $_SESSION['plateau'];
$joueurActif = $_SESSION['joueurActif'];

// Gérer les actions POST (sélectionner une pièce ou confirmer le déplacement)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['x']) && isset($_POST['y'])) {
        $x = (int)$_POST['x'];
        $y = (int)$_POST['y'];

        // Vérifier si la pièce peut être jouée
        $piece = $plateau->getPiece($x, $y);
        if ($piece !== null && $piece->getCouleur() === $joueurActif) {
            // Stocker les coordonnées de la pièce sélectionnée dans la session
            $_SESSION['pieceSelectionnee'] = ['x' => $x, 'y' => $y];
            // Rediriger vers la page de confirmation
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<p>Pièce non valide ou ne peut pas être jouée.</p>";
        }
    } elseif (isset($_POST['confirmer'])) {
        $confirmation = $_POST['confirmer'];
        $pieceSelectionnee = $_SESSION['pieceSelectionnee'] ?? null;

        if ($pieceSelectionnee) {
            $x = $pieceSelectionnee['x'];
            $y = $pieceSelectionnee['y'];

            if ($confirmation === 'oui') {
                // Effectuer le déplacement
                $action = new ActionSquadro($plateau);
                try {
                    $action->jouePiece($x, $y);
                    unset($_SESSION['pieceSelectionnee']); // Effacer la pièce sélectionnée après le déplacement
                    $_SESSION['joueurActif'] = $_SESSION['joueurActif'] === PieceSquadro::BLANC ? PieceSquadro::NOIR : PieceSquadro::BLANC;
                    echo "Déplacement effectué avec succès.";
                } catch (Exception $e) {
                    echo "Erreur lors du déplacement : " . htmlspecialchars($e->getMessage());
                }
            } else {
                // Annuler le déplacement
                unset($_SESSION['pieceSelectionnee']);
                echo "Déplacement annulé.";
            }

            // Rediriger vers la page principale après traitement
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// Afficher la page pour sélectionner une pièce ou confirmer le déplacement
if (isset($_SESSION['pieceSelectionnee'])) {
    $pieceSelectionnee = $_SESSION['pieceSelectionnee'];
    $x = $pieceSelectionnee['x'];
    $y = $pieceSelectionnee['y'];
    echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau, $x, $y);
} else {
    echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueurActif);
}