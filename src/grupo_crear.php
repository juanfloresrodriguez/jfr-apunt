<?php
session_start();
if (!isset($_SESSION['user']['cargo']) || $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: gestion_grupos.php?msg=No tienes permisos&type=error');
    exit;
}
include '../parts/db.php';
$pdo = (new DB())->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $tutor = intval($_POST['tutor'] ?? 0);
    if ($nombre && $tutor) {
        $stmt = $pdo->prepare("INSERT INTO grupo (nombre, IDTutor) VALUES (?, ?)");
        $stmt->execute([$nombre, $tutor]);
        header('Location: gestion_grupos.php?msg=Grupo creado correctamente&type=ok');
        exit;
    } else {
        $msg = 'Faltan datos';
    }
} else {
    $msg = '';
}
$profesores = $pdo->query("SELECT u.IDuser, u.username FROM usuario u INNER JOIN profesor p ON u.IDuser = p.IDuser")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-8 px-2 sm:px-6 flex items-center justify-center pt-20">
  <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 w-full max-w-md relative mt-10">
    <a href="gestion_grupos.php" class="absolute left-4 top-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg px-4 py-2 shadow-md transition-all duration-200">Volver</a>
    <h3 class="text-xl font-bold mb-4 text-center text-teal-700 dark:text-teal-300">Crear grupo</h3>
    <?php if (!empty($msg)): ?>
      <div class="mb-4 text-red-600 text-center font-semibold"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form action="" method="post" class="space-y-4 mt-8">
      <div>
        <label class="block mb-1 font-semibold text-white">Nombre</label>
        <input type="text" name="nombre" required class="w-full rounded border px-3 py-2 text-white" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
      </div>
      <div>
        <label class="block mb-1 font-semibold text-white">Tutor</label>
        <select name="tutor" required class="w-full rounded border px-3 py-2 text-white">
          <option value="">Selecciona un tutor</option>
          <?php foreach ($profesores as $prof): ?>
            <option value="<?= $prof['IDuser'] ?>" <?= (isset($_POST['tutor']) && $_POST['tutor'] == $prof['IDuser']) ? 'selected' : '' ?>><?= htmlspecialchars($prof['username']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200">Crear</button>
      </div>
    </form>
  </div>
</div> 