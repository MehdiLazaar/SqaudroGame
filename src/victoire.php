<?php
require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadroUI.php';
use src\PieceSquadro;
use src\PieceSquadroUI;
use src\PlateauSquadro;
// victoire.php
session_start();

// Vérifie si un joueur a gagné
$joueurGagnant = $_SESSION['victoire'] ?? null;
if ($joueurGagnant === null) {
    echo "Aucune victoire enregistrée.";
    exit;
}

echo "Le joueur " . ($joueurGagnant === PieceSquadro::BLANC ? 'blanc' : 'noir') . " a gagné !";

// Formulaire pour recommencer la partie
echo "<form action='index.php' method='POST'>
        <button type='submit'>Rejouer</button>
      </form>";
