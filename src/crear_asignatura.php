<?php
session_start();
if (!isset($_SESSION['user']['cargo']) || $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: asignaturas_cards.php');
    exit;
}
include '../parts/db.php';
$pdo = (new DB())->connect();

// Si es POST, procesar el guardado (código existente)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    if (empty($_POST['nombre']) || empty($_POST['profesor']) || empty($_POST['grupo'])) {
        $errores[] = 'Faltan campos obligatorios.';
    }
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $profesor = intval($_POST['profesor'] ?? 0);
    $grupo = intval($_POST['grupo'] ?? 0);

    // Procesar imagen
    $imagen_path = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $permitidas)) {
            $dir = '../uploads/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $nombre_archivo = basename($_FILES['imagen']['name']);
            $destino = $dir . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen_path = base64_encode($destino);
            } else {
                $errores[] = 'Error al subir la imagen.';
            }
        } else {
            $errores[] = 'Formato de imagen no permitido.';
        }
    }

    // Si no se subió imagen, usar la imagen por defecto y codificarla en base64
    if (!$imagen_path) {
        $imagen_path = base64_encode('../uploads/asignatura.jpg');
    }

    if (count($errores) === 0) {
        // Insertar asignatura con profesor (IDTutor)
        $stmt = $pdo->prepare("INSERT INTO asignatura (nombre, IDTutor, imagen, descripcion) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $profesor, $imagen_path, $descripcion]);
        $idAsignatura = $pdo->lastInsertId();
        // Asociar grupo con asignatura (sin profesor)
        $stmt2 = $pdo->prepare("INSERT INTO grupo_asignatura (IDgroup, idAsignatura) VALUES (?, ?)");
        $stmt2->execute([$grupo, $idAsignatura]);
        header('Location: asignaturas_cards.php?exito=1');
        exit;
    } else {
        $msg = implode(' ', $errores);
    }
} else {
    $msg = '';
}
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-8 px-2 sm:px-6 flex items-center justify-center relative pt-20">
  <a href="asignaturas_cards.php" class="absolute left-8 top-8 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg px-4 py-2 shadow-md transition-all duration-200 z-10">Volver a asignaturas</a>
  <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 w-full max-w-lg relative mt-10">
    <h3 class="text-2xl font-bold mb-8 text-center text-teal-700 dark:text-teal-300">Crear nueva asignatura</h3>
    <?php if (!empty($msg)): ?>
      <div class="mb-4 text-red-600 text-center font-semibold"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold text-white">Nombre</label>
        <input type="text" name="nombre" required class="w-full rounded border px-3 py-2 text-white">
      </div>
      <div>
        <label class="block mb-1 font-semibold text-white">Imagen</label>
        <input type="file" name="imagen" accept="image/*" class="w-full text-white">
      </div>
      <div>
        <label class="block mb-1 font-semibold text-white">Descripción</label>
        <textarea name="descripcion" rows="3" class="w-full rounded border px-3 py-2 text-white"></textarea>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-white">Profesor</label>
        <select name="profesor" required class="w-full rounded border px-3 py-2 text-white">
          <?php
          $profesores = $pdo->query("SELECT u.IDuser, u.username FROM usuario u INNER JOIN profesor p ON u.IDuser = p.IDuser")->fetchAll(PDO::FETCH_ASSOC);
          foreach ($profesores as $prof) {
            echo '<option value="' . $prof['IDuser'] . '">' . htmlspecialchars($prof['username']) . '</option>';
          }
          ?>
        </select>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-white">Grupo</label>
        <select name="grupo" required class="w-full rounded border px-3 py-2 text-white">
          <?php
          $grupos = $pdo->query("SELECT IDgroup, nombre FROM grupo")->fetchAll(PDO::FETCH_ASSOC);
          foreach ($grupos as $grupo) {
            echo '<option value="' . $grupo['IDgroup'] . '">' . htmlspecialchars($grupo['nombre']) . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200">Crear</button>
      </div>
    </form>
  </div>
</div> 