<?php
session_start();
if (!isset($_SESSION['user']['cargo']) || $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: gestion_grupos.php?msg=No tienes permisos&type=error');
    exit;
}
include '../parts/db.php';
$pdo = (new DB())->connect();

$id = intval($_POST['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM grupo WHERE IDgroup = ?");
    $stmt->execute([$id]);
    header('Location: gestion_grupos.php?msg=Grupo borrado correctamente&type=ok');
    exit;
} else {
    header('Location: gestion_grupos.php?msg=Faltan datos&type=error');
    exit;
} 