<?php
session_start();
if (!isset($_SESSION['user']['cargo']) || $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: gestion_grupos.php?msg=No tienes permisos&type=error');
    exit;
}
include '../parts/db.php';
$pdo = (new DB())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $tutor = intval($_POST['tutor'] ?? 0);
    if ($id && $nombre && $tutor) {
        $stmt = $pdo->prepare("UPDATE grupo SET nombre = ?, IDTutor = ? WHERE IDgroup = ?");
        $stmt->execute([$nombre, $tutor, $id]);
        header('Location: gestion_grupos.php?msg=Grupo actualizado correctamente&type=ok');
        exit;
    } else {
        $msg = 'Faltan datos';
    }
} else {
    $msg = '';
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        header('Location: gestion_grupos.php?msg=Grupo no encontrado&type=error');
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM grupo WHERE IDgroup = ?");
    $stmt->execute([$id]);
    $grupo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$grupo) {
        header('Location: gestion_grupos.php?msg=Grupo no encontrado&type=error');
        exit;
    }
}
$profesores = $pdo->query("SELECT u.IDuser, u.username FROM usuario u INNER JOIN profesor p ON u.IDuser = p.IDuser")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 flex items-center justify-center">
  <form action="" method="post" class="w-full max-w-[90%] mx-auto bg-gray-800/75 rounded-2xl shadow-2xl p-4 sm:p-14 border border-gray-200 dark:border-gray-800 flex flex-col gap-6 min-w-[320px] max-w-lg w-full relative">
    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-2 tracking-tight">Editar grupo</h2>
    <?php if (!empty($msg)): ?>
      <div class="border border-red-400 bg-red-100 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
        <span class="font-bold">Error:</span>
        <span><?= htmlspecialchars($msg) ?></span>
      </div>
    <?php endif; ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <label class="font-semibold text-gray-700 dark:text-white">Nombre</label>
    <input type="text" name="nombre" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" value="<?= htmlspecialchars($grupo['nombre'] ?? ($_POST['nombre'] ?? '')) ?>">
    <label class="font-semibold text-gray-700 dark:text-white">Tutor</label>
    <select name="tutor" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition">
      <option value="">Selecciona un tutor</option>
      <?php foreach ($profesores as $prof): ?>
        <option value="<?= $prof['IDuser'] ?>" <?= (isset($grupo['IDTutor']) && $grupo['IDTutor'] == $prof['IDuser']) || (isset($_POST['tutor']) && $_POST['tutor'] == $prof['IDuser']) ? 'selected' : '' ?>><?= htmlspecialchars($prof['username']) ?></option>
      <?php endforeach; ?>
    </select>
    <div class="flex gap-4 mt-4">
      <a href="gestion_grupos.php" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg p-3 text-center transition">Cancelar</a>
      <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-3 transition">Guardar cambios</button>
    </div>
  </form>
</div> 