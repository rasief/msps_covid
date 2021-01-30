<?php
require_once("DbConexion.php");

class DbDefunciones extends DbConexion {

    public function EliminarDatos() {
        try {
            $sql_delete = "delete from defunciones";
            //echo $sql;					
            $arrCamposInsert[0] = "@id";
            if ($this->ejecutarSentencia($sql_delete, $arrCamposInsert)) {
                $id_insert = 1;
            } else {
                $id_insert = 0;
            }

            return $id_insert;
        } catch (Exception $e) {
            return array();
        }
    }

    public function guardarDatos($array_datos_insert) {
        $insert = "INSERT INTO defunciones (cod_departamento, nom_departamento, cod_municipio, nom_municipio, fecha_def, cantidad)
                                                    VALUES ";
        $cont = 0;
        $sql_insert = "";

        try {
            foreach ($array_datos_insert as $fila_insert) {
                //$fecha_inicio_sintomas = "STR_TO_DATE('".substr($fila_insert['fecha_inicio_sintomas'], 0, strpos($fila_insert['fecha_inicio_sintomas'], " "))."', '%e/%c/%Y')";
                $cod_departamento = $fila_insert['cod_departamento'];
                $nom_departamento = $fila_insert['nom_departamento'];
                $cod_municipio = $fila_insert['cod_municipio'];
                $nom_municipio = $fila_insert['nom_municipio'];
                $dia = "STR_TO_DATE('" . $fila_insert['dia'] . "', '%Y/%m/%d')";
                $cantidad = $fila_insert['cantidad'];
                
                if ($cod_departamento != "" && $cod_municipio != "") {
                    if ($sql_insert != "") {
                        $sql_insert .= ", ";
                    }
                    $sql_insert .= "('" . $cod_departamento . "', '" . $nom_departamento . "', '" . $cod_municipio . "', '" . $nom_municipio . "', " . $dia . ", '" . $cantidad . "')";
                    $cont++;
                }
            }
            
            if ($cont > 0) {
                $sql_insert = $insert . $sql_insert;
                $arrCamposInsert[0] = "@id";
                if ($this->ejecutarSentencia($sql_insert, $arrCamposInsert)) {
                    $id_insert = 1;
                } else {
                    $id_insert = 0;
                    //echo $sql_insert;
                }
            } else {
                $id_insert = 1;
            }

            //echo $sql_insert;
            return $id_insert;
        } catch (Exception $e) {
            return array();
        }
    }

}
?>