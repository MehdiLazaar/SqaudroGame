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
        return '<button type="button" class="button is-small is-light is-rounded has-background-info" disabled>Case Vide</button>';
    }

    /**
     * Génère une case neutre (non cliquable, sans coordonnées).
     */
    public static function generationCaseNeutre(): string
    {
        return '<button type="button" class="button is-small is-dark is-rounded" disabled>Case Neutre</button>';
    }
    public static function generationPiece(PieceSquadro $piece, int $ligne, int $colonne, bool $estActif, PlateauSquadro $plateau): string
    {
        $couleur = ($piece->getCouleur() === PieceSquadro::BLANC) ? 'is-white' : 'is-black';

        // Obtenir les coordonnées de destination
        [$newX, $newY] = $plateau->getCoordDestination($ligne, $colonne);
        $caseDestination = $plateau->getPiece($newX, $newY);

        // 🔹 **Cas 1 : La pièce appartient à l'adversaire**
        if (!$estActif) {
            return '<button class="button is-small ' . $couleur . ' is-rounded is-static" disabled>Piece Bloquée</button>';
        }

        // 🔹 **Cas 2 : La case d'arrivée est occupée, donc la pièce ne peut pas bouger**
        if ($caseDestination->getCouleur() !== PieceSquadro::VIDE) {
            return '<button class="button is-small ' . $couleur . ' is-rounded is-static" disabled>Case Occupée</button>';
        }

        // 🔹 **Si la pièce est jouable, permettre le déplacement**
        return '
        <form action="#" method="POST">
            <input type="hidden" name="x" value="' . $ligne . '">
            <input type="hidden" name="y" value="' . $colonne . '">
            <button class="button is-small ' . $couleur . ' is-rounded" type="submit">Déplacer</button>
        </form>';
    }
    /**
     * Génère le code HTML du plateau de jeu avec la disposition spécifique
     */
    public static function generationPlateauJeu(PlateauSquadro $plateau, int $joueurActif): string
    {
        $vitessesBlanchesRetour = [1, 3, 2, 3, 1];
        $vitessesBlanchesAller = [3, 1, 2, 1, 3];
        $vitessesNoiresAller = [3, 1, 2, 1, 3];
        $vitessesNoiresRetour = [1, 3, 2, 3, 1];

        $html = '<table class="table is-bordered is-striped is-hoverable mx-auto" style="width: 60%;">'; // Réduction de la largeur

        // 🔹 **Ligne du haut avec cases rouges (Vitesses de retour des noirs)**
        $html .= '<tr>';
        $html .= '<td class="is-empty"></td>'; // Coin neutre
        $html .= '<td class="is-empty"></td>'; // Décalage visuel
        foreach ($vitessesNoiresRetour as $valeur) {
            $html .= '<td class="has-text-centered p-2"><div class="box has-background-danger has-text-white">' . $valeur . '</div></td>';
        }
        $html .= '</tr>';

        // 🔹 **Lignes du plateau avec les pièces et vitesses des blancs**
        for ($ligne = 0; $ligne < 7; $ligne++) {
            $html .= '<tr>';

            // 🔹 **Ajout des cases rouges à gauche (Vitesses de retour des blancs)**
            if ($ligne === 0) {
                $html .= '<td class="is-empty"></td>'; // Décalage visuel
            } elseif ($ligne >= 1 && $ligne <= 5) {
                $html .= '<td class="has-text-centered p-2"><div class="box has-background-danger has-text-white">' . $vitessesBlanchesRetour[$ligne - 1] . '</div></td>';
            } else {
                $html .= '<td class="is-empty"></td>'; // Coin neutre
            }

            // Génération des cases avec pièces
            for ($colonne = 0; $colonne < 7; $colonne++) {
                $piece = $plateau->getPiece($ligne, $colonne);
                $html .= '<td class="has-text-centered p-1">'; // Réduction du padding

                if ($piece->getCouleur() === PieceSquadro::VIDE) {
                    $html .= self::generationCaseVide(); // Applique le bleu clair pour les cases vides
                } elseif ($piece->getCouleur() === PieceSquadro::NEUTRE) {
                    $html .= self::generationCaseNeutre();
                } else {
                    $isActive = ($piece->getCouleur() === $joueurActif);
                    $html .= self::generationPiece($piece, $ligne, $colonne, $isActive, $plateau);
                }

                $html .= '</td>';
            }

            // 🔹 **Ajout des cases rouges à droite (Vitesses d'aller des blancs)**
            if ($ligne === 0) {
                $html .= '<td class="is-empty"></td>'; // Décalage visuel
            } elseif ($ligne >= 1 && $ligne <= 5) {
                $html .= '<td class="has-text-centered p-2"><div class="box has-background-danger has-text-white">' . $vitessesBlanchesAller[$ligne - 1] . '</div></td>';
            } else {
                $html .= '<td class="is-empty"></td>'; // Coin neutre
            }

            $html .= '</tr>';
        }

        // 🔹 **Ligne du bas avec cases rouges (Vitesses d'aller des noirs)**
        $html .= '<tr>';
        $html .= '<td class="is-empty"></td>'; // Coin neutre
        $html .= '<td class="is-empty"></td>'; // Décalage visuel
        foreach ($vitessesNoiresAller as $valeur) {
            $html .= '<td class="has-text-centered p-2"><div class="box has-background-danger has-text-white">' . $valeur . '</div></td>';
        }
        $html .= '</tr>';

        $html .= '</table>';
        return $html;
    }
}