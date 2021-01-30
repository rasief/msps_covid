<?php

require_once("DbConexion.php");

class DbInformes extends DbConexion {
	
	
    
    public function guardarDatosInformes($array_datos_insert) {	
	 
		$insert="INSERT INTO casos_covid_colombia (id_caso, fecha_notificacion, codigo_divipola, ciudad_ubicacion, departamento_distrito, atencion, edad, sexo, tipo, estado, pais_procedencia, fecha_inicio_sintomas, fecha_muerte, fecha_diagnostico, fecha_recuperado, fecha_reporte_web, tipo_recuperacion, codigo_departamento, codigo_pais, pertenencia_etnica, nombre_grupo_etnico, ubicacion_recuperado, fecha_cargue_archivo, nombre_archivo)
                                                    VALUES ";	
		$cont=0;	
		$sql_insert="";
		//substr('2020-03-02T00:00:00.000', 0, 10);
		try {
			
                    foreach($array_datos_insert as $fila_insert){

                            $id_caso = $fila_insert['id_caso'];
                            
                            if($fila_insert['fecha_notificacion'] == ''){
                                $fecha_notificacion = 'NULL';
                            }else{
                                $fecha_notificacion = "'".substr($fila_insert['fecha_notificacion'], 0, 10)."'";
                            }
                            
                            
                            $codigo_divipola = $fila_insert['codigo_divipola'];
                            $ciudad_ubicacion = $fila_insert['ciudad_ubicacion'];
                            $departamento_distrito = $fila_insert['departamento_distrito'];
                            $atencion = $fila_insert['atencion'];
                            $edad = $fila_insert['edad'];
                            $sexo = $fila_insert['sexo'];
                            $tipo = $fila_insert['tipo'];
                            $estado = $fila_insert['estado'];
                            $pais_procedencia = $fila_insert['pais_procedencia'];
                            
                            if($fila_insert['fecha_inicio_sintomas'] == ''){
                                $fecha_inicio_sintomas = 'NULL';
                            }
                            else{
                                $fecha_inicio_sintomas = "'".substr($fila_insert['fecha_inicio_sintomas'], 0, 10)."'";
                            }
                            
                            if($fila_insert['fecha_muerte'] == ''){
                                $fecha_muerte = 'NULL';
                            }
                            else{
                                $fecha_muerte = "'".substr($fila_insert['fecha_muerte'], 0, 10)."'";
                            }
                            
                            if($fila_insert['fecha_diagnostico'] == ''){
                                $fecha_diagnostico = 'NULL';
                            }
                            else{
                                $fecha_diagnostico = "'".substr($fila_insert['fecha_diagnostico'], 0, 10)."'";
                            }
                            
                            if($fila_insert['fecha_recuperado'] == ''){
                                $fecha_recuperado = 'NULL';
                            }
                            else{
                                $fecha_recuperado = "'".substr($fila_insert['fecha_recuperado'], 0, 10)."'";
                            }
                            
                            if($fila_insert['fecha_reporte_web'] == ''){
                                $fecha_reporte_web = 'NULL';
                            }
                            else{
                                $fecha_reporte_web = "'".substr($fila_insert['fecha_reporte_web'], 0, 10)."'";
                            }
                            
                            $tipo_recuperacion = $fila_insert['tipo_recuperacion'];
                            $codigo_departamento = $fila_insert['codigo_departamento'];
                            if($fila_insert['codigo_pais'] == ''){
                                $codigo_pais = '0';
                            }
                            else{
                                $codigo_pais = $fila_insert['codigo_pais'];
                            }
                            
                            $pertenencia_etnica = $fila_insert['pertenencia_etnica'];
                            $nombre_grupo_etnico = $fila_insert['nombre_grupo_etnico'];
                            $ubicacion_recuperado = $fila_insert['ubicacion_recuperado'];
                            $fecha_cargue_archivo = $fila_insert['fecha_cargue_archivo'];
                            $nombre_archivo = $fila_insert['nombre_archivo'];
                            
                            

                            if ($sql_insert != "") {
                                    $sql_insert.=", ";
                            }
                            $sql_insert.="('".$id_caso."', $fecha_notificacion, '".$codigo_divipola."', '".$ciudad_ubicacion."', '".$departamento_distrito."', '".$atencion."', '".$edad."', '".$sexo."', '".$tipo."', '".$estado."', '".$pais_procedencia."', $fecha_inicio_sintomas, $fecha_muerte, $fecha_diagnostico, $fecha_recuperado, $fecha_reporte_web, '".$tipo_recuperacion."', '".$codigo_departamento."', '".$codigo_pais."', '".$pertenencia_etnica."', '".$nombre_grupo_etnico."', '".$ubicacion_recuperado."', '".$fecha_cargue_archivo."', '".$nombre_archivo."' )";
                            $cont++;
                    }


                    $sql_insert = $insert.$sql_insert;
                    $arrCamposInsert[0] = "@id";
                    if ($this->ejecutarSentencia($sql_insert, $arrCamposInsert)) {
                            $id_insert = 1;
                    } else {
                            $id_insert = 0;				
                    }

                    //echo $sql_insert;

            return $id_insert;
        } catch (Exception $e) {
            return array();
        }
    }	
    
    
    public function getListaInformes() {
            try {
                $sql = "SELECT c.fecha_cargue_archivo, c.nombre_archivo, COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        GROUP BY c.fecha_cargue_archivo, c.nombre_archivo";

                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    public function getDatosSintomas($dato_fecha) {
            try {
                $sql = "SELECT tipo_estado, fecha_notificacion, SUM(cantidad) AS cantidad
                        FROM (
                            SELECT
                            CASE
                                WHEN c.fecha_inicio_sintomas IS NULL THEN 'ASINTOMATICOS'
                                ELSE 'SINTOMATICOS'
                            END AS tipo_estado,
                            IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion) AS fecha_notificacion,
                            COUNT(*) AS cantidad
                            FROM casos_covid_colombia c
                            WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                            GROUP BY tipo_estado, IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion)
                            UNION ALL
                            SELECT 'SINTOMATICOS', a.Date, 0
                            FROM (
                                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                            ) a
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('".$dato_fecha."')
                        ) T
                        GROUP BY tipo_estado, fecha_notificacion
                        ORDER BY fecha_notificacion, tipo_estado";
                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    public function getDatosSintomasMunicipal($dato_fecha, $cod_mun) {
            try {
                $sql = "SELECT tipo_estado, fecha_notificacion, SUM(cantidad) AS cantidad
                        FROM (
                            SELECT
                            CASE
                                WHEN c.fecha_inicio_sintomas IS NULL THEN 'ASINTOMATICOS'
                                ELSE 'SINTOMATICOS'
                            END AS tipo_estado,
                            IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion) AS fecha_notificacion,
                            COUNT(*) AS cantidad
                            FROM casos_covid_colombia c
                            WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                            AND c.codigo_divipola = '".$cod_mun."'
                            GROUP BY tipo_estado, IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion)
                            UNION ALL
                            SELECT 'SINTOMATICOS', a.Date, 0
                            FROM (
                                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                            ) a
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('".$dato_fecha."')
                        ) T
                        GROUP BY tipo_estado, fecha_notificacion
                        ORDER BY fecha_notificacion, tipo_estado";
                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    
    public function getDatosMuertes($dato_fecha) {
            try {
                $sql = "SELECT fecha_muerte, SUM(cantidad) AS cantidad
                        FROM (
                            SELECT c.fecha_muerte, COUNT(*) AS cantidad
                            FROM casos_covid_colombia c
                            WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                            AND c.atencion = 'Fallecido'
                            GROUP BY c.atencion, c.fecha_muerte
                            UNION ALL
                            SELECT a.Date, 0
                            FROM (
                                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                            ) a
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('".$dato_fecha."')
                        ) T
                        GROUP BY fecha_muerte
                        ORDER BY fecha_muerte";
                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    public function getDatosMuertesMunicipal($dato_fecha, $cod_mun) {
            try {
                $sql = "SELECT fecha_muerte, SUM(cantidad) AS cantidad
                        FROM (
                            SELECT c.fecha_muerte, COUNT(*) AS cantidad
                            FROM casos_covid_colombia c
                            WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                            AND c.atencion = 'Fallecido'
                            AND c.codigo_divipola = '".$cod_mun."'
                            GROUP BY c.atencion, c.fecha_muerte
                            UNION ALL
                            SELECT a.Date, 0
                            FROM (
                                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                            ) a
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('".$dato_fecha."')
                        ) T
                        GROUP BY fecha_muerte
                        ORDER BY fecha_muerte";
                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    
    public function getDatosMuertesGrupoEdad($dato_fecha) {
            try {
                $sql = "SELECT 
                        CASE
                            WHEN c.sexo = 'M' THEN 'Hombres'
                                 WHEN c.sexo = 'F' THEN 'Mujeres'
                            ELSE 'SIN DATO'
                        END AS nombre_sexo,
                        CASE
                            WHEN c.edad >= 95 THEN 'Mayor 95'
                            WHEN c.edad >= 90 THEN '90 - 94'
                            WHEN c.edad >= 85 THEN '85 - 89'
                            WHEN c.edad >= 80 THEN '80 - 84'
                            WHEN c.edad >= 75 THEN '75 - 79'
                            WHEN c.edad >= 70 THEN '70 - 74'
                            WHEN c.edad >= 65 THEN '65 - 69'
                            WHEN c.edad >= 60 THEN '60 - 64'
                            WHEN c.edad >= 55 THEN '55 - 59'
                            WHEN c.edad >= 50 THEN '50 - 54'
                            WHEN c.edad >= 45 THEN '45 - 49'
                            WHEN c.edad >= 40 THEN '40 - 45'
                            WHEN c.edad >= 35 THEN '35 - 39'
                            WHEN c.edad >= 30 THEN '30 - 34'
                            WHEN c.edad >= 25 THEN '25 - 29'
                            WHEN c.edad >= 20 THEN '20 - 24'
                            WHEN c.edad >= 15 THEN '15 - 19'
                            WHEN c.edad >= 10 THEN '10 - 14'
                            WHEN c.edad >= 5 THEN '5 - 9'
                            WHEN c.edad >= 0 THEN '0 - 4'
                            ELSE 'SIN DATO'
                        END AS grupo_edad,

                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND c.atencion = 'Fallecido'
                        GROUP BY nombre_sexo, grupo_edad
                        ORDER by c.edad desc";
                //echo $sql;
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getDatosContagiosGrupoEdad($dato_fecha) {
            try {
                $sql = "SELECT 
                        CASE
                            WHEN c.sexo = 'M' THEN 'Hombres'
                                 WHEN c.sexo = 'F' THEN 'Mujeres'
                            ELSE 'SIN DATO'
                        END AS nombre_sexo,
                        CASE
                            WHEN c.edad >= 95 THEN 'Mayor 95'
                            WHEN c.edad >= 90 THEN '90 - 94'
                            WHEN c.edad >= 85 THEN '85 - 89'
                            WHEN c.edad >= 80 THEN '80 - 84'
                            WHEN c.edad >= 75 THEN '75 - 79'
                            WHEN c.edad >= 70 THEN '70 - 74'
                            WHEN c.edad >= 65 THEN '65 - 69'
                            WHEN c.edad >= 60 THEN '60 - 64'
                            WHEN c.edad >= 55 THEN '55 - 59'
                            WHEN c.edad >= 50 THEN '50 - 54'
                            WHEN c.edad >= 45 THEN '45 - 49'
                            WHEN c.edad >= 40 THEN '40 - 45'
                            WHEN c.edad >= 35 THEN '35 - 39'
                            WHEN c.edad >= 30 THEN '30 - 34'
                            WHEN c.edad >= 25 THEN '25 - 29'
                            WHEN c.edad >= 20 THEN '20 - 24'
                            WHEN c.edad >= 15 THEN '15 - 19'
                            WHEN c.edad >= 10 THEN '10 - 14'
                            WHEN c.edad >= 5 THEN '5 - 9'
                            WHEN c.edad >= 0 THEN '0 - 4'
                            ELSE 'SIN DATO'
                        END AS grupo_edad,
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        GROUP BY nombre_sexo, grupo_edad
                        ORDER by c.edad desc";
                //echo $sql;
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getDatosGenerales($dato_fecha) {
            try {
                $sql = "SELECT 
                        CASE
                            WHEN c.atencion IN('Casa', 'Hospital', 'Hospital UCI' ) THEN 'Activos'
                            WHEN c.atencion IN('Recuperado') THEN 'Recuperados'   
                            WHEN c.atencion IN('Fallecido') THEN 'Fallecidos'   
                            ELSE 'Otros'
                        END AS tipo_atencion,
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        GROUP BY tipo_atencion";
                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getTasaContagioDpto($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        GROUP BY p.nom_departamento, c.codigo_departamento
                        ORDER BY tasa desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getTasaContagioMuni($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        GROUP BY p.nom_municipio, c.codigo_divipola
                        ORDER BY tasa  desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    
    public function getTasaMortaDpto($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        AND c.atencion = 'Fallecido'
                        GROUP BY p.nom_departamento, c.codigo_departamento
                        ORDER BY tasa desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getTasaMortaMuni($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        AND c.atencion = 'Fallecido'
                        GROUP BY p.nom_municipio, c.codigo_divipola
                        ORDER BY tasa  desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    
    public function getTasaContagioDptoMes($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        AND c.fecha_notificacion BETWEEN DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 45 DAY) AND DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 15 DAY)
                        GROUP BY p.nom_departamento, c.codigo_departamento
                        ORDER BY tasa desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getTasaContagioMuniMes($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        AND c.fecha_notificacion BETWEEN DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 45 DAY) AND DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 15 DAY)
                        GROUP BY p.nom_municipio, c.codigo_divipola
                        ORDER BY tasa  desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    
    public function getTasaMortaDptoMes($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        AND c.atencion = 'Fallecido'
                        AND c.fecha_muerte BETWEEN DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 45 DAY) AND DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 15 DAY)
                        GROUP BY p.nom_departamento, c.codigo_departamento
                        ORDER BY tasa desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getTasaMortaMuniMes($dato_fecha) {
            try {
                $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND((COUNT(*)/ p.cantidad) * 100000, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE c.fecha_cargue_archivo = '".$dato_fecha."'
                        AND p.anio = 2020
                        AND c.atencion = 'Fallecido'
                        AND c.fecha_muerte BETWEEN DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 45 DAY) AND DATE_SUB(date(c.fecha_cargue_archivo),INTERVAL 15 DAY)
                        GROUP BY p.nom_municipio, c.codigo_divipola
                        ORDER BY tasa  desc
                        LIMIT 20";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    public function getListaMunicipios() {
            try {
                $sql = "SELECT * FROM lista_municipios_especiales ORDER BY id";                
                return $this->getDatos($sql);
            } catch (Exception $e) {
                    return array();
            }
    }
    
    
    
    
    
    
    
    
    
    
}

?>