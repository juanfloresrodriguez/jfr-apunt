<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Apunt</title>
    <link rel="stylesheet" href="./output.css">
</head>
<body class="bg-gradient-to-br from-teal-600 via-blue-900 to-gray-900 min-h-screen flex flex-col">
    <div class="flex-1 flex flex-col items-center justify-center px-4 pt-16">
        <div class="bg-white/90 dark:bg-gray-900/80 rounded-3xl shadow-2xl p-10 max-w-[90%] w-full text-center border border-gray-200 dark:border-gray-800 mt-10">
            <!-- <img src="../img/logo.png" alt="Logo Apunt" class="mx-auto mb-6 w-24 h-24  rounded-full shadow-lg border-4 border-teal-500 bg-white"> -->
            <img src="../img/logo.webp" class="mx-auto mb-6 w-50 h-auto shadow-lg">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">Bienvenido a <span class="text-teal-600">Apunt</span></h1>
            <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-8">La plataforma digital para la gestión y el acceso a apuntes, asignaturas y contenidos educativos, diseñada para alumnos, profesores y equipo directivo.</p>
            <a href="../parts/login.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-8 py-3 shadow-md transition-all duration-200 text-lg mb-6">Iniciar Sesión</a>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 my-10">
                <div class="bg-gray-100/80 dark:bg-gray-800/80 rounded-xl p-6 shadow flex flex-col items-center">
                    <svg class="w-12 h-12 text-teal-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" /></svg>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Alumnos</h3>
                    <p class="text-gray-700 dark:text-gray-300 text-base">Accede fácilmente a tus asignaturas y contenidos, visualiza y descarga apuntes, y mantente organizado en tu aprendizaje.</p>
                </div>
                <div class="bg-gray-100/80 dark:bg-gray-800/80 rounded-xl p-6 shadow flex flex-col items-center">
                    <svg class="w-12 h-12 text-yellow-400 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Profesores</h3>
                    <p class="text-gray-700 dark:text-gray-300 text-base">Gestiona tus asignaturas, sube y edita contenidos, y facilita el acceso a materiales para tus alumnos de forma segura y eficiente.</p>
                </div>
                <div class="bg-gray-100/80 dark:bg-gray-800/80 rounded-xl p-6 shadow flex flex-col items-center">
                    <svg class="w-12 h-12 text-red-500 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Equipo Directivo</h3>
                    <p class="text-gray-700 dark:text-gray-300 text-base">Control total sobre usuarios, grupos y permisos. Supervisa la plataforma y garantiza la seguridad y el correcto funcionamiento.</p>
                </div>
            </div>
            <div class="my-10">
                <h2 class="text-2xl font-bold text-teal-700 dark:text-teal-300 mb-4">¿Por qué elegir Apunt?</h2>
                <ul class="text-left text-gray-700 dark:text-gray-300 space-y-2 max-w-2xl mx-auto">
                    <li><span class="font-semibold text-teal-600">✔ Seguridad y control de acceso:</span> Solo usuarios autorizados pueden acceder y modificar contenidos.</li>
                    <li><span class="font-semibold text-teal-600">✔ Gestión visual y moderna:</span> Interfaz intuitiva, tarjetas, paginación y diseño responsive.</li>
                    <li><span class="font-semibold text-teal-600">✔ Permisos avanzados:</span> Cada usuario ve y gestiona solo lo que le corresponde.</li>
                </ul>
            </div>
        </div>
        <footer class="mt-12 text-gray-400 text-sm">
            &copy; <?php echo date('Y'); ?> Apunt. Todos los derechos reservados.
        </footer>
    </div>
</body>
</html>