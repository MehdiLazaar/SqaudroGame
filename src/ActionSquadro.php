<?php

namespace src;

use Exception;
use src\PlateauSquadro;

class ActionSquadro {
    public PlateauSquadro $plateau;

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
        if ($piece === null || $piece->getCouleur() === PieceSquadro::VIDE || $piece->getCouleur() === PieceSquadro::NEUTRE) {
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
        $this->reculePiece($x, $y, $newX, $newY);
    }

    /**
     * Renvoie les pièces sautées par le déplacement au début de leur parcours.
     * @param int $xOrigine La position originale en x.
     * @param int $yOrigine La position originale en y.
     * @param int $xDestination La position de destination en x.
     * @param int $yDestination La position de destination en y.
     */
    private function reculePieces(int $xOrigine, int $yOrigine, int $xDestination, int $yDestination): void {
        $direction = $this->plateau->getPiece($xDestination, $yDestination)->getDirection();

        // Détermine les positions intermédiaires entre l'origine et la destination
        $rangeX = range(min($xOrigine, $xDestination), max($xOrigine, $xDestination));
        $rangeY = range(min($yOrigine, $yDestination), max($yOrigine, $yDestination));

        foreach ($rangeX as $x) {
            foreach ($rangeY as $y) {
                if (!($x == $xOrigine && $y == $yOrigine) && !($x == $xDestination && $y == $yDestination)) {
                    $piece = $this->plateau->getPiece($x, $y);
                    if ($piece !== null && $piece->getCouleur() !== PieceSquadro::VIDE && $piece->getCouleur() !== PieceSquadro::NEUTRE) {
                        $this->reculePiece($x, $y, $direction);
                    }
                }
            }
        }
    }

    /**
     * Renvoie une pièce au début de son parcours.
     * @param int $x La position de la pièce en x.
     * @param int $y La position de la pièce en y.
     * @param int $direction La direction initiale de la pièce.
     */
    private function reculePiece(int $x, int $y, int $direction): void {
        $piece = $this->plateau->getPiece($x, $y);
        if ($piece === null) {
            throw new Exception("Aucune pièce à reculer à la position ($x, $y).");
        }

        $newX = $x;
        $newY = $y;

        switch ($direction) {
            case PieceSquadro::NORD:
                $newX = 1;
                $newY = $y;
                break;
            case PieceSquadro::SUD:
                $newX = 6;
                $newY = $y;
                break;
            case PieceSquadro::EST:
                $newX = $x;
                $newY = 0;
                break;
            case PieceSquadro::OUEST:
                $newX = $x;
                $newY = 6;
                break;
        }

        // Déplace la pièce à son point de départ
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
        $this->plateau->setPiece($piece, $newX, $newY);
    }

    /**
     * Retire une pièce du plateau lorsqu'elle a terminé son aller-retour.
     * @param int $couleur La couleur de la pièce.
     * @param int $direction La direction de la pièce.
     */
    public function sortPiece(int $couleur, int $direction): void {
        echo "La pièce de couleur {$couleur} et de direction {$direction} est retirée du plateau.";
        if ($couleur === PieceSquadro::BLANC) {
            $this->plateau->retireColonneJouable($direction);
        } elseif ($couleur === PieceSquadro::NOIR) {
            $this->plateau->retireLigneJouable($direction);
        }
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
    public function pieceDoitSortir(PieceSquadro $piece, int $x, int $y): bool {
        $direction = $piece->getDirection();
        return ($direction === PieceSquadro::NORD && $x === 0) ||
            ($direction === PieceSquadro::SUD && $x === 5) ||
            ($direction === PieceSquadro::EST && $y === 5) ||
            ($direction === PieceSquadro::OUEST && $y === 0);
    }
}
