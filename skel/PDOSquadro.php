<?php
namespace Squadro;

use PDO;
use PDOException;
use PDOStatement;
use src\JoueurSquadro;
use src\PartieSquadro;
require_once __DIR__ . '/../src/JoueurSquadro.php';
require_once __DIR__ . '/../src/PartieSquadro.php';


class PDOSquadro
{
    private static PDO $pdo;

    public static function initPDO(string $sgbd, string $host, string $db, string $user, string $password): void
    {
        switch ($sgbd) {
/*            case 'mysql':
                TODO si nécessaire
                break;
                */
            case 'pgsql':
                self::$pdo = new PDO('pgsql:host=' . $host . ' dbname=' . $db . ' user=' . $user . ' password=' . $password);
                break;
            default:
                exit ("Type de sgbd non correct : $sgbd fourni, 'pgsql' attendu");
        }

        // pour récupérer aussi les exceptions provenant de PDOStatement
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* requêtes Préparées pour l'entitePlayerSquadro */
    private static PDOStatement $createPlayerSquadro;
    private static PDOStatement $selectPlayerByName;

    /******** Gestion des requêtes relatives à JoueurSquadro *************/
    public static function createPlayer(string $name): JoueurSquadro
    {
        if (!isset(self::$createPlayerSquadro))
            self::$createPlayerSquadro = self::$pdo->prepare('INSERT INTO JoueurSquadro(joueurNom) VALUES (:name)');

        self::$createPlayerSquadro->bindValue(':name', $name, PDO::PARAM_STR);
        self::$createPlayerSquadro->execute();

        return self::selectPlayerByName($name);
    }

    public static function selectPlayerByName(string $name): ?JoueurSquadro
    {
        if (!isset(self::$selectPlayerByName))
            self::$selectPlayerByName = self::$pdo->prepare('SELECT * FROM JoueurSquadro WHERE joueurNom=:name');
        self::$selectPlayerByName->bindValue(':name', $name, PDO::PARAM_STR);
        self::$selectPlayerByName->execute();
        $joueuer = self::$selectPlayerByName->fetchObject(JoueurSquadro::class);
        return ($joueuer) ? $joueuer : null;
    }

    /* requêtes préparées pour l'entite PartieSquadro */
    private static PDOStatement $createPartieSquadro;
    private static PDOStatement $savePartieSquadro;
    private static PDOStatement $addPlayerToPartieSquadro;
    private static PDOStatement $selectPartieSquadroById;
    private static PDOStatement $selectAllPartieSquadro;
    private static PDOStatement $selectAllPartieSquadroByPlayerName;

    /******** Gestion des requêtes relatives à PartieSquadro *************/

    /**
     * initialisation et execution de $createPartieSquadro la requête préparée pour enregistrer une nouvelle partie
     */
    public static function createPartieSquadro(string $playerName, string $json): void
    {
        if (!isset(self::$createPartieSquadro)) {
            self::$createPartieSquadro = self::$pdo->prepare(
                'INSERT INTO PartieSquadro (playerOne, gameStatus, json) VALUES (:playerOne, :gameStatus, :json)'
            );
        }

        try {
            self::$pdo->beginTransaction();

            // Vérifier si le joueur existe
            $player = self::selectPlayerByName($playerName);
            if (!$player) {
                echo "Le joueur n'existe pas.";
                return;
            }

            $playerId = $player->getId();
            $gameStatus = 'initialized'; // Remplacer 'constructed' par 'initialized' (correspond à la table)

            // Lier les valeurs correctes
            self::$createPartieSquadro->bindValue(':playerOne', $playerId, PDO::PARAM_INT);
            self::$createPartieSquadro->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
            self::$createPartieSquadro->bindValue(':json', $json, PDO::PARAM_STR);
            self::$createPartieSquadro->execute();

            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            echo "Erreur lors de la création de la partie : " . $e->getMessage();
        }
    }

    /**
     * initialisation et execution de $savePartieSquadro la requête préparée pour changer
     * l'état de la partie et sa représentation json
     */
    public static function savePartieSquadro(string $gameStatus, string $json, int $partieId): void
    {
        if (!isset(self::$savePartieSquadro)) {
            self::$savePartieSquadro = self::$pdo->prepare(
                'UPDATE PartieSquadro SET gameStatus = :gameStatus, json = :json WHERE partieId = :partieId'
            );
        }

        try {
            self::$pdo->beginTransaction();

            // Lier correctement les paramètres
            self::$savePartieSquadro->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
            self::$savePartieSquadro->bindValue(':json', $json, PDO::PARAM_STR);
            self::$savePartieSquadro->bindValue(':partieId', $partieId, PDO::PARAM_INT); // Correction ici

            self::$savePartieSquadro->execute();

            self::$pdo->commit();
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            echo "Erreur lors de la sauvegarde de la partie : " . $e->getMessage();
        }
    }


    /**
     * initialisation et execution de $addPlayerToPartieSquadro la requête préparée pour intégrer le second joueur
     */
    public static function addPlayerToPartieSquadro(string $playerName, string $json, int $gameId): void
    {
	/** TODO **/
    }

    /**
     * initialisation et execution de $selectPartieSquadroById la requête préparée pour récupérer
     * une instance de PartieSquadro en fonction de son identifiant
     */
    public static function getPartieSquadroById(int $gameId): ?PartieSquadro
    {
	/** TODO **/
        return null;
    }
    /**
     * initialisation et execution de $selectAllPartieSquadro la requête préparée pour récupérer toutes
     * les instances de PartieSquadro
     */
    public static function getAllPartieSquadro(): array
    {
	/** TODO **/
    }

    /**
     * initialisation et execution de $selectAllPartieSquadroByPlayerName la requête préparée pour récupérer les instances
     * de PartieSquadro accessibles au joueur $playerName
     * ne pas oublier les parties "à un seul joueur"
     */
    public static function getAllPartieSquadroByPlayerName(string $playerName): array
    {
	/** TODO **/
    }
    /**
     * initialisation et execution de la requête préparée pour récupérer
     * l'identifiant de la dernière partie ouverte par $playername
     */
    public static function getLastGameIdForPlayer(string $playerName): int
    {
	/** TODO **/
    }

}
