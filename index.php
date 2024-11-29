<?php
session_start();

// Parámetros del juego
$filas = 6;
$columnas = 6;
$minas = 3;

// Si no hay tablero, inicializarlo
if (!isset($_SESSION['tablero'])) {
    // Inicializar tablero
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

    // Colocar minas
    $minas_colocadas = 0;
    while ($minas_colocadas < $minas) {
        $x = rand(0, $filas - 1);
        $y = rand(0, $columnas - 1);
        if (!$tablero[$x][$y]['minado']) {
            $tablero[$x][$y]['minado'] = true;
            $minas_colocadas++;
            // Actualizar vecinas
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

    // Guardar estado inicial
    $_SESSION['tablero'] = $tablero;
    $_SESSION['jugando'] = true;
    $_SESSION['gano'] = false;
    $_SESSION['perdio'] = false;
}

// Procesar clic en una celda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = $_POST['x'];
    $y = $_POST['y'];

    // Verificar si es mina
    if ($_SESSION['tablero'][$x][$y]['minado']) {
        $_SESSION['jugando'] = false;
        $_SESSION['perdio'] = true;
    } else {
        // Descubrir celda
        $_SESSION['tablero'][$x][$y]['descubierto'] = true;

        // Verificar victoria
        $celdas_descubiertas = 0;
        foreach ($_SESSION['tablero'] as $fila) {
            foreach ($fila as $celda) {
                if ($celda['descubierto'] && !$celda['minado']) {
                    $celdas_descubiertas++;
                }
            }
        }

        if ($celdas_descubiertas === ($filas * $columnas - $minas)) {
            $_SESSION['jugando'] = false;
            $_SESSION['gano'] = true;
        }
    }

    // Devolver contenido actualizado
    include 'partial-game-container.php';
    exit;
}
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
    <div id="game-container">
        <?php include 'partial-game-container.php'; ?>
    </div>
</div>
<script src="./assets/js/game.js"></script>
</body>
</html>
