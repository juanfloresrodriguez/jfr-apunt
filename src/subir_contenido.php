<head>
    <link rel="stylesheet" href="../src/output.css">
</head>
<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();

if ($_SESSION['user']['cargo'] !== 'profesor' && $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    echo "<div class='text-center mt-10 text-red-600 font-bold text-xl'>No tienes permisos para subir contenido.</div>";
    exit;
}

include '../parts/db.php';

$pdo = (new DB())->connect();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $idAsignatura = $_POST['idAsignatura'] ?? '';
    $archivo = $_FILES['archivo'] ?? null;

    if ($nombre && $idAsignatura && $archivo && $archivo['error'] === 0) {
        $rutaDestino = '../uploads/' . basename($archivo['name']);
        $rutaEncriptada = base64_encode($rutaDestino);
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $stmt = $pdo->prepare("INSERT INTO CONTENIDO (IDAsignatura, NomDocumento, RutaDocumento, Descripción) VALUES (?, ?, ?, ?)");
            $stmt->execute([$idAsignatura, $nombre, $rutaEncriptada, $descripcion]);
            $mensaje = "¡Contenido subido correctamente!";
        } else {
            $mensaje = "Error al mover el archivo.";
        }
    } else {
        $mensaje = "Faltan datos o hubo un error con el archivo.";
    }
}

// Obtener asignaturas para el select
$asignaturas = $pdo->query("SELECT idAsignatura, nombre FROM ASIGNATURA")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../parts/header.php'; ?>
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-8 pt-20">
  <div class="bg-white/90 dark:bg-gray-900/80 p-8 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-800">
    <h2 class="text-3xl font-extrabold text-center text-gray-900 dark:text-white mb-6 tracking-tight">Subir contenido</h2>
    <?php if ($mensaje): ?>
      <div class="mb-4 p-3 rounded-lg <?php echo ($mensaje === '¡Contenido subido correctamente!') ? 'bg-green-100 text-green-800 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
      <div>
        <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1" for="nombre">Nombre del documento</label>
        <input type="text" name="nombre" id="nombre" required class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition placeholder-gray-400 dark:placeholder-gray-500">
      </div>
      <div>
        <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1" for="descripcion">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="3" class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition placeholder-gray-400 dark:placeholder-gray-500"></textarea>
      </div>
      <div>
        <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1" for="idAsignatura">Asignatura</label>
        <select name="idAsignatura" id="idAsignatura" required class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
          <option value="">Selecciona una</option>
          <?php foreach ($asignaturas as $asig): ?>
            <option value="<?= $asig['idAsignatura'] ?>"><?= htmlspecialchars($asig['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1" for="archivo">Archivo</label>
        <input type="file" name="archivo" id="archivo" required class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
      </div>
      <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-3 shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400">Subir</button>
    </form>
  </div>
</div> 
