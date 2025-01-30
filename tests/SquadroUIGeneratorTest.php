<?php

// Autoloading ou inclusion manuelle des fichiers nécessaires


require_once '../src/PlateauSquadro.php'; // Assurez-vous d'avoir inclus la classe PlateauSquadro
require_once '../src/PieceSquadroUI.php'; // Inclure la classe PieceSquadroUI si nécessaire
require_once '../src/SquadroUIGenerator.php'; // Inclure la classe SquadroUIGenerator

use src\PieceSquadro;
use src\PlateauSquadro;
use src\SquadroUIGenerator;

// Création d'un plateau fictif pour les tests
$plateau = new PlateauSquadro(); // Initialiser un objet PlateauSquadro fictif (à adapter à votre code)

// Simuler un joueur actif
$joueurActif = PieceSquadro::BLANC; // Par exemple, on simule que le joueur actif est le joueur blanc

// Test de la méthode générant la page de jeu pour jouer une pièce
echo "Test - Page Jouer une pièce :<br>";
echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueurActif);

// Test de la méthode générant la page de confirmation du déplacement
$x = 2; // Coordonnée x d'exemple
$y = 3; // Coordonnée y d'exemple
echo "<br><br>Test - Page Confirmer Déplacement :<br>";
echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau, $x, $y);

// Test de la méthode générant la page de victoire
$joueurGagnant = PieceSquadro::NOIR; // Par exemple, on simule que le joueur noir a gagné
echo "<br><br>Test - Page Victoire :<br>";
echo SquadroUIGenerator::genererPageVictoire($plateau, $joueurGagnant);

// Test de la génération d'un bouton
$texteBouton = "Rejouer";
$action = "index.php";
echo "<br><br>Test - Générer Bouton :<br>";
echo SquadroUIGenerator::genererBouton($texteBouton, $action);
?>
