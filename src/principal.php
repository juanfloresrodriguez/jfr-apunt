<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100%, initial-scale=1.0">
    <link rel="stylesheet" href="../src/output.css">
    <title>Document</title>
</head>
<body>
    <?php
    session_start();
    include_once '../parts/sesiones.php';
    comprobar_sesion();
    if($_SESSION['user']){
        header('Location: asignaturas_cards.php');
        exit();
    }
    include_once '../parts/header.php';
    include_once '../parts/home.php';
    ?>
</body>
</html>