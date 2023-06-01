<?php
session_start();
if (isset($_SESSION["user_id"])) {
	if ($_SESSION["user_role"] === "admin") {
	  header("Location: admin.php");
	  exit();
	} else {
	  header("Location: ../home.php");
	  exit();
	}
  }
  
if (!empty($_POST)) {
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		if ($_POST["username"] != "" && $_POST["password"] != "") {
			include "conexion.php";

			$user_id = null;
			$username = $_POST["username"];
			$password = $_POST["password"];

			// Verificar el estado de la IP
			$ip = $_SERVER['REMOTE_ADDR'];
			$sql3 = "SELECT * FROM ip_bloqueada WHERE ip = '$ip' AND bloqueada = 3 AND bloqueada_hasta > NOW()";
			$query3 = $con->query($sql3);

			if ($query3->num_rows > 0) {
				$_SESSION['ip_bloqueada'] = true;
				$_SESSION['bloqueada_hasta'] = $query3->fetch_assoc()['bloqueada_hasta'];
				print "<script>alert(\"Tu IP ha sido bloqueada. Inténtalo de nuevo más tarde.\");window.location='../login.php';</script>";
				exit();
			} else {
				$_SESSION['ip_bloqueada'] = false;
			}

			$sql1 = "SELECT * FROM user WHERE (username=\"$username\" OR email=\"$username\") AND password=\"$password\"";
			$query = $con->query($sql1);
			$user = $query->fetch_assoc();

			if ($user) {
				$user_id = $user["id"];
				$username = $user["username"];
				//$user_role = $user["role"]; // Obtener el rol del usuario
				$user_role = $user["role"];
				$_SESSION["user_role"] = $user_role;


				// Registro de datos de sesión
					$_SESSION["user_id"] = $user_id;
					$_SESSION["username"] = $username;

					// Obtener datos de sesión
					$fecha = date("Y-m-d H:i:s");
					$navegador = $_SERVER['HTTP_USER_AGENT'];
					$so = php_uname('s');
					$estado = 1; // Estado 1 para acceso válido

					// Insertar datos de sesión en la tabla "sesiones"
					$sql2 = "INSERT INTO sesiones (fecha, ip, navegador, so, estado, usuario) VALUES ('$fecha', '$ip', '$navegador', '$so', '$estado', '$username')";
					$con->query($sql2);

					// Redirigir según el rol del usuario
					if ($user_role === "admin") {
						header("Location: admin.php");
						exit();
					  } else {
						header("Location: ../home.php");
						exit();
					  }
					  

			} else {
				$estado = 0; // Estado 0 para acceso inválido

				// Insertar datos de sesión inválida en la tabla "sesiones"
				$sql2 = "INSERT INTO sesiones (fecha, ip, navegador, so, estado, usuario) VALUES (NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".php_uname('s')."', '$estado', '$username')";
				$con->query($sql2);

				// Verificar y actualizar el contador de intentos fallidos
				$sql4 = "SELECT * FROM intentos_fallidos WHERE ip = '$ip'";
				$query4 = $con->query($sql4);

				if ($query4->num_rows > 0) {
					$intentos = $query4->fetch_assoc()['intentos'] + 1;

					if ($intentos >= 3) { //Numero de intentos
						$bloqueada_hasta = date('Y-m-d H:i:s', strtotime('+3 minutes'));

						// Bloquear la IP y registrar el tiempo de bloqueo
						$sql5 = "UPDATE ip_bloqueada SET bloqueada = 1, bloqueada_hasta = '$bloqueada_hasta' WHERE ip = '$ip'";
						$con->query($sql5);

						$_SESSION['ip_bloqueada'] = true;
						$_SESSION['desbloqueo'] = false;
						$_SESSION['bloqueada_hasta'] = $bloqueada_hasta;
						print "<script>alert(\"Tu IP ha sido bloqueada. Inténtalo de nuevo más tarde.\");window.location='../login.php';</script>";
						exit();
					} else {
						// Actualizar el contador de intentos fallidos
						$sql5 = "UPDATE intentos_fallidos SET intentos = $intentos WHERE ip = '$ip'";
						$con->query($sql5);

						print "<script>alert(\"Acceso inválido. Intento $intentos de 3.\");window.location='../login.php';</script>";
						exit();
					}
				} else {
					// Insertar nueva entrada para la IP en la tabla "intentos_fallidos"
					$sql5 = "INSERT INTO intentos_fallidos (ip, intentos) VALUES ('$ip', 1)";
					$con->query($sql5);

					print "<script>alert(\"Acceso inválido. Intento 1 de 3.\");window.location='../login.php';</script>";
					exit();
				}
			}
		}
	}
} else {
	// Verificar el estado de la IP al cargar la página de inicio de sesión
	$ip = $_SERVER['REMOTE_ADDR'];
	$sql3 = "SELECT * FROM ip_bloqueada WHERE ip = '$ip' AND bloqueada = 1 AND bloqueada_hasta > NOW()";
	$query3 = $con->query($sql3);

	if ($query3->num_rows > 0) {
		$_SESSION['ip_bloqueada'] = true;
		$_SESSION['bloqueada_hasta'] = $query3->fetch_assoc()['bloqueada_hasta'];
	} else {
		// Verificar si la IP estaba bloqueada pero se desbloqueó
		if (isset($_SESSION['ip_bloqueada']) && $_SESSION['ip_bloqueada']) {
			unset($_SESSION['ip_bloqueada']);
			unset($_SESSION['desbloqueo']);
			unset($_SESSION['bloqueada_hasta']);
		} else {
			$_SESSION['ip_bloqueada'] = false;
		}
	}
}
?>
