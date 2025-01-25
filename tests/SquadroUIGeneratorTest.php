<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use src\PieceSquadro;
use src\PlateauSquadro;
use src\SquadroUIGenerator;

class SquadroUIGeneratorTest extends TestCase
{
    private PlateauSquadro $plateau;

    protected function setUp(): void
    {
        // Initialisation du plateau de jeu avant chaque test
        $this->plateau = new PlateauSquadro();
    }

    /**
     * Teste la génération de la page pour jouer une pièce.
     */
    public function testGenererPageJouerPiece(): void
    {
        // Génère la page pour le joueur blanc
        $html = SquadroUIGenerator::genererPageJouerPiece($this->plateau, PieceSquadro::BLANC);

        // Vérifie que le HTML contient le titre et le message du joueur actif
        $this->assertStringContainsString('<title>Jouer une pièce</title>', $html);
        $this->assertStringContainsString('C\'est au tour du joueur blanc de jouer.', $html);

        // Vérifie que le plateau est inclus dans le HTML
        $this->assertStringContainsString('<div class="plateau">', $html);
    }

    /**
     * Teste la génération de la page pour confirmer un déplacement.
     */
    public function testGenererPageConfirmerDeplacement(): void
    {
        // Génère la page pour confirmer un déplacement à la position (1, 0)
        $html = SquadroUIGenerator::genererPageConfirmerDeplacement($this->plateau, 1, 0);

        // Vérifie que le HTML contient le titre et le message de confirmation
        $this->assertStringContainsString('<title>Confirmer le déplacement</title>', $html);
        $this->assertStringContainsString('Confirmez-vous le déplacement de la pièce en (1, 0) ?', $html);

        // Vérifie que les boutons "Oui" et "Non" sont présents
        $this->assertStringContainsString('<button type="submit" name="confirmer" value="oui">Oui</button>', $html);
        $this->assertStringContainsString('<button type="submit" name="confirmer" value="non">Non</button>', $html);
    }

    /**
     * Teste la génération de la page de victoire.
     */
    public function testGenererPageVictoire(): void
    {
        // Génère la page de victoire pour le joueur noir
        $html = SquadroUIGenerator::genererPageVictoire($this->plateau, PieceSquadro::NOIR);

        // Vérifie que le HTML contient le titre et le message de victoire
        $this->assertStringContainsString('<title>Victoire !</title>', $html);
        $this->assertStringContainsString('Le joueur noir a gagné !', $html);

        // Vérifie que le bouton "Rejouer" est présent
        $this->assertStringContainsString('<button type="submit">Rejouer</button>', $html);
    }

    /**
     * Teste la génération d'un bouton personnalisé.
     */
    public function testGenererBouton(): void
    {
        // Génère un bouton avec le texte "Cliquez ici" et l'action "action.php"
        $html = SquadroUIGenerator::genererBouton("Cliquez ici", "action.php");

        // Vérifie que le HTML du bouton est correct
        $this->assertStringContainsString('<form action="action.php" method="POST">', $html);
        $this->assertStringContainsString('<button type="submit">Cliquez ici</button>', $html);
    }
}