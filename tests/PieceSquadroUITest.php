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
        echo "<br>";

        // Test 2 : Génération d'une case neutre
        echo "Test 2 - Génération d'une case neutre :\n";
        echo PieceSquadroUI::generationCaseNeutre() . "\n\n";
        echo "<br>";

        // Test 3 : Génération d'une pièce noire (non cliquable)
        $pieceNoire = PieceSquadro::initNoirSud(); // Utilisez initNoirSud()
        echo "Test 3 - Génération d'une pièce noire (non cliquable) :\n";
        $htmlPieceNoire = PieceSquadroUI::generationPiece($pieceNoire, 0, 3);
        echo $htmlPieceNoire . "\n\n";
        echo "<br>";

        // Vérifie que l'attribut value contient les bonnes coordonnées
        if (strpos($htmlPieceNoire, 'value="0,3"') !== false) {
            echo "OK : L'attribut value contient bien '0,3'.\n";
        } else {
            echo "ERREUR : L'attribut value ne contient pas '0,3'.\n";
        }
        echo "<br>";

        // Test 4 : Génération d'une pièce blanche (cliquable)
        $pieceBlanche = PieceSquadro::initBlancOuest(); // Utilisez initBlancOuest()
        echo "Test 4 - Génération d'une pièce blanche (cliquable) :\n";
        $htmlPieceBlanche = PieceSquadroUI::generationPiece($pieceBlanche, 6, 1, true);
        echo $htmlPieceBlanche . "\n\n";
        echo "<br>";

        // Vérifie que l'attribut value contient les bonnes coordonnées
        if (strpos($htmlPieceBlanche, 'value="6,1"') !== false) {
            echo "OK : L'attribut value contient bien '6,1'.\n";
        } else {
            echo "ERREUR : L'attribut value ne contient pas '6,1'.\n";
        }
        echo "<br>";

        // Test 5 : Génération d'un formulaire
        echo "Test 5 - Génération d'un formulaire :\n";
        $htmlForm = PieceSquadroUI::generateForm(6, 1);
        echo $htmlForm . "\n\n";
        echo "<br>";

        // Vérifie que le formulaire contient les bonnes valeurs pour x et y
        $xFound = strpos($htmlForm, 'name="x" value="6"') !== false;
        $yFound = strpos($htmlForm, 'name="y" value="1"') !== false;

        if ($xFound && $yFound) {
            echo "OK : Le formulaire contient bien les coordonnées x=6 et y=1.\n";
        } else {
            echo "ERREUR : Le formulaire ne contient pas les coordonnées x=6 et y=1.\n";
        }
        echo "<br>";

        // Test 6 : Génération du plateau de jeu pour le joueur blanc
        echo "Test 6 - Génération du plateau de jeu pour le joueur blanc :\n";
        echo PieceSquadroUI::generatePlateau($plateau, PieceSquadro::BLANC) . "\n";
        echo "<br>";

        // Test 7 : Génération du plateau de jeu pour le joueur noir
        echo "Test 7 - Génération du plateau de jeu pour le joueur noir :\n";
        echo PieceSquadroUI::generatePlateau($plateau, PieceSquadro::NOIR) . "\n";
        echo "<br>";
    }
}
PieceSquadroUITest::run();