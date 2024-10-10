<!DOCTYPE html>
<html lang='es'>
<head>
	<meta charset="UTF-8">
	<title><?= $tituloPagina ?></title>
</head>
<body>
<div id="contenedor">

<?php require('/TFG/Vista/plantillas/comun/cabecera.php');?>

<?= $contenidoPrincipal ?>

<?php require('/TFG/Vista/plantillas/comun/footer.php');?>

</div>
</body>
</html>