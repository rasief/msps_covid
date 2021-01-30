<?php
	@$nombreArchivo = $_GET["nombre_archivo"];
	
	$nombreAux = $nombreArchivo;
	$posAux = strrpos($nombreArchivo, "/");
	if ($posAux !== false) {
		$nombreAux = substr($nombreArchivo, $posAux + 1);
	}
	
	header("Content-disposition: attachment; filename=".$nombreAux);
	header("Content-type: application/pdf");
	readfile("../".$nombreArchivo);
?>