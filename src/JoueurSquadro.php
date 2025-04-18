<?php

namespace src;

use PhpParser\Node\Scalar\String_;

class JoueurSquadro {
    private string $nomJoueur;
    private int $id;
    //Constructeur
    public function __construct(string $nomJoueur, int $id) {
        $this->nomJoueur = $nomJoueur;
        $this->id = $id;
    }
    public function getNomJoueur() : string {
        return $this->nomJoueur;
    }
    public function setNomJoueur(string $nom) : void {
        $this->nomJoueur = $nom;
    }
    public function getId(): int {
        return $this->id;
    }
    public function setId(int $id) : void {
        $this->id = $id;
    }
    public function toJson(): string {
        return json_encode([
            "nomJoueur" => $this->nomJoueur,
            "id" => $this->id
        ]);
    }
    public static function fromJson(string $json): JoueurSquadro {
        $data = json_decode($json, true);
        if (!isset($data['nomJoueur']) || !isset($data['id'])) {
            throw new \InvalidArgumentException("Format JSON invalide.");
        }

        return new JoueurSquadro($data['nomJoueur'], $data['id']);
    }
}