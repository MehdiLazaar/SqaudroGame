<?php

namespace src;

use InvalidArgumentException;
use OutOfBoundsException;

class ArrayPieceSquadro implements \ArrayAccess, \Countable {
    // Tableau pour stocker les pièces
    private array $pieces = [];

    // Constructeur
    /*public function __construct(array $pieces = []) {
        $this->pieces = $pieces;
    }*/
    public function __construct(array $pieces = []) {
        foreach ($pieces as $piece) {
            if (!$piece instanceof PieceSquadro) {
                throw new InvalidArgumentException("Toutes les pièces doivent être des instances de PieceSquadro.");
            }
        }
        $this->pieces = $pieces;
    }

    public function add(PieceSquadro $piece): void {
        if (!$piece instanceof PieceSquadro) {
            throw new InvalidArgumentException("La valeur doit être une instance de PieceSquadro");
        }
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
    /*public function __toString(): string {
        $str = "";
        foreach ($this->pieces as $piece) {
            $str .= $piece . "\n";
        }
        return $str;
    }*/
    public function __toString(): string {
        return implode("\n", array_map(fn($piece) => $piece->__toString(), $this->pieces));
    }

    public function toJson(): string {
        return json_encode(array_map(fn($piece) => [
            'couleur' => $piece->getCouleur(),
            'direction' => $piece->getDirection()
        ], $this->pieces));
    }



    public static function fromJson(string $json): self {
        $array = json_decode($json, true);
        if ($array === null) {
            throw new InvalidArgumentException("JSON invalide");
        }

        $instance = new self();
        foreach ($array as $pieceData) {
            $instance->add(PieceSquadro::fromJson(json_encode($pieceData)));
        }
        return $instance;
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
            throw new InvalidArgumentException("La valeur doit être une instance de PieceSquadro");
        }
        if ($offset !== null && !is_int($offset)) {
            throw new InvalidArgumentException("L'index doit être un entier ou null");
        }
        if ($offset === null) {
            $this->pieces[] = $value;
        } else {
            $this->pieces[$offset] = $value;
        }
    }

    // Supprime la pièce à l'index donné
    public function offsetUnset($offset): void {
        unset($this->pieces[$offset]);
        $this->pieces = array_values($this->pieces);
    }


    // Retourne le nombre total de pièces
    public function count(): int {
        return count($this->pieces);
    }
}