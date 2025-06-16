<html class="dark">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinar el rol actual (real o simulado)
$rolActual = $_SESSION['user']['cargo'] ?? null;
function esDirectivoMenu() {
    return isset($_SESSION['user']['cargo']) && str_starts_with($_SESSION['user']['cargo'], 'equipo_directivo');
}
function esProfesorMenu() {
    return isset($_SESSION['user']['cargo']) && 
        ($_SESSION['user']['cargo'] === 'profesor' || $_SESSION['user']['cargo'] === 'profesor_alumno');
}
function nombrePerfil($cargo) {
    if ($cargo === 'equipo_directivo') return 'Equipo directivo';
    if ($cargo === 'equipo_directivo_alumno') return 'Alumno (simulado)';
    if ($cargo === 'equipo_directivo_profesor') return 'Profesor (simulado)';
    if ($cargo === 'profesor') return 'Profesor';
    if ($cargo === 'profesor_alumno') return 'Alumno (simulado)';
    return ucfirst($cargo);
}
?>

<head>
  <link rel="stylesheet" href="../src/output.css">
</head>

<header class="fixed top-0 w-full bg-white dark:bg-gray-900 z-50">
  <div class="mx-auto flex h-16 max-w-screen-xl items-center gap-8 px-4 sm:px-6 lg:px-8">
    <a class="block text-teal-600 dark:text-teal-300" href="#">
      <span class="sr-only">Home</span>
      <img src="../img/logo.png" alt="logo" class="w-20 h-auto">
    </a>

    <div class="flex flex-1 items-center justify-end md:justify-between">
      <?php if (!isset($_SESSION['user'])): ?>
        <nav aria-label="Global" class="hidden md:block">
          <ul class="flex items-center gap-6 text-sm">
            <li>
              <a
                class="text-gray-500 transition hover:text-gray-500/75 dark:text-white dark:hover:text-white/75"
                href="../src/index.php"
              >
                Inicio
              </a>
            </li>
          </ul>
        </nav>
        <div class="flex items-center gap-4">
          <a href="../parts/login.php" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-2.5 shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400">Iniciar sesión</a>
        </div>
      <?php else: ?>
        <nav aria-label="Global" class="hidden md:block">
          <ul class="flex items-center gap-6 text-sm">
            <li>
              <a
                class="text-gray-500 transition hover:text-gray-500/75 dark:text-white dark:hover:text-white/75"
                href="../src/principal.php"
              >
                Inicio
              </a>
            </li>

            <li>
              <a
                class="text-gray-500 transition hover:text-gray-500/75 dark:text-white dark:hover:text-white/75"
                href="../src/asignaturas_cards.php"
              >
                Asignaturas
              </a>
            </li>

            <?php if (isset($_SESSION['user']['cargo']) && 
                ($_SESSION['user']['cargo'] === 'profesor' || $_SESSION['user']['cargo'] === 'equipo_directivo')): ?>
                <li>
                    <a class="text-gray-500 transition hover:text-gray-500/75 dark:text-white dark:hover:text-white/75"
                       href="../src/subir_contenido.php">
                        Subir Contenido
                    </a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user']['cargo']) && $_SESSION['user']['cargo'] === 'equipo_directivo'): ?>
              <li>
                  <a class="text-gray-500 transition hover:text-gray-500/75 dark:text-white dark:hover:text-white/75"
                     href="../src/gestion_grupos.php">
                      Gestión de grupos
                  </a>
              </li>
                <li>
                    <a class="text-gray-500 transition hover:text-gray-500/75 dark:text-white dark:hover:text-white/75"
                       href="../src/gestion_usuarios.php">
                        Gestión de usuarios
                    </a>
                </li>
            <?php endif; ?>
          </ul>
        </nav>

        <div class="flex items-center gap-4">
          <div class="sm:flex sm:gap-4 relative w-full justify-end">
              <button id="userMenuBtn" type="button" class="text-gray-500 dark:text-white font-semibold focus:outline-none flex items-center gap-2">
                  <?php echo $_SESSION['user']['username']; ?>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
              </button>
              <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                  <?php if (esDirectivoMenu() || esProfesorMenu()): ?>
                      <div class="relative" id="cambiarPerfilWrapper">
                          <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                              Perfil actual: <span class="font-bold text-teal-700 dark:text-teal-300"><?php echo nombrePerfil($rolActual); ?></span>
                          </div>
                          <button id="cambiarPerfilBtn" type="button" class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between">
                              Cambiar perfil
                              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                          </button>
                          <div id="submenuPerfil" class="hidden absolute left-full top-0 ml-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                              <form method="post" action="/apunt/parts/cambiar_perfil.php">
                                  <?php if (esDirectivoMenu()): ?>
                                      <?php if ($rolActual !== 'equipo_directivo_alumno'): ?>
                                          <button type="submit" name="perfil" value="alumno" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Alumno</button>
                                      <?php endif; ?>
                                      <?php if ($rolActual !== 'equipo_directivo_profesor'): ?>
                                          <button type="submit" name="perfil" value="profesor" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Profesor</button>
                                      <?php endif; ?>
                                      <?php if ($rolActual !== 'equipo_directivo'): ?>
                                          <button type="submit" name="perfil" value="equipo_directivo" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Equipo directivo</button>
                                      <?php endif; ?>
                                  <?php elseif (esProfesorMenu()): ?>
                                      <?php if ($rolActual !== 'profesor_alumno'): ?>
                                          <button type="submit" name="perfil" value="alumno" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Alumno</button>
                                      <?php endif; ?>
                                      <?php if ($rolActual !== 'profesor'): ?>
                                          <button type="submit" name="perfil" value="profesor" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Profesor</button>
                                      <?php endif; ?>
                                  <?php endif; ?>
                              </form>
                          </div>
                      </div>
                  <?php endif; ?>
                  <a href="/apunt/src/usuario_editar.php?id=<?php echo $_SESSION['user']['IDuser']; ?>" class="block px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Editar mi perfil</a>
                  <a href="/apunt/parts/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">Cerrar sesión</a>
              </div>
          </div>

          <button
            class="block rounded-sm bg-gray-100 p-2.5 text-gray-600 transition hover:text-gray-600/75 md:hidden dark:bg-gray-800 dark:text-white dark:hover:text-white/75"
          >
            <span class="sr-only">Toggle menu</span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="size-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
