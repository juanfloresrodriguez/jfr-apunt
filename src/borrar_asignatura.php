<?php
session_start();
if (!isset($_SESSION['user']['cargo']) || $_SESSION['user']['cargo'] !== 'equipo_directivo') {
    header('Location: asignaturas_cards.php?msg=No tienes permisos&type=error');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    include '../parts/db.php';
    $pdo = (new DB())->connect();
    // Borrar contenidos asociados y archivos físicos
    $stmt = $pdo->prepare("SELECT RutaDocumento FROM CONTENIDO WHERE IDAsignatura = ?");
    $stmt->execute([$id]);
    $rutas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($rutas as $rutaEncriptada) {
        $ruta = base64_decode($rutaEncriptada);
        if ($ruta && file_exists($ruta)) {
            @unlink($ruta);
        }
    }
    $pdo->prepare("DELETE FROM CONTENIDO WHERE IDAsignatura = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM grupo_asignatura WHERE idAsignatura = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM asignatura WHERE idAsignatura = ?")->execute([$id]);
    header('Location: asignaturas_cards.php?msg=Asignatura eliminada correctamente&type=ok');
    exit;
} else {
    header('Location: asignaturas_cards.php?msg=Petición no válida&type=error');
    exit;
} 