"use strict";
document.addEventListener('DOMContentLoaded', pueblaContenidos);

let itemsPorPagina = 5;
let ULpaginacion;
let paginas;
let contenidos;

function pueblaContenidos() {
    contenidos = Array.from(document.querySelectorAll('#lista-contenido > div'));
    ULpaginacion = document.getElementById('paginacion-contenido');
    if (ULpaginacion) {
        ULpaginacion.addEventListener('click', actualizaPaginaContenidos);
        muestraPaginaContenidos(1);
    }
    
}

function muestraPaginaContenidos(pagina) {
    paginas = Math.ceil(contenidos.length / itemsPorPagina);
    
    if (paginas <= 1) {
      ULpaginacion.innerHTML = '';
      return;
    }    
    
    const aMostrar = contenidos.slice((pagina - 1) * itemsPorPagina, pagina * itemsPorPagina);
    contenidos.forEach(item => item.style.display = 'none');
    aMostrar.forEach(item => item.style.display = '');
    
    let pagAnterior = '';
    let pagSiguiente = '';
    if (pagina > 1)
        pagAnterior = `<li><a class=\"flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white\" href=\"#\">${pagina - 1}</a></li>`;
    if (pagina < paginas)
        pagSiguiente = `<li><a class=\"flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white\" href=\"#\">${pagina + 1}</a></li>`;

    ULpaginacion.innerHTML = `
    <nav aria-label=\"Page navigation example\">
      <ul class=\"inline-flex -space-x-px text-base h-10\">
        <li>
          <a class=\"flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ${pagina === 1 ? 'opacity-50 pointer-events-none' : ''}\" href=\"#\" aria-label=\"Previous\">
            <span aria-hidden=\"true\">&laquo;</span>
          </a>
        </li>
        ${pagAnterior}
        <li>
          <a class=\"flex items-center justify-center px-4 h-10 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white\" href=\"#\">${pagina}</a>
        </li>
        ${pagSiguiente}
        <li>
          <a class=\"flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ${pagina === paginas ? 'opacity-50 pointer-events-none' : ''}\" href=\"#\" aria-label=\"Next\">
            <span aria-hidden=\"true\">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>`;
}

function actualizaPaginaContenidos(evento) {
    evento.preventDefault();
    if (!(evento.target.tagName === 'A' || evento.target.tagName === 'SPAN')) return;
    if (evento.target.textContent.trim() === '»')
        muestraPaginaContenidos(paginas);
    else if (evento.target.textContent.trim() === '«')
        muestraPaginaContenidos(1);
    else
        muestraPaginaContenidos(+evento.target.textContent.trim());
} 