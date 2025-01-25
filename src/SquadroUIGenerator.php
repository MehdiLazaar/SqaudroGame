<?php

namespace src;

class SquadroUIGenerator {

    /**
     * Génère le début du HTML pour la page.
     *
     * @param string $title Le titre de la page.
     * @return string Le HTML du début de la page.
     */
    public static function getDebutHTML(string $title = "SqaudroGame"): string {
        return '<!DOCTYPE html>
                <html lang="fr">
                <head>
                <meta charset="utf-8" />
                <title>'.$title.'</title>
                <link rel="stylesheet" href="#" />
                </head>
                <div>
                <div class="containerdeH1" <h1>'.$title.'</h1></div>
                
    ';
    }
    /**
     * Génère la fin du HTML pour la page.
     *
     * @return string Le HTML de la fin de la page.
     */
    protected static function getFinHTML(): string {
        return "</form></div></body>\n</html>";
    }
    /**
     * Génère une page pour proposer de jouer une pièce du joueur actif.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu.
     * @param int $joueurActif La couleur du joueur actif (BLANC ou NOIR).
     * @return string Le HTML de la page.
     */
    public static function genererPageJouerPiece(PlateauSquadro $plateau, int $joueurActif): string {
        $html = self::getDebutHTML("Jouer une pièce");

        // Affiche le plateau de jeu
        $html .= '<div class="plateau">';
        $html .= PieceSquadroUI::generationPlateauJeu($plateau, $joueurActif);
        $html .= '</div>';

        // Affiche un message indiquant le joueur actif
        $html .= '<p>C\'est au tour du joueur ' . ($joueurActif === PieceSquadro::BLANC ? 'blanc' : 'noir') . ' de jouer.</p>';

        $html .= self::getFinHTML();
        return $html;
    }

    /**
     * Génère une page pour confirmer le déplacement de la pièce choisie.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu.
     * @param int $x La coordonnée x de la pièce.
     * @param int $y La coordonnée y de la pièce.
     * @return string Le HTML de la page.
     */
    public static function genererPageConfirmerDeplacement(PlateauSquadro $plateau, int $x, int $y): string {
        $html = self::getDebutHTML("Confirmer le déplacement");

        // Affiche le plateau de jeu
        $html .= '<div class="plateau">';
        $html .= PieceSquadroUI::generationPlateauJeu($plateau, $plateau->getPiece($x, $y)->getCouleur());
        $html .= '</div>';

        // Affiche un message de confirmation
        $html .= '<p>Confirmez-vous le déplacement de la pièce en (' . $x . ', ' . $y . ') ?</p>';

        // Boutons de confirmation et d'annulation
        $html .= '<form action="confirmer.php" method="POST">
                    <input type="hidden" name="x" value="' . $x . '">
                    <input type="hidden" name="y" value="' . $y . '">
                    <button type="submit" name="confirmer" value="oui">Oui</button>
                    <button type="submit" name="confirmer" value="non">Non</button>
                  </form>';

        $html .= self::getFinHTML();
        return $html;
    }

    /**
     * Génère une page pour afficher le plateau final et le message de victoire.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu.
     * @param int $joueurGagnant La couleur du joueur gagnant (BLANC ou NOIR).
     * @return string Le HTML de la page.
     */
    public static function genererPageVictoire(PlateauSquadro $plateau, int $joueurGagnant): string {
        $html = self::getDebutHTML("Victoire !");

        // Affiche le plateau de jeu
        $html .= '<div class="plateau">';
        $html .= PieceSquadroUI::generationPlateauJeu($plateau, $joueurGagnant);
        $html .= '</div>';

        // Affiche le message de victoire
        $html .= '<p>Le joueur ' . ($joueurGagnant === PieceSquadro::BLANC ? 'blanc' : 'noir') . ' a gagné !</p>';

        // Bouton pour recommencer une nouvelle partie
        $html .= '<form action="index.php" method="POST">
                    <button type="submit">Rejouer</button>
                  </form>';

        $html .= self::getFinHTML();
        return $html;
    }

    /**
     * Génère un composant HTML pour un bouton.
     *
     * @param string $texte Le texte du bouton.
     * @param string $action L'action du bouton (URL ou script).
     * @return string Le HTML du bouton.
     */
    public static function genererBouton(string $texte, string $action): string {
        return '<form action="' . $action . '" method="POST">
                    <button type="submit">' . $texte . '</button>
                </form>';
    }

}