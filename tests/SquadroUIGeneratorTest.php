<?php

// Inclure les fichiers nécessaires avec le bon chemin relatif
require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';

use src\PieceSquadro;
use src\PlateauSquadro;
use src\SquadroUIGenerator;

// Démarrer la session
session_start();

// Initialiser le plateau et le joueur actif si la session est vide
if (!isset($_SESSION['plateau'])) {
    $_SESSION['plateau'] = new PlateauSquadro();
    $_SESSION['joueurActif'] = PieceSquadro::BLANC; // Le joueur blanc commence
}

$plateau = $_SESSION['plateau'];
$joueurActif = $_SESSION['joueurActif'];

// Vérifier si une pièce a été sélectionnée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x']) && isset($_POST['y'])) {
    $x = (int)$_POST['x'];
    $y = (int)$_POST['y'];

    // Afficher les informations de la pièce sélectionnée
    echo "Pièce sélectionnée : ($x, $y)<br>";

    // Stocker les coordonnées de la pièce sélectionnée dans la session
    $_SESSION['pieceSelectionnee'] = ['x' => $x, 'y' => $y];

    // Rediriger vers la page de confirmation
    header("Location: confirmer_deplacement.php");
    exit();
}

// Afficher la page pour sélectionner une pièce
echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueurActif);
