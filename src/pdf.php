<?php
// Recibe la ruta codificada en base64 por GET
if (!isset($_GET['ruta'])) {
    http_response_code(400);
    echo 'Ruta no especificada.';
    exit;
}

$ruta = base64_decode($_GET['ruta']);
if (!$ruta || !file_exists($ruta)) {
    http_response_code(404);
    echo 'Archivo no encontrado.';
    exit;
}

// Forzar visualización en navegador
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($ruta) . '"');
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
exit; 