<?php

namespace Squadro;

use Squadro\PDOSquadro;

require_once __DIR__ . '../skel/PDOSquadro.php';
session_start();

function getPageLogin(): string
{
    $form = '<!DOCTYPE html>
<html class="no-js" lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="Author" content="Salhi/Lazaar" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css" />
    <title>Accès à la salle de jeux</title>
  </head>
  <body>
    <section class="section">
      <div class="container">
        <div class="box has-text-centered">
          <h1 class="title">Accès au salon Squadro</h1>
          <h2 class="subtitle">Identification du joueur</h2>
          <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
            <div class="field">
              <label class="label">Nom</label>
              <div class="control">
                <input class="input" type="text" name="playerName" placeholder="Entrez votre nom" required>
              </div>
            </div>
            <div class="field">
              <div class="control">
                <button class="button is-primary" type="submit" name="action" value="connecter">Se connecter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </body>
</html>';
    return $form;
}

if (isset($_REQUEST['playerName'])) {
    // Connexion à la base de données
    require_once 'env/db.php';
    PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
    $player = PDOSquadro::selectPlayerByName($_REQUEST['playerName']);
    if (is_null($player)) {
        $player = PDOSquadro::createPlayer($_REQUEST['playerName']);
    }
    $_SESSION['player'] = $player;
    header('HTTP/1.1 303 See Other');
    header('Location: index.php');
} else {
    echo getPageLogin();
}