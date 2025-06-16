"use strict";
// document.addEventListener('DOMContentLoaded', pueblaAsignaturas);

// let itemsPorPagina = 5;
// let ULpaginacion;
// let paginas;
// let asignaturas;

// function pueblaAsignaturas() {
//     asignaturas = Array.from(document.querySelectorAll('#grid-asignaturas > div'));
//     ULpaginacion = document.getElementById('paginacion-asignaturas');
//     if (ULpaginacion) {
//         ULpaginacion.addEventListener('click', actualizaPagina);
//         muestraPagina(1);
//     }
// }

// function muestraPagina(pagina) {
//     paginas = Math.ceil(asignaturas.length / itemsPorPagina);
//     const aMostrar = asignaturas.slice((pagina - 1) * itemsPorPagina, pagina * itemsPorPagina);
//     asignaturas.forEach(item => item.style.display = 'none');
//     aMostrar.forEach(item => item.style.display = '');

//     let pagAnterior = '';
//     let pagSiguiente = '';
//     if (pagina > 1)
//         pagAnterior = `<li><a class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" href="#">${pagina - 1}</a></li>`;
//     if (pagina < paginas)
//         pagSiguiente = `<li><a class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" href="#">${pagina + 1}</a></li>`;

//     ULpaginacion.innerHTML = `
//     <nav aria-label="Page navigation example">
//       <ul class="inline-flex -space-x-px text-base h-10">
//         <li>
//           <a class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ${pagina === 1 ? 'opacity-50 pointer-events-none' : ''}" href="#" aria-label="Previous">
//             <span aria-hidden="true">&laquo;</span>
//           </a>
//         </li>
//         ${pagAnterior}
//         <li>
//           <a class="flex items-center justify-center px-4 h-10 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white" href="#">${pagina}</a>
//         </li>
//         ${pagSiguiente}
//         <li>
//           <a class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ${pagina === paginas ? 'opacity-50 pointer-events-none' : ''}" href="#" aria-label="Next">
//             <span aria-hidden="true">&raquo;</span>
//           </a>
//         </li>
//       </ul>
//     </nav>`;
// }

// function actualizaPagina(evento) {
//     evento.preventDefault();
//     if (!(evento.target.tagName === 'A' || evento.target.tagName === 'SPAN')) return;
//     if (evento.target.textContent.trim() === '»')
//         muestraPagina(paginas);
//     else if (evento.target.textContent.trim() === '«')
//         muestraPagina(1);
//     else
//         muestraPagina(+evento.target.textContent.trim());
// }

// Mostrar los contenidos de una asignatura seleccionada
function mostrarContenidos(idAsignatura, nombre) {
  document.getElementById('asignaturas-view').classList.add('hidden');
  document.getElementById('contenidos-view').classList.remove('hidden');
  document.getElementById('detalle-view').classList.add('hidden');
  document.getElementById('titulo-asignatura').textContent = nombre;
  // Petición AJAX para obtener los contenidos
  fetch('asignaturas_cards.php?ajax=contenidos&id=' + idAsignatura)
    .then(res => res.json())
    .then(data => {
      let html = '';
      if (data.length === 0) {
        html = '<p class="text-gray-500 dark:text-gray-400">No hay contenidos para esta asignatura.</p>';
      } else {
        data.forEach(cont => {
          html += `<div class='max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col justify-between'>
            <a href='javascript:void(0)' onclick='mostrarDetalle(${cont.IDcontenido})'>
              <h5 class='mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white'>${cont.NomDocumento}</h5>
            </a>
            <p class='mb-3 font-normal text-gray-700 dark:text-gray-400'>${cont.Descripcion || ''}</p>
            <a href='javascript:void(0)' onclick='mostrarDetalle(${cont.IDcontenido})' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-4'>
              Ver detalle
              <svg class='rtl:rotate-180 w-3.5 h-3.5 ms-2' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 14 10'>
                  <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M1 5h12m0 0L9 1m4 4L9 9'/>
              </svg>
            </a>
          </div>`;
        });
      }
      document.getElementById('contenidos-cards').innerHTML = html;
    });
}

// Volver al listado de asignaturas
function volverAsignaturas() {
  document.getElementById('asignaturas-view').classList.remove('hidden');
  document.getElementById('contenidos-view').classList.add('hidden');
  document.getElementById('detalle-view').classList.add('hidden');
}

// Mostrar el detalle de un contenido
function mostrarDetalle(idContenido) {
  document.getElementById('asignaturas-view').classList.add('hidden');
  document.getElementById('contenidos-view').classList.add('hidden');
  document.getElementById('detalle-view').classList.remove('hidden');
  // Petición AJAX para obtener el detalle
  fetch('asignaturas_cards.php?ajax=detalle&id=' + idContenido)
    .then(res => res.json())
    .then(data => {
      let html = '';
      if (!data) {
        html = '<p class="text-red-600">No se pudo cargar el contenido.</p>';
      } else {
        html = `<div class='p-6 rounded-2xl shadow-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700'>
          <h4 class='text-xl font-bold text-teal-700 dark:text-teal-300 mb-2'>${data.NomDocumento}</h4>
          <p class='text-gray-700 dark:text-gray-300 mb-4'>${data.Descripcion || ''}</p>`;
        if (data.RutaDocumento && data.tipo === 'pdf') {
          html += `<iframe src='pdf.php?ruta=${encodeURIComponent(data.RutaDocumento)}' class='w-full h-96 rounded-lg border' frameborder='0'></iframe>`;
        } else if (data.RutaDocumento && data.tipo === 'img') {
          html += `<img src='${data.rutaFisica}' alt='${data.NomDocumento}' class='w-full max-h-96 object-contain rounded-lg border mb-2'>`;
        } else if (data.RutaDocumento) {
          html += `<a href='${data.rutaFisica}' target='_blank' class='inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200'>Descargar archivo</a>`;
        } else {
          html += `<span class='inline-block bg-red-100 text-red-700 font-semibold rounded-lg px-5 py-2 border border-red-400'>Archivo no disponible</span>`;
        }
        html += '</div>';
      }
      document.getElementById('detalle-card').innerHTML = html;
    });
}

// Volver al listado de contenidos de la asignatura
function volverContenidos() {
  document.getElementById('asignaturas-view').classList.add('hidden');
  document.getElementById('contenidos-view').classList.remove('hidden');
  document.getElementById('detalle-view').classList.add('hidden');
}

// Eliminar una asignatura (solo equipo directivo)
function eliminarAsignatura(id) {
  if (confirm('¿Seguro que quieres eliminar esta asignatura? Se eliminarán también sus contenidos.')) {
    const formData = new FormData();
    formData.append('id', id);
    fetch('borrar_asignatura.php', {
      method: 'POST',
      body: formData
    })
    .then(res => {
      if (res.redirected) {
        window.location.href = res.url;
      } else {
        window.location.reload();
      }
    });
  }
} 