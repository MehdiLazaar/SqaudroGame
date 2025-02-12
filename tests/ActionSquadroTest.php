<?php
use PHPUnit\Framework\TestCase;
use src\ActionSquadro;
use src\PieceSquadro;
use src\PlateauSquadro;

class ActionSquadroTest extends TestCase
{
    private $plateau;
    private $actionSquadro;

    protected function setUp(): void
    {
        // Création d'un objet PlateauSquadro réel
        $this->plateau = new PlateauSquadro();

        // Initialisation de l'actionSquadro avec le PlateauSquadro
        $this->actionSquadro = new ActionSquadro($this->plateau);

        // Configuration de la session pour les tests
        $_SESSION['joueur'] = PieceSquadro::BLANC;
    }

    public function testGetJoueurRetourneBlancParDefaut()
    {
        // Test de la couleur du joueur actif
        $this->assertEquals(PieceSquadro::BLANC, $this->actionSquadro->getJoueur());
    }

    public function testChangerJoueur()
    {
        // Test de l'inversion du joueur
        $this->actionSquadro->changerJoueur();
        $this->assertEquals(PieceSquadro::NOIR, $_SESSION['joueur']);

        $this->actionSquadro->changerJoueur();
        $this->assertEquals(PieceSquadro::BLANC, $_SESSION['joueur']);
    }

    public function testEstJouablePieceAvecPieceBlanche()
    {
        // Utilisation de la méthode statique pour créer une pièce blanche
        $pieceBlanche = PieceSquadro::initBlancEst();

        // Placer la pièce blanche sur le plateau
        $this->plateau->setPiece($pieceBlanche, 0, 0);

        // Test si la pièce est jouable par le joueur actuel (blanc)
        $this->assertTrue($this->actionSquadro->estJouablePiece(0, 0));
    }

    public function testEstJouablePieceAvecPieceNoire()
    {
        // Utilisation de la méthode statique pour créer une pièce noire
        $pieceNoire = PieceSquadro::initNoirSud();

        // Placer la pièce noire sur le plateau
        $this->plateau->setPiece($pieceNoire, 0, 0);

        // Test si la pièce n'est pas jouable par le joueur actuel (blanc)
        $this->assertFalse($this->actionSquadro->estJouablePiece(0, 0));
    }

    public function testJouePieceAvecPieceIncorrecte()
    {
        // Utilisation de la méthode statique pour créer une pièce noire
        $pieceNoire = PieceSquadro::initNoirSud();

        // Placer la pièce noire sur le plateau
        $this->plateau->setPiece($pieceNoire, 0, 0);

        // Essayer de jouer une pièce incorrecte (le joueur est blanc)
        $this->expectException(InvalidArgumentException::class);
        $this->actionSquadro->jouePiece(0, 0);  // Le joueur est blanc, mais la pièce est noire
    }

    public function testSortPiece()
    {
        // Utilisation de la méthode statique pour créer une pièce blanche
        $pieceBlanche = PieceSquadro::initBlancEst();

        // Placer la pièce blanche sur le plateau
        $this->plateau->setPiece($pieceBlanche, 0, 0);

        // Simuler la sortie de la pièce
        $this->actionSquadro->sortPiece(PieceSquadro::BLANC, 2);

        // Vérifier que la pièce blanche a été ajoutée à la session des pièces sorties
        $this->assertCount(1, $_SESSION['piecesBlanchesSorties']);
        $this->assertEquals(2, $_SESSION['piecesBlanchesSorties'][0]);
    }

    public function testRemporteVictoireAvecBlanc()
    {
        // Simuler des pièces sorties pour le joueur blanc
        $_SESSION['piecesBlanchesSorties'] = [1, 2, 3, 4];

        // Vérification si le joueur blanc a gagné
        $this->assertTrue($this->actionSquadro->remporteVictoire(PieceSquadro::BLANC));
    }

    public function testRemporteVictoireAvecNoir()
    {
        // Simuler des pièces sorties pour le joueur noir
        $_SESSION['piecesNoiresSorties'] = [1, 2, 3, 4];

        // Vérification si le joueur noir a gagné
        $this->assertTrue($this->actionSquadro->remporteVictoire(PieceSquadro::NOIR));
    }
}
