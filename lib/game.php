<?php

session_start();

// Initialize $nextPlayer with a default value
$nextPlayer = 'X'; // Default starting player

// Initialize the game board if it doesn't exist
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = [
        ['', '', ''],
        ['', '', ''],
        ['', '', '']
    ];
}

$board = &$_SESSION['board'];

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        // Reset the game
        $_SESSION['board'] = [
            ['', '', ''],
            ['', '', ''],
            ['', '', '']
        ];
        // Redirect to clear POST data
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['cell'])) {
        [$row, $col] = explode(',', $_POST['cell']);
        $player = $_POST['player']; // 'X' or 'O'

        // Validate and make the move
        $row = (int)$row;
        $col = (int)$col;
        if ($row >= 0 && $row < 3 && $col >= 0 && $col < 3 && $board[$row][$col] === '') {
            $board[$row][$col] = $player;
        }

        // Determine the next player
        $nextPlayer = $player === 'X' ? 'O' : 'X';
    }
    // No else block is needed here for $nextPlayer initialization since it's done at the start.
}

function checkWinner($board) {
    // Check rows, columns, and diagonals
    for ($i = 0; $i < 3; $i++) {
        if ($board[$i][0] === $board[$i][1] && $board[$i][1] === $board[$i][2] && $board[$i][0] !== '') {
            return $board[$i][0];
        }
        
        if ($board[0][$i] === $board[1][$i] && $board[1][$i] === $board[2][$i] && $board[0][$i] !== '') {
            return $board[0][$i];
        }
    }

    // Diagonals
    if ($board[0][0] === $board[1][1] && $board[1][1] === $board[2][2] && $board[0][0] !== '') {
        return $board[0][0];
    }
    
    if ($board[0][2] === $board[1][1] && $board[1][1] === $board[2][0] && $board[0][2] !== '') {
        return $board[0][2];
    }

    // Check for draw
    foreach ($board as $row) {
        if (in_array('', $row, true)) {
            return false;
        }
    }

    return 'Draw';
}

echo "<form action='' method='post'>";
for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
        $cellValue = $board[$row][$col] ?: '-';
        echo "<button type='submit' name='cell' value='{$row},{$col}'>{$cellValue}</button>";
    }
    echo "<br>";
}
echo "<input type='hidden' name='player' value='{$nextPlayer}'>";
echo "</form>";

echo "<form action='' method='post'>";
echo "<input type='hidden' name='reset' value='1'>";
echo "<button type='submit'>Reset Game</button>";
echo "</form>";

$winner = checkWinner($board);
if ($winner) {
    echo $winner === 'Draw' ? "It's a draw!" : "Player {$winner} wins!";
    // Optionally, reset the board to start a new game
    unset($_SESSION['board']);
}

?>
