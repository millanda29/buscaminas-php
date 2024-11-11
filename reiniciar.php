<?php
// Iniciar la sesión para modificar los valores almacenados
session_start();

// Destruir la sesión para reiniciar el estado del juego
session_unset();  // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir al jugador a la página principal para empezar un nuevo juego
header("Location: index.php");
exit();
?>
