function enviarCelda(x, y) {
    var formData = new FormData();
    formData.append('x', x);
    formData.append('y', y);

    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('game-container').innerHTML = data;
    })
    .catch(error => console.error('Error al enviar la celda:', error));
}
