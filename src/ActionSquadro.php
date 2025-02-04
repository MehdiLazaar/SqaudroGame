<?php
namespace src;

use InvalidArgumentException;
use OutOfBoundsException;
use src\PieceSquadro;
use src\PlateauSquadro;

class ActionSquadro
{
    private PlateauSquadro $plateau;
    private int $countBlancSortie = 0;
    private int $countNoirSortie = 0;
    private bool $partieTerminee = false; // Flag pour bloquer le jeu après la victoire
    private array $historiquePositions = [];

    public function __construct(PlateauSquadro $plateau)
    {
        $this->plateau = $plateau;

        // Initialisation des compteurs dans la session avec des noms uniques
        if (!isset($_SESSION['squadro_count_noir_sortie'])) {
            $_SESSION['squadro_count_noir_sortie'] = $this->countNoirSortie;
        }
        if (!isset($_SESSION['squadro_count_blanc_sortie'])) {
            $_SESSION['squadro_count_blanc_sortie'] = $this->countBlancSortie;
        }
    }

    /**
     * Vérifie si une pièce est jouable à une position donnée.
     *
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @param int $joueurActif La couleur du joueur actif.
     * @return bool True si la pièce est jouable, false sinon.
     */
    public function estJouablePiece(int $x, int $y, int $joueurActif): bool
    {
        if ($this->partieTerminee) {
            return false; // Bloquer les mouvements si la partie est terminée
        }

        $piece = $this->plateau->getPiece($x, $y);
        return $piece !== null && $piece->getCouleur() === $joueurActif && !$this->aTermineAllerRetour($x, $y);
    }

    /**
     * Joue une pièce en déplaçant celle-ci et en appliquant les règles associées.
     *
     * @param int $x La position de départ en x.
     * @param int $y La position de départ en y.
     * @param int $joueurActif La couleur du joueur actif.
     */
    public function jouerPiece(int $x, int $y, int $joueurActif): void
    {
        if ($this->partieTerminee) {
            echo "La partie est terminée. Aucun mouvement possible.";
            return;
        }

        $piece = $this->plateau->getPiece($x, $y);

        if ($piece === null || $piece->getCouleur() !== $joueurActif) {
            throw new InvalidArgumentException("Cette pièce ne vous appartient pas ou n'est pas valide !");
        }

        // Calculer la destination
        [$newX, $newY] = $this->plateau->getCoordDestination($x, $y);
        echo "Déplacement de ($x, $y) vers ($newX, $newY)<br>";

        if ($newX < 0 || $newX >= 7 || $newY < 0 || $newY >= 7) {
            throw new OutOfBoundsException("Mouvement hors limites !");
        }

        // Gérer les collisions sur le trajet
        $this->gererCollisionsSurTrajet($x, $y, $newX, $newY, $piece);

        // Gérer les collisions multiples
        $this->gererCollisionsMultiples($newX, $newY, $piece);

        // Rendre la case actuelle vide
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
        echo "Case ($x, $y) rendue vide.<br>";

        // Placer la pièce à sa nouvelle position
        $this->plateau->setPiece($piece, $newX, $newY);
        echo "Pièce déplacée à ($newX, $newY).<br>";

        // Inverser la direction si nécessaire
        if ($this->aAtteintZoneRetournement($newX, $newY, $piece)) {
            $piece->inverseDirection();
            echo "Direction inversée.<br>";
        }

        // Retirer la pièce si elle a terminé son aller-retour
        if ($this->aTermineAllerRetour($newX, $newY)) {
            $this->sortirPiece($piece->getCouleur(), $newX, $newY);
            echo "Pièce retirée du plateau.<br>";
        }

        // Vérifier si une couleur a remporté la partie
        if ($this->remporteVictoire()) {
            $this->afficherMessageVictoire();
        }
    }

    /**
     * Gère les collisions sur le trajet de la pièce.
     *
     * @param int $x La position de départ en x.
     * @param int $y La position de départ en y.
     * @param int $newX La position finale en x.
     * @param int $newY La position finale en y.
     * @param PieceSquadro $piece La pièce qui se déplace.
     */
    private function gererCollisionsSurTrajet(int $x, int $y, int $newX, int $newY, PieceSquadro $piece): void
    {
        $couleur = $piece->getCouleur();

        if ($couleur === PieceSquadro::BLANC) {
            for ($col = min($y, $newY) + 1; $col <= max($y, $newY); $col++) {
                $pieceAdverse = $this->plateau->getPiece($x, $col);
                if ($pieceAdverse !== null && $pieceAdverse->getCouleur() === PieceSquadro::NOIR) {
                    $this->gererReculPieceAdverse($x, $col, $pieceAdverse);
                }
            }
        } elseif ($couleur === PieceSquadro::NOIR) {
            for ($row = min($x, $newX) + 1; $row <= max($x, $newX); $row++) {
                $pieceAdverse = $this->plateau->getPiece($row, $y);
                if ($pieceAdverse !== null && $pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
                    $this->gererReculPieceAdverse($row, $y, $pieceAdverse);
                }
            }
        }
    }

