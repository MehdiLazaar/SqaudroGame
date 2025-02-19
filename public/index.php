<?php
session_start();

use src\PlateauSquadro;
use src\ActionSquadro;
use src\PieceSquadro;
use src\SquadroUIGenerator;
use src\PieceSquadroUI;

require_once __DIR__ . '/../src/PieceSquadro.php';
require_once __DIR__ . '/../src/PlateauSquadro.php';
require_once __DIR__ . '/../src/ActionSquadro.php';
require_once __DIR__ . '/../src/PieceSquadroUI.php';
require_once __DIR__ . '/../src/SquadroUIGenerator.php';

if (!isset($_SESSION['plateau'])) {
    $plateau = new PlateauSquadro();
    $_SESSION['plateau'] = $plateau->toJson();
    $_SESSION['joueur'] = PieceSquadro::BLANC;
} else {
    try {
        $plateau = PlateauSquadro::fromJson($_SESSION['plateau']);
    } catch (Exception $e) {
        echo SquadroUIGenerator::pageDErreur("Erreur y a pas de plateau de jeu!!!! : " . $e->getMessage());
        session_destroy();
        exit;
    }
}

$actionSquadro = new ActionSquadro($plateau);
$joueur = $_SESSION['joueur'];

if ($actionSquadro->remporteVictoire($joueur)) {
    $_SESSION['partieTerminee'] = true;
    $_SESSION['gagnant'] = $joueur;
    echo SquadroUIGenerator::genererPageVictoire($plateau, $joueur);
    session_destroy();
    exit;
}

$action = $_POST['action'] ?? 'default';

switch ($action) {
    case 'selectionnerPiece':
        if (isset($_POST['x']) && isset($_POST['y'])) {
            $_SESSION['pieceChoisi'] = [
                'x' => (int)$_POST['x'],
                'y' => (int)$_POST['y']
            ];
            echo SquadroUIGenerator::genererPageConfirmerDeplacement(
                $plateau,
                $_SESSION['pieceChoisi']['x'],
                $_SESSION['pieceChoisi']['y'],
            );
            exit;
        }
        break;

    case 'confirmerDeplacement':
        if (isset($_SESSION['pieceChoisi'])) {
            $ligne = $_SESSION['pieceChoisi']['x'];
            $colonne = $_SESSION['pieceChoisi']['y'];

            $actionSquadro->jouePiece($ligne, $colonne);
            unset($_SESSION['pieceChoisi']);

            if ($actionSquadro->remporteVictoire($joueur)) {
                $_SESSION['partieTerminee'] = true;
                $_SESSION['gagnant'] = $joueur;
                echo SquadroUIGenerator::genererPageVictoire($plateau, $joueur);
                session_destroy();
                exit;
            }
        }
        break;

    case 'annulerSelection':
        unset($_SESSION['pieceChoisi']);
        break;

    default:
        echo SquadroUIGenerator::genererPageJouerPiece($plateau, $joueur);
        exit;
}

$_SESSION['plateau'] = $plateau->toJson();

