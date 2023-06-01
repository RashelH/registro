<?php session_start(); ?>
<html>
	<head>
		<title>Formulario de Registro</title>
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	</head>
	<body>
		<?php include "php/navbar.php"; ?>

		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h2>Login</h2>

					<form role="form" name="login" action="php/login.php" method="post">
						<div class="form-group">
							<label for="username">Nombre de usuario o email</label>
							<input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario">
						</div>
						<div class="form-group">
							<label for="password">Contraseña</label>
							<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
						</div>

						<div class="form-group">
			<div class="g-recaptcha" data-sitekey="6LfBGdclAAAAACTsz24mPdJIsRFbJCFlyJInQ9Bi">
			</div>

						<button type="submit" class="btn btn-primary" <?php
							if (isset($_SESSION['ip_bloqueada']) && $_SESSION['ip_bloqueada'] && (!isset($_SESSION['desbloqueo']) || $_SESSION['desbloqueo'] === false)) {
								$currentTimestamp = time();
								$blockedUntil = $_SESSION['bloqueada_hasta'];
								$blockedUntilTimestamp = strtotime($blockedUntil);
								if ($currentTimestamp >= $blockedUntilTimestamp) {
									unset($_SESSION['ip_bloqueada']);
									unset($_SESSION['desbloqueo']);
								} else {
									echo 'disabled';
								}
							}
						?>>Acceder</button>
					</form>
				</div>
			</div>
		</div>

		<script src="js/valida_login.js"></script>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</body>
</html>
