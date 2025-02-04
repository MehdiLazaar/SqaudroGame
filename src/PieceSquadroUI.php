<?php

namespace src;

class PieceSquadroUI {
    /**
     * Génère le code HTML d'une case vide.
     *
     * @return string code HTML de la case vide.
     */
    public static function generationCaseVide(int $x, int $y): string {
        return '<button type="button" 
                    class="w-full h-full bg-gray-200 cursor-not-allowed rounded-md" 
                    value="' . $x . ',' . $y . '" disabled>
            </button>';
    }

    /**
     * Génère une case neutre (non cliquable, sans coordonnées).
     */
    public static function generationCaseNeutre(): string {
        return '<button type="button" 
                    class="w-full h-full bg-gray-800 cursor-not-allowed rounded-md" 
                    disabled>
            </button>';
    }
    public static function generationPiece(PieceSquadro $piece, int $x, int $y, bool $active = false): string {
        $couleur = $piece->getCouleur();
        $classCouleur = $couleur === PieceSquadro::BLANC ? 'bg-blue-500' : 'bg-black';
        $classActive = $active ? '' : 'cursor-not-allowed opacity-50';
        $classComplet = $classCouleur . ' ' . $classActive . ' rounded-md';

        return '<form action="" method="POST" class="w-full h-full">
                <input type="hidden" name="x" value="' . $x . '">
                <input type="hidden" name="y" value="' . $y . '">
                <button type="submit" 
                        class="piece ' . $classComplet . '"
                        aria-label="Déplacer pièce en position (' . $x . ', ' . $y . ')">
                </button>
            </form>';
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
        return '<form action="#" method="POST">
                    <input type="hidden" name="x" value="' . $x . '">
                    <input type="hidden" name="y" value="' . $y . '">
                    <button type="submit" class="piece">Déplacer</button>
                </form>';
    }
    /**
     * Génère le code HTML du plateau de jeu avec la disposition spécifique
     */
    /*public static function generationPlateauJeu(PlateauSquadro $plateau, int $joueurActif): string {
        $html = '<div class="flex justify-center mt-5">
            <table class="table-fixed border-collapse border border-gray-500">';
        for ($x = 0; $x < 7; $x++) {
            $html .= '<tr>';
            for ($y = 0; $y < 7; $y++) {
                $piece = $plateau->getPiece($x, $y);
                $isCorner = ($x === 0 || $x === 6) && ($y === 0 || $y === 6);

                $html .= '<td class="border border-gray-500 w-16 h-16 p-0 text-center">';

                if ($isCorner) {
                    $html .= self::generationCaseNeutre();
                } else {
                    if ($piece === null) {
                        $html .= self::generationCaseVide($x, $y);
                    } else {
                        $isActive = ($piece->getCouleur() === $joueurActif);
                        $html .= self::generationPiece($piece, $x, $y, $isActive);
                    }
                }

                $html .= '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table></div>';
        return $html;
    }*/
    public static function generationPlateauJeu(PlateauSquadro $plateau, int $joueurActif): string
    {
        $html = '<div class="flex justify-center mt-5">
                <table class="table-fixed border-collapse border border-gray-500">';
        for ($x = 0; $x < 7; $x++) {
            $html .= '<tr>';
            for ($y = 0; $y < 7; $y++) {
                $piece = $plateau->getPiece($x, $y);
                $isCorner = ($x === 0 || $x === 6) && ($y === 0 || $y === 6);

                $html .= '<td class="border border-gray-500 w-16 h-16 p-0 text-center">';
                if ($isCorner) {
                    $html .= self::generationCaseNeutre();
                } else {
                    if ($piece === null) {
                        $html .= self::generationCaseVide($x, $y);
                    } else {
                        $isActive = ($piece->getCouleur() === $joueurActif);
                        $html .= self::generationPiece($piece, $x, $y, $isActive);
                    }
                }
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table></div>';
        return $html;
    }
}