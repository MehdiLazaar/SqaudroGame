<?php

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadroUI.php';
use src\PieceSquadro;
use src\PieceSquadroUI;
use src\PlateauSquadro;

class PieceSquadroUITest
{
    public static function run()
    {
        // Crée un plateau de jeu
        $plateau = new PlateauSquadro();

        // Test 1 : Génération d'une case vide
        echo "Test 1 - Génération d'une case vide :\n";
        echo PieceSquadroUI::generationCaseVide() . "\n\n";

        // Test 2 : Génération d'une case neutre
        echo "Test 2 - Génération d'une case neutre :\n";
        echo PieceSquadroUI::generationCaseNeutre() . "\n\n";

        // Test 3 : Génération d'une pièce noire (non cliquable)
        $pieceNoire = PieceSquadro::initNoirNord();
        echo "Test 3 - Génération d'une pièce noire (non cliquable) :\n";
        echo PieceSquadroUI::generationPiece($pieceNoire, 0, 3) . "\n\n";

        // Test 4 : Génération d'une pièce blanche (cliquable)
        $pieceBlanche = PieceSquadro::initBlancEst();
        echo "Test 4 - Génération d'une pièce blanche (cliquable) :\n";
        echo PieceSquadroUI::generationPiece($pieceBlanche, 6, 1, true) . "\n\n";

        // Test 5 : Génération d'un formulaire
        echo "Test 5 - Génération d'un formulaire :\n";
        echo PieceSquadroUI::generateForm(6, 1) . "\n\n";

        // Test 6 : Génération du plateau de jeu pour le joueur blanc
        echo "Test 6 - Génération du plateau de jeu pour le joueur blanc :\n";
        echo PieceSquadroUI::generatePlateau($plateau, PieceSquadro::BLANC) . "\n";
    }
}

// Exécute les tests
PieceSquadroUITest::run();