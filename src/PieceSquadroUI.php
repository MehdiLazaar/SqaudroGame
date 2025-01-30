<?php

namespace src;

class PieceSquadroUI {
    /**
     * Génère le code HTML d'une case vide.
     *
     * @return string code HTML de la case vide.
     */
    public static function generationCaseVide(int $x, int $y, bool $isActive = true): string {
        $disabled = $isActive ? '' : 'disabled';
        return '<button type="button" class="caseVide" value="' . $x . ',' . $y . '" ' . $disabled . '></button>';
    }

    /**
     * Génère une case neutre (non cliquable, sans coordonnées).
     */
    public static function generationCaseNeutre(): string {
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
    public static function generationPiece(PieceSquadro $piece, int $x, int $y, bool $active = false): string {
        $couleur = $piece->getCouleur();
        $class = 'piece' . ($couleur === PieceSquadro::BLANC ? 'Blanche' : 'Noir');
        $disabled = $active ? '' : 'disabled';

        return '<button type="button" class="' . $class . '" value="' . $x . ',' . $y . '" ' . $disabled . '></button>';
    }
    /**
     * Génère le code HTML d'un formulaire pour envoyer les coordonnées de la pièce cliquée.
     *
     * @param int $x coordonnée x de la pièce.
     * @param int $y coordonnée y de la pièce.
     * @return string code HTML du formulaire.
     */
    public static function generationFormulaire(int $x, int $y): string
    {
        return '<form action="deplacer.php" method="POST">
                    <input type="hidden" name="x" value="' . $x . '">
                    <input type="hidden" name="y" value="' . $y . '">
                    <button type="submit" class="piece">Déplacer</button>
                </form>';
    }
    /**
     * Génère le code HTML du plateau de jeu avec la disposition spécifique
     */
    public static function generationPlateauJeu(PlateauSquadro $plateau, int $joueurActif): string {
        $html = '<table class="plateau">';

        for ($x = 0; $x < 7; $x++) {
            $html .= '<tr>';
            for ($y = 0; $y < 7; $y++) {
                $piece = $plateau->getPiece($x, $y);
                $isCorner = ($x === 0 || $x === 6) && ($y === 0 || $y === 6);

                if ($isCorner) {
                    // Cases neutres (toujours désactivées)
                    $html .= '<td>' . self::generationCaseNeutre() . '</td>';
                } else {
                    if ($piece === null) {
                        // Cases vides : activées ou désactivées selon le joueur actif
                        $isActive = self::isCaseVideActive($x, $y, $plateau, $joueurActif);
                        $html .= '<td>' . self::generationCaseVide($x, $y, $isActive) . '</td>';
                    } else {
                        // Pièces : activées ou désactivées selon le joueur actif
                        $isActive = ($piece->getCouleur() === $joueurActif);
                        $html .= '<td>' . self::generationPiece($piece, $x, $y, $isActive) . '</td>';
                    }
                }
            }
            $html .= '</tr>';
        }

        return $html . '</table>';
    }

    /**
     * Détermine si une case vide est active en fonction du joueur actif.
     */
    private static function isCaseVideActive(int $x, int $y, PlateauSquadro $plateau, int $joueurActif): bool {
        if ($joueurActif === PieceSquadro::BLANC) {
            // Pour les blancs : vérifier s'il y a une pièce blanche à l'ouest
            for ($i = $y - 1; $i >= 0; $i--) {
                $piece = $plateau->getPiece($x, $i);
                if ($piece !== null && $piece->getCouleur() === PieceSquadro::BLANC) {
                    return true;
                }
            }
        } else {
            // Pour les noirs : vérifier s'il y a une pièce noire au sud
            for ($i = $x + 1; $i < 7; $i++) {
                $piece = $plateau->getPiece($i, $y);
                if ($piece !== null && $piece->getCouleur() === PieceSquadro::NOIR) {
                    return true;
                }
            }
        }
        return false;
    }
}