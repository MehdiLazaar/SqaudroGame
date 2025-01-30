<?php
namespace src;
use InvalidArgumentException;

class PieceSquadro {

    // Attributes
    public const BLANC = 0;
    public const NOIR = 1;
    public const VIDE = -1;
    public const NEUTRE = -2;

    public const NORD = 0;
    public const EST = 1;
    public const SUD = 2;
    public const OUEST = 3;

    private int $couleur;
    private int $direction;

    //Constructeur
    private function __construct(int $couleur, int $direction) {
        $this->couleur = $couleur;
        $this->direction = $direction;
    }

    // Methode static pour initialiser les pieces
    public static function initVide(): PieceSquadro {
        return new PieceSquadro(self::VIDE, self::VIDE);
    }

    public static function initNeutre(): PieceSquadro {
        return new PieceSquadro(self::NEUTRE, self::NEUTRE);
    }

    public static function initNoirNord(): PieceSquadro {
        return new PieceSquadro(self::NOIR, self::NORD);
    }

    public static function initNoirSud(): PieceSquadro {
        return new PieceSquadro(self::NOIR, self::SUD);
    }

    public static function initBlancEst(): PieceSquadro {
        return new PieceSquadro(self::BLANC, self::EST);
    }

    public static function initBlancOuest(): PieceSquadro {
        return new PieceSquadro(self::BLANC, self::OUEST);
    }

    // Getters
    public function getCouleur(): int {
        return $this->couleur;
    }

    public function getDirection(): int {
        return $this->direction;
    }

    // Methode pour inverser la direction
    public function inverseDirection(): void {
        if ($this->direction === self::NORD) {
            $this->direction = self::SUD;
        } elseif ($this->direction === self::SUD) {
            $this->direction = self::NORD;
        } elseif ($this->direction === self::EST) {
            $this->direction = self::OUEST;
        } elseif ($this->direction === self::OUEST) {
            $this->direction = self::EST;
        }
    }

    // Convertion de l'objet en chaine de caractère
    public function __toString(): string {
        return "PieceSquadro [Couleur: {$this->couleur}, Direction: {$this->direction}]";
    }

    //Convertion de l'objet en JSON
    public function toJson(): string {
        return json_encode([
            'couleur' => $this->couleur,
            'direction' => $this->direction,
        ]);
    }

    //Creation d'un objet à partir de sa representation JSON
    public static function fromJson(string $json): PieceSquadro {
        $data = json_decode($json, true);

        if (!isset($data['couleur']) || !isset($data['direction'])) {
            throw new InvalidArgumentException("Invalid JSON data for PieceSquadro");
        }

        return new PieceSquadro($data['couleur'], $data['direction']);
    }
}
