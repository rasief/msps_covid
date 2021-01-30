<?php

//verificamos si hay cambios de lenguaje mediante POST
		
		if(isset($_POST["lang"])){
			$lang = $_POST["lang"];
			if(!empty($lang)){
				$_SESSION["lang"] = $lang;
			}
		}
		// verificamos la sesion creada
		if(isset($_SESSION['lang'])){
			// si es true, se crea el require y la variable lang
			$lang = $_SESSION["lang"];
			require "../lang/".$lang.".php";
			// si no hay sesion por default se carga el lenguaje espanol
		}else{
			require "../lang/es.php";			
		}

?>