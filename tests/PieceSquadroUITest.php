<?php

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadroUI.php';

use src\PieceSquadro;
use src\PieceSquadroUI;
use src\PlateauSquadro;

// Test 1 : Génération d'une pièce blanche active
$pieceBlanche = PieceSquadro::initBlancOuest();
echo PieceSquadroUI::generationPiece($pieceBlanche, 1, 0, true);
echo "<br>";

// Test 2 : Génération d'une case vide inactive
echo PieceSquadroUI::generationCaseVide(2, 3, false);
echo "<br>";
// Test 3 : Génération d'une case neutre
echo PieceSquadroUI::generationCaseNeutre();
echo "<br>";
// Test 4 : Génération du plateau complet (joueur blanc)
$plateau = new PlateauSquadro();
echo PieceSquadroUI::generationPlateauJeu($plateau, PieceSquadro::BLANC);

?>