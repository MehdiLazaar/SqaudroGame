<?php

namespace src;
use src\PieceSquadroUI;
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
                    <title>' . htmlspecialchars($title) . '</title>
                    <link rel="stylesheet" href="path/to/tailwind.css" /> <!-- Assurez-vous que ce lien est correct -->
                </head>
                <body>
                    <div class="container">
                        <h1>' . htmlspecialchars($title) . '</h1>
        ';
    }
    /**
     * Génère la fin du HTML pour la page.
     *
     * @return string Le HTML de la fin de la page.
     */
    public static function getFinHTML(): string {
        return "</div></body>\n</html>";
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
        $html .= '<div class="plateau">';
        $html .= PieceSquadroUI::generationPlateauJeu($plateau, $plateau->getPiece($x, $y)->getCouleur());
        $html .= '</div>';
        $html .= '<p>Confirmez-vous le déplacement de la pièce en (' . $x . ', ' . $y . ') ?</p>';

        // Formulaire pour "Oui"
        $html .= '<form action="#" method="POST">
                <input type="hidden" name="confirmer" value="oui">
                <button type="submit">Oui</button>
              </form>';

        // Formulaire pour "Non"
        $html .= '<form action="#" method="POST">
                <input type="hidden" name="confirmer" value="non">
                <button type="submit">Non</button>
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
        $html .= '<form action="#" method="POST">
                    <button type="submit">Rejouer</button>
                  </form>';

        $html .= self::getFinHTML();
        return $html;
    }
}
