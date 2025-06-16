<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();
include '../parts/db.php';
$pdo = (new DB())->connect();

// Obtener asignaturas según el tipo de usuario
if ($_SESSION['user']['cargo'] === 'alumno') {
    $stmt = $pdo->prepare("SELECT IDgroup FROM grupo_alumno WHERE IDuser = ?");
    $stmt->execute([$_SESSION['user']['IDuser']]);
    $grupo = $stmt->fetchColumn();
    if ($grupo) {
        $stmt = $pdo->prepare("SELECT a.idAsignatura, a.nombre, a.imagen, a.descripcion FROM asignatura a INNER JOIN grupo_asignatura ga ON a.idAsignatura = ga.idAsignatura WHERE ga.IDgroup = ?");
        $stmt->execute([$grupo]);
        $asignaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $asignaturas = [];
    }
} else if (
    $_SESSION['user']['cargo'] === 'profesor' ||
    $_SESSION['user']['cargo'] === 'profesor_alumno' ||
    $_SESSION['user']['cargo'] === 'equipo_directivo_profesor'
) {
    // Obtener grupos del profesor
    $stmt = $pdo->prepare("SELECT IDgroup FROM grupo_profesor WHERE IDuser = ?");
    $stmt->execute([$_SESSION['user']['IDuser']]);
    $grupos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Asignaturas donde es tutor
    $stmt = $pdo->prepare("SELECT idAsignatura, nombre, imagen, descripcion FROM asignatura WHERE IDTutor = ?");
    $stmt->execute([$_SESSION['user']['IDuser']]);
    $asig_tutor = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Asignaturas de sus grupos
    $asig_grupo = [];
    if ($grupos) {
        $in = str_repeat('?,', count($grupos) - 1) . '?';
        $stmt = $pdo->prepare("SELECT a.idAsignatura, a.nombre, a.imagen, a.descripcion
                               FROM asignatura a
                               INNER JOIN grupo_asignatura ga ON a.idAsignatura = ga.idAsignatura
                               WHERE ga.IDgroup IN ($in)");
        $stmt->execute($grupos);
        $asig_grupo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Unir y eliminar duplicados por idAsignatura
    $asignaturas = array_merge($asig_tutor, $asig_grupo);
    $asignaturas = array_unique($asignaturas, SORT_REGULAR);
} else {
    $asignaturas = $pdo->query("SELECT idAsignatura, nombre, imagen, descripcion FROM ASIGNATURA")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-8 px-2 pt-20 sm:px-10">
<div class="w-full max-w-[90%] mx-auto bg-gray-800/75 rounded-2xl shadow-2xl p-4 sm:p-14 border border-gray-200 dark:border-gray-800">
  <!-- <div class="max-w-full mx-auto bg-white/50 rounded-2xl shadow-2xl p-6 sm:p-14 border border-gray-200 dark:border-gray-800"> -->
    <h2 class="text-white text-4xl text-center my-10">Asignaturas</h2>
    <?php if (isset($_SESSION['user']['cargo']) && $_SESSION['user']['cargo'] === 'equipo_directivo'): ?>
      <div class="flex justify-start mb-6">
        <a href="crear_asignatura.php" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg ms-2 px-5 py-2 shadow-md transition-all duration-200">Nueva Asignatura</a>
      </div>
    <?php endif; ?>
    <div id="asignaturas-view">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-12 my-10" id="lista-contenido">
        <?php foreach ($asignaturas as $asig): ?>
          <div class="relative group p-2">
            <a href="contenidos.php?id=<?= $asig['idAsignatura'] ?>"
              class="bg-white rounded-2xl shadow-xl border border-gray-200 hover:shadow-2xl transition duration-200 flex flex-col items-center p-0 w-full max-w-xs mx-auto min-h-[420px] min-w-[220px] 2xl:min-w-[240px] 2xl:max-w-sm">
              <img src="<?= !empty($asig['imagen']) ? base64_decode(htmlspecialchars($asig['imagen'])) : '../uploads/asignatura.jpg' ?>"
                class="rounded-t-2xl w-full h-56 object-cover" alt="<?= htmlspecialchars($asig['nombre']) ?>">
              <div class="flex flex-col items-center justify-center flex-1 px-6 py-6">
                <h5 class="text-2xl font-bold text-gray-900 mb-2 text-center"><?= htmlspecialchars($asig['nombre']) ?></h5>
                <p class="text-center text-gray-700 mb-2 text-base"><?= !empty($asig['descripcion']) ? htmlspecialchars($asig['descripcion']) : 'Sin descripción' ?></p>
              </div>
            </a>
            <?php if (isset($_SESSION['user']['cargo']) && $_SESSION['user']['cargo'] === 'equipo_directivo'): ?>
              <div class="flex gap-2 w-full mt-2 mb-2">
                <a href="editar_asignatura.php?id=<?= $asig['idAsignatura'] ?>" class="w-1/2 bg-teal-600 hover:bg-teal-700 text-gray-900 font-semibold rounded-lg px-0 py-2 shadow-md transition-all duration-200 flex items-center justify-center" title="Editar asignatura">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487c-.513-.513-1.348-.513-1.86 0l-1.05 1.05 3.72 3.72 1.05-1.05c.513-.513.513-1.348 0-1.86l-1.86-1.86zM15.012 7.357l-8.1 8.1a2.25 2.25 0 00-.573.958l-.684 2.053a.375.375 0 00.47.47l2.053-.684c.36-.12.693-.32.958-.573l8.1-8.1-3.72-3.72z" />
                  </svg>
                </a>
                <button type="button" onclick="eliminarAsignatura(<?= $asig['idAsignatura'] ?>)" class="w-1/2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg px-0 py-2 shadow-md transition-all duration-200 flex items-center justify-center" title="Eliminar asignatura">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <div id="paginacion-contenido" class="flex justify-center mt-4"></div>
    </div>
    <div id="contenidos-view" class="hidden">
      <button onclick="volverAsignaturas()" class="mb-6 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-lg px-4 py-2 shadow transition">Volver a asignaturas</button>
      <h3 id="titulo-asignatura" class="text-2xl font-bold text-teal-700 dark:text-teal-300 mb-4"></h3>
      <div id="contenidos-cards" class="grid grid-cols-1 sm:grid-cols-2 gap-6"></div>
    </div>
    <div id="detalle-view" class="hidden">
      <button onclick="volverContenidos()" class="mb-6 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-lg px-4 py-2 shadow transition">Volver a contenidos</button>
      <div id="detalle-card"></div>
    </div>
  </div>
</div>
<?php include '../parts/footer.php'; ?>
<!-- Enlace al JS externo de paginación y lógica de asignaturas -->
<script src="./asignaturas_cards.js"></script>
<script src="./paginacion_contenidos.js"></script>
</body>
</html>
<?php
// AJAX para obtener contenidos de una asignatura
if (isset($_GET['ajax']) && $_GET['ajax'] === 'contenidos' && isset($_GET['id'])) {
    $idAsignatura = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT IDcontenido, NomDocumento, Descripción as Descripcion FROM CONTENIDO WHERE IDAsignatura = ?");
    $stmt->execute([$idAsignatura]);
    $contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($contenidos);
    exit;
}
// AJAX para obtener detalle de un contenido
if (isset($_GET['ajax']) && $_GET['ajax'] === 'detalle' && isset($_GET['id'])) {
    $idContenido = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT NomDocumento, RutaDocumento, Descripción as Descripcion FROM CONTENIDO WHERE IDcontenido = ?");
    $stmt->execute([$idContenido]);
    $cont = $stmt->fetch(PDO::FETCH_ASSOC);
    $result = null;
    if ($cont) {
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
        $result = [
            'NomDocumento' => $cont['NomDocumento'],
            'Descripcion' => $cont['Descripcion'],
            'RutaDocumento' => $cont['RutaDocumento'],
            'tipo' => $tipo,
            'rutaFisica' => $rutaFisica
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?> 