<head>
    <link rel="stylesheet" href="../src/output.css">
</head>
<?php
include_once 'db.php';
session_start();

if(isset($_SESSION['user'])) {
    header("Location: ../src/principal.php");
    exit();
}

$errorLogin = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once '../parts/sesiones.php';
    $usu = comprobar_usuario($_POST['username'], $_POST['password']);
    if($usu === false) {
        $errorLogin = "Usuario o contraseña incorrectos.";
    } else {
        $_SESSION['user'] = $usu;
        header("Location: ../src/principal.php");
        exit();
    }
}
?>
<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 bg-opacity-90 backdrop-blur-sm flex items-center justify-center min-h-screen z-40">
    <form action="" method="POST" id="login-form" class="bg-white/90 dark:bg-gray-900/80 p-8 rounded-2xl shadow-2xl flex flex-col gap-6 min-w-[320px] max-w-xs w-full border border-gray-200 dark:border-gray-800">
        
        <?php if(!empty($errorLogin)): ?>
            <div class="border border-red-400 bg-red-100 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
                <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="font-bold">Error:</span>
                <span><?php echo $errorLogin; ?></span>
            </div>
            <?php endif; ?>
            
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-2 tracking-tight">Iniciar Sesión</h2>
            <input type="text" name="username" id="username" placeholder="Usuario" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition placeholder-gray-400 dark:placeholder-gray-500">
            <input type="password" name="password" id="password" placeholder="Contraseña" required class="p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-600 transition placeholder-gray-400 dark:placeholder-gray-500">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg p-3 shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400">Entrar</button>
        </form>
    </div>
<?php include_once 'header.php'; ?>
    
