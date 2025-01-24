<?php

require_once '../src/PieceSquadro.php';
require __DIR__ . '/../vendor/autoload.php';
use src\PlateauSquadro;
use src\PieceSquadro;
use PHPUnit\Framework\TestCase;

class TestPlateauSquadro extends TestCase
{
    private PlateauSquadro $plateau;

    protected function setUp(): void
    {
        $this->plateau = new PlateauSquadro();
    }

    // Teste l'initialisation du plateau
    public function testInitialisationPlateau(): void
    {
        $plateau = $this->plateau->getPlateau();

        // Vérifie que le plateau est bien un tableau 7x7
        $this->assertCount(7, $plateau);
        foreach ($plateau as $ligne) {
            $this->assertCount(7, $ligne);
        }

        // Vérifie que les pièces blanches sont bien à gauche (ouest)
        for ($ligne = 1; $ligne <= 5; $ligne++) {
            $pieceBlanche = $this->plateau->getPiece($ligne, 0);
            $this->assertNotNull($pieceBlanche);
            $this->assertEquals(PieceSquadro::BLANC, $pieceBlanche->getCouleur());
            $this->assertEquals(PieceSquadro::OUEST, $pieceBlanche->getDirection()); // Vérifie OUEST
        }

        // Vérifie que les pièces noires sont bien en bas (sud)
        for ($colonne = 1; $colonne <= 5; $colonne++) {
            $pieceNoire = $this->plateau->getPiece(6, $colonne);
            $this->assertNotNull($pieceNoire);
            $this->assertEquals(PieceSquadro::NOIR, $pieceNoire->getCouleur());
            $this->assertEquals(PieceSquadro::SUD, $pieceNoire->getDirection()); // Vérifie SUD
        }

        // Vérifie que les cases neutres sont bien initialisées
        for ($x = 1; $x <= 5; $x++) {
            for ($y = 1; $y <= 5; $y++) {
                $pieceNeutre = $this->plateau->getPiece($x, $y);
                $this->assertNotNull($pieceNeutre);
                $this->assertEquals(PieceSquadro::NEUTRE, $pieceNeutre->getCouleur());
            }
        }
    }

    // Teste la méthode getPiece
    public function testGetPiece(): void
    {
        // Vérifie que les cases vides retournent null
        $this->assertNull($this->plateau->getPiece(0, 0));
        $this->assertNull($this->plateau->getPiece(6, 6));

        // Vérifie que les cases avec des pièces retournent bien les pièces
        $pieceBlanche = $this->plateau->getPiece(1, 0); // Pièce blanche à gauche (ouest)
        $this->assertNotNull($pieceBlanche);
        $this->assertEquals(PieceSquadro::BLANC, $pieceBlanche->getCouleur());

        $pieceNoire = $this->plateau->getPiece(6, 1); // Pièce noire en bas (sud)
        $this->assertNotNull($pieceNoire);
        $this->assertEquals(PieceSquadro::NOIR, $pieceNoire->getCouleur());
    }

    // Teste la méthode setPiece
    public function testSetPiece(): void
    {
        $piece = PieceSquadro::initBlancEst();
        $this->plateau->setPiece($piece, 3, 3);

        $pieceRecuperee = $this->plateau->getPiece(3, 3);
        $this->assertNotNull($pieceRecuperee);
        $this->assertEquals(PieceSquadro::BLANC, $pieceRecuperee->getCouleur());
    }

    // Teste la méthode retireLigneJouable
    public function testRetireLigneJouable(): void
    {
        $this->plateau->retireLigneJouable(3);
        $lignesJouables = $this->plateau->getLignesJouables();

        $this->assertNotContains(3, $lignesJouables);
        $this->assertEquals([1, 2, 4, 5], $lignesJouables);
    }

    // Teste la méthode retireColonneJouable
    public function testRetireColonneJouable(): void
    {
        $this->plateau->retireColonneJouable(2);
        $colonnesJouables = $this->plateau->getColonnesJouables();

        $this->assertNotContains(2, $colonnesJouables);
        $this->assertEquals([1, 3, 4, 5], $colonnesJouables);
    }

