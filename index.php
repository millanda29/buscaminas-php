<?php
session_start();

// Tamaño del tablero
$filas = 6;
$columnas = 6;
$minas = 3;

// Si aún no hay un tablero en la sesión, lo creamos
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

    // Guardamos el tablero en la sesión
    $_SESSION['tablero'] = $tablero;
    $_SESSION['jugando'] = true;
    $_SESSION['perdio'] = false;
    $_SESSION['gano'] = false;
}

// Verificar si el jugador hizo clic en una celda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = $_POST['x'];
    $y = $_POST['y'];

    // Si el jugador hace clic en una mina, pierde el juego
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
}

$tablero = $_SESSION['tablero'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscaminas</title>
    <link rel="icon" href="./img/icon.png" type="image/png">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-image: url('./img/fondo.png');
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h1 {
            margin-bottom: 20px;
        }
        .contenedor {
            width: 300px;
            margin: 20px;
            padding: 20px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            border-collapse: collapse;
            margin: 20px 0;
        }
        td {
            width: 40px;
            height: 40px;
            text-align: center;
            border: 1px solid #ccc;
            cursor: pointer;
            background-color: #f0f0f0;
            font-size: 18px;
        }
        td.descubierto {
            background-color: #fff;
        }
        td.minado {
            background-color: red;
        }
        td:hover {
            background-color: #e0e0e0;
        }
        .mensaje {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
        }
        .win {
            color: green;
        }
        .lose {
            color: red;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h1>Buscaminas</h1>

    <div id="game-container">
        <?php if (!$_SESSION['jugando']): ?>
            <div class="mensaje <?= $_SESSION['gano'] ? 'win' : 'lose' ?>">
                <?= $_SESSION['gano'] ? '¡Ganaste! 🎉' : '¡Perdiste! 💥' ?>
            </div>
            <a href="reiniciar.php" style="margin-top: 20px; display: inline-block;">Jugar de nuevo</a>
        <?php else: ?>
            <table>
                <?php for ($i = 0; $i < $filas; $i++): ?>
                    <tr>
                        <?php for ($j = 0; $j < $columnas; $j++): ?>
                            <td class="<?= $_SESSION['tablero'][$i][$j]['descubierto'] ? 'descubierto' : '' ?>"
                                <?php if (!$_SESSION['tablero'][$i][$j]['descubierto']): ?>
                                    onclick="enviarCelda(<?= $i ?>, <?= $j ?>)">
                                <?php endif; ?>
                                >
                                <?php
                                if ($_SESSION['tablero'][$i][$j]['descubierto']) {
                                    if ($_SESSION['tablero'][$i][$j]['minado']) {
                                        echo '💣';
                                    } else {
                                        echo $_SESSION['tablero'][$i][$j]['vecinas'] > 0 ? $_SESSION['tablero'][$i][$j]['vecinas'] : '';
                                    }
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </table>
        <?php endif; ?>
    </div>

</div>

<script>
    // Función para enviar la celda seleccionada mediante AJAX
    function enviarCelda(x, y) {
        // Crear un FormData con los valores de la celda
        var formData = new FormData();
        formData.append('x', x);
        formData.append('y', y);

        // Usar fetch para enviar los datos
        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Obtener la respuesta del servidor
        .then(data => {
            // Actualizar la vista con la nueva información
            document.getElementById('game-container').innerHTML = data;
        })
        .catch(error => console.error('Error al enviar la celda:', error));
    }
</script>

</body>
</html>
