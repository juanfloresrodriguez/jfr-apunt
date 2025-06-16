<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();
include '../parts/db.php';
$pdo = (new DB())->connect();

if (!isset($_SESSION['user']['cargo']) || $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: asignaturas_cards.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: asignaturas_cards.php');
    exit();
}

// Obtener datos actuales
$stmt = $pdo->prepare('SELECT * FROM asignatura WHERE idAsignatura = ?');
$stmt->execute([$id]);
$asig = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$asig) {
    header('Location: asignaturas_cards.php');
    exit();
}

// Obtener grupos
$grupos = $pdo->query("SELECT IDgroup, nombre FROM grupo")->fetchAll(PDO::FETCH_ASSOC);
// Obtener grupo actual de la asignatura
$stmt = $pdo->prepare("SELECT IDgroup FROM grupo_asignatura WHERE idAsignatura = ?");
$stmt->execute([$id]);
$grupo_actual = $stmt->fetchColumn();

// Obtener profesores
$profesores = $pdo->query("SELECT u.IDuser, u.username FROM usuario u INNER JOIN profesor p ON u.IDuser = p.IDuser")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = $asig['imagen'];
    $grupo = $_POST['grupo'] ?? '';
    $profesor = $_POST['profesor'] ?? '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['tmp_name']) {
        $imgData = file_get_contents($_FILES['imagen']['tmp_name']);
        $imagen = base64_encode($imgData);
    }
    if ($nombre && $grupo && $profesor) {
        $stmt = $pdo->prepare('UPDATE asignatura SET nombre = ?, descripcion = ?, imagen = ?, IDTutor = ? WHERE idAsignatura = ?');
        $stmt->execute([$nombre, $descripcion, $imagen, $profesor, $id]);
        // Actualizar grupo_asignatura
        $pdo->prepare('DELETE FROM grupo_asignatura WHERE idAsignatura = ?')->execute([$id]);
        $pdo->prepare('INSERT INTO grupo_asignatura (IDgroup, idAsignatura) VALUES (?, ?)')->execute([$grupo, $id]);
        header('Location: asignaturas_cards.php');
        exit();
    } else {
        $error = 'El nombre, el grupo y el profesor son obligatorios.';
    }
}
?>

<?php include '../parts/header.php'; ?>
<body class="bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 min-h-screen flex items-center justify-center pt-1">
    <div class="w-full flex items-center justify-center pt-24">
        <form method="POST" enctype="multipart/form-data" class="w-full max-w-[90%] mx-auto bg-gray-800/75 rounded-2xl shadow-2xl p-4 sm:p-14 border border-gray-200 dark:border-gray-800 flex flex-col gap-6 min-w-[320px] max-w-md w-full">
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-2 tracking-tight">Editar asignatura</h2>
            <?php if ($error): ?>
                <div class="border border-red-400 bg-red-100 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
                    <span class="font-bold">Error:</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            <label class="font-semibold text-gray-700 dark:text-white">Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($asig['nombre']) ?>" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
            <label class="font-semibold text-gray-700 dark:text-white">Descripción</label>
            <textarea name="descripcion" rows="3" class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition"><?= htmlspecialchars($asig['descripcion']) ?></textarea>
            <label class="font-semibold text-gray-700 dark:text-white">Imagen (opcional)</label>
            <input type="file" name="imagen" accept="image/*" class="mb-2">
            <!-- <?php if ($asig['imagen']): ?>
                <img src="<?= base64_decode($asig['imagen']) ?>" alt="Imagen actual" class="w-32 h-32 object-cover rounded-lg mx-auto mb-2">
            <?php endif; ?> -->
            <label class="font-semibold text-gray-700 dark:text-white">Grupo</label>
            <select name="grupo" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
                <option value="">Selecciona un grupo</option>
                <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['IDgroup'] ?>" <?= ($g['IDgroup'] == $grupo_actual) ? 'selected' : '' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <label class="font-semibold text-gray-700 dark:text-white">Profesor</label>
            <select name="profesor" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
                <option value="">Selecciona un profesor</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['IDuser'] ?>" <?= ($p['IDuser'] == $asig['IDTutor']) ? 'selected' : '' ?>><?= htmlspecialchars($p['username']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-4 mt-4">
                <a href="asignaturas_cards.php" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg p-3 text-center transition">Cancelar</a>
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-3 transition">Guardar cambios</button>
            </div>
        </form>
    </div>
</body>
</html> 