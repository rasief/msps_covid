<?php

require_once("DbConexion.php");

class DbMunicipios extends DbConexion {//Clase que hace referencia a la tabla: listas_detalle

    public function getListaDepartamentos() {
        try {
            $sql = "SELECT * FROM departamentos
                    ORDER BY cod_dep";
           
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getListaMunicipiosDepartamento($codDep) {
        try {
            $sql = "SELECT * " .
                    "FROM municipios " .
                    "WHERE cod_dep=" . $codDep . " " .
                    "ORDER BY cod_mun_dane";
           
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getDepartamento($cod_dep) {
        try {
            $sql = "SELECT * FROM departamentos
                    WHERE cod_dep=" . $cod_dep;
           
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getMunicipio($cod_mun_dane) {
        try {
            $sql = "SELECT * FROM municipios
                    WHERE cod_mun_dane=" . $cod_mun_dane;
           
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getPoblacionMunicipio($cod_mun, $anio) {
        try {
            $sql = "SELECT *
                    FROM poblacion_municipio
                    WHERE anio=" . $anio . "
                    AND cod_municipio=" . $cod_dep;
            
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getPoblacionDepartamento($cod_dep, $anio) {
        try {
            $sql = "SELECT *
                    FROM poblacion_departamento
                    WHERE anio=" . $anio . "
                    AND cod_departamento=" . $cod_dep;
            
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getPoblacionPais($anio) {
        try {
            $sql = "SELECT SUM(cantidad) AS cantidad
                    FROM poblacion_municipio
                    WHERE anio=" . $anio;
            
            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

}