// Menú desplegable usuario
const userMenuBtn = document.getElementById('userMenuBtn');
const userMenu = document.getElementById('userMenu');
const cambiarPerfilBtn = document.getElementById('cambiarPerfilBtn');
const submenuPerfil = document.getElementById('submenuPerfil');
const cambiarPerfilWrapper = document.getElementById('cambiarPerfilWrapper');

if (userMenuBtn && userMenu) {
  userMenuBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    userMenu.classList.toggle('hidden');
    if (submenuPerfil) submenuPerfil.classList.add('hidden');
  });
  document.addEventListener('click', function(e) {
    if (!userMenu.contains(e.target) && e.target !== userMenuBtn) {
      userMenu.classList.add('hidden');
      if (submenuPerfil) submenuPerfil.classList.add('hidden');
    }
  });
}
if (cambiarPerfilBtn && submenuPerfil && cambiarPerfilWrapper) {
  let submenuTimeout;
  cambiarPerfilBtn.addEventListener('mouseenter', function() {
    clearTimeout(submenuTimeout);
    submenuPerfil.classList.remove('hidden');
  });
  cambiarPerfilBtn.addEventListener('mouseleave', function() {
    submenuTimeout = setTimeout(() => submenuPerfil.classList.add('hidden'), 200);
  });
  submenuPerfil.addEventListener('mouseenter', function() {
    clearTimeout(submenuTimeout);
    submenuPerfil.classList.remove('hidden');
  });
  submenuPerfil.addEventListener('mouseleave', function() {
    submenuTimeout = setTimeout(() => submenuPerfil.classList.add('hidden'), 200);
  });
}
</script>
