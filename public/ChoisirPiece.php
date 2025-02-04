<?php
// ChoisirPiece.php

require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';

use src\PieceSquadro;
use src\PieceSquadroUI;
use src\PlateauSquadro;
use src\SquadroUIGenerator;

session_start();

// Initialiser le plateau et le joueur actif si la session est vide
if (!isset($_SESSION['plateau'])) {
    $_SESSION['plateau'] = new PlateauSquadro();
    $_SESSION['joueurActif'] = PieceSquadro::BLANC; // Le joueur blanc commence
}

$plateau = $_SESSION['plateau'];
$joueurActif = $_SESSION['joueurActif'];

// Gérer la sélection d'une pièce
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x']) && isset($_POST['y'])) {
    $x = (int)$_POST['x'];
    $y = (int)$_POST['y'];

    // Vérifier si la pièce peut être jouée
    $piece = $plateau->getPiece($x, $y);
    if ($piece !== null && $piece->getCouleur() === $joueurActif) {
        // Stocker les coordonnées de la pièce sélectionnée dans la session
        $_SESSION['pieceSelectionnee'] = ['x' => $x, 'y' => $y];
        header("Location: ConfirmerChoix.php");
        exit();
    } else {
        echo "<p>Pièce non valide ou ce n'est pas votre tour.</p>";
    }
}

// Afficher le plateau pour choisir une pièce
echo SquadroUIGenerator::getDebutHTML("Choisir une pièce");
echo '<div class="plateau">';
echo PieceSquadroUI::generationPlateauJeu($plateau, $joueurActif);
echo '</div>';
echo '<p>C\'est au tour du joueur ' . ($joueurActif === PieceSquadro::BLANC ? 'blanc' : 'noir') . ' de jouer.</p>';
echo '<a href="Rejouer.php" class="btn-rejouer">Recommencer une nouvelle partie</a>';
echo SquadroUIGenerator::getFinHTML();