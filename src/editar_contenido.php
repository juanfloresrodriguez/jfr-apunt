<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();
include '../parts/db.php';
$pdo = (new DB())->connect();

if (!isset($_SESSION['user']['cargo']) || !in_array($_SESSION['user']['cargo'], ['profesor', 'equipo_directivo'])) {
    header('Location: principal.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: principal.php');
    exit();
}

// Obtener datos actuales del contenido
$stmt = $pdo->prepare('SELECT * FROM CONTENIDO WHERE IDcontenido = ?');
$stmt->execute([$id]);
$cont = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cont) {
    header('Location: principal.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $ruta = $cont['RutaDocumento'];
    if (isset($_FILES['archivo']) && $_FILES['archivo']['tmp_name']) {
        $fileData = file_get_contents($_FILES['archivo']['tmp_name']);
        $fileName = $_FILES['archivo']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uploadDir = '../uploads/';
        $newFileName = uniqid('cont_') . '.' . $ext;
        $filePath = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $filePath)) {
            $ruta = base64_encode($filePath);
        } else {
            $error = 'Error al subir el archivo.';
        }
    }
    if ($nombre && !$error) {
        $stmt = $pdo->prepare('UPDATE CONTENIDO SET NomDocumento = ?, Descripción = ?, RutaDocumento = ? WHERE IDcontenido = ?');
        $stmt->execute([$nombre, $descripcion, $ruta, $id]);
        header('Location: detalle_contenido.php?id=' . $id);
        exit();
    } else if (!$error) {
        $error = 'El nombre es obligatorio.';
    }
}

include '../parts/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar contenido</title>
    <link rel="stylesheet" href="./output.css">
</head>
<body class="bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 min-h-screen flex items-center justify-center pt-20">
    <form method="POST" enctype="multipart/form-data" class="bg-white/90 dark:bg-gray-900/80 p-8 rounded-2xl shadow-2xl flex flex-col gap-6 min-w-[320px] max-w-md w-full border border-gray-200 dark:border-gray-800">
        <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-2 tracking-tight">Editar contenido</h2>
        <?php if ($error): ?>
            <div class="border border-red-400 bg-red-100 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
                <span class="font-bold">Error:</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        <label class="font-semibold text-gray-700 dark:text-white">Nombre</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($cont['NomDocumento']) ?>" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
        <label class="font-semibold text-gray-700 dark:text-white">Descripción</label>
        <textarea name="descripcion" rows="3" class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition"><?= htmlspecialchars($cont['Descripción']) ?></textarea>
        <label class="font-semibold text-gray-700 dark:text-white">Archivo (opcional, reemplaza el actual)</label>
        <input type="file" name="archivo" class="mb-2">
        <?php if ($cont['RutaDocumento']): ?>
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Archivo actual: <?= htmlspecialchars(basename(base64_decode($cont['RutaDocumento']))) ?></div>
        <?php endif; ?>
        <div class="flex gap-4 mt-4">
            <a href="detalle_contenido.php?id=<?= $id ?>" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg p-3 text-center transition">Cancelar</a>
            <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-3 transition">Guardar cambios</button>
        </div>
    </form>
</body>
</html> 