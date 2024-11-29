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
