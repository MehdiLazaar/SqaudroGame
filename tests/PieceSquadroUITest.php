<?php


// Inclure les fichiers nécessaires avec le bon chemin relatif
require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';

use src\PieceSquadro;
use src\PlateauSquadro;
use src\PieceSquadroUI;

// Initialiser un plateau de jeu simple pour le test
$plateau = new PlateauSquadro();

// Définir une pièce blanche en position (1, 0)
$plateau->setPiece(PieceSquadro::initBlancOuest(), 1, 0);

// Définir une pièce noire en position (6, 1)
$plateau->setPiece(PieceSquadro::initNoirSud(), 6, 1);

// Définir une pièce vide en position (3, 3)
$plateau->setPiece(PieceSquadro::initVide(), 3, 3);

// Définir une case neutre en position (0, 0) - déjà fait par le constructeur

// Définir le joueur actif (par exemple, blanc)
$joueurActif = PieceSquadro::BLANC;

// Générer le plateau de jeu
$plateauHtml = PieceSquadroUI::generationPlateauJeu($plateau, $joueurActif);

// Afficher le HTML généré
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Test PieceSquadroUI</title>
    <link rel="stylesheet" href="path/to/tailwind.css" /> <!-- Assurez-vous que ce lien est correct -->
</head>
<body>
    <div class="container">
        <h1>Test de PieceSquadroUI</h1>
        ' . $plateauHtml . '
    </div>
</body>
</html>';