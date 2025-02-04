<?php
// Rejouer.php

session_start();

// Réinitialiser la session
$_SESSION = [];

// Rediriger vers la page principale
header("Location: ChoisirPiece.php");
exit();