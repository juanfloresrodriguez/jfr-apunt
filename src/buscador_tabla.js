// buscador_tabla.js
function activarBuscadorTabla(inputSelector, tablaSelector) {
    const input = document.querySelector(inputSelector);
    const tabla = document.querySelector(tablaSelector);

    if (!input || !tabla) return;

    // Detectar el índice de la columna 'Nombre' o 'Usuario'
    let indice = -1;
    const ths = tabla.querySelectorAll('thead th');
    ths.forEach((th, i) => {
        const texto = th.textContent.trim().toLowerCase();
        if (texto === 'nombre' || texto === 'usuario') {
            indice = i;
        }
    });
    if (indice === -1) return; // No hay columna válida

    input.addEventListener('input', function () {
        const filtro = input.value.toLowerCase();
        const filas = tabla.querySelectorAll('tbody tr');
        filas.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            const texto = celdas[indice]?.textContent.toLowerCase() || '';
            fila.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });

    // --- ORDENACIÓN POR COLUMNAS ---
    // Cuando haces clic en cualquier encabezado de la tabla, ordena las filas por esa columna.
    // Si vuelves a hacer clic, invierte el orden (ascendente/descendente). Funciona con texto y números.
    ths.forEach((th, i) => {
        th.style.cursor = 'pointer';
        let ordenAscendente = true;
        th.addEventListener('click', function () {
            const filas = Array.from(tabla.querySelectorAll('tbody tr'));
            filas.sort((a, b) => {
                const aText = a.querySelectorAll('td')[i]?.textContent.trim().toLowerCase() || '';
                const bText = b.querySelectorAll('td')[i]?.textContent.trim().toLowerCase() || '';
                if (!isNaN(aText) && !isNaN(bText)) {
                    // Ordenar como número si ambos son numéricos
                    return ordenAscendente ? (Number(aText) - Number(bText)) : (Number(bText) - Number(aText));
                }
                return ordenAscendente ? aText.localeCompare(bText) : bText.localeCompare(aText);
            });
            // Reinsertar filas ordenadas
            const tbody = tabla.querySelector('tbody');
            filas.forEach(fila => tbody.appendChild(fila));
            ordenAscendente = !ordenAscendente;
        });
    });
} 