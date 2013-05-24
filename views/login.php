<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<title>Iniciar sesión</title>
		<link href="css/bootstrap.css" rel="stylesheet">
		<script src="js/jquery-2.0.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<style>
			body {
				background-color: #eee;
			}
			.form-signin {
				background-color: #fff;
				border: 1px solid #E5E5E5;
				border-radius: 5px 5px 5px 5px;
				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
				margin: 20px auto 0;
				max-width: 300px;
				padding: 10px 20px;
			}
			.alert {
				width: 350px;
				margin: 10px auto;
				text-align: center;
				padding-right: 14px;
			}
			.alert + div .form-signin {
				margin: 0 auto;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<?php if( $isErrorLogin ): ?>
			<div class="alert alert-error">
				¡Nombre de usario y/o contraseña no son validos!
			</div>
			<?php endif; ?>
			<div>
				<form class="form-signin" method="post">
					<h3>Iniciar sesión</h3>
					<ul class="nav control-group<?php echo $isErrorLogin ? ' error' : '' ?>">
						<li>
							<input type="text" placeholder="Usuario" name="username">
						</li>
						<li>
							<input type="password" placeholder="Contraseña" name="password">
						</li>
						<li>
							<button class="btn btn-medium btn-primary" type="submit">Iniciar sesión</button>
						</li>
					</ul>
				</form>
			</div>
		</div> <!-- /container -->
</html>