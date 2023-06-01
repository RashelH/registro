<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "usuario") {
  header("Location: login.php");
  exit();
}
?>
<html>
	<head>
		<title>.: HOME :.</title>
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	</head>
	<body>
	<?php include "php/navbar.php"; ?>
<div class="container">
<div class="row">
<div class="col-md-6">
		<h2>Bienvenido</h2>
</div>
</div>
</div>
	</body>
</html>