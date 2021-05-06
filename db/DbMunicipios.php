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

    public function getListaDistritosClasificacion() {
        try {
            $sql = "SELECT * FROM distritos_clasificacion
                    ORDER BY nom_dep, nom_mun";
            
            //echo($sql . "<br>");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getListaMunicipiosPoblacion($anio) {
        try {
            $sql = "SELECT M.*, D.nom_dep, IFNULL(PM.cantidad, 0) AS poblacion
                    FROM municipios M
                    INNER JOIN departamentos D ON M.cod_dep=D.cod_dep
                    LEFT JOIN poblacion_municipio PM ON M.cod_mun_dane=PM.cod_municipio AND PM.anio=" . $anio . "
                    ORDER BY M.cod_mun_dane";
           
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getListaDepartamentosPoblacion($anio) {
        try {
            $sql = "SELECT D.*, IFNULL(PD.cantidad, 0) AS poblacion
                    FROM departamentos D
                    LEFT JOIN poblacion_departamento PD ON D.cod_dep=PD.cod_departamento AND PD.anio=" . $anio . "
                    ORDER BY D.cod_dep";
           
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function getListaMunicipiosMayusc() {
        try {
            $sql = "SELECT M.cod_mun_dane, UPPER(D.nom_dep) AS nom_dep, UPPER(M.nom_mun) AS nom_mun
                    FROM municipios M
                    INNER JOIN departamentos D ON M.cod_dep=D.cod_dep
                    ORDER BY M.cod_mun_dane";
           
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
}
