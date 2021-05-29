<?php

require_once("DbConexion.php");

class DbInformes extends DbConexion {

    public function guardarDatosInformes($array_datos_insert) {
        $insert = "INSERT INTO casos_covid_colombia (id_caso, fecha_notificacion, codigo_divipola, ciudad_ubicacion, departamento_distrito, atencion, edad, sexo, tipo, estado, pais_procedencia, fecha_inicio_sintomas, fecha_muerte, fecha_diagnostico, fecha_recuperado, fecha_reporte_web, tipo_recuperacion, codigo_departamento, codigo_pais, pertenencia_etnica, nombre_grupo_etnico, ubicacion_recuperado, fecha_cargue_archivo, nombre_archivo)
                 VALUES ";
        $cont = 0;
        $sql_insert = "";
        //substr('2020-03-02T00:00:00.000', 0, 10);
        try {
            foreach ($array_datos_insert as $fila_insert) {
                $id_caso = $fila_insert['id_caso'];

                if ($fila_insert['fecha_notificacion'] == '') {
                    $fecha_notificacion = 'NULL';
                } else {
                    $fecha_notificacion = "STR_TO_DATE('" . substr($fila_insert['fecha_notificacion'], 0, strpos($fila_insert['fecha_notificacion'], " ")) . "', '%e/%c/%Y')";
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

                if ($fila_insert['fecha_inicio_sintomas'] == '') {
                    $fecha_inicio_sintomas = 'NULL';
                } else {
                    $fecha_inicio_sintomas = "STR_TO_DATE('" . substr($fila_insert['fecha_inicio_sintomas'], 0, strpos($fila_insert['fecha_inicio_sintomas'], " ")) . "', '%e/%c/%Y')";
                }

                if ($fila_insert['fecha_muerte'] == '') {
                    $fecha_muerte = 'NULL';
                } else {
                    $fecha_muerte = "STR_TO_DATE('" . substr($fila_insert['fecha_muerte'], 0, strpos($fila_insert['fecha_muerte'], " ")) . "', '%e/%c/%Y')";
                }

                if ($fila_insert['fecha_diagnostico'] == '') {
                    $fecha_diagnostico = 'NULL';
                } else {
                    $fecha_diagnostico = "STR_TO_DATE('" . substr($fila_insert['fecha_diagnostico'], 0, strpos($fila_insert['fecha_diagnostico'], " ")) . "', '%e/%c/%Y')";
                }

                if ($fila_insert['fecha_recuperado'] == '') {
                    $fecha_recuperado = 'NULL';
                } else {
                    $fecha_recuperado = "STR_TO_DATE('" . substr($fila_insert['fecha_recuperado'], 0, strpos($fila_insert['fecha_recuperado'], " ")) . "', '%e/%c/%Y')";
                }

                if ($fila_insert['fecha_reporte_web'] == '') {
                    $fecha_reporte_web = 'NULL';
                } else {
                    $fecha_reporte_web = "STR_TO_DATE('" . substr($fila_insert['fecha_reporte_web'], 0, strpos($fila_insert['fecha_reporte_web'], " ")) . "', '%e/%c/%Y')";
                }

                $tipo_recuperacion = $fila_insert['tipo_recuperacion'];
                $codigo_departamento = $fila_insert['codigo_departamento'];
                if ($fila_insert['codigo_pais'] == '') {
                    $codigo_pais = '0';
                } else {
                    $codigo_pais = $fila_insert['codigo_pais'];
                }

                $pertenencia_etnica = $fila_insert['pertenencia_etnica'];
                $nombre_grupo_etnico = $fila_insert['nombre_grupo_etnico'];
                $ubicacion_recuperado = $fila_insert['ubicacion_recuperado'];
                $fecha_cargue_archivo = $fila_insert['fecha_cargue_archivo'];
                $nombre_archivo = $fila_insert['nombre_archivo'];

                if ($sql_insert != "") {
                    $sql_insert .= ", ";
                }
                $sql_insert .= "('" . $id_caso . "', $fecha_notificacion, '" . $codigo_divipola . "', '" . $ciudad_ubicacion . "', '" . $departamento_distrito . "', '" . $atencion . "', '" . $edad . "', '" . $sexo . "', '" . $tipo . "', '" . $estado . "', '" . $pais_procedencia . "', $fecha_inicio_sintomas, $fecha_muerte, $fecha_diagnostico, $fecha_recuperado, $fecha_reporte_web, '" . $tipo_recuperacion . "', '" . $codigo_departamento . "', '" . $codigo_pais . "', '" . $pertenencia_etnica . "', '" . $nombre_grupo_etnico . "', '" . $ubicacion_recuperado . "', '" . $fecha_cargue_archivo . "', '" . $nombre_archivo . "' )";
                $cont++;
            }

            $sql_insert = $insert . $sql_insert;
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

    public function borrarDatosInformes() {
        try {
            $sql = "TRUNCATE TABLE casos_covid_colombia";

            $arrCampos[0] = "@id";
            if ($this->ejecutarSentencia($sql, $arrCampos)) {
                $id_delete = 1;
            } else {
                $id_delete = 0;
            }

            return $id_delete;
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaInformes() {
        try {
            $sql = "SELECT c.fecha_cargue_archivo, c.nombre_archivo,
                    DATE_FORMAT(c.fecha_cargue_archivo, '%d/%m/%Y %h:%i:%s %p') AS fecha_cargue_archivo_t, COUNT(*) as cantidad
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
                            WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
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
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('" . $dato_fecha . "')
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
                            WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "' ";
            if (strlen($cod_mun) > 2) {
                $sql .= "AND c.codigo_divipola = '" . $cod_mun . "' ";
            } else {
                $sql .= "AND c.codigo_departamento = '" . $cod_mun . "' ";
            }
            $sql .= "GROUP BY tipo_estado, IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion)
                            UNION ALL
                            SELECT 'SINTOMATICOS', a.Date, 0
                            FROM (
                                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                            ) a
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('" . $dato_fecha . "')
                        ) T
                        GROUP BY tipo_estado, fecha_notificacion
                        ORDER BY fecha_notificacion, tipo_estado";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * 
     * @param type $dato_fecha
     * @param type $cod_mun
     * @param type $gedad
     * @return type
     * $gedad = 1 : Menores de 15
     * $gedad = 2 : De 15 - 60
     * $gedad = 3 : Mayores de 60
     */
    public function getDatosSintomasMunicipalGedad($dato_fecha, $cod_mun, $gedad) {
        try {
            $condicion = "";
            switch ($gedad) {
                case "1":
                    $condicion = " AND c.edad < 15 ";
                    break;
                case "2":
                    $condicion = " AND c.edad >= 15 AND c.edad <= 60 ";
                    break;
                case "3":
                    $condicion = " AND c.edad > 60 ";
                    break;
            }

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
                            WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "' ";
            if (strlen($cod_mun) > 2) {
                $sql .= "AND c.codigo_divipola = '" . $cod_mun . "' ";
            } else {
                $sql .= "AND c.codigo_departamento = '" . $cod_mun . "' ";
            }
            $sql .= "$condicion
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
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('" . $dato_fecha . "')
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
                            WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
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
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('" . $dato_fecha . "')
                        ) T
                        GROUP BY fecha_muerte
                        ORDER BY fecha_muerte";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosDefunciones($dato_fecha) {
        try {
            $sql = "SELECT d.fecha_def, sum(d.cantidad) cant_defunciones 
                        FROM defunciones d
                        GROUP BY d.fecha_def
                        ORDER BY d.fecha_def asc";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosDefuncionesMunicipal($dato_fecha, $cod_muni) {
        try {
            $sql = "SELECT d.fecha_def, sum(d.cantidad) cant_defunciones 
                        FROM defunciones d ";
            if (strlen($cod_muni) > 2) {
                $sql .= "WHERE d.cod_municipio = '" . $cod_muni . "' ";
            } else {
                $sql .= "WHERE d.cod_departamento = '" . $cod_muni . "' ";
            }
            $sql .= "GROUP BY d.fecha_def
                        ORDER BY d.fecha_def asc";

            //echo($sql);
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
                            WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                            AND c.atencion = 'Fallecido' ";
            if (strlen($cod_mun) > 2) {
                $sql .= "AND c.codigo_divipola = '" . $cod_mun . "' ";
            } else {
                $sql .= "AND c.codigo_departamento = '" . $cod_mun . "' ";
            }
            $sql .= "GROUP BY c.atencion, c.fecha_muerte
                            UNION ALL
                            SELECT a.Date, 0
                            FROM (
                                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                            ) a
                            WHERE a.Date BETWEEN '2020-03-01' AND DATE('" . $dato_fecha . "')
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
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.atencion = 'Fallecido'
                        GROUP BY nombre_sexo, grupo_edad
                        ORDER by c.edad desc";
            //echo $sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosMuertesGrupoEdadMunicipal($dato_fecha, $cod_mun) {
        try {
            $sql = "SELECT s.nombre_detalle AS nombre_sexo, e.nombre_detalle AS grupo_edad, IFNULL(cantidad, 0) AS cantidad
                        FROM listas_detalle s
                        CROSS JOIN listas_detalle e
                        LEFT JOIN (
                        SELECT
                        CASE
                            WHEN c.sexo = 'M' THEN 'Hombres'
                            WHEN c.sexo = 'F' THEN 'Mujeres'
                            ELSE 'Sin dato'
                        END AS nombre_sexo,
                        CASE
                            WHEN c.edad >= 100 THEN 'Mayor 100'
                            WHEN c.edad >= 95 THEN '95 - 99'
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
                            WHEN c.edad >= 40 THEN '40 - 44'
                            WHEN c.edad >= 35 THEN '35 - 39'
                            WHEN c.edad >= 30 THEN '30 - 34'
                            WHEN c.edad >= 25 THEN '25 - 29'
                            WHEN c.edad >= 20 THEN '20 - 24'
                            WHEN c.edad >= 15 THEN '15 - 19'
                            WHEN c.edad >= 10 THEN '10 - 14'
                            WHEN c.edad >= 5 THEN '5 - 9'
                            WHEN c.edad >= 0 THEN '0 - 4'
                            ELSE 'Sin dato'
                        END AS grupo_edad,
                        COUNT(*) AS cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.atencion = 'Fallecido' ";
            if (strlen($cod_mun) > 2) {
                $sql .= "AND c.codigo_divipola = '" . $cod_mun . "' ";
            } else {
                $sql .= "AND c.codigo_departamento = '" . $cod_mun . "' ";
            }
            $sql .= "GROUP BY nombre_sexo, grupo_edad
                        ) AS t ON s.nombre_detalle=t.nombre_sexo AND e.nombre_detalle=t.grupo_edad
                        WHERE s.id_lista=15
                        AND e.id_lista=16
                        ORDER BY e.orden DESC, s.orden";

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
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        GROUP BY nombre_sexo, grupo_edad
                        ORDER by c.edad desc";
            //echo $sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosContagiosGrupoEdadMunicipal($dato_fecha, $cod_mun) {
        try {
            $sql = "SELECT s.nombre_detalle AS nombre_sexo, e.nombre_detalle AS grupo_edad, IFNULL(cantidad, 0) AS cantidad
                        FROM listas_detalle s
                        CROSS JOIN listas_detalle e
                        LEFT JOIN (
                        SELECT
                        CASE
                            WHEN c.sexo = 'M' THEN 'Hombres'
                            WHEN c.sexo = 'F' THEN 'Mujeres'
                            ELSE 'Sin dato'
                        END AS nombre_sexo,
                        CASE
                            WHEN c.edad >= 100 THEN 'Mayor 100'
                            WHEN c.edad >= 95 THEN '95 - 99'
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
                            WHEN c.edad >= 40 THEN '40 - 44'
                            WHEN c.edad >= 35 THEN '35 - 39'
                            WHEN c.edad >= 30 THEN '30 - 34'
                            WHEN c.edad >= 25 THEN '25 - 29'
                            WHEN c.edad >= 20 THEN '20 - 24'
                            WHEN c.edad >= 15 THEN '15 - 19'
                            WHEN c.edad >= 10 THEN '10 - 14'
                            WHEN c.edad >= 5 THEN '5 - 9'
                            WHEN c.edad >= 0 THEN '0 - 4'
                            ELSE 'Sin dato'
                        END AS grupo_edad,
                        COUNT(*) AS cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "' ";
            if (strlen($cod_mun) > 2) {
                $sql .= "AND c.codigo_divipola = '" . $cod_mun . "' ";
            } else {
                $sql .= "AND c.codigo_departamento = '" . $cod_mun . "' ";
            }
            $sql .= "GROUP BY nombre_sexo, grupo_edad
                        ) AS t ON s.nombre_detalle=t.nombre_sexo AND e.nombre_detalle=t.grupo_edad
                        WHERE s.id_lista=15
                        AND e.id_lista=16
                        ORDER BY e.orden DESC, s.orden";

            //echo $sql;
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosGenerales($dato_fecha, $ind_orden_inv = 0) {
        try {
            $sql = "SELECT
                        CASE
                            WHEN c.atencion = 'Activo' THEN 'Activos'
                            WHEN c.atencion = 'Recuperado' THEN 'Recuperados'   
                            WHEN c.atencion = 'Fallecido' THEN 'Fallecidos'   
                            ELSE 'Otros'
                        END AS tipo_atencion,
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        GROUP BY tipo_atencion ";
            if ($ind_orden_inv == 1) {
                $sql .= "ORDER BY tipo_atencion DESC";
            } else {
                $sql .= "ORDER BY tipo_atencion";
            }

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    //, $cod_mun
    public function getDatosGeneralesMunicipal($dato_fecha) {
        try {
            /* $sql = "SELECT 'Casos' AS tipo_atencion, c.codigo_divipola, 
              COUNT(*) as cantidad
              FROM casos_covid_colombia c
              WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
              AND c.codigo_divipola IN (SELECT m.cod_mun_dane FROM lista_municipios_especiales m WHERE m.ind_activo=1)
              GROUP BY c.codigo_divipola
              UNION ALL
              SELECT
              CASE
              WHEN c.atencion='Activo' THEN 'Activos'
              WHEN c.atencion='Recuperado' THEN 'Recuperados'
              ELSE 'Otros'
              END AS tipo_atencion, c.codigo_divipola,
              COUNT(*) as cantidad
              FROM casos_covid_colombia c
              WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
              AND c.codigo_divipola IN (SELECT m.cod_mun_dane FROM lista_municipios_especiales m WHERE m.ind_activo=1)
              AND c.atencion IN ('Activo', 'Recuperado')
              GROUP BY tipo_atencion, c.codigo_divipola
              UNION ALL
              SELECT 'Casos' AS tipo_atencion, c.codigo_departamento,
              COUNT(*) AS cantidad
              FROM casos_covid_colombia c
              WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
              AND c.codigo_departamento IN (SELECT m.cod_mun_dane FROM lista_municipios_especiales m WHERE m.ind_activo=1 AND m.cod_mun_dane<100)
              GROUP BY c.codigo_departamento
              UNION
              SELECT
              CASE
              WHEN c.atencion='Activo' THEN 'Activos'
              WHEN c.atencion='Recuperado' THEN 'Recuperados'
              ELSE 'Otros'
              END AS tipo_atencion, c.codigo_departamento,
              COUNT(*) AS cantidad
              FROM casos_covid_colombia c
              WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
              AND c.codigo_departamento IN (SELECT m.cod_mun_dane FROM lista_municipios_especiales m WHERE m.ind_activo=1 AND m.cod_mun_dane<100)
              AND c.atencion IN ('Activo', 'Recuperado')
              GROUP BY tipo_atencion, c.codigo_departamento"; */
            $sql = "SELECT 'Casos' AS tipo_atencion, c.codigo_divipola, 
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        GROUP BY c.codigo_divipola
                        UNION ALL
                        SELECT
                        CASE
                            WHEN c.atencion='Activo' THEN 'Activos'
                            WHEN c.atencion='Recuperado' THEN 'Recuperados'   
                            ELSE 'Otros'
                        END AS tipo_atencion, c.codigo_divipola,
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.atencion IN ('Activo', 'Recuperado')
                        GROUP BY tipo_atencion, c.codigo_divipola
                        UNION ALL
                        SELECT 'Casos' AS tipo_atencion, c.codigo_departamento, 
                        COUNT(*) AS cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        GROUP BY c.codigo_departamento
                        UNION 
                        SELECT
                        CASE
                            WHEN c.atencion='Activo' THEN 'Activos'
                            WHEN c.atencion='Recuperado' THEN 'Recuperados'   
                            ELSE 'Otros'
                        END AS tipo_atencion, c.codigo_departamento,
                        COUNT(*) AS cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.atencion IN ('Activo', 'Recuperado')
                        GROUP BY tipo_atencion, c.codigo_departamento";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosGeneralesDef($dato_fecha) {
        try {
            $sql = "SELECT 
                        CASE
                            WHEN c.atencion IN('Fallecido') THEN 'Fallecidos'   
                            ELSE 'Otros'
                        END AS tipo_atencion,
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.atencion = 'Fallecido'
                        GROUP BY tipo_atencion";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosGeneralesDefMunicipal($dato_fecha) {
        try {
            $sql = "SELECT 
                        CASE
                            WHEN c.atencion IN('Fallecido') THEN 'Fallecidos'   
                            ELSE 'Otros'
                        END AS tipo_atencion, c.codigo_divipola, 
                        COUNT(*) as cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.codigo_divipola IN (SELECT m.cod_mun_dane FROM lista_municipios_especiales m)
                        AND c.atencion = 'Fallecido'
                        GROUP BY tipo_atencion, c.codigo_divipola
                        UNION ALL
                        SELECT
                        CASE
                            WHEN c.atencion='Fallecido' THEN 'Fallecidos'   
                            ELSE 'Otros'
                        END AS tipo_atencion, c.codigo_departamento, 
                        COUNT(*) AS cantidad
                        FROM casos_covid_colombia c
                        WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND c.codigo_departamento IN (SELECT m.cod_mun_dane FROM lista_municipios_especiales m WHERE m.ind_activo=1 AND m.cod_mun_dane<100)
                        AND c.atencion = 'Fallecido'
                        GROUP BY tipo_atencion, c.codigo_departamento";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getTasaContagioDpto($dato_fecha) {
        try {
            $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
                        GROUP BY p.nom_departamento, c.codigo_departamento
                        ORDER BY tasa desc
                        LIMIT 20";
            
            //echo($sql . "<br>");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getTasaContagioMuni($dato_fecha) {
        try {
            $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
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
            $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
                        AND c.atencion = 'Fallecido'
                        GROUP BY p.nom_departamento, c.codigo_departamento
                        ORDER BY tasa desc
                        LIMIT 20";
            
            //echo($sql . "<br>");
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getTasaMortaMuni($dato_fecha) {
        try {
            $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
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
            $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
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
            $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
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
            $sql = "SELECT SUBSTR(p.nom_departamento, 1, 20) as nom_departamento, c.codigo_departamento, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_departamento p ON p.cod_departamento = c.codigo_departamento
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
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
            $sql = "SELECT SUBSTR(p.nom_municipio, 1, 20) as nom_municipio, c.codigo_divipola, ROUND(100000.0 * COUNT(*)/ p.cantidad, 2) AS tasa 
                        FROM casos_covid_colombia c
                        INNER JOIN poblacion_municipio p ON p.cod_municipio = c.codigo_divipola
                        WHERE /*c.fecha_cargue_archivo = '" . $dato_fecha . "'
                        AND */p.anio = YEAR('" . $dato_fecha . "')
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
            $sql = "SELECT ME.id, ME.cod_dep, ME.cod_mun_dane, M.nom_mun, ME.ind_gedad
                    FROM lista_municipios_especiales ME
                    INNER JOIN municipios M ON ME.cod_mun_dane=M.cod_mun_dane
                    WHERE ME.ind_activo=1
                    UNION ALL
                    SELECT ME.id, ME.cod_dep, ME.cod_mun_dane, D.nom_dep AS nom_mun, ME.ind_gedad
                    FROM lista_municipios_especiales ME
                    INNER JOIN departamentos D ON ME.cod_mun_dane=D.cod_dep
                    WHERE ME.ind_activo=1
                    ORDER BY id";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getTasaLetalidad($dato_fecha) {
        try {
            $sql = "SELECT tipo_estado, estado_paciente, fecha_notificacion, SUM(cantidad) AS cantidad
                        FROM (
                                SELECT
                                 CASE
                                     WHEN c.fecha_inicio_sintomas IS NULL THEN 'ASINTOMATICOS'
                                     ELSE 'SINTOMATICOS'
                                 END AS tipo_estado,

                                 CASE
                                     WHEN c.atencion = 'Fallecido' THEN 'Fallecido'
                                     ELSE 'Vivo'
                                 END AS estado_paciente, 

                                 IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion) AS fecha_notificacion,

                                 COUNT(*) AS cantidad
                                 FROM casos_covid_colombia c
                                 WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'
                                 GROUP BY tipo_estado, estado_paciente, IFNULL(c.fecha_inicio_sintomas, c.fecha_notificacion)
                                 UNION all
                                 SELECT 'SINTOMATICOS',  'Fallecido', a.Date, 0
                                    FROM (
                                      SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                      FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                                    ) a
                                    WHERE a.Date BETWEEN '2020-02-27' AND DATE('" . $dato_fecha . "')
                                  UNION all
                                 SELECT 'ASINTOMATICOS',  'Vivo', a.Date, 0
                                    FROM (
                                      SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS DATE
                                      FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                                    ) a
                                    WHERE a.Date BETWEEN '2020-02-27' AND DATE('" . $dato_fecha . "')      


                        ) T
                        WHERE T.fecha_notificacion BETWEEN '2020-02-27' AND DATE('" . $dato_fecha . "')
                        GROUP BY tipo_estado, estado_paciente, fecha_notificacion
                        ORDER BY fecha_notificacion, tipo_estado";
            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getFrecuenciaCasosMuertesMuni($dato_fecha, $cod_mun) {
        try {
            $sql = "SELECT YEAR(c.fecha_notificacion) AS ano_noti, MONTH(c.fecha_notificacion) AS mes_noti,
                    CASE
                        WHEN c.atencion IN ('Fallecido') THEN 'Fallecidos'   
                        ELSE 'Vivo'
                    END AS tipo_atencion,
                    COUNT(*) as cantidad
                    FROM casos_covid_colombia c
                    WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "' ";
            if (strlen($cod_mun) > 2) {
                $sql .= "AND c.codigo_divipola = '" . $cod_mun . "' ";
            } else {
                $sql .= "AND c.codigo_departamento = '" . $cod_mun . "' ";
            }
            $sql .= "GROUP BY ano_noti, mes_noti, tipo_atencion";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getListaFechas($dato_fecha) {
        try {
            $sql = "SELECT *
                    FROM (
                      SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) DAY AS fecha
                      FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
                      CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS d
                    ) a
                    WHERE a.fecha BETWEEN '2020-06-01' AND DATE('" . $dato_fecha . "')
                    ORDER BY a.fecha";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosGeneralesMunicipalCantidades($dato_fecha) {
        try {
            $sql = "SELECT C.codigo_divipola, PM.cantidad AS poblacion, COUNT(*) AS casos, SUM(CASE C.atencion WHEN 'Fallecido' THEN 1 ELSE 0 END) AS fallecidos,
                    SUM(CASE WHEN C.edad<60 THEN 1 ELSE 0 END) AS casos_0_59, SUM(CASE WHEN C.atencion='Fallecido' AND C.edad<60 THEN 1 ELSE 0 END) AS fallecidos_0_59,
                    SUM(CASE WHEN C.edad>=60 THEN 1 ELSE 0 END) AS casos_60, SUM(CASE WHEN C.atencion='Fallecido' AND C.edad>=60 THEN 1 ELSE 0 END) AS fallecidos_60
                    FROM casos_covid_colombia C
                    INNER JOIN poblacion_municipio PM ON C.codigo_divipola=PM.cod_municipio
                    WHERE C.fecha_cargue_archivo='" . $dato_fecha . "'
                    /*AND PM.anio=YEAR('" . $dato_fecha . "')*/
                    AND PM.anio='2020'
                    GROUP BY C.codigo_divipola
                    UNION ALL
                    SELECT C.codigo_departamento, PM.cantidad AS poblacion, COUNT(*) AS casos, SUM(CASE C.atencion WHEN 'Fallecido' THEN 1 ELSE 0 END) AS fallecidos,
                    SUM(CASE WHEN C.edad<60 THEN 1 ELSE 0 END) AS casos_0_59, SUM(CASE WHEN C.atencion='Fallecido' AND C.edad<60 THEN 1 ELSE 0 END) AS fallecidos_0_59,
                    SUM(CASE WHEN C.edad>=60 THEN 1 ELSE 0 END) AS casos_60, SUM(CASE WHEN C.atencion='Fallecido' AND C.edad>=60 THEN 1 ELSE 0 END) AS fallecidos_60
                    FROM casos_covid_colombia C
                    INNER JOIN poblacion_departamento PM ON C.codigo_departamento=PM.cod_departamento
                    WHERE C.fecha_cargue_archivo='" . $dato_fecha . "'
                    /*AND PM.anio=YEAR('" . $dato_fecha . "')*/
                    AND PM.anio='2020'
                    GROUP BY C.codigo_departamento";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function getDatosGeneralesEdades($dato_fecha) {
        try {
            $sql = "SELECT SUM(CASE WHEN c.edad<60 THEN 1 ELSE 0 END) AS casos_0_59, SUM(CASE WHEN c.edad>=60 THEN 1 ELSE 0 END) AS casos_60,
                    SUM(CASE WHEN c.edad<60 AND c.atencion='Fallecido' THEN 1 ELSE 0 END) AS fallecidos_0_59,
                    SUM(CASE WHEN c.edad>=60 AND c.atencion='Fallecido' THEN 1 ELSE 0 END) AS fallecidos_60
                    FROM casos_covid_colombia c
                    WHERE c.fecha_cargue_archivo = '" . $dato_fecha . "'";

            return $this->getUnDato($sql);
        } catch (Exception $e) {
            return array();
        }
    }

    public function guardarMunicipiosEspeciales($array_municipios) {
        try {
            $sql = "DELETE FROM lista_municipios_especiales";
            $arr_campos = array();
            $this->ejecutarSentencia($sql, $arr_campos);

            if (count($array_municipios) > 0) {
                $sql = "";
                foreach ($array_municipios as $municipio_aux) {
                    $cod_mun_dane = intval($municipio_aux, 10);
                    $cod_dep = floor($cod_mun_dane / 1000);
                    if ($sql != "") {
                        $sql .= ", ";
                    }
                    $sql .= "(" . $cod_dep . ", " . $cod_mun_dane . ", 'Aplicación', 1, 1)";
                }

                $sql = "INSERT INTO lista_municipios_especiales (cod_dep, cod_mun_dane, nom_mun, ind_activo, ind_gedad) VALUES " . $sql;
                
                //echo($sql . "<br>");
                $arr_campos = array();
                $this->ejecutarSentencia($sql, $arr_campos);
            }
            
            return 1;
        } catch (Exception $e) {
            return -1;
        }
    }

    public function getListaCasosUltimasSemanas() {
        try {
            $sql = "SELECT D.cod_dep, D.nom_dep, M.cod_mun_dane, M.nom_mun,
                    SUM(CASE WHEN IFNULL(CC.fecha_inicio_sintomas, CC.fecha_notificacion)<=DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) THEN 1 ELSE 0 END) AS casos,
                    SUM(CASE WHEN CC.atencion='Fallecido' AND CC.fecha_muerte<=DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) THEN 1 ELSE 0 END) AS defunciones,
                    SUM(CASE WHEN IFNULL(CC.fecha_inicio_sintomas, CC.fecha_notificacion) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+5 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) THEN 1 ELSE 0 END) AS casos_sem1,
                    SUM(CASE WHEN CC.atencion='Fallecido' AND CC.fecha_muerte BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+5 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) THEN 1 ELSE 0 END) AS defun_sem1,
                    SUM(CASE WHEN IFNULL(CC.fecha_inicio_sintomas, CC.fecha_notificacion) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+12 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+6 DAY) THEN 1 ELSE 0 END) AS casos_sem2,
                    SUM(CASE WHEN CC.atencion='Fallecido' AND CC.fecha_muerte BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+12 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+6 DAY) THEN 1 ELSE 0 END) AS defun_sem2,
                    SUM(CASE WHEN IFNULL(CC.fecha_inicio_sintomas, CC.fecha_notificacion) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+19 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+13 DAY) THEN 1 ELSE 0 END) AS casos_sem3,
                    SUM(CASE WHEN CC.atencion='Fallecido' AND CC.fecha_muerte BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+19 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+13 DAY) THEN 1 ELSE 0 END) AS defun_sem3,
                    SUM(CASE WHEN IFNULL(CC.fecha_inicio_sintomas, CC.fecha_notificacion) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+26 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+20 DAY) THEN 1 ELSE 0 END) AS casos_sem4,
                    SUM(CASE WHEN CC.atencion='Fallecido' AND CC.fecha_muerte BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+26 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+20 DAY) THEN 1 ELSE 0 END) AS defun_sem4,
                    SUM(CASE WHEN IFNULL(CC.fecha_inicio_sintomas, CC.fecha_notificacion) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+33 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+27 DAY) THEN 1 ELSE 0 END) AS casos_sem5,
                    SUM(CASE WHEN CC.atencion='Fallecido' AND CC.fecha_muerte BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+33 DAY) AND DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())+27 DAY) THEN 1 ELSE 0 END) AS defun_sem5
                    FROM casos_covid_colombia CC
                    INNER JOIN municipios M ON CC.codigo_divipola=M.cod_mun_dane
                    INNER JOIN departamentos D ON M.cod_dep=D.cod_dep
                    GROUP BY D.cod_dep, M.cod_mun_dane
                    ORDER BY D.cod_dep, M.cod_mun_dane";

            return $this->getDatos($sql);
        } catch (Exception $e) {
            return array();
        }
    }
    
}

?>
