<?php
namespace src;



class SquadroUIGenerator {
    private const COULEURS = [
        PieceSquadro::BLANC => 'blanc',
        PieceSquadro::NOIR => 'noir'
    ];

    /**
     * Génère le début du HTML pour la page
     */
    private static function getDebutHTML(string $title = "SquadroGame"): string {
        return '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>'.$title.'</title>
                <style>'.self::genererStyle().'</style>
            </head>
            <body>
                <div class="container">
                    <h1>'.$title.'</h1>';
    }

    /**
     * Génère la fin du HTML pour la page
     */
    private static function getFinHTML(): string {
        return '</div></body></html>';
    }

    /**
     * Génère la page principale de jeu
     */
    public static function genererPageJouerPiece(PlateauSquadro $plateau, int $joueurActif): string {
        $html = self::getDebutHTML("Tour des ".self::COULEURS[$joueurActif]);

        $html .= '<div class="plateau">';
        $html .= PieceSquadroUI::generationPlateauJeu($plateau, $joueurActif);
        $html .= '</div>';

        $html .= '<div class="status">';
        $html .= '<p>Joueur actif : '.self::COULEURS[$joueurActif].'</p>';
        $html .= '</div>';

        return $html.self::getFinHTML();
    }

    /**
     * Génère la page de confirmation de déplacement
     */
    public static function genererPageConfirmerDeplacement(
        PlateauSquadro $plateau,
        int $x,
        int $y,
        array $destination,
        int $joueurActif
    ): string {
        $piece = $plateau->getPiece($x, $y);
        if(!$piece) throw new \InvalidArgumentException("Pas de pièce à cette position");

        $html = self::getDebutHTML("Confirmation de déplacement");

        $html .= '<div class="confirmation-box">';
        $html .= '<h3>Déplacement proposé :</h3>';
        $html .= '<p>De ('.$x.','.$y.') vers ('.$destination[0].','.$destination[1].')</p>';

        $html .= '<div class="actions">';
        $html .= self::genererFormulaireConfirmation($x, $y, $destination, $joueurActif);
        $html .= self::genererBouton("Annuler", "index.php");
        $html .= '</div>';

        return $html.self::getFinHTML();
    }

    /**
     * Génère la page de victoire
     */
    public static function genererPageVictoire(PlateauSquadro $plateau, int $joueurGagnant): string {
        $html = self::getDebutHTML("Victoire des ".self::COULEURS[$joueurGagnant]);

        $html .= '<div class="victoire">';
        $html .= '<h2>Les '.self::COULEURS[$joueurGagnant].' remportent la partie !</h2>';
        $html .= '<div class="plateau-final">';
        $html .= PieceSquadroUI::generationPlateauJeu($plateau, $joueurGagnant);
        $html .= '</div>';
        $html .= self::genererBouton("Rejouer", "nouvelle_partie.php");
        $html .= '</div>';

        return $html.self::getFinHTML();
    }

    /**
     * Génère un formulaire de confirmation
     */
    private static function genererFormulaireConfirmation(
        int $x,
        int $y,
        array $destination,
        int $joueurActif
    ): string {
        return '<form method="POST" action="confirmer.php">
            <input type="hidden" name="x" value="'.$x.'">
            <input type="hidden" name="y" value="'.$y.'">
            <input type="hidden" name="dest_x" value="'.$destination[0].'">
            <input type="hidden" name="dest_y" value="'.$destination[1].'">
            <input type="hidden" name="joueur" value="'.$joueurActif.'">
            <button type="submit" class="btn-confirm">Confirmer</button>
        </form>';
    }

    /**
     * Génère un bouton stylisé
     */
    public static function genererBouton(string $texte, string $action): string {
        return '<a href="'.$action.'" class="btn">'.$texte.'</a>';
    }

    /**
     * Génère les styles CSS
     */
    private static function genererStyle(): string {
        return '
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        
        .plateau {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .confirmation-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .actions {
            margin-top: 15px;
            text-align: center;
        }
        ';
    }
}