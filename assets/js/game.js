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
