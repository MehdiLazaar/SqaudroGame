<?php

namespace src;
class PlateauSquadro
{
    // Constantes
    const BLANC_V_ALLER = [0, 1, 3, 2, 3, 1, 0];
    const BLANC_V_RETOUR = [0, 3, 1, 2, 1, 3, 0];
    const NOIR_V_ALLER = [0, 3, 1, 2, 1, 3, 0];
    const NOIR_V_RETOUR = [0, 1, 3, 2, 3, 1, 0];

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

    // Méthodes privées pour initialisation des cases
    private function initCasesVides(): void
    {
        $this->plateau = array_fill(0, 7, array_fill(0, 7, null));
    }

    private function initCasesNeutres(): void
    {
        for ($x = 1; $x <= 5; $x++) {
            for ($y = 1; $y <= 5; $y++) {
                $this->plateau[$x][$y] = new CaseNeutre();
            }
        }
    }

    private function initCasesBlanches(): void
    {
        $ligneBlanche = 6;
        for ($colonne = 1; $colonne <= 5; $colonne++) {
            $this->plateau[$ligneBlanche][$colonne] = new PieceBlanche();
        }
    }

    private function initCasesNoires(): void
    {
        $ligneNoire = 0;
        for ($colonne = 1; $colonne <= 5; $colonne++) {
            $this->plateau[$ligneNoire][$colonne] = new PieceNoire();
        }
    }

    // Getters et setters
    public function getPlateau(): array
    {
        return $this->plateau;
    }

    public function getPiece(int $x, int $y): ?PieceSquadro
    {
        return $this->plateau[$x][$y] ?? null;
    }

    public function setPiece(PieceSquadro $piece, int $x, int $y): void
    {
        $this->plateau[$x][$y] = $piece;
    }

    public function getLignesJouables(): array
    {
        return $this->lignesJouables;
    }

    public function getColonnesJouables(): array
    {
        return $this->colonnesJouables;
    }

    public function retireLigneJouable(int $index): void
    {
        unset($this->lignesJouables[$index]);
        $this->lignesJouables = array_values($this->lignesJouables);
    }

    public function retireColonneJouable(int $index): void
    {
        unset($this->colonnesJouables[$index]);
        $this->colonnesJouables = array_values($this->colonnesJouables);
    }

    public function getCoordDestination(int $x, int $y): array
    {
        if (!isset($this->plateau[$x][$y])) {
            throw new InvalidArgumentException("Aucune pièce à la position donnée.");
        }
        $vitesse = $this->plateau[$x][$y] instanceof PieceBlanche
            ? (self::BLANC_V_ALLER[$x] ?? 0)
            : (self::NOIR_V_ALLER[$y] ?? 0);
        return [$x + $vitesse, $y];
    }

    public function getDestination(int $x, int $y): ?PieceSquadro
    {
        $coords = $this->getCoordDestination($x, $y);
        return $this->getPiece($coords[0], $coords[1]);
    }

    // Méthodes de sérialisation
    public function toJson(): string
    {
        return json_encode($this->plateau);
    }

    public static function fromJson(string $json): PlateauSquadro
    {
        $data = json_decode($json, true);
        $instance = new self();
        foreach ($data as $x => $row) {
            foreach ($row as $y => $cell) {
                if (is_array($cell)) {
                    switch ($cell['type']) {
                        case 'PieceBlanche':
                            $instance->plateau[$x][$y] = new PieceBlanche();
                            break;
                        case 'PieceNoire':
                            $instance->plateau[$x][$y] = new PieceNoire();
                            break;
                        case 'CaseNeutre':
                            $instance->plateau[$x][$y] = new CaseNeutre();
                            break;
                        default:
                            $instance->plateau[$x][$y] = null;
                    }
                }
            }
        }
        return $instance;
    }

    public function __toString(): string
    {
        return print_r($this->plateau, true);
    }
}

// Classes associées
abstract class PieceSquadro
{
    // Classe abstraite pour les pièces du jeu
}

class PieceBlanche extends \PieceSquadro
{
    public function __toString(): string
    {
        return "B";
    }
}

class PieceNoire extends PieceSquadro
{
    public function __toString(): string
    {
        return "N";
    }
}

class CaseNeutre
{
    public function __toString(): string
    {
        return ".";
    }
}
