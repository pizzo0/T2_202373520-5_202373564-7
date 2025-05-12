const btnEliminarArticulo = document.getElementById('eliminar-articulo');

btnEliminarArticulo.addEventListener('click',() => {
    const id_articulo = btnEliminarArticulo.getAttribute('data-articulo');

    const confirmar = confirm('¿Estás seguro de que quieres eliminar este artículo?');

    if (!confirmar) return;

    fetch(`/php/api/articulo.eliminar.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id_articulo: id_articulo })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al eliminar el artículo');
            }
            return response.json();
        })
        .then(data => {
            console.log('Artículo eliminado con éxito:', data);
            window.location.href = '/';
        })
        .catch(error => {
            console.error('Error:', error);
        });
});