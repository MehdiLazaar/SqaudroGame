<?php
namespace src;

use InvalidArgumentException;
use OutOfBoundsException;
use src\PieceSquadro;
use src\PlateauSquadro;

class ActionSquadro
{
    private PlateauSquadro $plateau;
    private bool $endgame = false;
    private array $dernierePos = [];

    public function __construct(PlateauSquadro $plateau)
    {
        $this->plateau = $plateau;
    }
    public function getJoueur(): int
    {
        return $_SESSION['joueur'] ?? PieceSquadro::BLANC;
    }

    public function changerJoueur(): void
    {
        $_SESSION['joueur'] = ($_SESSION['joueur'] === PieceSquadro::BLANC)
            ? PieceSquadro::NOIR
            : PieceSquadro::BLANC;
    }

    /**
     * Vérifie si une pièce est jouable à une position donnée.
     *
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @param int $joueur La couleur du joueur actif.
     * @return bool True si la pièce est jouable, false sinon.
     */
    public function estJouablePiece(int $x, int $y): bool
    {
        $joueurEnCours = $this->getJoueur();
        return !$this->endgame && $this->plateau->getPiece($x, $y)->getCouleur() === $joueurEnCours;
    }
    /**
     * Joue une pièce en déplaçant celle-ci et en appliquant les règles associées.
     *
     * @param int $x La position de départ en x.
     * @param int $y La position de départ en y.
     */
    public function jouePiece(int $x, int $y): void{
        if ($this->endgame) return;
        //On recupere le joueur en question soit noir ou blanc
        $joueur = $this->getJoueur();
        //Recuperer la piece à la position initiale
        $piece = $this->plateau->getPiece($x, $y);

        // Verifier si la couleur de la piece correspond à celle du joueur en question
        if ($piece->getCouleur() !== $joueur) {
            throw new InvalidArgumentException("ce n'est pas votre piece !");
        }
        // Récupérer les nouvelles coordonnées de destination
        [$newX, $newY] = $this->plateau->getCoordDestination($x, $y);

        if ($this->estHorsLimites($newX, $newY) || !$this->caseLibre($newX, $newY)) {
            // Sortie si la destination est hors limites ou occupée
            return;
        }
        // Vérification des pièces traversées et application des règles de recul
        $this->traiterCollisions($piece, $x, $y, $newX, $newY);

        $this->plateau->setPiece($piece,$newX, $newY);
        $this->plateau->setPiece(PieceSquadro::initVide(),$x, $y);
        //historique du deplacement
        $this->dernierePos["$newX-$newY"][] = ["x" => $x, "y" => $y];
        // Inversion de direction si la pièce atteint un bord opposé
        if ($this->BordPlateauOuestEstNordSud($newX, $newY, $piece)) {
            $piece->inverseDirection();
        }
        //On verifie si la piece a terminé son trajet et doit etre comptée comme sortie
        if (($newX === 6 && $piece->getCouleur() === PieceSquadro::NOIR) ||
            ($newY === 0 && $piece->getCouleur() === PieceSquadro::BLANC)) {
            $this->sortPiece($piece->getCouleur(), ($piece->getCouleur() === PieceSquadro::BLANC) ? $x : $y);
        }

        if ($this->remporteVictoire($piece->getCouleur())) {
            $this->victoire();
        }
        //On change le joueur du blanc vers noir et vice versa.
        $this->chnagerJoueur();
    }
    /**
     *
     * @param PieceSquadro $piece la piece en question
     * @param integer $x La position en x de la pièce.
     * @param integer $y La position en y de la pièce.
     * @param integer $newX La nouvelle position en x de la pièce.
     * @param integer $newY La nouvelle position en y de la pièce.
     * @return void on ne retourne rien
     * Fonction qui permet de verifier les collisions entre les pieces blanches et noires
     */
    private function traiterCollisions(PieceSquadro $piece, int $x, int $y, int $newX, int $newY): void {
        // Détection des collisions sur la trajectoire
        if ($piece->getCouleur() === PieceSquadro::BLANC) {
            $start = min($y, $newY) + 1;
            $end = max($y, $newY);

            for ($i = $start; $i <= $end; $i++) {
                $pieceSurCase = $this->plateau->getPiece($x, $i);
                if ($pieceSurCase->getCouleur() === PieceSquadro::NOIR) {
                    // Reculer la pièce noire
                    $this->reculePiece($x, $i);
                }
            }
        } elseif ($piece->getCouleur() === PieceSquadro::NOIR) {
            $start = min($x, $newX) + 1;
            $end = max($x, $newX);

            for ($i = $start; $i <= $end; $i++) {
                $pieceSurCase = $this->plateau->getPiece($i, $y);
                if ($pieceSurCase->getCouleur() === PieceSquadro::BLANC) {
                    // Reculer la pièce blanche
                    $this->reculePiece($i, $y);
                }
            }
        }

        // Vérification de la case de destination
        $pieceSurCase = $this->plateau->getPiece($newX, $newY);
        if ($pieceSurCase->getCouleur() !== PieceSquadro::VIDE && $pieceSurCase->getCouleur() !== $piece->getCouleur()) {
            $this->reculePiece($newX, $newY);
        }
    }
    // Gestion du recul d'une pièce adverse
    private function reculePiece(int $x, int $y): void
    {
        $piece = $this->plateau->getPiece($x, $y);

        if ($piece->getCouleur() === PieceSquadro::VIDE) return;

        if ($piece->getCouleur() === PieceSquadro::BLANC) {
            $departX = $x;
            $departY = $this->estEnRetour($piece, $x, $y) ? 6 : 0;
        } elseif ($piece->getCouleur() === PieceSquadro::NOIR) {
            $departX = $this->estEnRetour($piece, $x, $y) ? 0 : 6;
            $departY = $y;
        }

        $this->plateau->setPiece($piece,$departX, $departY);
        $this->plateau->setPiece(PieceSquadro::initVide(),$x, $y);
    }
    // Vérification des limites du plateau
    private function estHorsLimites(int $x, int $y): bool
    {
        return $x < 0 || $x >= 7 || $y < 0 || $y >= 7;
    }
    private function estEnRetour(PieceSquadro $piece, int $x, int $y): bool {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $y > 3) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $x < 3);
    }

    // Vérification si une case est vide
    private function caseLibre(int $x, int $y): bool
    {
        return $this->plateau->getPiece($x, $y)->getCouleur() === PieceSquadro::VIDE;
    }
    //
    private function BordPlateauOuestEstNordSud(int $x, int $y, PieceSquadro $piece): bool {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $y === 6) ||
            ($piece->getCouleur() === PieceSquadro::NOIR && $x === 0);
    }

    /**
     * Retire une pièce du plateau lorsqu'elle a terminé son aller-retour.
     *
     * @param int $couleur La couleur de la piece.
     * @param int $rang le rang de la piece
     */
    public function sortPiece(int $couleur, int $rang): void
    {
        if ($couleur === PieceSquadro::BLANC) {
            $_SESSION['piecesBlanchesSorties'][] = $rang;
        } elseif ($couleur === PieceSquadro::NOIR) {
            $_SESSION['piecesNoiresSorties'][] = $rang;
        }
    }

    /**
     * Vérifie si une couleur a remporté la partie.
     *
     * @return bool True si une couleur a gagné, false sinon.
     */
    public function remporteVictoire(int $couleur): bool {
        return ($couleur === PieceSquadro::BLANC && count($_SESSION['piecesBlanchesSorties'] ?? []) >= 4) ||
            ($couleur === PieceSquadro::NOIR && count($_SESSION['piecesNoiresSorties'] ?? []) >= 4);
    }

    /**
     * Affiche un message de victoire et termine la partie.
     */
    private function victoire(): void{
        $gagnant = (count($_SESSION['piecesBlanchesSorties'] ?? []) >= 4) ? "Blancs" : "Noirs";

        echo '<div class="hero is-info is-fullheight">
            <div class="hero-body">
                <div class="container has-text-centered">
                    <h1 class="title is-size-1 has-text-white">YOU WIN</h1>
                    <h2 class="subtitle is-size-3 has-text-white">Le gagnant du jour est ' . $gagnant . '</h2>
                    <a href="reset.php" class="button is-primary is-large mt-5">Autre partie</a>
                </div>
            </div>
        </div>';

        $this->endgame = true;
        exit();
    }

}