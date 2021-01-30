<?php

require_once("DbConexion.php");

class DbListas extends DbConexion {//Clase que hace referencia a la tabla: listas_detalle

    public function getListaDetalles($idLista) {
        try {
            $sql = "SELECT id_detalle, codigo_detalle, nombre_detalle, orden " .
                    "FROM listas_detalle " .
                    "WHERE id_lista=" . $idLista . " " .
                    "ORDER BY orden";
			

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //Esta funcion me retorna los valores desde id_detalle 3 hasta ide_detalle 10
    public function getTipodocumento() {
        try {
            $sql = "SELECT *, nombre_detalle FROM listas_detalle 
                    WHERE id_detalle BETWEEN 3 AND 10;";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    //Esta funcion retorna los valores con id_lista = 6 
    public function getListaEtnia() {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_lista = 6";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    //Esta funcion retorna los valores con id_lista = 5 
    public function getListaZona() {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_lista = 5";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    //Esta funcion retorna los valores con id_lista = 5 
    public function getListaTipoSangre($idTipoSangre) {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_lista = $idTipoSangre";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
    //Esta funcion retorna los valores con id_lista = 1 
    public function getListaRh($idLista) {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_lista = $idLista";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    //Esta funcion retorna los valores con id_lista = 1 
    public function getTipoSexo() {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_lista = 1";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
    
    //Esta funcion retorna los valores con id_lista = 1 
    public function getListaDesplazado() {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_lista = 10";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
	
	
    public function getDetalle($id_detalle) {
        try {
            $sql = "SELECT * 
                    FROM listas_detalle
                    WHERE id_detalle = $id_detalle";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    /*Lista editasbles*/
    
     public function getListaDetallesEditabel($idLista) {
        try {
            $sql = "SELECT id_listas_editable_detalle, codigo_lista_editable_detalle, nombre_lista_editable_detalle, estado_lista_editable_detalle " .
                    "FROM listas_editable_detalle " .
                    "WHERE id_lista_editable=" . $idLista . " " .
                    "AND estado_lista_editable_detalle = 1 " .
                    "ORDER BY CAST(codigo_lista_editable_detalle AS SIGNED), estado_lista_editable_detalle ";            		

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getListaDetallesEditabelTodos($idLista) {
        try {
            $sql = "SELECT id_listas_editable_detalle, codigo_lista_editable_detalle, nombre_lista_editable_detalle, estado_lista_editable_detalle " .
                    "FROM listas_editable_detalle " .
                    "WHERE id_lista_editable=" . $idLista . " " .
                    "ORDER BY CAST(codigo_lista_editable_detalle AS SIGNED), estado_lista_editable_detalle ";            		

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    public function getListaProgramasProductiva($idLista) {
        try {
            $sql = "SELECT id_listas_editable_detalle, codigo_lista_editable_detalle, nombre_lista_editable_detalle, estado_lista_editable_detalle " .
                    "FROM listas_editable_detalle " .
                    "WHERE id_lista_editable=" . $idLista . " " .
                    "AND estado_lista_editable_detalle = 1 " .
                    "AND etapa_productiva = 1 " .
                    "ORDER BY CAST(codigo_lista_editable_detalle AS SIGNED), estado_lista_editable_detalle ";            		

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    public function getListaProgramasProductivaTodos($idLista) {
        try {
            $sql = "SELECT id_listas_editable_detalle, codigo_lista_editable_detalle, nombre_lista_editable_detalle, estado_lista_editable_detalle " .
                    "FROM listas_editable_detalle " .
                    "WHERE id_lista_editable=" . $idLista . " " .
                    "AND etapa_productiva = 1 " .
                    "ORDER BY CAST(codigo_lista_editable_detalle AS SIGNED), estado_lista_editable_detalle ";            		

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
    
    public function getListaDetallesEditabelTotal($idLista) {
        try {
            $sql = "SELECT id_listas_editable_detalle, codigo_lista_editable_detalle, nombre_lista_editable_detalle, estado_lista_editable_detalle " .
                    "FROM listas_editable_detalle " .
                    "WHERE id_lista_editable=" . $idLista . " " .                    
                    "ORDER BY CAST(codigo_lista_editable_detalle AS SIGNED), estado_lista_editable_detalle ";            		
            
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
    public function getListaEstadoCertificacion() {
        try {
            $sql = "SELECT codigo_lista_editable_detalle, nombre_lista_editable_detalle, estado_lista_editable_detalle " .
                    "FROM listas_editable_detalle " .
                    "WHERE id_lista_editable= 5 " .                    
                    "ORDER BY CAST(codigo_lista_editable_detalle AS SIGNED), estado_lista_editable_detalle ";            		
            
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
     public function getItemListaEditable($id_lista) {
        try {
           $sql = "SELECT * FROM listas_editable where id_lista_editable = ".$id_lista;            		
           
           //echo $sql;

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
    public function getListaEditable() {
        try {
            $sql = "SELECT id_lista_editable, nombre_lista_editable FROM listas_editable ";            		
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    public function getItemListaDetalleEditable($id_lista_detalle) {
        try {
           $sql = "SELECT * FROM listas_editable_detalle where id_listas_editable_detalle = ".$id_lista_detalle." order by CAST(codigo_lista_editable_detalle AS SIGNED)";            		
           
           return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    
    
    public function InsertItemLista($txt_codigo, $txt_nombre, $cmb_estado, $cmb_lista_editable, $id_listas_detalle, $txt_abreviatura, $cmb_productiva, $id_usuario_crea) {

        try {
            $sql = "CALL pa_crear_editar_lista_item('".$txt_codigo."', '".$txt_nombre."', '".$cmb_estado."', '".$cmb_lista_editable."', '".$id_listas_detalle."', '".$txt_abreviatura."', '".$cmb_productiva."', $id_usuario_crea, 1, @id)";  
            //echo $sql;            
            $arrCampos[0] = "@id";
            $arrResultado = $this->ejecutarSentencia($sql, $arrCampos);
            $id_registro_creado = $arrResultado["@id"];            
            return $id_registro_creado;                          
        } catch (Exception $e) {
            return -2;
        }
    }
    
    public function EditarItemLista($txt_codigo, $txt_nombre, $cmb_estado, $cmb_lista_editable, $id_listas_detalle, $txt_abreviatura, $cmb_productiva, $id_usuario_crea) {

        try {
            $sql = "CALL pa_crear_editar_lista_item('".$txt_codigo."', '".$txt_nombre."', '".$cmb_estado."', '".$cmb_lista_editable."', '".$id_listas_detalle."', '".$txt_abreviatura."', '".$cmb_productiva."', $id_usuario_crea, 2, @id)";  
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
