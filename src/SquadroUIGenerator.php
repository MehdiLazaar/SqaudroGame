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

}