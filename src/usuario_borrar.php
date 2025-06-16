<?php
include_once '../parts/sesiones.php';
session_start();
comprobar_sesion();

if ($_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: gestion_usuarios.php?msg=No tienes permisos&type=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestion_usuarios.php?msg=Petición incorrecta&type=error');
    exit;
}

include '../parts/db.php';
$pdo = (new DB())->connect();

$id = $_POST['id'] ?? null;
$tipo = $_POST['tipo'] ?? null;
if (!$id || !$tipo) {
    header('Location: gestion_usuarios.php?msg=Petición incorrecta&type=error');
    exit;
}

// Validar existencia
$stmt = $pdo->prepare("SELECT IDuser FROM USUARIO WHERE IDuser = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: gestion_usuarios.php?msg=Usuario no encontrado&type=error');
    exit;
}

// Eliminar de tablas específicas
if ($tipo === 'alumno') {
    $pdo->prepare("DELETE FROM GRUPO_ALUMNO WHERE IDuser = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM ALUMNO WHERE IDuser = ?")->execute([$id]);
} elseif ($tipo === 'profesor') {
    $pdo->prepare("DELETE FROM GRUPO_PROFESOR WHERE IDuser = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM PROFESOR WHERE IDuser = ?")->execute([$id]);
} elseif ($tipo === 'equipo_directivo') {
    $pdo->prepare("DELETE FROM EQUIPO_DIRECTIVO WHERE IDuser = ?")->execute([$id]);
}
// Eliminar usuario
$pdo->prepare("DELETE FROM USUARIO WHERE IDuser = ?")->execute([$id]);

header('Location: gestion_usuarios.php?msg=Usuario eliminado correctamente&type=ok');
exit; 