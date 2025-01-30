<?php

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadroUI.php';

use src\PieceSquadro;
use src\PlateauSquadro;
use src\PieceSquadroUI;

// Start the session to manage persistent data
session_start();

// Initialize the board if not already done
if (!isset($_SESSION['plateau'])) {
    $_SESSION['plateau'] = new PlateauSquadro();
}

// Retrieve the board from the session
$plateau = $_SESSION['plateau'];

// Error handling for piece selection form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x']) && isset($_POST['y'])) {
    $x = filter_input(INPUT_POST, 'x', FILTER_VALIDATE_INT);
    $y = filter_input(INPUT_POST, 'y', FILTER_VALIDATE_INT);

    if ($x === false || $y === false) {
        echo "Invalid coordinates provided.<br>";
    } else {
        // Display the selected piece coordinates
        echo "Selected piece at coordinates: ($x, $y)<br>";

        // Store the selected piece coordinates in the session for move confirmation
        $_SESSION['selected_piece'] = ['x' => $x, 'y' => $y];
    }
}

// Error handling for move confirmation form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer'])) {
    $confirmation = filter_input(INPUT_POST, 'confirmer', FILTER_SANITIZE_STRING);
    $selected_piece = $_SESSION['selected_piece'] ?? null;

    if ($selected_piece === null) {
        echo "No piece selected for confirmation.<br>";
    } else {
        // Display the confirmation result and the selected piece coordinates
        echo "Move confirmation: $confirmation<br>";
        echo "Selected piece at coordinates: (" . $selected_piece['x'] . ", " . $selected_piece['y'] . ")<br>";

        // If the move is confirmed, process the move
        if ($confirmation === 'oui') {
            // Here, you would add the logic to update the board state
            // For example, move the piece to a new position, check for win conditions, etc.
            // This part is not implemented in this step and should be done in the next steps
        }
    }
}

// Display the current state of the board
echo PieceSquadroUI::generationPlateauJeu($plateau, PieceSquadro::BLANC);

?>