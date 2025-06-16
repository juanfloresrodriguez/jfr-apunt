<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();
include '../parts/db.php';
$pdo = (new DB())->connect();

$idContenido = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$idContenido) {
    echo '<div class="text-center text-red-600 mt-10">ID de contenido no válido.</div>';
    exit;
}

// Obtener datos del contenido y la asignatura
$stmt = $pdo->prepare("SELECT c.NomDocumento, c.RutaDocumento, c.Descripción as Descripcion, c.IDAsignatura, a.nombre as nombreAsignatura FROM CONTENIDO c JOIN asignatura a ON c.IDAsignatura = a.idAsignatura WHERE c.IDcontenido = ?");
$stmt->execute([$idContenido]);
$cont = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cont) {
    echo '<div class="text-center text-red-600 mt-10">Contenido no encontrado.</div>';
    exit;
}

// Procesar ruta y tipo
$ruta = base64_decode($cont['RutaDocumento']);
$tipo = '';
$rutaFisica = $ruta;
if ($ruta && file_exists($ruta)) {
    $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    if ($ext === 'pdf') $tipo = 'pdf';
    elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])) $tipo = 'img';
    else $tipo = 'file';
} else {
    $rutaFisica = '';
}
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 flex flex-col">
  <div class="flex-1 flex flex-col w-full h-full items-center justify-start pt-8">
    <div class="max-w-[90%] w-full mx-auto bg-white/90 dark:bg-gray-900/80 rounded-2xl shadow-2xl p-4 sm:p-8 border border-gray-200 dark:border-gray-800 flex flex-col items-center">
      <div class="w-full flex items-center justify-between mb-2">
        <a href="contenidos.php?id=<?= $cont['IDAsignatura'] ?>" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-lg px-4 py-2 shadow transition">Volver a contenidos</a>
        <?php if (isset($_SESSION['user']['cargo']) && in_array($_SESSION['user']['cargo'], ['profesor', 'equipo_directivo'])): ?>
          <div class="flex gap-2">
            <a href="editar_contenido.php?id=<?= $idContenido ?>" class="bg-teal-500 hover:bg-teal-600 text-gray-900 rounded-lg px-2 py-2 shadow-md transition flex items-center justify-center" title="Editar contenido">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487c-.513-.513-1.348-.513-1.86 0l-1.05 1.05 3.72 3.72 1.05-1.05c.513-.513.513-1.348 0-1.86l-1.86-1.86zM15.012 7.357l-8.1 8.1a2.25 2.25 0 00-.573.958l-.684 2.053a.375.375 0 00.47.47l2.053-.684c.36-.12.693-.32.958-.573l8.1-8.1-3.72-3.72z" />
              </svg>
            </a>
            <form method="POST" action="borrar_contenido.php" onsubmit="return confirm('¿Seguro que quieres eliminar este contenido?');">
              <input type="hidden" name="id" value="<?= $idContenido ?>">
              <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-lg px-2 py-2 shadow-md transition flex items-center justify-center" title="Eliminar contenido">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </form>
          </div>
        <?php endif; ?>
      </div>
      <h2 class="text-3xl font-bold text-center text-teal-700 dark:text-teal-300 m-0"><?= htmlspecialchars($cont['NomDocumento']) ?></h2>
      <h3 class="text-lg text-center text-gray-700 dark:text-gray-300 mb-2">Asignatura: <?= htmlspecialchars($cont['nombreAsignatura']) ?></h3>
      <p class="text-gray-700 dark:text-gray-300 mb-4 text-center"><?= !empty($cont['Descripcion']) ? htmlspecialchars($cont['Descripcion']) : 'Sin descripción' ?></p>
      <div class="w-full flex-1 flex justify-center items-center mt-2">
        <?php if ($rutaFisica && $tipo === 'pdf'): ?>
          <iframe src="pdf.php?ruta=<?= urlencode($cont['RutaDocumento']) ?>" class="w-full h-[75vh] rounded-lg border border-gray-200 dark:border-gray-700 bg-white" frameborder="0"></iframe>
        <?php elseif ($rutaFisica && $tipo === 'img'): ?>
          <img src="<?= htmlspecialchars($rutaFisica) ?>" alt="<?= htmlspecialchars($cont['NomDocumento']) ?>" class="w-auto h-[80vh] object-contain rounded-lg border border-gray-200 dark:border-gray-700 mb-2">
        <?php elseif ($rutaFisica): ?>
          <a href="<?= htmlspecialchars($rutaFisica) ?>" target="_blank" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200">Descargar archivo</a>
        <?php else: ?>
          <span class="inline-block bg-red-100 text-red-700 font-semibold rounded-lg px-5 py-2 border border-red-400">Archivo no disponible</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div> 