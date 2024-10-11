<!DOCTYPE html>
<html lang='es'>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="/css/estilo.css" />
	<title><?= $tituloPagina ?></title>
</head>
<body>
<div id="contenedor">

<?php require('comun/cabecera.php');?>

<?= $contenidoPrincipal ?>

<?php require('comun/footer.php');?>

</div>
</body>
</html>