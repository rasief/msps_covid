<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbViajeros.php");
require_once("../funciones/Utilidades.php");

require_once("../funciones/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsxDate;

$dbVariables = new DbVariables();
$dbViajeros = new DbViajeros();

$utilidades = new Utilidades();

$opcion = $_REQUEST['opcion'];

function validar_motivo_viaje($motivo_viaje) {
    $motivo_viaje = strtolower($motivo_viaje);
    $ind_valido = 1;
    if (strpos($motivo_viaje, "deportad") === 0) {
        $ind_valido = 0;
    } else if (strpos($motivo_viaje, "expulsad") === 0) {
        $ind_valido = 0;
    } else if (strpos($motivo_viaje, "inadmitid") === 0) {
        $ind_valido = 0;
    } else if (strpos($motivo_viaje, "tránsit") === 0 || strpos($motivo_viaje, "transit") === 0) {
        $ind_valido = 0;
    } else if (strpos($motivo_viaje, "tripula") === 0) {
        $ind_valido = 0;
    }
    
    return $ind_valido;
}

switch ($opcion) {
    case "1": //Formulario con la fecha de viaje
        //Se obtiene la siguiente fecha a cargar
        $fecha_obj = $dbViajeros->getFechaSiguiente();
        ?>
        <input type="hidden" id="hdd_fecha_viaje" name="hdd_fecha_viaje" value="<?= $fecha_obj["fecha_viaje"] ?>"/>
        Fecha a cargar: <?= $fecha_obj["fecha_viaje"] ?>
        <?php
        break;
    
    case "2": //Procesamiento del archivo de viajeros
        ?>
        <div id="d_carga_interna">
            <?php
            $fecha_viaje = $utilidades->str_decode($_REQUEST["fecha_viaje"]);
            
            //Se cargan los nombres del archivo
            $nombre_ori_aux = $_FILES["fil_arch"]["name"];
            $tmp_nombre_aux = $_FILES["fil_arch"]["tmp_name"];
            
            $extension_aux = strtolower($utilidades->get_extension_arch($nombre_ori_aux));
            
            if ($extension_aux === "xlsx") {
                $libro_des = IOFactory::load($tmp_nombre_aux);
                
                //Se obtiene la primera hoja, debería ser la única
                $hoja_aux = $libro_des->getSheet(0);
                
                //Se obtiene el número de filas
                $i = 1;
                while ($hoja_aux->getCellByColumnAndRow(1, $i)->getValue() != null) {
                    $i++;
                }
                $num_filas = $i - 1;
                
                //Se recorren las filas del archivo
                $mapa_valores = array();
                for ($i = 2; $i <= $num_filas; $i++) {
                    //Se cargan las variables que se consideran relevantes para la clasificación del registro
                    $fecha_viaje_obj = $hoja_aux->getCell("G" . $i)->getValue();
                    $fecha_viaje_aux = XlsxDate::excelToDateTimeObject($fecha_viaje_obj)->format("Y-m-d");
                    $nacionalidad_aux = $hoja_aux->getCell("L" . $i)->getValue();
                    $ind_nacional_aux = strtolower($nacionalidad_aux) == "colombia" ? 1 : 0;
                    $pregunta_4_aux = $hoja_aux->getCell("U" . $i)->getValue();
                    $ind_pregunta_4_aux = strtolower($pregunta_4_aux) == "si" ? 1 : (strtolower($pregunta_4_aux) == "no" ? 0 : -1);
                    $pregunta_5_aux = $hoja_aux->getCell("V" . $i)->getValue();
                    $ind_pregunta_5_aux = strtolower($pregunta_5_aux) == "si" ? 1 : (strtolower($pregunta_5_aux) == "no" ? 0 : -1);
                    $pregunta_6_aux = $hoja_aux->getCell("W" . $i)->getValue();
                    $ind_pregunta_6_aux = strtolower($pregunta_6_aux) == "si" ? 1 : (strtolower($pregunta_6_aux) == "no" ? 0 : -1);
                    $pregunta_7_aux = $hoja_aux->getCell("X" . $i)->getValue();
                    $ind_pregunta_7_aux = strtolower($pregunta_7_aux) == "si" ? 1 : (strtolower($pregunta_7_aux) == "no" ? 0 : -1);
                    //$motivo_viaje_aux = $hoja_aux->getCell("Y" . $i)->getValue();
                    
                    //Se valida la fecha del archivo
                    if ($i == 2 && $fecha_viaje_aux != $fecha_viaje) {
                        echo("Error - El archivo cargado no corresponde a la fecha esperada.");
                        break;
                    }
                    
                    //Se filtran los registros
                    if (/*validar_motivo_viaje($motivo_viaje_aux) === 1 && */$ind_pregunta_4_aux >= 0 && $ind_pregunta_5_aux >= 0 && $ind_pregunta_6_aux >= 0 && $ind_pregunta_7_aux >= 0) {
                        $llave_aux = $fecha_viaje_aux . "#" . $ind_nacional_aux . "#" . $ind_pregunta_4_aux . "#" . $ind_pregunta_5_aux . "#" . $ind_pregunta_6_aux . "#" . $ind_pregunta_7_aux;
                        
                        if (isset($mapa_valores[$llave_aux])) {
                            $arr_aux = $mapa_valores[$llave_aux];
                            
                            $arr_aux["cantidad"] = $arr_aux["cantidad"] + 1;
                        } else {
                            $arr_aux = array(
                                "fecha_viaje" => $fecha_viaje_aux,
                                "ind_nacional" => $ind_nacional_aux,
                                "ind_pregunta_4" => $ind_pregunta_4_aux,
                                "ind_pregunta_5" => $ind_pregunta_5_aux,
                                "ind_pregunta_6" => $ind_pregunta_6_aux,
                                "ind_pregunta_7" => $ind_pregunta_7_aux,
                                "cantidad" => 1
                            );
                        }
                        
                        $mapa_valores[$llave_aux] = $arr_aux;
                    }
                }
                
                unset($libro_des);
                
                if (count($mapa_valores) > 0) {
                    //Se insertan los nuevos registros
                    $resultado = $dbViajeros->crearConsolidadoViajeros($mapa_valores);
                    
                    if ($resultado <= 0) {
                        echo("Error interno al procesar el archivo (" . $resultado . ")");
                    }
                }
            }
            ?>
        </div>
        <?php
        break;
        
    case "3": //Filtros de fecha para los indicadores
        $fecha_obj = $dbViajeros->getFechasMinMax();
        ?>
        <div class="col-xs-2" style="margin-left:10px;">
            Fecha inicial
        </div>
        <div class="col-xs-3">
            <input type="date" id="txt_fecha_ini" name="txt_fecha_ini" value="<?= $fecha_obj["fecha_min"] ?>"/>
        </div>
        <div class="col-xs-2">
            Fecha final
        </div>
        <div class="col-xs-3">
            <input type="date" id="txt_fecha_fin" name="txt_fecha_fin" value="<?= $fecha_obj["fecha_max"] ?>"/>
        </div>
        <div class="col-xs-1">
            <button type="button" id="btn_calcular_indicadores" name="btn_calcular_indicadores" class="btn btn-success" onclick="calcular_indicadores();">Calcular</button>
        </div>
        <?php
        break;
    
    case "4": //Cálculo de indicadores
        $id_usuario = $_SESSION["idUsuario"];
        $fecha_ini = $utilidades->str_decode($_REQUEST["fecha_ini"]);
        $fecha_fin = $utilidades->str_decode($_REQUEST["fecha_fin"]);
        
        $lista_datos = $dbViajeros->getListaConsolidados($fecha_ini, $fecha_fin);
        
        $mapa_indicadores = array();
        foreach ($lista_datos as $dato_aux) {
            $numerador_1 = $dato_aux["si_4"];
            $denominador_1 = $dato_aux["total"];
            
            $numerador_2 = $dato_aux["no_4_si_6_no_7"];
            $denominador_2 = $dato_aux["total"] - $dato_aux["si_4"];
            
            $numerador_3 = $dato_aux["no_4_no_6_si_7"];
            $denominador_3 = $dato_aux["total"] - $dato_aux["si_4"];
            
            $numerador_4 = $dato_aux["no_4_si_6_si_7"];
            $denominador_4 = $dato_aux["total"] - $dato_aux["si_4"];
            
            $numerador_5 = $dato_aux["no_4_no_6_no_7"];
            $denominador_5 = $dato_aux["total"] - $dato_aux["si_4"];
            
            $arr_indicadores = array(
                1 => ($denominador_1 > 0 ? round($numerador_1 / $denominador_1 * 100, 2) : 0),
                2 => ($denominador_2 > 0 ? round($numerador_2 / $denominador_2 * 100, 2) : 0),
                3 => ($denominador_3 > 0 ? round($numerador_3 / $denominador_3 * 100, 2) : 0),
                4 => ($denominador_4 > 0 ? round($numerador_4 / $denominador_4 * 100, 2) : 0),
                5 => ($denominador_5 > 0 ? round($numerador_5 / $denominador_5 * 100, 2) : 0)
            );
            
            for ($i = 1; $i <= 5; $i++) {
                if (isset($mapa_indicadores[$i])) {
                    $mapa_fechas_aux = $mapa_indicadores[$i];
                } else {
                    $mapa_fechas_aux = array();
                }
                if (isset($mapa_fechas_aux[$dato_aux["fecha_viaje"]])) {
                    $arr_aux = $mapa_fechas_aux[$dato_aux["fecha_viaje"]];
                } else {
                    $arr_aux = array(
                        "extranjero" => 0,
                        "nacional" => 0
                    );
                }
                
                if ($dato_aux["ind_nacional"] == "1") {
                    $arr_aux["nacional"] = $arr_indicadores[$i];
                } else {
                    $arr_aux["extranjero"] = $arr_indicadores[$i];
                }
                
                $mapa_fechas_aux[$dato_aux["fecha_viaje"]] = $arr_aux;
                $mapa_indicadores[$i] = $mapa_fechas_aux;
            }
        }
        
        //Se crea el archivo de salida
        $nombre_arch_salida = "tmp/indicadores_viajeros_" . $id_usuario . ".txt";
        $arch_salida = fopen($nombre_arch_salida, "w") or die("Error al crear el archivo");
        fwrite($arch_salida, "Fecha_Ingreso|Extranjero|Nacional|Indicador");
        foreach ($mapa_indicadores as $indicador_aux => $mapa_fechas_aux) {
            foreach ($mapa_fechas_aux as $fecha_aux => $arr_valores_aux) {
                fwrite($arch_salida, "\n" . $fecha_aux . "|" . $arr_valores_aux["extranjero"] . "|" . $arr_valores_aux["nacional"] . "|" . $indicador_aux);
            }
        }
        fclose($arch_salida);
        ?>
        <input type="hidden" id="hdd_ruta_indicadores" name="hdd_ruta_indicadores" value="../informes/<?= $nombre_arch_salida ?>"/>
        <?php
        break;
}
?>
