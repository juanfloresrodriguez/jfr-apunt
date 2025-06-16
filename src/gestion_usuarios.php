<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();

if ($_SESSION['user']['cargo'] !== 'equipo_directivo') {
    echo "<div class='text-center mt-10 text-red-600 font-bold text-xl'>No tienes permisos para acceder a esta sección.</div>";
    exit;
}

include '../parts/db.php';
$pdo = (new DB())->connect();

// Mensajes de éxito/error
$mensaje = $_GET['msg'] ?? '';
$tipo_mensaje = $_GET['type'] ?? '';

// Obtener grupos para mostrar
$grupos = $pdo->query("SELECT IDgroup, nombre FROM GRUPO")->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios por tipo
$alumnos = $pdo->query("SELECT u.IDuser, u.username, u.email, g.nombre as grupo FROM USUARIO u JOIN ALUMNO a ON u.IDuser = a.IDuser LEFT JOIN GRUPO_ALUMNO ga ON u.IDuser = ga.IDuser LEFT JOIN GRUPO g ON ga.IDgroup = g.IDgroup")->fetchAll(PDO::FETCH_ASSOC);
$profesores = $pdo->query("SELECT u.IDuser, u.username, u.email, g.nombre as grupo FROM USUARIO u JOIN PROFESOR p ON u.IDuser = p.IDuser LEFT JOIN GRUPO_PROFESOR gp ON u.IDuser = gp.IDuser LEFT JOIN GRUPO g ON gp.IDgroup = g.IDgroup")->fetchAll(PDO::FETCH_ASSOC);
$directivos = $pdo->query("SELECT u.IDuser, u.username, u.email FROM USUARIO u JOIN EQUIPO_DIRECTIVO e ON u.IDuser = e.IDuser")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../parts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 py-6 px-2 sm:px-6 pt-20">
  <div class="w-full max-w-[90%] mx-auto bg-gray-800/75 rounded-2xl shadow-2xl p-4 sm:p-14 border border-gray-200 dark:border-gray-800">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
      <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold text-white dark:text-white tracking-tight">Gestión de usuarios</h2>
      <a href="usuario_crear.php" class="w-full sm:w-auto text-center bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-5 py-2 shadow-md transition-all duration-200">Crear usuario</a>
    </div>
    <?php if ($mensaje): ?>
      <div class="mb-6 p-3 rounded-lg <?php echo ($tipo_mensaje === 'ok') ? 'bg-green-100 text-white border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?> text-center font-semibold text-sm sm:text-base">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>
    <div class="mb-10">

    <!-- EQUIPO DIRECTIVO -->
    <div class="flex justify-between items-center">
      <h3 class="text-base sm:text-xl font-bold text-teal-700 dark:text-teal-300 mb-2">Equipo Directivo</h3>
      <input type="text" id="buscador-directivos" class="mb-2 p-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" placeholder="Buscar usuario">
    </div>
      <div class="overflow-x-auto mb-6">
        <table class="min-w-[600px] w-full border-collapse rounded-xl overflow-hidden shadow bg-white dark:bg-gray-900 text-xs sm:text-sm md:text-base" id="tabla-directivos">
          <thead>
            <tr class="bg-gray-100 dark:bg-gray-800 text-white dark:text-white">
              <th class="p-3 text-left font-semibold w-1/4">Usuario</th>
              <th class="p-3 text-left font-semibold w-1/4">Email</th>
              <th></th>
              <th class="p-3 text-center font-semibold w-1/4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($directivos as $d): ?>
              <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition">
                <td class="p-3 text-white dark:text-white font-medium w-1/4"><?= htmlspecialchars($d['username']) ?></td>
                <td class="p-3 text-white dark:text-white text-sm w-1/4">
                  <?= htmlspecialchars($d['email']) ?>
                </td>
                <td></td>
                <td class="p-3 w-1/4">
                  <div class="flex gap-2 items-center justify-center">
                    <a href="usuario_editar.php?id=<?= urlencode($d['IDuser']) ?>" class="w-24 text-center bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-1 text-xs sm:text-sm font-semibold shadow transition">Editar</a>
                    <form action="usuario_borrar.php" method="POST" class="w-24 inline" onsubmit="return confirm('¿Seguro que quieres borrar este usuario?');">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($d['IDuser']) ?>">
                      <input type="hidden" name="tipo" value="equipo_directivo">
                      <button type="submit" class="w-full text-center bg-red-600 hover:bg-red-700 text-white rounded px-3 py-1 text-xs sm:text-sm font-semibold shadow transition">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- PROFESORES -->
      <div class="flex justify-between items-center">
      <h3 class="text-base sm:text-xl font-bold text-teal-700 dark:text-teal-300 mb-2">Profesores</h3>
      <input type="text" id="buscador-profesores" class="mb-2 p-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" placeholder="Buscar usuario">
    </div>
      <div class="overflow-x-auto mb-6">
        <table class="min-w-[600px] w-full border-collapse rounded-xl overflow-hidden shadow bg-white dark:bg-gray-900 text-xs sm:text-sm md:text-base" id="tabla-profesores">
          <thead>
            <tr class="bg-gray-100 dark:bg-gray-800 text-white dark:text-white">
              <th class="p-3 text-left font-semibold w-1/4">Usuario</th>
              <th class="p-3 text-left font-semibold w-1/4">Email</th>
              <th class="p-3 text-left font-semibold w-1/4">Grupo</th>
              <th class="p-3 text-center font-semibold w-1/4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($profesores as $p): ?>
              <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition">
                <td class="p-3 text-white dark:text-white font-medium w-1/4"><?= htmlspecialchars($p['username']) ?></td>
                <td class="p-3 text-white dark:text-white text-sm w-1/4">
                  <?= htmlspecialchars($p['email']) ?>
                </td>
                <td class="p-3 text-teal-700 dark:text-teal-300 text-sm w-1/4">
                  <?= htmlspecialchars($p['grupo'] ?? '-') ?>
                </td>
                <td class="p-3 w-1/4">
                  <div class="flex gap-2 items-center justify-center">
                    <a href="usuario_editar.php?id=<?= urlencode($p['IDuser']) ?>" class="w-24 text-center bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-1 text-xs sm:text-sm font-semibold shadow transition">Editar</a>
                    <form action="usuario_borrar.php" method="POST" class="w-24 inline" onsubmit="return confirm('¿Seguro que quieres borrar este usuario?');">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($p['IDuser']) ?>">
                      <input type="hidden" name="tipo" value="profesor">
                      <button type="submit" class="w-full text-center bg-red-600 hover:bg-red-700 text-white rounded px-3 py-1 text-xs sm:text-sm font-semibold shadow transition">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ALUMNOS -->
      <div class="flex justify-between items-center">
        <h3 class="text-base sm:text-xl font-bold text-teal-700 dark:text-teal-300 mb-2">Alumnos</h3>
        <input type="text" id="buscador-alumnos" class="mb-2 p-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition" placeholder="Buscar usuario">
    </div>
      <div class="overflow-x-auto">
        <table class="min-w-[600px] w-full border-collapse rounded-xl overflow-hidden shadow bg-white dark:bg-gray-900 text-xs sm:text-sm md:text-base" id="tabla-alumnos">
          <thead>
            <tr class="bg-gray-100 dark:bg-gray-800 text-white dark:text-white">
              <th class="p-3 text-left font-semibold w-1/4">Usuario</th>
              <th class="p-3 text-left font-semibold w-1/4">Email</th>
              <th class="p-3 text-left font-semibold w-1/4">Grupo</th>
              <th class="p-3 text-center font-semibold w-1/4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($alumnos as $a): ?>
              <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition">
                <td class="p-3 text-white dark:text-white font-medium w-1/4"><?= htmlspecialchars($a['username']) ?></td>
                <td class="p-3 text-white dark:text-white text-sm w-1/4">
                  <?= htmlspecialchars($a['email']) ?>
                </td>
                <td class="p-3 text-teal-700 dark:text-teal-300 text-sm w-1/4">
                  <?= htmlspecialchars($a['grupo'] ?? '-') ?>
                </td>
                <td class="p-3 w-1/4">
                  <div class="flex gap-2 items-center justify-center">
                    <a href="usuario_editar.php?id=<?= urlencode($a['IDuser']) ?>" class="w-24 text-center bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-1 text-xs sm:text-sm font-semibold shadow transition">Editar</a>
                    <form action="usuario_borrar.php" method="POST" class="w-24 inline" onsubmit="return confirm('¿Seguro que quieres borrar este usuario?');">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($a['IDuser']) ?>">
                      <input type="hidden" name="tipo" value="alumno">
                      <button type="submit" class="w-full text-center bg-red-600 hover:bg-red-700 text-white rounded px-3 py-1 text-xs sm:text-sm font-semibold shadow transition">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="../src/buscador_tabla.js">
</script>
<script>
  activarBuscadorTabla('#buscador-directivos', '#tabla-directivos');
  activarBuscadorTabla('#buscador-profesores', '#tabla-profesores');
  activarBuscadorTabla('#buscador-alumnos', '#tabla-alumnos');
</script>
<!-- <?php include '../parts/footer.php'; ?>  -->