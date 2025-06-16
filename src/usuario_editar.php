<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();

include '../parts/db.php';
$pdo = (new DB())->connect();

$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null;
// Permitir solo si es el propio usuario o equipo directivo
if (!$id || (!isset($_SESSION['user']['IDuser']) || ($_SESSION['user']['IDuser'] != $id && $_SESSION['user']['cargo'] !== 'equipo_directivo'))) {
    header('Location: ../src/principal.php');
    exit;
}

// Obtener grupos para el select
$grupos = $pdo->query("SELECT IDgroup, nombre FROM GRUPO")->fetchAll(PDO::FETCH_ASSOC);

// Cargar datos actuales
$stmt = $pdo->prepare("SELECT u.username, u.email, g.IDgroup FROM USUARIO u
    LEFT JOIN GRUPO_ALUMNO ga ON u.IDuser = ga.IDuser
    LEFT JOIN GRUPO_PROFESOR gp ON u.IDuser = gp.IDuser
    LEFT JOIN GRUPO g ON (ga.IDgroup = g.IDgroup OR gp.IDgroup = g.IDgroup)
    WHERE u.IDuser = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$usuario) {
    header('Location: gestion_usuarios.php?msg=Usuario no encontrado&type=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tipoNuevo = $_POST['tipo'] ?? '';
    $grupo = $_POST['grupo'] ?? '';

    // Solo el equipo directivo puede cambiar tipo y grupo
    if ($_SESSION['user']['cargo'] !== 'equipo_directivo') {
        $tipoNuevo = $tipo;
        $grupo = $usuario['IDgroup'] ?? '';
    }

    if ($username && $email && $tipoNuevo) {
        $stmt = $pdo->prepare("UPDATE USUARIO SET username = ?, email = ? WHERE IDuser = ?");
        $stmt->execute([$username, $email, $id]);
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE USUARIO SET password = ? WHERE IDuser = ?")->execute([$hash, $id]);
        }
        // Actualizar tipo y grupo SOLO si cambia el tipo
        if ($tipoNuevo !== $tipo) {
            if ($tipo === 'alumno') $pdo->prepare("DELETE FROM ALUMNO WHERE IDuser = ?")->execute([$id]);
            if ($tipo === 'profesor') $pdo->prepare("DELETE FROM PROFESOR WHERE IDuser = ?")->execute([$id]);
            if ($tipo === 'equipo_directivo') $pdo->prepare("DELETE FROM EQUIPO_DIRECTIVO WHERE IDuser = ?")->execute([$id]);
            if ($tipoNuevo === 'alumno') $pdo->prepare("INSERT INTO ALUMNO (IDuser) VALUES (?)")->execute([$id]);
            if ($tipoNuevo === 'profesor') $pdo->prepare("INSERT INTO PROFESOR (IDuser) VALUES (?)")->execute([$id]);
            if ($tipoNuevo === 'equipo_directivo') $pdo->prepare("INSERT INTO EQUIPO_DIRECTIVO (IDuser) VALUES (?)")->execute([$id]);
        }
        // Actualizar grupo
        if ($tipoNuevo === 'alumno') {
            $pdo->prepare("DELETE FROM GRUPO_ALUMNO WHERE IDuser = ?")->execute([$id]);
            if ($grupo) $pdo->prepare("INSERT INTO GRUPO_ALUMNO (IDgroup, IDuser) VALUES (?, ?)")->execute([$grupo, $id]);
        } elseif ($tipoNuevo === 'profesor') {
            $pdo->prepare("DELETE FROM GRUPO_PROFESOR WHERE IDuser = ?")->execute([$id]);
            if ($grupo) $pdo->prepare("INSERT INTO GRUPO_PROFESOR (IDgroup, IDuser) VALUES (?, ?)")->execute([$grupo, $id]);
        }
        if ($_SESSION['user']['cargo'] === 'equipo_directivo') {
            header('Location: gestion_usuarios.php?msg=Usuario editado correctamente&type=ok');
            exit;
        } else {
            $msg = 'Tus datos han sido actualizados correctamente.';
            $type = 'ok';
        }
    } else {
        $msg = 'No se han realizado cambios.';
        $type = 'error';
    }
}
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 flex items-center justify-center pt-16">
  <form method="POST" class="w-full max-w-[90%] mx-auto bg-gray-800/75 rounded-2xl shadow-2xl p-4 sm:p-14 border border-gray-200 dark:border-gray-800 flex flex-col gap-6 min-w-[320px] max-w-lg w-full relative">
    <?php if ($_SESSION['user']['cargo'] === 'equipo_directivo'): ?>
      <!-- <a href="gestion_usuarios.php" class="absolute left-4 top-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg px-4 py-2 shadow-md transition-all duration-200">Volver</a> -->
    <?php endif; ?>
    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-2 tracking-tight">Editar usuario</h2>
    <?php if (!empty($msg)): ?>
      <div class="border <?php echo ($type === 'ok') ? 'border-green-400 bg-green-100 text-green-700' : 'border-red-400 bg-red-100 text-red-700'; ?> px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
        <span class="font-bold"><?php echo ($type === 'ok') ? 'Éxito:' : 'Error:'; ?></span>
        <span><?= htmlspecialchars($msg) ?></span>
      </div>
    <?php endif; ?>
    <label class="font-semibold text-gray-700 dark:text-white">Nombre de usuario</label>
    <input type="text" class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" id="username" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>
    <label class="font-semibold text-gray-700 dark:text-white">Email</label>
    <input type="email" class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
    <label class="font-semibold text-gray-700 dark:text-white">Contraseña (dejar vacío para no cambiar)</label>
    <input type="password" class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" id="password" name="password">
    <?php if ($_SESSION['user']['cargo'] === 'equipo_directivo'): ?>
    <label class="font-semibold text-gray-700 dark:text-white">Tipo de usuario</label>
    <select class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" id="tipo" name="tipo" required onchange="mostrarGrupo()">
      <option value="alumno" <?= $tipo === 'alumno' ? 'selected' : '' ?>>Alumno</option>
      <option value="profesor" <?= $tipo === 'profesor' ? 'selected' : '' ?>>Profesor</option>
      <option value="equipo_directivo" <?= $tipo === 'equipo_directivo' ? 'selected' : '' ?>>Equipo Directivo</option>
    </select>
      <label class="font-semibold text-gray-700 dark:text-white">Grupo</label>
      <select class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" id="grupo" name="grupo">
        <option value="">Sin grupo</option>
        <?php foreach ($grupos as $g): ?>
          <option value="<?= $g['IDgroup'] ?>" <?= (!empty($usuario['IDgroup']) && $usuario['IDgroup'] == $g['IDgroup']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>
    <div class="flex gap-4 mt-4">
      <?php if ($_SESSION['user']['cargo'] === 'equipo_directivo'): ?>
        <a href="gestion_usuarios.php" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg p-3 text-center transition">Cancelar</a>
      <?php endif; ?>
      <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-3 transition">Guardar cambios</button>
    </div>
  </form>
</div>
<script>
function mostrarGrupo() {
  var tipo = document.getElementById('tipo').value;
  document.getElementById('grupoDiv').style.display = (tipo === 'alumno' || tipo === 'profesor') ? 'block' : 'none';
}
window.onload = mostrarGrupo;
</script>
<!-- <?php include '../parts/footer.php'; ?>  -->