<?php
// AnnulerChoix.php

session_start();

// Annuler la sélection de la pièce
if (isset($_SESSION['pieceSelectionnee'])) {
    unset($_SESSION['pieceSelectionnee']);
}

// Rediriger vers la page principale
header("Location: ChoisirPiece.php");
exit();