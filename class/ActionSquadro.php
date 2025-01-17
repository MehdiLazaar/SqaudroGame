<?php
//nom de package
namespace squadroGame;
class ActionSquadro {
    private PlateauSquadro $plateau;

    public function __construct(PlateauSquadro $p) {
        $this->plateau = $p;
    }

    /**
     * Vérifie si une pièce est jouable à une position donnée.
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @return bool True si la pièce est jouable, false sinon.
     */
    public function estJouablePiece(int $x, int $y): bool {
        $piece = $this->plateau->getPiece($x, $y);

        // Vérifie si la case contient une pièce valide
        if ($piece->getCouleur() === PieceSquadro::VIDE) {
            return false;
        }

        // Vérifie si la pièce peut avancer dans sa direction
        $direction = $piece->getDirection();
        $destination = $this->plateau->getCoordDestination($x, $y);

        if ($destination === null) {
            return false;
        }

        return true;
    }

    /**
     * Joue une pièce à une position donnée.
     * Déplace la pièce selon sa direction, retourne les pièces adverses sautées,
     * et la retire si elle a terminé son parcours.
     * @param int $x La position en x.
     * @param int $y La position en y.
     */
    public function jouePiece(int $x, int $y): void {
        $piece = $this->plateau->getPiece($x, $y);

        if (!$this->estJouablePiece($x, $y)) {
            throw new Exception("La pièce ne peut pas être jouée.");
        }

        // Calcul de la destination
        [$newX, $newY] = $this->plateau->getCoordDestination($x, $y);

        // Déplace la pièce
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
        $this->plateau->setPiece($piece, $newX, $newY);

        // Vérifie si la pièce doit être retirée (a terminé son aller-retour)
        if ($this->pieceDoitSortir($piece, $newX, $newY)) {
            $this->sortPiece($piece->getCouleur(), $piece->getDirection());
        }

        // Gestion des pièces sautées
        $this->gererReculPieces($x, $y, $newX, $newY);
    }

    /**
     * Renvoie une pièce au début de son parcours (aller ou retour).
     * @param int $x La position en x.
     * @param int $y La position en y.
     */
    public function reculePiece(int $x, int $y): void {
        $piece = $this->plateau->getPiece($x, $y);
        $direction = $piece->getDirection();

        // Réinitialise la position de la pièce selon sa direction
        if ($direction === PieceSquadro::NORD || $direction === PieceSquadro::SUD) {
            $newX = ($direction === PieceSquadro::NORD) ? 0 : 5;
            $newY = $y;
        } else {
            $newY = ($direction === PieceSquadro::EST) ? 5 : 0;
            $newX = $x;
        }

        // Place la pièce à son point de départ
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
        $this->plateau->setPiece($piece, $newX, $newY);
    }

    /**
     * Retire une pièce du plateau lorsqu'elle a fini son aller-retour.
     * @param int $couleur La couleur de la pièce.
     * @param int $rang Le rang ou l'identifiant de la pièce.
     */
    public function sortPiece(int $couleur, int $rang): void {
        // Placeholder : log ou suppression selon l'identité de la pièce
        echo "La pièce de couleur {$couleur} et de rang {$rang} est retirée du plateau.";
    }

    /**
     * Vérifie si une couleur a remporté la victoire.
     * @param int $couleur La couleur à vérifier.
     * @return bool True si la couleur a gagné, false sinon.
     */
    public function remporteVictoire(int $couleur): bool {
        $lignesJouables = $this->plateau->getLignesJouables();
        $colonnesJouables = $this->plateau->getColonnesJouables();

        if ($couleur === PieceSquadro::BLANC && empty($colonnesJouables)) {
            return true;
        }

        if ($couleur === PieceSquadro::NOIR && empty($lignesJouables)) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si une pièce doit être retirée après son déplacement.
     * @param PieceSquadro $piece La pièce à vérifier.
     * @param int $x La position en x.
     * @param int $y La position en y.
     * @return bool True si la pièce doit être retirée, false sinon.
     */
    private function pieceDoitSortir(PieceSquadro $piece, int $x, int $y): bool {
        $direction = $piece->getDirection();
        return ($direction === PieceSquadro::NORD && $x === 0) ||
            ($direction === PieceSquadro::SUD && $x === 5) ||
            ($direction === PieceSquadro::EST && $y === 5) ||
            ($direction === PieceSquadro::OUEST && $y === 0);
    }

    /**
     * Gère le recul des pièces adverses sautées lors d'un déplacement.
     * @param int $startX Position de départ en x.
     * @param int $startY Position de départ en y.
     * @param int $endX Position d'arrivée en x.
     * @param int $endY Position d'arrivée en y.
     */
    private function gererReculPieces(int $startX, int $startY, int $endX, int $endY): void {
        // Détection et gestion des pièces sautées
        // Logique à implémenter selon les règles spécifiques
        echo "Gestion des pièces sautées entre ($startX, $startY) et ($endX, $endY)";
    }
}





















?>