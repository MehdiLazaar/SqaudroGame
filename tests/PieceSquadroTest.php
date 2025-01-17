<?php

require_once '../src/PieceSquadro.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use src\PieceSquadro;


/**
 * Classe de test pour la classe PieceSquadro.
 */
class PieceSquadroTest extends TestCase
{
    /**
     * Teste l'initialisation d'une pièce vide.
     */
    public function testInitVide()
    {
        $piece = PieceSquadro::initVide();
        $this->assertEquals(PieceSquadro::VIDE, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::VIDE, $piece->getDirection());
    }

    /**
     * Teste l'initialisation d'une pièce neutre.
     */
    public function testInitNeutre()
    {
        $piece = PieceSquadro::initNeutre();
        $this->assertEquals(PieceSquadro::NEUTRE, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::NEUTRE, $piece->getDirection());
    }

    /**
     * Teste l'initialisation d'une pièce noire orientée au nord.
     */
    public function testInitNoirNord()
    {
        $piece = PieceSquadro::initNoirNord();
        $this->assertEquals(PieceSquadro::NOIR, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::NORD, $piece->getDirection());
    }

    /**
     * Teste l'initialisation d'une pièce noire orientée au sud.
     */
    public function testInitNoirSud()
    {
        $piece = PieceSquadro::initNoirSud();
        $this->assertEquals(PieceSquadro::NOIR, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::SUD, $piece->getDirection());
    }

    /**
     * Teste l'initialisation d'une pièce blanche orientée à l'est.
     */
    public function testInitBlancEst()
    {
        $piece = PieceSquadro::initBlancEst();
        $this->assertEquals(PieceSquadro::BLANC, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::EST, $piece->getDirection());
    }

    /**
     * Teste l'initialisation d'une pièce blanche orientée à l'ouest.
     */
    public function testInitBlancOuest()
    {
        $piece = PieceSquadro::initBlancOuest();
        $this->assertEquals(PieceSquadro::BLANC, $piece->getCouleur());
        $this->assertEquals(PieceSquadro::OUEST, $piece->getDirection());
    }

    /**
     * Teste la méthode __toString().
     */
    public function testToString()
    {
        $piece = PieceSquadro::initNoirNord();
        $this->assertEquals("PieceSquadro [Couleur: 1, Direction: 0]", (string)$piece);
    }

    /**
     * Teste la méthode inverseDirection().
     */
    public function testInverseDirection()
    {
        // vérif retournement pièces blanches
        $piece = PieceSquadro::initBlancOuest();
        $piece->inverseDirection();
        $direction = $piece->getDirection();
        $this->assertEquals(PieceSquadro::EST, $direction);
        // vérif retournement pièces noires
        $piece = PieceSquadro::initNoirNord();
        $piece->inverseDirection();
        $direction = $piece->getDirection();
        $this->assertEquals(PieceSquadro::SUD, $direction);
    }

    /**
     * Test la réversibilité de la méthode toJson avec la méthode fromJson
     */
    public function testJson() {
        $piece = PieceSquadro::initNoirNord();
        $json = $piece->toJson();
        $pieceFromJson = PieceSquadro::fromJson($json);
        $this->assertEquals($json, $pieceFromJson->toJson());
        $this->assertEquals($pieceFromJson->getCouleur(), $piece->getCouleur());
        $this->assertEquals($pieceFromJson->getDirection(), $piece->getDirection());
    }
}
