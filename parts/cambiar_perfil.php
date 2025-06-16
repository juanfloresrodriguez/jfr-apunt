<?php
session_start();
if (!isset($_SESSION['user']) || 
    !(str_starts_with($_SESSION['user']['cargo'], 'equipo_directivo') || $_SESSION['user']['cargo'] === 'profesor' || $_SESSION['user']['cargo'] === 'profesor_alumno')) {
    header('Location: /apunt/src/index.php');
    exit;
}

if (isset($_POST['perfil'])) {
    $perfil = $_POST['perfil'];
    if (str_starts_with($_SESSION['user']['cargo'], 'equipo_directivo')) {
        if ($perfil === 'alumno') {
            $_SESSION['user']['cargo'] = 'equipo_directivo_alumno';
        } elseif ($perfil === 'profesor') {
            $_SESSION['user']['cargo'] = 'equipo_directivo_profesor';
        } elseif ($perfil === 'equipo_directivo') {
            $_SESSION['user']['cargo'] = 'equipo_directivo';
        }
    } elseif ($_SESSION['user']['cargo'] === 'profesor' || $_SESSION['user']['cargo'] === 'profesor_alumno') {
        if ($perfil === 'alumno') {
            $_SESSION['user']['cargo'] = 'profesor_alumno';
        } elseif ($perfil === 'profesor') {
            $_SESSION['user']['cargo'] = 'profesor';
        }
    }
}
// Redirigir a la página anterior si existe, si no a la principal
if (!empty($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: /apunt/src/index.php');
}
exit; 