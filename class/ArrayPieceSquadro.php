<?php

namespace squadroGame;

use InvalidArgumentException;
use OutOfBoundsException;

class ArrayPieceSquadro implements \ArrayAccess, \Countable {
    // Tableau pour stocker les pièces
    private array $pieces = [];

    // Constructeur
    public function __construct() {
        $this->pieces = [
            PieceSquadro::initVide(),
            PieceSquadro::initNeutre(),
            PieceSquadro::initNoirNord(),
            PieceSquadro::initNoirSud(),
            PieceSquadro::initBlancEst(),
            PieceSquadro::initBlancOuest()
        ];
    }
    public function add(PieceSquadro $piece): void {
        $this->pieces[] = $piece;
    }
    public function remove(int $index): void {
        if (isset($this->pieces[$index])) {
            unset($this->pieces[$index]);
            // Reindex the array
            $this->pieces = array_values($this->pieces);
        } else {
            throw new OutOfBoundsException("$index n'existe pas.");
        }
    }
    public function __toString(): string {
        $str = "";
        foreach ($this->pieces as $piece) {
            $str .= $piece . "\n";
        }
        return $str;
    }

    public function toJson(): string {
        $jsonArray = array_map(fn($piece) => json_decode($piece->toJson(), true), $this->pieces);
        return json_encode($jsonArray);
    }

    public static function fromJson(string $json): ArrayPieceSquadro {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new InvalidArgumentException("Invalide JSON data pour ArrayPieceSquadro.");
        }

        $arrayPiece = new ArrayPieceSquadro();
        foreach ($data as $pieceData) {
            $arrayPiece->add(PieceSquadro::fromJson(json_encode($pieceData)));
        }

        return $arrayPiece;
    }

    // Vérifie si une pièce existe à l'index donné
    public function offsetExists($offset): bool {
        return isset($this->pieces[$offset]);
    }

    // Retourne la pièce à l'index donné ou null si elle n'existe pas
    public function offsetGet($offset): mixed {
        return $this->pieces[$offset] ?? null;
    }

    /*
     * Ajoute ou met à jour une pièce à l'index donné,
     * en vérifiant que la valeur est une instance de PieceSquadro
     * */
    public function offsetSet($offset, $value): void {
        if (!$value instanceof PieceSquadro) {
            throw new \InvalidArgumentException("La valeur doit être une instance de PieceSquadro");
        }
        if (is_null($offset)) {
            $this->pieces[] = $value;
        } else {
            $this->pieces[$offset] = $value;
        }
    }

    // Supprime la pièce à l'index donné
    public function offsetUnset($offset): void {
        unset($this->pieces[$offset]);
    }

    // Retourne le nombre total de pièces
    public function count(): int {
        return count($this->pieces);
    }
}