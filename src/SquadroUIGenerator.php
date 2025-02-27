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
    public static function getDebutHTML(string $title = "SquadroGame"): string {
        return '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>' . htmlspecialchars($title) . '</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
            </head>
            <body>
                <section class="section">
                    <div class="container">
                        <div class="box has-text-centered">
                            <h1 class="title is-2">' . htmlspecialchars($title) . '</h1>
                        </div>
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

        // Affiche le plateau de jeu avec un cadre stylisé
        $html .= '<div class="box has-text-centered">
                <h2 class="subtitle">Plateau de jeu</h2>
                <div class="plateau">
                    ' . PieceSquadroUI::generationPlateauJeu($plateau, $joueurActif) . '
                </div>
              </div>';

        // Affiche un message indiquant le joueur actif
        $html .= '<div class="notification is-info has-text-centered">
                <p class="is-size-5">C\'est au tour du joueur 
                    <strong class="has-text-' . ($joueurActif === PieceSquadro::BLANC ? 'light' : 'dark') . '">'
            . ($joueurActif === PieceSquadro::BLANC ? 'Blanc' : 'Noir') .
            '</strong> de jouer.
                </p>
              </div>';

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

        // Message de confirmation
        $html .= '<div class="notification is-warning has-text-centered">
                <p class="is-size-5">Confirmez-vous le déplacement de la pièce en 
                   <strong>(' . $x . ', ' . $y . ')</strong> ?
                </p>
              </div>';

        // Boutons de confirmation sous forme de formulaire avec Bulma
        // 1er formulaire = confirmerChoix
        $html .= '<div class="buttons is-centered">
                <form action="../public/traiteActionSquadro.php" method="POST">
                    <input type="hidden" name="confirmer" value="oui">
                    <button type="submit" class="button is-success">Oui</button>
                </form>';

        // 2e formulaire = annulerChoix
        $html .= '<form action="../public/traiteActionSquadro.php" method="POST">
                    <input type="hidden" name="action" value="annulerChoix">
                    <button type="submit" class="button is-danger">Annuler</button>
              </form>
              </div>';

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

        $html .= '<div class="notification is-success has-text-centered">
                <p class="is-size-4">
                    🎉 Félicitations ! Le joueur 
                    <strong class="has-text-' . ($joueurGagnant === PieceSquadro::BLANC ? 'primary' : 'dark') . '">'
            . ($joueurGagnant === PieceSquadro::BLANC ? 'Blanc' : 'Noir') .
            '</strong> a gagné ! 🏆
                </p>
              </div>';

        // On envoie action=rejouer
        $html .= '<div class="buttons is-centered">
                <form action="../public/traiteActionSquadro.php" method="POST">
                    <input type="hidden" name="action" value="rejouer">
                    <button type="submit" class="button is-info is-large">🔄 Rejouer</button>
                </form>
              </div>';

        $html .= self::getFinHTML();
        return $html;
    }
    /**
     * Génère une page d'erreur avec un message spécifique.
     *
     * @param string $message Le message d'erreur à afficher.
     * @return string Le HTML de la page d'erreur.
     */
    public static function pageDErreur(string $message): string {
        $html = self::getDebutHTML("Erreur");

        // Contenu de la page d'erreur avec une alerte stylisée
        $html .= '<div class="notification is-danger is-light has-text-centered">
                <h2 class="title is-4">⚠️ Une erreur est survenue</h2>
                <p class="is-size-5">' . htmlspecialchars($message) . '</p>
              </div>';

        // Bouton de retour à l'accueil centré
        $html .= '<div class="has-text-centered">
                <a href="../public/index.php" class="button is-primary is-medium">
                    🔄 Retour à l\'accueil
                </a>
              </div>';

        $html .= self::getFinHTML();
        return $html;
    }
    /**
     * Génère une balise HTML avec le contenu et les attributs donnés.
     *
     * @param string $tag Le nom de la balise (exemple : "div", "a", "button").
     * @param string $content Le contenu HTML placé à l'intérieur de la balise.
     * @param array $attributes Un tableau associatif des attributs (clé => valeur).
     * @return string La chaîne HTML générée.
     */
    public static function intoBalise(string $tag, string $content = '', array $attributes = []): string
    {
        $attrStr = '';
        foreach ($attributes as $name => $value) {
            // Sécurisation de la valeur des attributs avec htmlspecialchars
            $attrStr .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }
        if ($content === '') {
            return "<$tag$attrStr />";
        } else {
            return "<$tag$attrStr>$content</$tag>";
        }
    }
}
