<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();
include '../parts/db.php';
$pdo = (new DB())->connect();

// Procesar borrado
if (
    isset($_POST['borrar_id']) &&
    ($_SESSION['user']['cargo'] === 'profesor' || $_SESSION['user']['cargo'] === 'equipo_directivo')
) {
    $idContenido = intval($_POST['borrar_id']);
    // Obtener la ruta antes de borrar
    $stmt = $pdo->prepare("SELECT RutaDocumento FROM CONTENIDO WHERE IDcontenido = ?");
    $stmt->execute([$idContenido]);
    $rutaEncriptada = $stmt->fetchColumn();
    if ($rutaEncriptada) {
        $ruta = base64_decode($rutaEncriptada);
        // Borrar archivo físico si existe
        if ($ruta && file_exists($ruta)) {
            unlink($ruta);
        }
        // Borrar de la base de datos
        $stmt = $pdo->prepare("DELETE FROM CONTENIDO WHERE IDcontenido = ?");
        $stmt->execute([$idContenido]);
    }
    // Redirigir para evitar reenvío de formulario
    header("Location: asignaturas.php?id=" . urlencode($_GET['id'] ?? ''));
    exit;
}

// Obtener todas las asignaturas
if ($_SESSION['user']['cargo'] === 'alumno') {
    // Obtener el grupo del alumno
    $stmt = $pdo->prepare("SELECT IDgroup FROM grupo_alumno WHERE IDuser = ?");
    $stmt->execute([$_SESSION['user']['IDuser']]);
    $grupo = $stmt->fetchColumn();

    if ($grupo) {
        // Obtener solo las asignaturas de ese grupo
        $stmt = $pdo->prepare("SELECT a.idAsignatura, a.nombre 
                               FROM asignatura a
                               INNER JOIN grupo_asignatura ga ON a.idAsignatura = ga.idAsignatura
                               WHERE ga.IDgroup = ?");
        $stmt->execute([$grupo]);
        $asignaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $asignaturas = [];
    }
} else {
    // Profesores y equipo directivo ven todas las asignaturas
    $asignaturas = $pdo->query("SELECT idAsignatura, nombre FROM ASIGNATURA")->fetchAll(PDO::FETCH_ASSOC);
}

// Si se selecciona una asignatura, obtener sus contenidos
$contenidos = [];
$asignaturaSeleccionada = null;
if (isset($_GET['id'])) {
    $idAsignatura = intval($_GET['id']);
    $asignaturaSeleccionada = $pdo->prepare("SELECT nombre FROM ASIGNATURA WHERE idAsignatura = ?");
    $asignaturaSeleccionada->execute([$idAsignatura]);
    $asignaturaSeleccionada = $asignaturaSeleccionada->fetchColumn();
    $stmt = $pdo->prepare("SELECT IDcontenido, NomDocumento, RutaDocumento, Descripción FROM CONTENIDO WHERE IDAsignatura = ?");
    $stmt->execute([$idAsignatura]);
    $contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include '../parts/header.php'; ?>

<!-- Modal de confirmación -->
<div id="modal-borrar" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8 max-w-md w-full border border-gray-200 dark:border-gray-800">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">¿Seguro que quieres borrar este contenido?</h3>
    <form id="modal-form" method="POST">
      <input type="hidden" name="borrar_id" id="modal-borrar-id">
      <div class="flex justify-end gap-4">
        <button type="button" onclick="cerrarModal()" class="px-4 py-2 rounded-lg bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white font-semibold hover:bg-gray-400 dark:hover:bg-gray-600">Cancelar</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">Borrar</button>
      </div>
    </form>
  </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-8 px-4 pt-20">
  <div class="w-full max-w-[90%] mx-auto bg-gray-800/75 rounded-2xl shadow-2xl p-4 sm:p-14 border border-gray-200 dark:border-gray-800">
    <h2 class="text-3xl font-extrabold text-center text-gray-900 dark:text-white mb-8 tracking-tight">Asignaturas</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
      <?php foreach ($asignaturas as $asig): ?>
        <a href="?id=<?= $asig['idAsignatura'] ?>" class="block p-5 rounded-xl shadow-md bg-teal-100 dark:bg-gray-800 hover:bg-teal-200 dark:hover:bg-gray-700 transition text-lg font-semibold text-teal-900 dark:text-white border border-teal-300 dark:border-gray-700 <?php if(isset($_GET['id']) && $_GET['id'] == $asig['idAsignatura']) echo 'ring-2 ring-teal-500'; ?>">
          <?= htmlspecialchars($asig['nombre']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($asignaturaSeleccionada): ?>
      <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Contenidos de "<?= htmlspecialchars($asignaturaSeleccionada) ?>"</h3>
      <?php if (count($contenidos) > 0): ?>
        <ul class="space-y-4">
          <?php foreach ($contenidos as $cont): ?>
            <?php $ruta = base64_decode($cont['RutaDocumento']); ?>
            <li class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow">
              <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                  <span class="font-semibold text-teal-700 dark:text-teal-300 text-lg"><?= htmlspecialchars($cont['NomDocumento']) ?></span>
                  <?php if (!empty($cont['Descripción'])): ?>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1 mb-1"> <?= htmlspecialchars($cont['Descripción']) ?> </p>
                  <?php endif; ?>
                </div>
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                  <?php if ($ruta && file_exists($ruta)): ?>
                    <a href="pdf.php?ruta=<?= urlencode($cont['RutaDocumento']) ?>" target="_blank" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200">Ver documento</a>
                  <?php else: ?>
                    <span class="inline-block bg-red-100 text-red-700 font-semibold rounded-lg px-5 py-2 border border-red-400">Archivo no disponible</span>
                  <?php endif; ?>
                  <?php if (isset($_SESSION['user']['cargo']) &&
                      ($_SESSION['user']['cargo'] === 'profesor' || $_SESSION['user']['cargo'] === 'equipo_directivo')): ?>
                    <button type="button" onclick="abrirModalBorrar(<?= $cont['IDcontenido'] ?>)" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200">Borrar</button>
                  <?php endif; ?>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-gray-500 dark:text-gray-400">No hay contenidos para esta asignatura.</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<script>
function abrirModalBorrar(id) {
  document.getElementById('modal-borrar-id').value = id;
  document.getElementById('modal-borrar').classList.remove('hidden');
}
function cerrarModal() {
  document.getElementById('modal-borrar').classList.add('hidden');
}
</script> 