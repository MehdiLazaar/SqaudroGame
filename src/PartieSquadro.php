<?php

namespace src;
require_once 'PlateauSquadro.php';
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
    public function toJson(int $id): string {
        return json_encode([
            'partieId' => $id > 0 ? $id : $this->getPartieID(),
            'joueurActif' => $this->joueurActif,
            'gameStatus' => $this->gameStatus,
            'joueurs' => array_map(function($joueur) {
                return [
                    'nomJoueur' => $joueur->getNomJoueur(),
                    'id' => $joueur->getId()
                ];
            }, $this->joueurs),
            'plateau' => $this->plateau->toJson()
        ]);
    }
    public static function fromJson(): PartieSquadro {
        if (!isset($_SESSION['partieSquadro'])) {
            throw new \Exception("Aucune donnée de partie dans la session");
        }
        $json = $_SESSION['partieSquadro'];
        $data = json_decode($json, true);
        if (!$data) {
            throw new \Exception("Données JSON invalides");
        }
        // Création du premier joueur
        $playerOneData = $data['joueurs'][self::PLAYER_ONE];
        $playerOne = new JoueurSquadro($playerOneData['nomJoueur'], $playerOneData['id']);
        $partie = new PartieSquadro($playerOne);
        // Ajout du second joueur si présent
        if (isset($data['joueurs'][self::PLAYER_TWO])) {
            $playerTwoData = $data['joueurs'][self::PLAYER_TWO];
            $playerTwo = new JoueurSquadro($playerTwoData['nomJoueur'], $playerTwoData['id']);
            $partie->addJoueur($playerTwo);
        }
        $partie->gameStatus  = $data['gameStatus'];
        $partie->joueurActif = $data['joueurActif'];
        $partie->setPartieID($data['partieId']);
        // Reconstitution du plateau
        $partie->plateau = PlateauSquadro::fromJson($data['plateau']);
        return $partie;
    }
}