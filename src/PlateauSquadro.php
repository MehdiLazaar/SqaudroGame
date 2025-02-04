<?php
namespace src;
use InvalidArgumentException;


class PlateauSquadro
{
    // Constantes pour les vitesses
    public const BLANC_V_ALLER = [0, 1, 3, 2, 3, 1, 0];
    public const BLANC_V_RETOUR = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_ALLER = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_RETOUR = [0, 1, 3, 2, 3, 1, 0];

    // Attributs
    private array $plateau;
    private array $lignesJouables = [1, 2, 3, 4, 5];
    private array $colonnesJouables = [1, 2, 3, 4, 5];

    // Constructeur
    public function __construct()
    {
        $this->initCasesVides();
        $this->initCasesNeutres();
        $this->initCasesBlanches();
        $this->initCasesNoires();
    }

    // Méthodes privées d'initialisation
    private function initCasesVides(): void
    {
        $this->plateau = array_fill(0, 7, array_fill(0, 7, PieceSquadro::initVide()));
    }

    private function initCasesNeutres(): void {
        // Définir les coins comme cases neutres
        $corners = [
            [0, 0], [0, 6], [6, 0], [6, 6]
        ];
        foreach ($corners as $pos) {
            $this->plateau[$pos[0]][$pos[1]] = PieceSquadro::initNeutre();
        }
    }

    private function initCasesBlanches(): void
    {
        // Ouest du plateau
        $colonneBlanche = 0;
        for ($ligne = 1; $ligne <= 5; $ligne++) {
            $this->plateau[$ligne][$colonneBlanche] = PieceSquadro::initBlancOuest();
        }
    }

    private function initCasesNoires(): void
    {
        $ligneNoire = 6;
        for ($colonne = 1; $colonne <= 5; $colonne++) {
            $this->plateau[$ligneNoire][$colonne] = PieceSquadro::initNoirSud();
        }
    }

    // Getters
    public function getPlateau(): array
    {
        return $this->plateau;
    }

    public function getPiece(int $x, int $y): ?PieceSquadro
    {
        $case = $this->plateau[$x][$y];
        return $case->getCouleur() === PieceSquadro::VIDE ? null : $case;
    }

    public function getLignesJouables(): array
    {
        return $this->lignesJouables;
    }

    public function getColonnesJouables(): array
    {
        return $this->colonnesJouables;
    }

    // Setters
    public function setPiece(PieceSquadro $piece, int $x, int $y): void
    {
        $this->plateau[$x][$y] = $piece;
    }

    // Méthodes pour gérer les lignes et colonnes jouables
    public function retireLigneJouable(int $index): void
    {
        if (!in_array($index, $this->lignesJouables, true)) {
            throw new InvalidArgumentException("Index de ligne invalide : $index");
        }

        $key = array_search($index, $this->lignesJouables, true);
        unset($this->lignesJouables[$key]);
        $this->lignesJouables = array_values($this->lignesJouables);
    }

    public function retireColonneJouable(int $index): void
    {
        if (!in_array($index, $this->colonnesJouables, true)) {
            throw new InvalidArgumentException("Index de colonne invalide : $index");
        }

        $key = array_search($index, $this->colonnesJouables, true);
        unset($this->colonnesJouables[$key]);
        $this->colonnesJouables = array_values($this->colonnesJouables); // Réindexation des valeurs
    }

