<?php
require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadroUI.php';
use src\PieceSquadro;
use src\PieceSquadroUI;
use src\PlateauSquadro;
// deplacement.php
session_start();

// Récupérer le plateau et la pièce sélectionnée
$plateau = $_SESSION['plateau'];
$joueurActif = $_SESSION['joueurActif'];
$pieceSelectionnee = $_SESSION['pieceSelectionnee'] ?? null;

// Si aucune pièce n'est sélectionnée, on redirige ou on affiche un message d'erreur
if ($pieceSelectionnee === null) {
    echo "Aucune pièce sélectionnée.";
    exit;
}

// Récupérer les nouvelles coordonnées de déplacement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x'], $_POST['y'])) {
    $nouvelleX = (int)$_POST['x'];
    $nouvelleY = (int)$_POST['y'];

    // Déplacer la pièce
    $x = $pieceSelectionnee['x'];
    $y = $pieceSelectionnee['y'];

    // Assurez-vous que la méthode `deplacerPiece` existe et fonctionne
    $plateau->deplacerPiece($x, $y, $nouvelleX, $nouvelleY);

    // Mettre à jour la session avec le nouveau plateau
    $_SESSION['plateau'] = $plateau;

    // Passer au joueur suivant
    $_SESSION['joueurActif'] = ($joueurActif === PieceSquadro::BLANC) ? PieceSquadro::NOIR : PieceSquadro::BLANC;

    // Rediriger vers la page d'index pour voir le plateau mis à jour
    header('Location: index.php');
    exit;
}

// Afficher un formulaire pour déplacer la pièce
echo "<form action='deplacement.php' method='POST'>
        <input type='number' name='x' placeholder='Nouvelle X' required>
        <input type='number' name='y' placeholder='Nouvelle Y' required>
        <button type='submit'>Déplacer</button>
      </form>";
