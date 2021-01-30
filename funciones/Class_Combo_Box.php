<?php

class Combo_Box{
 	//VARIABLES DE LA CLASE
 	private $conector;
	private $nombre_calendario_seleccionado;
 	/**
 	 * FUNCION DE LA CLASE
 	 **/
 	function __construct(){
 	}
 	 public function get($nombre, $activo, $tabla, $mensaje, $al_cambio = 0, $activo_combo='0', $estilo='', $index='', $class='') {
 	 	
		if($class==''){$class='select';}
		
 	 	if ($activo_combo=="disabled") {
			echo'<select class="'.$class.'" style="'.$estilo.'" id="'.$nombre.'" name="'.$nombre.'" onChange="'.$al_cambio.'" disabled="'.$activo_combo.'" tabindex="'.$index.'">';
		}
		else{
		    echo'<select class="'.$class.'" style="'.$estilo.'" id="'.$nombre.'" name="'.$nombre.'" onChange="'.$al_cambio.'" tabindex="'.$index.'">';	
		}
		if ($mensaje != ''){
		  echo'<option class="dos" value="">'.$mensaje.'</option>';
		} 
		$i=0;
		$estilo='uno';
 	 	while (@$tabla[$i][0] || @$tabla[$i][0]=="0") {
 	 		$id=$tabla[$i][0];
 	 		$nombre_valor=$tabla[$i][1];
			if ($id==$activo) {
 	 			echo'<option class="'.$estilo.'" value="'.$id.'" selected="selected">'.$nombre_valor.'</option>';
 	 		} else {
 	 			echo'<option class="'.$estilo.'" value="'.$id.'">'.$nombre_valor.'</option>';
 	 		}
 	 		$i++;
 	 		
 	 		if ($estilo=='uno') {
				$estilo='dos';
			} else {
				$estilo='uno';
			}
 	 	}
 	 	echo"</select>";
 	 }
	 
	 
	 public function getComboDb($nombre, $activo, $tabla, $campos, $mensaje, $al_cambio = 0, $activo_combo='1', $estilo='', $index='', $class='') {
 	 		
		if($class==''){$class='select';}	
 	 	if ($activo_combo=='0') {
 	 		echo'<select class="'.$class.'" style="'.$estilo.'" id="'.$nombre.'" name="'.$nombre.'" onChange="'.$al_cambio.'" disabled="disabled" tabindex="'.$index.'">';
		}
		else{
		    echo'<select class="'.$class.'" style="'.$estilo.'" id="'.$nombre.'" name="'.$nombre.'" onChange="'.$al_cambio.'" tabindex="'.$index.'">';	
		}
		if ($mensaje != ''){
		  echo'<option class="dos" value="">'.$mensaje.'</option>';
		} 
		$i=0;
		$estilo='uno';
		$lista_campos = explode(",",$campos);
		
 	 	while (@$tabla[$i][$lista_campos[0]] || @$tabla[$i][$lista_campos[0]]=="0") {
 	 		$id=$tabla[$i][$lista_campos[0]];
			
			$nombre_valor = '';
			$cant_campos=count($lista_campos);
			for ($j = 0; $j <= $cant_campos - 1; $j++){
				if($j>0){
				  $campo=trim($lista_campos[$j]);
				  if($j==$cant_campos-1){
					 $nombre_valor=$nombre_valor.$tabla[$i][$campo];
				  }
				  else{
					 $nombre_valor=$nombre_valor.$tabla[$i][$campo].' - ';
				  }
				}
			}
			if ($id==$activo) {
 	 			echo'<option class="'.$estilo.'" value="'.$id.'" selected="selected">'.$nombre_valor.'</option>';
 	 		} else {
 	 			echo'<option class="'.$estilo.'" value="'.$id.'">'.$nombre_valor.'</option>';
 	 		}
 	 		$i++;
 	 		
 	 		if ($estilo=='uno') {
				$estilo='dos';
			} else {
				$estilo='uno';
			}
 	 	}
 	 	echo"</select>";
 	 }
	 
	 
	 
 	 
     public function get_grupo($nombre, $activo, $tabla_grupo, $tabla, $mensaje, $al_cambio = 0, $activo_combo='0'){
 	 	
 	 	if ($activo_combo == "disabled"){
			echo"<SELECT id='$nombre' name=\"$nombre\" onChange='$al_cambio' disabled=\"$activo_combo\" >";
		}
		else{
		    echo"<SELECT id='$nombre' name=\"$nombre\" onChange='$al_cambio' >";	
		}
		if ($mensaje != ''){
		  echo'<option value=0> '.$mensaje.'  ';
		} 
		foreach($tabla_grupo as $fila_grupo)
		{
		     $id_grupo = $fila_grupo[0];
		     $nombre_grupo = $fila_grupo[1];
             echo"<optgroup label='$nombre_grupo'>";
		    foreach($tabla as $fila_tabla)
		    {
		        $id=$fila_tabla[0];
	 	 		$nombre_valor=$fila_tabla[1];
	 	 		$id_subgrupo=$fila_tabla[2];
	 	 		if($id_subgrupo == $id_grupo)
	 	 		{
		            if($id==$activo){
		 	 			echo"<OPTION VALUE=\"$id\" selected='selected'>$nombre_valor<br>";
		 	 		}
		 	 		else{
		 	 			echo"<OPTION VALUE=\"$id\">$nombre_valor <br>";
		 	 		}
	 	 		}	
		    }
             echo"</optgroup>";
		}
 	 	echo"</SELECT>";
 	 }
 	
 }
?>