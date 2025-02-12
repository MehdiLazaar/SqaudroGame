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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
    <div class="container">
        <h1>Test de PieceSquadroUI</h1>
        ' . $plateauHtml . '
    </div>
</body>
</html>';