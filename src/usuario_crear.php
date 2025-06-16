<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();

if ($_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: gestion_usuarios.php?msg=No tienes permisos&type=error');
    exit;
}

include '../parts/db.php';
$pdo = (new DB())->connect();

// Obtener grupos para el select
$grupos = $pdo->query("SELECT IDgroup, nombre FROM GRUPO")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $grupo = $_POST['grupo'] ?? '';

    if ($username && $email && $password && $tipo) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USUARIO WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $msg = 'El usuario o email ya existe.';
            $type = 'error';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO USUARIO (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $email]);
            $idUser = $pdo->lastInsertId();
            if ($tipo === 'alumno') {
                $pdo->prepare("INSERT INTO ALUMNO (IDuser) VALUES (?)")->execute([$idUser]);
                if ($grupo) {
                    $pdo->prepare("INSERT INTO GRUPO_ALUMNO (IDgroup, IDuser) VALUES (?, ?)")->execute([$grupo, $idUser]);
                }
            } elseif ($tipo === 'profesor') {
                $pdo->prepare("INSERT INTO PROFESOR (IDuser) VALUES (?)")->execute([$idUser]);
                if ($grupo) {
                    $pdo->prepare("INSERT INTO GRUPO_PROFESOR (IDgroup, IDuser) VALUES (?, ?)")->execute([$grupo, $idUser]);
                }
            } elseif ($tipo === 'equipo_directivo') {
                $pdo->prepare("INSERT INTO EQUIPO_DIRECTIVO (IDuser) VALUES (?)")->execute([$idUser]);
            }
            header('Location: gestion_usuarios.php?msg=Usuario creado correctamente&type=ok');
            exit;
        }
    } else {
        $msg = 'Faltan datos obligatorios.';
        $type = 'error';
    }
}
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-6 px-2 sm:px-6 flex items-center justify-center pt-20">
  <div class="w-full max-w-lg bg-white/90 dark:bg-gray-900/80 rounded-2xl shadow-2xl p-4 sm:p-8 border border-gray-200 dark:border-gray-800">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
      <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Crear usuario</h2>
      <a href="gestion_usuarios.php" class="w-full sm:w-auto text-center bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-lg px-4 py-2 shadow transition">Volver</a>
    </div>
    <?php if (!empty($msg)): ?>
      <div class="mb-6 p-3 rounded-lg <?php echo ($type === 'ok') ? 'bg-green-100 text-white border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?> text-center font-semibold text-sm sm:text-base">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>
    <form method="POST" class="flex flex-col gap-2 sm:gap-5">
      <div>
        <label for="username" class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Nombre de usuario</label>
        <input type="text" class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm sm:text-base" id="username" name="username" required>
      </div>
      <div>
        <label for="email" class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Email</label>
        <input type="email" class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm sm:text-base" id="email" name="email" required>
      </div>
      <div>
        <label for="password" class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Contraseña</label>
        <input type="password" class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm sm:text-base" id="password" name="password" required>
      </div>
      <div>
        <label for="tipo" class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Tipo de usuario</label>
        <select class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm sm:text-base" id="tipo" name="tipo" required onchange="mostrarGrupo()">
          <option value="">Selecciona un tipo</option>
          <option value="alumno">Alumno</option>
          <option value="profesor">Profesor</option>
          <option value="equipo_directivo">Equipo Directivo</option>
        </select>
      </div>
      <div id="grupoDiv" style="display:none;">
        <label for="grupo" class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Grupo</label>
        <select class="w-full p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white text-sm sm:text-base" id="grupo" name="grupo">
          <option value="">Sin grupo</option>
          <?php foreach ($grupos as $g): ?>
            <option value="<?= $g['IDgroup'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4 mt-2">
        <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">Crear usuario</button>
      </div>
    </form>
  </div>
</div>
<script>
function mostrarGrupo() {
  var tipo = document.getElementById('tipo').value;
  document.getElementById('grupoDiv').style.display = (tipo === 'alumno' || tipo === 'profesor') ? 'block' : 'none';
}
</script>
<!-- <?php include '../parts/footer.php'; ?>  -->