    // Teste la méthode getCoordDestination avec les vitesses de déplacement
    public function testGetCoordDestinationBlancAller(): void
    {
        // Place une pièce blanche en (6, 1) avec une direction EST (aller)
        $pieceBlanche = PieceSquadro::initBlancEst();
        $this->plateau->setPiece($pieceBlanche, 6, 1);

        // Teste le déplacement d'une pièce blanche vers l'est
        $coords = $this->plateau->getCoordDestination(6, 1);
        $this->assertEquals([6, 2], $coords); // Vitesse BLANC_V_ALLER[1] = 1
    }

    public function testGetCoordDestinationBlancRetour(): void
    {
        // Place une pièce blanche en (6, 3) avec une direction OUEST (retour)
        $pieceBlanche = PieceSquadro::initBlancOuest();
        $this->plateau->setPiece($pieceBlanche, 6, 3);

        // Teste le déplacement d'une pièce blanche vers l'ouest
        $coords = $this->plateau->getCoordDestination(6, 3);
        $this->assertEquals([6, 1], $coords); // Vitesse BLANC_V_RETOUR[3] = 2
    }

    public function testGetCoordDestinationNoirNord(): void
    {
        // Place une pièce noire en (0, 3) avec une direction NORD (aller)
        $pieceNoire = PieceSquadro::initNoirNord();
        $this->plateau->setPiece($pieceNoire, 0, 3);

        // Teste le déplacement d'une pièce noire vers le nord
        $coords = $this->plateau->getCoordDestination(0, 3);
        $this->assertEquals([0, 3], $coords); // Vitesse NOIR_V_ALLER[0] = 3, mais limité à 0
    }

    public function testGetCoordDestinationNoirSud(): void
    {
        // Place une pièce noire en (0, 3) avec une direction SUD (retour)
        $pieceNoire = PieceSquadro::initNoirSud();
        $this->plateau->setPiece($pieceNoire, 0, 3);

        // Teste le déplacement d'une pièce noire vers le sud
        $coords = $this->plateau->getCoordDestination(0, 3);
        $this->assertEquals([0, 3], $coords); // Vitesse NOIR_V_RETOUR[0] = 0 (pas de déplacement)
    }

    public function testGetCoordDestinationCaseVide(): void
    {
        // Teste le déplacement d'une case vide
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Aucune pièce à déplacer à la case (3, 3)");
        $this->plateau->getCoordDestination(3, 3); // Case vide
    }

    public function testGetCoordDestinationCaseNeutre(): void
    {
        // Teste le déplacement d'une case neutre
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Aucune pièce à déplacer à la case (2, 2)");
        $this->plateau->getCoordDestination(2, 2); // Case neutre
    }

    public function testGetDestinationCaseOccupee(): void
    {
        // Place une pièce blanche en (6, 1) avec une direction EST (aller)
        $pieceBlanche = PieceSquadro::initBlancEst();
        $this->plateau->setPiece($pieceBlanche, 6, 1);

        // Place une pièce noire en (6, 2) pour bloquer le déplacement
        $pieceNoire = PieceSquadro::initNoirNord();
        $this->plateau->setPiece($pieceNoire, 6, 2);

        // Teste la destination d'une pièce blanche vers l'est (case occupée)
        $piece = $this->plateau->getDestination(6, 1);
        $this->assertNotNull($piece); // La case (6, 2) est occupée par une pièce noire
        $this->assertEquals(PieceSquadro::NOIR, $piece->getCouleur());
    }

    // Teste la méthode toJson et fromJson
    public function testJsonSerialization(): void
    {
        $json = $this->plateau->toJson();
        $newPlateau = PlateauSquadro::fromJson($json);

        // Vérifie que les plateaux sont identiques
        $this->assertEquals($this->plateau->getPlateau(), $newPlateau->getPlateau());
    }

    // Teste la méthode __toString
    public function testToString(): void
    {
        $stringRepresentation = (string) $this->plateau;

        // Vérifie que les pièces blanches sont correctement représentées
        $this->assertStringContainsString("[BLANC OUEST]", $stringRepresentation);

        // Vérifie que les pièces noires sont correctement représentées
        $this->assertStringContainsString("[NOIR SUD]", $stringRepresentation);

        // Vérifie que les cases vides sont correctement représentées
        $this->assertStringContainsString("[VIDE]", $stringRepresentation);
    }
}