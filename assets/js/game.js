function enviarCelda(x, y) {
    const formData = new FormData();
    formData.append('x', x);
    formData.append('y', y);

    fetch('index.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('game-container').innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}
