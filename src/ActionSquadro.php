<?php

namespace src;

use Exception;
use src\PlateauSquadro;

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
        $destination = $this->plateau->getCoordDestination($x, $y);

        return $destination !== null;
    }

    /**
     * Joue une pièce en déplaçant celle-ci et en appliquant les règles associées.
     * @param int $x La position de départ en x.
     * @param int $y La position de départ en y.
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

        // Gère le retournement ou la sortie de la pièce si nécessaire
        if ($this->pieceDoitSortir($piece, $newX, $newY)) {
            $this->sortPiece($piece->getCouleur(), $piece->getDirection());
        } else {
            $piece->inverseDirection();
        }

        // Gère les pièces sautées par le déplacement
        $this->gererReculPieces($x, $y, $newX, $newY);
    }

    /**
     * Renvoie une pièce au début de son parcours.
     * @param int $x La position de la pièce en x.
     * @param int $y La position de la pièce en y.
     */
    public function reculePiece(int $x, int $y): void {
        $piece = $this->plateau->getPiece($x, $y);
        $direction = $piece->getDirection();

        // Réinitialise la position de la pièce selon sa direction initiale
        $newX = ($direction === PieceSquadro::NORD) ? 0 : (($direction === PieceSquadro::SUD) ? 5 : $x);
        $newY = ($direction === PieceSquadro::OUEST) ? 0 : (($direction === PieceSquadro::EST) ? 5 : $y);

        // Déplace la pièce à son point de départ
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
        $this->plateau->setPiece($piece, $newX, $newY);
    }

    /**
     * Retire une pièce du plateau lorsqu'elle a terminé son aller-retour.
     * @param int $couleur La couleur de la pièce.
     * @param int $rang Le rang ou l'identifiant de la pièce.
     */
    public function sortPiece(int $couleur, int $rang): void {
        echo "La pièce de couleur {$couleur} et de rang {$rang} est retirée du plateau.";
    }

    /**
     * Vérifie si une couleur a remporté la partie.
     * @param int $couleur La couleur à vérifier.
     * @return bool True si la couleur a gagné, false sinon.
     */
    public function remporteVictoire(int $couleur): bool {
        if ($couleur === PieceSquadro::BLANC && empty($this->plateau->getColonnesJouables())) {
            return true;
        }

        if ($couleur === PieceSquadro::NOIR && empty($this->plateau->getLignesJouables())) {
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
     * Gère les pièces adverses sautées lors du déplacement.
     * @param int $startX Position de départ en x.
     * @param int $startY Position de départ en y.
     * @param int $endX Position d'arrivée en x.
     * @param int $endY Position d'arrivée en y.
     */
    private function gererReculPieces(int $startX, int $startY, int $endX, int $endY): void {
        // Détection des pièces sautées
        // Cette logique dépend des règles du jeu Squadro
        echo "Gestion des pièces sautées entre ($startX, $startY) et ($endX, $endY)";
    }
}
