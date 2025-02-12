<?php
use PHPUnit\Framework\TestCase;
use src\PlateauSquadro;
use src\PieceSquadro;

class TestPlateauSquadro extends TestCase
{
    private PlateauSquadro $plateau;

    protected function setUp(): void
    {
        // Crée une instance de PlateauSquadro avant chaque test
        $this->plateau = new PlateauSquadro();
    }
    public function testGetCoordDestinationNoirSud()
    {
        // Place une pièce noire sur la case [6][1] avec la direction SUD
        $pieceNoire = PieceSquadro::initNoirSud();
        $this->plateau->setPiece($pieceNoire, 6, 1);

        // Vérifie que la destination de cette pièce est correcte
        $coords = $this->plateau->getCoordDestination(6, 1);

        // La pièce noire se déplace verticalement vers le sud avec une vitesse spécifique
        $vitesseNoir = PlateauSquadro::NOIR_V_ALLER[1]; // Vérifiez la valeur ici
        $expectedCoords = [6 - $vitesseNoir, 1]; // La destination devrait être [5, 1] si vitesseNoir est 1

        $this->assertEquals($expectedCoords, $coords);
    }

    public function testGetCoordDestinationHorsLimites()
    {
        // Place une pièce noire sur la case [6][1] avec la direction SUD
        $pieceNoire = PieceSquadro::initNoirSud();
        $this->plateau->setPiece($pieceNoire, 6, 1);

        // Cette pièce noire se déplacerait hors des limites du plateau
        $coords = $this->plateau->getCoordDestination(6, 1);

        // La destination ne doit pas sortir du plateau (minimale à 0)
        $vitesseNoir = PlateauSquadro::NOIR_V_ALLER[1]; // Vérifiez la valeur ici
        $newX = max(0, min(6, 6 - $vitesseNoir)); // S'assurer que newX ne dépasse pas les limites
        $expectedCoords = [$newX, 1]; // Destination corrigée avec les limites du plateau

        $this->assertEquals($expectedCoords, $coords);
    }

    public function testGetCoordDestinationRetour()
    {
        // Place une pièce blanche sur la case [1][0] avec la direction EST
        $pieceBlanche = PieceSquadro::initBlancEst();
        $this->plateau->setPiece($pieceBlanche, 1, 0);

        // Vérifie que la destination de cette pièce est correcte (retour vers l'est)
        $coords = $this->plateau->getCoordDestination(1, 0);

        // La pièce blanche se déplace horizontalement en Est avec une vitesse spécifique
        $vitesseBlanc = PlateauSquadro::BLANC_V_RETOUR[0]; // Vérifiez la valeur ici
        $expectedCoords = [1, 0 + $vitesseBlanc]; // La destination devrait être [1, 2] si vitesseBlanc est 2

        $this->assertEquals($expectedCoords, $coords);
    }
}