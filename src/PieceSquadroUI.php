<?php

namespace src;

class PieceSquadroUI {
    /**
     * Génère le code HTML d'une case vide.
     *
     * @return string code HTML de la case vide.
     */
    public static function generationCaseVide(): string
    {
        return '<button type="button" class="caseVide" disabled></button>';
    }
    /**
     * Génère le code HTML d'une case neutre.
     *
     * @return string code HTML de la case neutre.
     */
    public static function generationCaseNeutre(): string
    {
        return '<button type="button" class="caseNeutre" disabled></button>';
    }
    /**
     * Génère le code HTML d'une pièce.
     *
     * @param PieceSquadro $piece la pièce à afficher.
     * @param int $x coordonnée x de la pièce.
     * @param int $y coordonnée y de la pièce.
     * @param bool $isActive indique si la pièce est cliquable (joueur actif).
     * @return string Code HTML de la pièce.
     */
    public static function generationPiece(PieceSquadro $piece, int $x, int $y, bool $active = false): string
    {
        $couleur = $piece->getCouleur();
        $desactivee = $active ? '' : 'disabled';

        $class = 'piece' . ($couleur === PieceSquadro::BLANC ? 'Blanche' : 'Noir');

        return '<button type="button" class="' . $class . '" data-x="' . $x . '" data-y="' . $y . '" ' . $desactivee . '></button>';
    }
    /**
     * Génère le code HTML d'un formulaire pour envoyer les coordonnées de la pièce cliquée.
     *
     * @param int $x coordonnée x de la pièce.
     * @param int $y coordonnée y de la pièce.
     * @return string code HTML du formulaire.
     */
    public static function generateForm(int $x, int $y): string
    {
        return '<form action="play.php" method="POST">
                    <input type="hidden" name="x" value="' . $x . '">
                    <input type="hidden" name="y" value="' . $y . '">
                    <button type="submit" class="piece">Déplacer</button>
                </form>';
    }
    /**
     * Génère le code HTML du plateau de jeu.
     *
     * @param PlateauSquadro $plateau le plateau de jeu.
     * @param int $joueurActif la couleur du joueur actif (BLANC ou NOIR).
     * @return string code HTML du plateau.
     */
    public static function generatePlateau(PlateauSquadro $plateau, int $joueurActif): string
    {
        $html = '<div class="plateau">';

        for ($x = 0; $x < 7; $x++) {
            for ($y = 0; $y < 7; $y++) {
                $piece = $plateau->getPiece($x, $y);

                if ($piece === null) {
                    // Case vide
                    $html .= self::generationCaseVide();
                } elseif ($piece->getCouleur() === PieceSquadro::NEUTRE) {
                    // Case neutre
                    $html .= self::generationCaseNeutre();
                } else {
                    // Pièce noire ou blanche
                    $isActive = ($piece->getCouleur() === $joueurActif);
                    $html .= self::generationPiece($piece, $x, $y, $isActive);
                }
            }
            $html .= '<br>';
        }

        $html .= '</div>';
        return $html;
    }
}