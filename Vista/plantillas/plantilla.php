<!DOCTYPE html>
<html lang='es'>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="../Recursos/css/estilo.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<title><?= $tituloPagina ?></title>
</head>
<body>

<?php require('comun/cabecera.php');?>

<div id="contenedor">
	<?= $contenidoPrincipal ?>
</div>

<?php require('comun/footer.php');?>

</body>
</html>