    // Méthodes pour calculer les destinations des pièces
    /*public function getCoordDestination(int $x, int $y): array {
        $piece = $this->getPiece($x, $y);

        if ($piece === null || $piece->getCouleur() === PieceSquadro::VIDE || $piece->getCouleur() === PieceSquadro::NEUTRE) {
            throw new InvalidArgumentException("Aucune pièce valide à la position ($x, $y).");
        }

        $couleur = $piece->getCouleur();
        $direction = $piece->getDirection();
        $vitesse = 0;

        // Calcul de la vitesse en fonction de la couleur et de la direction
        if ($couleur === PieceSquadro::BLANC) {
            if ($direction === PieceSquadro::EST) {
                $vitesse = self::BLANC_V_ALLER[$y];
            } elseif ($direction === PieceSquadro::OUEST) {
                $vitesse = self::BLANC_V_RETOUR[$y];
            }
        } elseif ($couleur === PieceSquadro::NOIR) {
            if ($direction === PieceSquadro::NORD) {
                $vitesse = self::NOIR_V_ALLER[$x];
            } elseif ($direction === PieceSquadro::SUD) {
                $vitesse = self::NOIR_V_RETOUR[$x];
            }
        }

        // Calcul des nouvelles coordonnées
        switch ($direction) {
            case PieceSquadro::NORD:
                $newX = max(0, $x - $vitesse);
                $newY = $y;
                break;
            case PieceSquadro::SUD:
                $newX = min(6, $x + $vitesse);
                $newY = $y;
                break;
            case PieceSquadro::EST:
                $newX = $x;
                $newY = min(6, $y + $vitesse);
                break;
            case PieceSquadro::OUEST:
                $newX = $x;
                $newY = max(0, $y - $vitesse);
                break;
            default:
                throw new InvalidArgumentException("Direction invalide pour la pièce à ($x, $y).");
        }

        return [$newX, $newY];
    }*/
    public function getCoordDestination(int $x, int $y): array
    {
        $piece = $this->getPiece($x, $y);

        if ($piece === null || $piece->getCouleur() === PieceSquadro::VIDE || $piece->getCouleur() === PieceSquadro::NEUTRE) {
            throw new InvalidArgumentException("Aucune pièce valide à la position ($x, $y).");
        }

        $couleur = $piece->getCouleur();
        $direction = $piece->getDirection();
        $vitesse = 0;

        // Calculer la vitesse selon la couleur et la direction
        if ($couleur === PieceSquadro::BLANC) {
            if ($direction === PieceSquadro::EST) {
                $vitesse = self::BLANC_V_ALLER[$y];
            } elseif ($direction === PieceSquadro::OUEST) {
                $vitesse = self::BLANC_V_RETOUR[$y];
            }
        } elseif ($couleur === PieceSquadro::NOIR) {
            if ($direction === PieceSquadro::NORD) {
                $vitesse = self::NOIR_V_ALLER[$x];
            } elseif ($direction === PieceSquadro::SUD) {
                $vitesse = self::NOIR_V_RETOUR[$x];
            }
        }

        // Calculer les nouvelles coordonnées
        switch ($direction) {
            case PieceSquadro::NORD:
                $newX = max(0, $x - $vitesse);
                $newY = $y;
                break;
            case PieceSquadro::SUD:
                $newX = min(6, $x + $vitesse);
                $newY = $y;
                break;
            case PieceSquadro::EST:
                $newX = $x;
                $newY = min(6, $y + $vitesse);
                break;
            case PieceSquadro::OUEST:
                $newX = $x;
                $newY = max(0, $y - $vitesse);
                break;
            default:
                throw new InvalidArgumentException("Direction invalide pour la pièce à ($x, $y).");
        }

        // Limiter les coordonnées au plateau (0 à 6)
        $newX = max(0, min(6, $newX));
        $newY = max(0, min(6, $newY));

        return [$newX, $newY];
    }

    public function getDestination(int $x, int $y): ?PieceSquadro
    {
        try {
            $coords = $this->getCoordDestination($x, $y);
            $newX = $coords[0];
            $newY = $coords[1];

            // Vérifie si les coordonnées sont hors limites
            if ($newX < 0 || $newX > 6 || $newY < 0 || $newY > 6) {
                return null;
            }

            // Retourne la pièce à la destination (peut être null si la case est vide)
            return $this->getPiece($newX, $newY);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    // Méthodes de sérialisation
    public function toJson(): string
    {
        $plateauArray = [];

        foreach ($this->plateau as $x => $row) {
            foreach ($row as $y => $piece) {
                $plateauArray[$x][$y] = json_decode($piece->toJson(), true);
            }
        }

        return json_encode($plateauArray);
    }

    public static function fromJson(string $json): PlateauSquadro
    {
        $data = json_decode($json, true);
        $instance = new self();

        foreach ($data as $x => $row) {
            foreach ($row as $y => $cell) {
                if (is_array($cell)) {
                    $instance->plateau[$x][$y] = PieceSquadro::fromJson(json_encode($cell));
                } else {
                    $instance->plateau[$x][$y] = PieceSquadro::initVide();
                }
            }
        }

        return $instance;
    }

    // Méthode __toString pour afficher l'état du plateau
    public function __toString(): string
    {
        $output = "";

        for ($x = 0; $x < 7; $x++) {
            for ($y = 0; $y < 7; $y++) {
                $piece = $this->getPiece($x, $y);
                if ($piece === null) {
                    $output .= "[VIDE] ";
                } else {
                    $couleur = $piece->getCouleur() === PieceSquadro::BLANC ? "BLANC" : "NOIR";
                    $direction = $piece->getDirection() === PieceSquadro::OUEST ? "OUEST" : "SUD";
                    $output .= "[{$couleur} {$direction}] ";
                }
            }
            $output .= PHP_EOL;
        }

        return $output;
    }

}