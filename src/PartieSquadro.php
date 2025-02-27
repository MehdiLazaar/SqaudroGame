<?php

namespace src;

class PartieSquadro {
    private const PLAYER_ONE = 0;
    public const PLAYER_TWO = 1;
    private ?int $partieId = 0;
    private array $joueurs;
    public int $joueurActif;
    public string $gameStatus = 'initialized';
    public PlateauSquadro $plateau;

    public function __construct(JoueurSquadro $playerOne){
        $this->joueurs[self::PLAYER_ONE] = $playerOne;
        $this->joueurActif = self::PLAYER_ONE;
        $this->plateau = new PlateauSquadro();
    }
    public function addJoueur(JoueurSquadro $player) : void {
        if (!isset($this->joueurs[self::PLAYER_TWO])) {
            $this->joueurs[self::PLAYER_TWO] = $player;
        } else {
            throw new \Exception("La partie est déjà complète !");
        }
    }
    public function getJoueurActif(): JoueurSquadro {
        return $this->joueurs[$this->joueurActif];
    }
    public function getNomJoueurActif(string $nom): string {
        return $this->getJoueurActif()->getNomJoueur();
    }
    public function __toString(): string {
        return "Partie ID: " . $this->getPartieID() .
            " | Joueur actif: " . $this->getNomJoueurActif($this->getJoueurActif()->getNomJoueur());
    }
    public function getPartieID() : int {
        return $this->partieId ?? 0;
    }
    public function setPartieID(int $id) : void {
        $this->partieId = $id;
    }
    public function getJoueurs() : array {
        return $this->joueurs;
    }
    public function toJson(int $id) : string {
        return json_encode([
            'partieId' => $this->getPartieID(),
            'joueurActif' => $this->getJoueurActif(),
            'gameStatus' => $this->gameStatus,
            'joueurs' => array_map(fn($joueur) => [
                'nomJoueur' => $joueur->getNomJoueur(),
                'id' => $joueur->getId()
            ], $this->joueurs)
        ]);
    }
    public static function fromJson(): PartieSquadro {
        // On suppose que les données sont stockées en session sous le nom 'partieSquadro'
        if (!isset($_SESSION['partieSquadro'])) {
            throw new \Exception("Aucune partie sauvegardée en session !");
        }
        $json = $_SESSION['partieSquadro'];
        $data = json_decode($json, true);
        if (!$data) {
            throw new \Exception("Données JSON invalides !");
        }
        // Création de la partie avec le premier joueur
        $playerOne = new JoueurSquadro($data['joueurs'][self::PLAYER_ONE]['nom'], $data['joueurs'][self::PLAYER_ONE]['id']);

        $partie = new self($playerOne);
        $partie->setPartieID($data['partieId']);
        $partie->joueurActif = $data['joueurActif'];
        $partie->gameStatus = $data['gameStatus'];
        // Ajout du deuxième joueur s'il existe
        if (isset($data['joueurs'][self::PLAYER_TWO])) {
            $playerTwo = new JoueurSquadro();
            $playerTwo->setNomJoueur($data['joueurs'][self::PLAYER_TWO]['nom']);
            $playerTwo->setId($data['joueurs'][self::PLAYER_TWO]['id']);
            $partie->joueurs[self::PLAYER_TWO] = $playerTwo;
        }
        return $partie;
    }
}