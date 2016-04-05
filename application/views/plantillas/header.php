<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $titulo ?></title>
	<link rel="stylesheet" href="/inventarios/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/inventarios/assets/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/inventarios/assets/css/inventarios.css">
	<script src="/inventarios/assets/js/jquery-2.2.3.min.js"></script>
	<script src="/inventarios/assets/js/bootstrap.min.js"></script>
	<script src="/inventarios/assets/js/inventarios.js"></script>
</head>
<body>
	<header>
		<?php if(!$login){ ?>
			<h1>HEADER</h1>
		<?php }?>
	</header>