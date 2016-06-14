<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title><?= $titulo ?></title>
	<link rel="stylesheet" href="/inventarios/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/inventarios/assets/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/inventarios/assets/css/inventarios.css">
	<script src="/inventarios/assets/js/jquery-2.2.3.min.js"></script>
	<script src="/inventarios/assets/js/bootstrap.min.js"></script>
	<script src="/inventarios/assets/js/bootbox.min.js"></script>
	<script src="/inventarios/assets/js/funciones.js"></script>
	<script src="/inventarios/assets/js/<?= $archivo_js ?>"></script>
</head>
<body>
	<header>
		<?php if(!$login){ ?>
			<nav class="navbar navbar-default">
				<div class="container-fluid">
			    <!-- Brand and toggle get grouped for better mobile display -->
			    <div class="navbar-header">
			    	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			      	</button>
			      	<a class="navbar-brand" href="/inventarios/inicio/index/<?= $id_usuario ?>">Sistema de Inventarios (Tenis)</a>
			    </div>

			    <!-- Collect the nav links, forms, and other content for toggling -->
			    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			      	<ul class="nav navbar-nav navbar-right">
			        	<li class="dropdown">
			          		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $nombre ?> <span class="caret"></span></a>
				          	<ul class="dropdown-menu">
				            	<li>
				            		<a href="#">Action</a>
				            	</li>
				            	<li role="separator" class="divider">
				            	</li>
				            	<li>
				            		<a id="cerrar_s" href="#">Cerrar Sesión</a>
				            	</li>
				          	</ul>
			        	</li>
			    	</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>
		<?php }?>
	</header>