<?php
 	$ruta = utf8_decode($_GET["ruta"]);
	$nombre = utf8_decode($_GET["nombre_arch"]);
	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: inline; filename=".$nombre);
	//header("Content-Transfer-Encoding: binary");
	header("Accept-Ranges: bytes");
	
	readfile($ruta);
?>