    /**
     * Gère le recul d'une pièce adverse.
     *
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @param PieceSquadro $pieceAdverse La pièce adverse.
     */
    private function gererReculPieceAdverse(int $x, int $y, PieceSquadro $pieceAdverse): void
    {
        $couleur = $pieceAdverse->getCouleur();

        // Calculer la nouvelle position pour la pièce adverse
        if ($couleur === PieceSquadro::BLANC) {
            $newY = $pieceAdverse->getDirection() === PieceSquadro::EST ? 0 : 6;
            $this->plateau->setPiece($pieceAdverse, $x, $newY);
        } elseif ($couleur === PieceSquadro::NOIR) {
            $newX = $pieceAdverse->getDirection() === PieceSquadro::NORD ? 6 : 0;
            $this->plateau->setPiece($pieceAdverse, $newX, $y);
        }

        // Rendre la case vide après le déplacement
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
    }

    /**
     * Gère les collisions multiples sur la case cible.
     *
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @param PieceSquadro $piece La pièce qui se déplace.
     */
    private function gererCollisionsMultiples(int $x, int $y, PieceSquadro $piece): void
    {
        $pieceSurCase = $this->plateau->getPiece($x, $y);
        if ($pieceSurCase !== null && $pieceSurCase->getCouleur() !== $piece->getCouleur()) {
            // Repousser la pièce adverse
            $this->gererReculPieceAdverse($x, $y, $pieceSurCase);
        }
    }

    /**
     * Vérifie si la pièce a atteint la zone de retournement.
     *
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @param PieceSquadro $piece La pièce à vérifier.
     * @return bool True si la pièce a atteint la zone de retournement, false sinon.
     */
    private function aAtteintZoneRetournement(int $x, int $y, PieceSquadro $piece): bool
    {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $y === 6) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $x === 0);
    }

    /**
     * Vérifie si la pièce a terminé son aller-retour.
     *
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @return bool True si la pièce a terminé son aller-retour, false sinon.
     */
    public function aTermineAllerRetour(int $x, int $y): bool
    {
        $piece = $this->plateau->getPiece($x, $y);
        return ($piece->getCouleur() === PieceSquadro::BLANC && $y === 0) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $x === 6);
    }

    /**
     * Vérifie si une pièce a déjà effectué son aller.
     *
     * @param PieceSquadro $piece La pièce à vérifier.
     * @return bool True si la pièce a déjà effectué son aller, false sinon.
     */
    private function aDejaEffectueAller(PieceSquadro $piece): bool
    {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $piece->getDirection() === PieceSquadro::OUEST) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $piece->getDirection() === PieceSquadro::SUD);
    }

    /**
     * Retire une pièce du plateau lorsqu'elle a terminé son aller-retour.
     *
     * @param int $couleur La couleur de la pièce.
     * @param int $x La position en x.
     * @param int $y La position en y.
     */
    public function sortirPiece(int $couleur, int $x, int $y): void
    {
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);

        if ($couleur === PieceSquadro::BLANC) {
            $_SESSION['squadro_count_blanc_sortie'] = ($_SESSION['squadro_count_blanc_sortie'] ?? 0) + 1;
        } elseif ($couleur === PieceSquadro::NOIR) {
            $_SESSION['squadro_count_noir_sortie'] = ($_SESSION['squadro_count_noir_sortie'] ?? 0) + 1;
        }
    }

    /**
     * Vérifie si une couleur a remporté la partie.
     *
     * @return bool True si une couleur a gagné, false sinon.
     */
    public function remporteVictoire(): bool
    {
        return ($_SESSION['squadro_count_blanc_sortie'] ?? 0) >= 4 || ($_SESSION['squadro_count_noir_sortie'] ?? 0) >= 4;
    }

    /**
     * Affiche un message de victoire et termine la partie.
     */
    private function afficherMessageVictoire(): void
    {
        $gagnant = ($_SESSION['squadro_count_blanc_sortie'] ?? 0) >= 4 ? "Blancs" : "Noirs";

        $text = <<<TEXT
<script src="https://cdn.tailwindcss.com"></script>
<div class="flex items-center justify-center min-h-screen bg-cover bg-center bg-no-repeat">
    <div class="max-w-md mx-auto text-center bg-white bg-opacity-60 p-8 rounded-lg shadow-lg">
        <div class="text-9xl font-bold text-indigo-600 mb-4">YOU WIN</div>
        <h1 class="text-4xl font-bold text-gray-800 mb-6">Le gagnant du jour est $gagnant</h1>
        <a href="?reset=true"
           class="inline-block bg-indigo-600 text-white font-semibold px-6 py-3 rounded-md hover:bg-indigo-700 transition-colors duration-300">
            Autre partie
        </a>
    </div>
</div>
TEXT;

        echo $text;
        $this->partieTerminee = true;
        exit();
    }
}