<?php
namespace Squadro;

use Exception;
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
    public static function createPlayer(string $name): JoueurSquadro{
        if (!isset(self::$createPlayerSquadro)) {
            self::$createPlayerSquadro = self::$pdo->prepare(
                'INSERT INTO JoueurSquadro(joueurNom) VALUES (:name)'
            );
        }
        self::$createPlayerSquadro->execute([':name' => $name]);
        $dernierId = self::$pdo->lastInsertId();
        return new JoueurSquadro($name,(int)$dernierId);
    }

    public static function selectPlayerByName(string $name): ?JoueurSquadro{
        if (!isset(self::$selectPlayerByName)) {
            self::$selectPlayerByName = self::$pdo->prepare(
                'SELECT id, joueurNom AS nomJoueur FROM JoueurSquadro WHERE joueurNom = :name'
            );
        }
        self::$selectPlayerByName->bindValue(':name', $name, PDO::PARAM_STR);
        self::$selectPlayerByName->execute();
        $player = self::$selectPlayerByName->fetch(PDO::FETCH_ASSOC);
        return $player ? new JoueurSquadro($player['nomJoueur'], $player['id']) : null;
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
    public static function createPartieSquadro(string $playerName, string $json): int
    {
        if (!isset(self::$createPartieSquadro)) {
            self::$createPartieSquadro = self::$pdo->prepare(
                'INSERT INTO PartieSquadro (playerOne, gameStatus, json) VALUES (:playerOne, :gameStatus, :json)'
            );
        }

        try {
            self::$pdo->beginTransaction();

            // Vérification que le joueur existe
            $player = self::selectPlayerByName($playerName);
            if (!$player) {
                throw new Exception("Le joueur '$playerName' n'existe pas dans la base de données.");
            }

            // Création de la partie avec le joueur
            self::$createPartieSquadro->execute([
                ':playerOne' => $player->getId(),
                ':gameStatus' => 'initialized',
                ':json' => $json
            ]);

            // Récupérer l'ID de la dernière partie insérée
            $dernierId = self::$pdo->lastInsertId();

            // Mettre à jour l'objet PartieSquadro avec l'ID de la partie
            $partie = PartieSquadro::fromJson(); // Récupère la partie depuis la session
            $partie->setPartieID((int)$dernierId);

            // Mettre à jour le JSON avec le nouvel ID de la partie
            $updatedJson = $partie->toJson($dernierId);

            // Mettre à jour la base de données avec le JSON mis à jour
            self::savePartieSquadro('initialized', $updatedJson, $dernierId); // Appel correct de la méthode

            // Sauvegarder l'ID de la partie en session uniquement pour cette nouvelle partie
            $_SESSION['newPartieId'] = $dernierId;
            self::$pdo->commit();

            // Retourner l'ID de la partie insérée
            return (int)$dernierId;
        } catch (Exception $e) {
            self::$pdo->rollBack();
            echo "Erreur : " . $e->getMessage();
            return 0; // Retourner 0 en cas d'erreur
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
            self::$savePartieSquadro->execute([
                ':gameStatus' => $gameStatus,
                ':json' => $json,
                ':partieId' => $partieId
            ]);
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
