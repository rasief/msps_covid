<?php
	class Barra_Progreso {
		function __construct(){
		}
		
		public function get($nombre, $ancho = "100%", $visible = true, $porcentaje_inicial = 0) {
			$display_aux = "none";
			if ($visible) {
				$display_aux = "block";
			}
	?>
    <div id="<?php echo($nombre); ?>" name="<?php echo($nombre); ?>" class="div_barra_progreso" style="display:<?php echo($display_aux); ?>; margin:auto; width:<?php echo($ancho); ?>">
        &nbsp;
    	<div id="<?php echo($nombre); ?>_int" name="<?php echo($nombre); ?>_int" class="div_barra_progreso_int" style="width:<?php echo($porcentaje_inicial); ?>%;"><?php echo($porcentaje_inicial); ?>%</div>
    </div>
    <?php
		}
	}
        
        
        
        
        
        
        
?>
