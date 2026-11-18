// ==============================================================
// BÚSQUEDA Y FILTRADO EN TIEMPO REAL - KEBA MOTOS
// ==============================================================

function inicializarBuscador(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (!input || !table) return;

    input.addEventListener('keyup', function () {
        const filtro = this.value.toLowerCase().trim();
        const filas = table.querySelectorAll('tbody tr');

        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            if (textoFila.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    inicializarBuscador('inputBuscar', 'tablaRegistros');
});

