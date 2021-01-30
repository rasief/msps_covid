<?php
	function ajustarCaracteres($cadena) {
		$cadena = str_replace("á", chr(225), $cadena);
		$cadena = str_replace("Á", chr(193), $cadena);
		$cadena = str_replace("é", chr(233), $cadena);
		$cadena = str_replace("É", chr(201), $cadena);
		$cadena = str_replace("í", chr(237), $cadena);
		$cadena = str_replace("Í", chr(205), $cadena);
		$cadena = str_replace("ó", chr(243), $cadena);
		$cadena = str_replace("Ó", chr(211), $cadena);
		$cadena = str_replace("ú", chr(250), $cadena);
		$cadena = str_replace("Ú", chr(218), $cadena);
		$cadena = str_replace("ü", chr(252), $cadena);
		$cadena = str_replace("Ü", chr(220), $cadena);
		$cadena = str_replace("ñ", chr(241), $cadena);
		$cadena = str_replace("Ñ", chr(209), $cadena);
		$cadena = str_replace("&iquot;", chr(34), $cadena);
		$cadena = str_replace("&iexcl;", chr(161), $cadena);
		$cadena = str_replace("¿", chr(34), $cadena);
		$cadena = str_replace("¡", chr(161), $cadena);
		$cadena = html_entity_decode($cadena, ENT_QUOTES, "ISO8859-1");
		
		return $cadena;
	}
?>
