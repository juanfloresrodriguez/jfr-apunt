<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();
include '../parts/db.php';
include '../parts/header.php';
$pdo = (new DB())->connect();


$idAsignatura = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$idAsignatura) {
    echo '<div class="text-center text-red-600 mt-10">ID de asignatura no válido.</div>';
    exit;
}

// Obtener nombre de la asignatura
$stmt = $pdo->prepare("SELECT nombre FROM asignatura WHERE idAsignatura = ?");
$stmt->execute([$idAsignatura]);
$asig = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$asig) {
    echo '<div class="text-center text-red-600 mt-10">Asignatura no encontrada.</div>';
    exit;
}

// Obtener contenidos
$stmt = $pdo->prepare("SELECT IDcontenido, NomDocumento, Descripción as Descripcion FROM CONTENIDO WHERE IDAsignatura = ?");
$stmt->execute([$idAsignatura]);
$contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-8 px-2 sm:px-10 pt-20">
  <div class="max-w-full mx-auto bg-white/90 dark:bg-gray-900/80 rounded-2xl shadow-2xl p-6 sm:p-14 border border-gray-200 dark:border-gray-800">
    <a href="asignaturas_cards.php" class="inline-block mb-6 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-lg px-4 py-2 shadow transition">Volver a asignaturas</a>
    <h2 class="text-3xl font-bold text-center text-teal-700 dark:text-teal-300 mb-10"><?= htmlspecialchars($asig['nombre']) ?></h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-12 my-10" id="lista-contenido">
      <?php if (empty($contenidos)): ?>
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400">No hay contenidos para esta asignatura.</div>
      <?php else: ?>
        <?php foreach ($contenidos as $cont): ?>
          <div class="bg-white rounded-2xl shadow-xl border border-gray-200 dark:bg-gray-800 dark:border-gray-700 flex flex-col p-6 min-h-[320px] min-w-[220px] 2xl:min-w-[240px] 2xl:max-w-sm">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 text-center"><?= htmlspecialchars($cont['NomDocumento']) ?></h3>
            <p class="text-gray-700 dark:text-gray-300 mb-4 text-center"><?= !empty($cont['Descripcion']) ? htmlspecialchars($cont['Descripcion']) : 'Sin descripción' ?></p>
            <a href="detalle_contenido.php?id=<?= $cont['IDcontenido'] ?>" class="mt-auto inline-block bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg px-4 py-2 shadow transition text-center">Ver detalle</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div id="paginacion-contenido" class="flex justify-center mt-4"></div>
    <script src="./paginacion_contenidos.js"></script>
  </div>
</div> 