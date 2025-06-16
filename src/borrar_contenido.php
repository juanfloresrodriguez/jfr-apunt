<?php
session_start();
if (!isset($_SESSION['user']['cargo']) || !in_array($_SESSION['user']['cargo'], ['profesor', 'equipo_directivo'])) {
    header('Location: contenidos.php?msg=No tienes permisos&type=error');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    include '../parts/db.php';
    $pdo = (new DB())->connect();
    // Obtener la ruta del archivo
    $stmt = $pdo->prepare("SELECT RutaDocumento, IDAsignatura FROM CONTENIDO WHERE IDcontenido = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $ruta = base64_decode($row['RutaDocumento']);
        if ($ruta && file_exists($ruta)) {
            @unlink($ruta);
        }
        $idAsignatura = $row['IDAsignatura'];
        $pdo->prepare("DELETE FROM CONTENIDO WHERE IDcontenido = ?")->execute([$id]);
        header('Location: contenidos.php?id=' . urlencode($idAsignatura) . '&msg=Contenido eliminado&type=ok');
        exit;
    } else {
        header('Location: contenidos.php?msg=Contenido no encontrado&type=error');
        exit;
    }
} else {
    header('Location: contenidos.php?msg=Petición no válida&type=error');
    exit;
} 