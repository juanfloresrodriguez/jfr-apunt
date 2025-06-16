<?php
function comprobar_sesion(){
	if(!isset($_SESSION['user'])){
		header("Location: ../src/principal.php");
		exit();
	}		
}

function comprobar_usuario($username, $password)
{
	$pdo = (new DB())->connect();
	$query = "SELECT * FROM USUARIO WHERE username = :username";
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':username', $username);
	$stmt->execute();
	$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

	if($usuario && password_verify($password, $usuario['password'])){
		$id = $usuario['IDuser'];
		$cargo = null;

		// Es equipo directivo?
		$stmt2 = $pdo->prepare("SELECT 1 FROM Equipo_Directivo WHERE IDuser = :id");
		$stmt2->execute([':id' => $id]);
		if ($stmt2->fetch()) {
			$cargo = 'equipo_directivo';
		}

		// Es profesor?
		$stmt2 = $pdo->prepare("SELECT 1 FROM Profesor WHERE IDuser = :id");
		$stmt2->execute([':id' => $id]);
		if ($stmt2->fetch()) {
			$cargo = 'profesor';
		}

		// Es alumno?
		$stmt2 = $pdo->prepare("SELECT 1 FROM Alumno WHERE IDuser = :id");
		$stmt2->execute([':id' => $id]);
		if ($stmt2->fetch()) {
			$cargo = 'alumno';
		}

		$usuario['cargo'] = $cargo;
		return $usuario;
	}
	return false;
}