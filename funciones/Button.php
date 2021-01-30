<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Button
 *
 * @author Diana
 */
require_once("../principal/ContenidoHtml.php");

class Button {

    public function getButton($tipoAcceso, $valor, $type, $id, $class, $onclick) {
        $contenido = new ContenidoHtml();
        $tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

        $visible = true;

        switch ($tipoAcceso) {
            case 2://Comprueba acceso del tipo completo
                if ($tipo_acceso_menu != 2) {
                    $visible = false; //Niega la visualización
                }
                break;
        }

        if ($visible) {//Indicador de visibilidad
            ?>
            <button type="<?php echo $type; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>" onclick="<?php echo $onclick; ?>"><?php echo $valor; ?></button>
            <?php
        }
    }

}
