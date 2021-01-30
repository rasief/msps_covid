<?php

require_once("DbConexion.php");

class DbFormatos extends DbConexion {//Clase que hace referencia a la tabla: formatos

    
    /*Lista editasbles*/
    
     public function getListaFormatos() {
        try {
            $sql = "SELECT *, DATE_FORMAT(fecha_formato, '%d/%m/%Y') AS format_fecha_formato from formatos_cartas";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
     public function getFomato($id_formato) {
        try {
           $sql = "SELECT *, DATE_FORMAT(fecha_formato, '%d/%m/%Y') AS format_fecha_formato from formatos_cartas where id = ".$id_formato;            		
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    public function EditarFormato($nombre_formato, $codigo_formato, $version_formato, $fecha_formato, $id_formato, $id_usuario_crea) {

        try {
            //
            $sql = "CALL pa_crear_editar_formatos('".$nombre_formato."', '".$codigo_formato."', '".$version_formato."', STR_TO_DATE('".$fecha_formato."', '%d/%m/%Y'), '".$id_formato."', $id_usuario_crea, 2, @id)";  
            //echo $sql;            
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_registro_creado = $arrResultado["@id"];            
            return $id_registro_creado;                       
        } catch (Exception $e) {
            return -2;
        }
    }
    
    
    

}

?>
