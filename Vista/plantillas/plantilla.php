<?php 
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if (isset($_SERVER['REQUEST_URI'])) { 
    $_SESSION['url_anterior'] = $_SERVER['REQUEST_URI']; 
}



?>

<!DOCTYPE html>
<html lang='es'>
<head>
	<meta charset="UTF-8">
	<meta name="usuario" content="<?php echo isset($_SESSION['nick']) ? $_SESSION['nick'] : null; ?>">
	<link rel="stylesheet" type="text/css" href="../Recursos/css/estilo.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<script src="http://localhost:3000/socket.io/socket.io.js"></script> 
	<script src="../Recursos/js/socket.js"></script>
	<title><?= $tituloPagina ?></title>
</head>
<body>

<?php 
require('comun/cabecera.php');

?>

<div id="contenedor">
	<div id="notificaciones"></div>
	<?= $contenidoPrincipal ?>
</div>

<?php require('comun/footer.php');?>

</body>
</html>