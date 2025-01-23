<?php

require_once '../src/PieceSquadro.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use src\ActionSquadro;
use src\PieceSquadro;
use src\PlateauSquadro;

class ActionSquadroTest extends TestCase
{
    private PlateauSquadro $plateau;
    private ActionSquadro $action;

    protected function setUp(): void
    {
        // Initialisation du plateau et de l'action
        $this->plateau = new PlateauSquadro();
        $this->action = new ActionSquadro($this->plateau);
    }

    /**
     * Teste la méthode estJouablePiece.
     */
    public function testEstJouablePiece(): void
    {
        // Vérifie qu'une pièce vide n'est pas jouable
        $this->assertFalse($this->action->estJouablePiece(0, 0));

        // Vérifie qu'une pièce valide est jouable
        $this->assertTrue($this->action->estJouablePiece(6, 1)); // Pièce blanche initiale
    }

    /**
     * Teste la méthode sortPiece.
     */
    public function testSortPiece(): void
    {
        // Simule la sortie d'une pièce
        $this->expectOutputString("La pièce de couleur 0 et de rang 1 est retirée du plateau.");
        $this->action->sortPiece(PieceSquadro::BLANC, 1);
    }

    /**
     * Teste la méthode remporteVictoire.
     */
    public function testRemporteVictoire(): void
    {
        // Vérifie qu'aucune couleur n'a gagné au début
        $this->assertFalse($this->action->remporteVictoire(PieceSquadro::BLANC));
        $this->assertFalse($this->action->remporteVictoire(PieceSquadro::NOIR));

        // Simule une victoire pour les blancs en retirant toutes les colonnes jouables
        $this->plateau->retireColonneJouable(1);
        $this->plateau->retireColonneJouable(2);
        $this->plateau->retireColonneJouable(3);
        $this->plateau->retireColonneJouable(4);
        $this->plateau->retireColonneJouable(5);

        $this->assertTrue($this->action->remporteVictoire(PieceSquadro::BLANC));
    }

    /**
     * Teste la méthode pieceDoitSortir.
     */
    public function testPieceDoitSortir(): void
    {
        // Crée une pièce blanche en position de sortie
        $piece = PieceSquadro::initBlancEst();
        $this->assertTrue($this->action->pieceDoitSortir($piece, 6, 5)); // Doit sortir à l'est

        // Crée une pièce noire en position de sortie
        $piece = PieceSquadro::initNoirSud();
        $this->assertTrue($this->action->pieceDoitSortir($piece, 5, 1)); // Doit sortir au sud
    }
}