<?php
class Utilidades {
    function limpiar_tags($tags) {
        $tags = strip_tags($tags);
        $tags = stripslashes($tags);
        $tags = htmlentities($tags);
		
        return $tags;
    }
	
	function str_encode($texto) {
		$texto_rta = str_replace("+", "|PLUS|", $texto);
		$texto_rta = str_replace(chr(10), "|ENTER|", $texto_rta);
		$texto_rta = str_replace("&", "|AMP|", $texto_rta);
		$texto_rta = str_replace("'", "", $texto_rta);
		$texto_rta = str_replace('"', "|DQUOT|", $texto_rta);
		
		return $texto_rta;
	}
	
	function str_decode($texto) {
		$texto_rta = str_replace("|PLUS|", "+", $texto);
		$texto_rta = str_replace("|ENTER|", chr(10), $texto_rta);
		$texto_rta = str_replace("|AMP|", "&", $texto_rta);
		$texto_rta = str_replace("'", "", $texto_rta);
		$texto_rta = str_replace('"', "", $texto_rta);
		$texto_rta = str_replace("|DQUOT|", '"', $texto_rta);
		
		return $texto_rta;
	}
	
	function get_extension_arch($nombre_arch) {
		$pos_punto = strrpos($nombre_arch, ".", -1);
		$extension = substr($nombre_arch, $pos_punto + 1);
		
		return $extension;
	}
	
	//Funcio para cambiar las variables que llegan por get a post
	function get_a_post(){
		//Cambiar variables get a post
		if($_GET)
		{
		    $keys_get = array_keys($_GET);
		    foreach ($keys_get as $key_get)
		     {
		        $_POST[$key_get] = $_GET[$key_get];
		        error_log("variable $key_get viene desde $ _GET");
		     }
		}
	}
	
	
	
	
}
?>
