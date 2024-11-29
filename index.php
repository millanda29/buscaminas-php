<?php
session_start();

// Tamaño del tablero
$filas = 6;
$columnas = 6;
$minas = 3;

// Inicializar el tablero solo si no existe en la sesión
if (!isset($_SESSION['tablero'])) {
    // Crear tablero vacío
    $tablero = [];
    for ($i = 0; $i < $filas; $i++) {
        for ($j = 0; $j < $columnas; $j++) {
            $tablero[$i][$j] = [
                'minado' => false,
                'descubierto' => false,
                'vecinas' => 0,
            ];
        }
    }

    // Colocar minas aleatoriamente
    $minas_colocadas = 0;
    while ($minas_colocadas < $minas) {
        $x = rand(0, $filas - 1);
        $y = rand(0, $columnas - 1);
        if (!$tablero[$x][$y]['minado']) {
            $tablero[$x][$y]['minado'] = true;
            $minas_colocadas++;

            // Actualizar las celdas vecinas
            for ($dx = -1; $dx <= 1; $dx++) {
                for ($dy = -1; $dy <= 1; $dy++) {
                    $nx = $x + $dx;
                    $ny = $y + $dy;
                    if ($nx >= 0 && $nx < $filas && $ny >= 0 && $ny < $columnas && !$tablero[$nx][$ny]['minado']) {
                        $tablero[$nx][$ny]['vecinas']++;
                    }
                }
            }
        }
    }

    // Guardar el tablero en la sesión
    $_SESSION['tablero'] = $tablero;
    $_SESSION['jugando'] = true;
    $_SESSION['perdio'] = false;
    $_SESSION['gano'] = false;
}

// Verificar si el jugador hizo clic en una celda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = intval($_POST['x']);
    $y = intval($_POST['y']);

    // Si el jugador hace clic en una mina, pierde
    if ($_SESSION['tablero'][$x][$y]['minado']) {
        $_SESSION['perdio'] = true;
        $_SESSION['jugando'] = false;
    } else {
        // Descubrir la celda
        $_SESSION['tablero'][$x][$y]['descubierto'] = true;

        // Verificar si el jugador ha ganado
        $celdas_descubiertas = 0;
        foreach ($_SESSION['tablero'] as $fila) {
            foreach ($fila as $celda) {
                if ($celda['descubierto'] && !$celda['minado']) {
                    $celdas_descubiertas++;
                }
            }
        }
        if ($celdas_descubiertas === ($filas * $columnas - $minas)) {
            $_SESSION['gano'] = true;
            $_SESSION['jugando'] = false;
        }
    }

    // Responder con HTML parcial si es una solicitud AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        include 'partial-game-container.php';
        exit;
    }
}

$tablero = $_SESSION['tablero'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscaminas</title>
    <link rel="icon" href="./assets/img/icon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="contenedor">
    <h1>Buscaminas</h1>

    <!-- Contenedor del juego -->
    <div id="game-container">
        <?php include 'partial-game-container.php'; ?>
    </div>
</div>

<script src="./assets/js/game.js"></script>

</body>
</html>
