<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbListas.php");
require_once("../db/DbMunicipios.php");
require_once("../funciones/Utilidades.php");

require_once("../funciones/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWrite;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$dbVariables = new DbVariables();
$dbListas = new DbListas();
$dbMunicipios = new DbMunicipios();

$utilidades = new Utilidades();

function calcular_alertas($mapa_datos, $lista_fechas) {
    //Se completan las fechas y se hallan los incrementos diarios
    $mapa_datos_aux = array();
    foreach ($mapa_datos as $cod_mun => $mapa_fechas_aux) {
        $cantidad_ant = -1;
        $lista_fechas_rev = array();
        foreach ($lista_fechas as $arr_fechas_aux) {
            $fecha_aux = str_replace("-", "/", $arr_fechas_aux["fecha"]);
            if (isset($mapa_fechas_aux[$fecha_aux])) {
                $cantidad_aux = $mapa_fechas_aux[$fecha_aux];
            } else {
                $cantidad_aux = 0;
            }
            
            //Se calcula el incremento con respecto al día anterior
            if ($cantidad_ant > 0) {
                $incremento_aux = ($cantidad_aux - $cantidad_ant) / $cantidad_ant;
            } else {
                $incremento_aux = 0;
            }
            
            array_push($lista_fechas_rev, array("fecha" => $fecha_aux, "cantidad" => $cantidad_aux, "incremento" => $incremento_aux));
            
            $cantidad_ant = $cantidad_aux;
        }
        
        $mapa_datos_aux[$cod_mun] = $lista_fechas_rev;
    }
    
    //Se calculan las alertas
    foreach ($mapa_datos_aux as $cod_mun => $lista_fechas_aux) {
        foreach ($lista_fechas_aux as $i => $arr_aux) {
            if ($i >= 34) {
                //Se obtienen los datos con los que se hará el cálculo de la alerta,
                //que corresponderán a los últimos 20 días válidos, descartando para esto los últimos 15 días calendario
                $lista_dias_alerta_aux = array_slice($lista_fechas_aux, $i - 34, 20);
                
                //Se obtiene el promedio de los incrementos
                $suma_aux = 0;
                $cuenta_no_cero_aux = 0;
                foreach ($lista_dias_alerta_aux as $arr_alerta_aux) {
                    $suma_aux += $arr_alerta_aux["incremento"];
                    if ($arr_alerta_aux["incremento"] != 0) {
                        $cuenta_no_cero_aux++;
                    }
                }
                $promedio_aux = $suma_aux / count($lista_dias_alerta_aux);
                
                //Se obtiene la desviación estándar de los incrementos
                $suma_aux = 0;
                foreach ($lista_dias_alerta_aux as $arr_alerta_aux) {
                    $suma_aux += pow($arr_alerta_aux["incremento"] - $promedio_aux, 2);
                }
                $desv_est_aux = sqrt($suma_aux / (count($lista_dias_alerta_aux) - 1));
                
                //Se obtiene el valor de la alerta
                if ($lista_fechas_aux[$i - 15]["incremento"] > ($promedio_aux + $desv_est_aux) && $cuenta_no_cero_aux >= 19) {
                    $val_alerta_aux = 1;
                } else {
                    $val_alerta_aux = 0;
                }
                
                $arr_aux["alerta"] = $val_alerta_aux;
                
                $lista_fechas_aux[$i] = $arr_aux;
            }
        }
        $mapa_datos_aux[$cod_mun] = $lista_fechas_aux;
    }
    
    //Se convierte el resultado a un doble mapa de municipios y fechas
    $mapa_datos_rev = array();
    foreach ($mapa_datos_aux as $cod_mun => $lista_fechas_aux) {
        foreach ($lista_fechas_aux as $arr_aux) {
            $mapa_datos_rev[$arr_aux["fecha"]][$cod_mun] = $arr_aux;
        }
    }

    return $mapa_datos_rev;
}

//Tener en cuenta que la primera columna se numera con 1
function obtener_columna($numero) {
    $arr_valores = [26, 676, 17576, 456976];
    $arr_digitos = [26, 702, 18278, 475254];
    //$arr_valores = [4, 16, 64, 256];
    //$arr_digitos = [4, 20, 84, 340];
    
    $ind_letra = 0;
    while ($numero > $arr_digitos[$ind_letra]) {
        $ind_letra++;
    }
    
    $columna = "";
    
    for ($i = $ind_letra; $i >= 0; $i--) {
        $val_letra = intdiv($numero, $arr_valores[$i]);
        if ($i > 0) {
            if ($numero % $arr_valores[$i] <= $arr_digitos[$i - 1]) {
                $val_letra -= 1;
            }
        } else if ($numero % $arr_valores[$i] == 0) {
            $val_letra -= 1;
        }
        $numero -= $val_letra * $arr_valores[$i];
        
        if ($val_letra > 0) {
            $columna .= chr($val_letra + 64);
        }
    }
    
    $columna .= chr($numero + 64);
    
    return $columna;
}

function crear_hoja_alertas($doc_salida, $mapa_datos) {
    $doc_salida->getActiveSheet()
            ->setCellValue("A1", "Fecha");
    
    $cont_columna = 2;
    foreach ($mapa_datos as $mapa_municipios_aux) {
        foreach ($mapa_municipios_aux as $cod_mun => $arr_aux) {
            $doc_salida->getActiveSheet()
                    ->setCellValue(obtener_columna($cont_columna) . "1", $cod_mun);
            
            $cont_columna++;
        }
        break;
    }
    $doc_salida->getActiveSheet()
            ->setCellValue(obtener_columna($cont_columna) . "1", "Total");
    
    $ind_agregado = false;
    $contador_linea = 2;
    foreach ($mapa_datos as $fecha_aux => $mapa_municipios_aux) {
        $cont_columna = 1;
        $suma_alertas = 0;
        foreach ($mapa_municipios_aux as $arr_datos) {
            if (isset($arr_datos["alerta"])) {
                $ind_agregado = true;
                if ($cont_columna == 1) {
                    //Se agrega la primera columna con la fecha
                    $doc_salida->getActiveSheet()
                            ->setCellValue(obtener_columna($cont_columna) . $contador_linea, $fecha_aux);
                    $cont_columna++;
                }
                $doc_salida->getActiveSheet()
                        ->setCellValue(obtener_columna($cont_columna) . $contador_linea, $arr_datos["alerta"]);
                
                $suma_alertas += $arr_datos["alerta"];
                $cont_columna++;
            } else {
                break;
            }
        }
        
        if ($ind_agregado) {
            $doc_salida->getActiveSheet()
                    ->setCellValue(obtener_columna($cont_columna) . $contador_linea, $suma_alertas);
            $contador_linea++;
        }
    }
    
    return $doc_salida;
}

$opcion = $_REQUEST['opcion'];

switch ($opcion) {
    case "1": //Cálculo de las alertas
        $id_usuario = $_SESSION["idUsuario"];
        $nombre_arch_salida = "";
        ?>
        <div id="d_carga_interna">
            <?php
            $fecha_ini = "";
            
            //Se obtiene el listado de departamentos
            $lista_departamentos = $dbMunicipios->getListaDepartamentos();
            $mapa_departamentos = array();
            foreach ($lista_departamentos as $departamento_aux) {
                $mapa_departamentos[$departamento_aux["cod_dep"]] = $departamento_aux["nom_dep"];
            }
                
            //Se cargan los nombres del archivo de casos
            $nombre_arch = $_FILES["fil_arch_casos"]["name"];
            $nombre_tmp = $_FILES["fil_arch_casos"]["tmp_name"];
            
            $extension_aux = strtolower($utilidades->get_extension_arch($nombre_arch));
            
            $mapa_casos = array();
            $mapa_casos_dep = array();
            if ($extension_aux === "csv") {
                //Se establece la zona horaria
                date_default_timezone_set("America/Bogota");
                
                //Se abre el archivo csv
                $arch_aux = fopen($nombre_tmp, "r") or die("Problem open file");
                $cont = 0;
                $cod_mun_ant = "";
                while (($arr_datos_aux = fgetcsv($arch_aux, 100000, ";")) !== false) {
                    $arr_datos_aux = array_map("utf8_encode", $arr_datos_aux);
                    
                    if ($cont > 0) {
                        //Se halla el codigo del municipios
                        $cod_mun = $arr_datos_aux[0];
                        if ($cod_mun == "") {
                            $cod_mun = $cod_mun_ant;
                        } else {
                            $cod_mun_ant = $cod_mun;
                        }
                        $cod_dep = substr($cod_mun, 0, 2);
                        $cod_dep .= " - " . $mapa_departamentos[$cod_dep];
                        
                        if (isset($mapa_casos[$cod_mun])) {
                            $arr_mun_aux = $mapa_casos[$cod_mun];
                        } else {
                            $arr_mun_aux = array();
                        }
                        
                        if (isset($mapa_casos_dep[$cod_dep])) {
                            $arr_dep_aux = $mapa_casos_dep[$cod_dep];
                        } else {
                            $arr_dep_aux = array();
                        }
                        
                        $arr_mun_aux[$arr_datos_aux[1]] = intval($arr_datos_aux[2], 10);
                        $mapa_casos[$cod_mun] = $arr_mun_aux;
                        
                        if (isset($arr_dep_aux[$arr_datos_aux[1]])) {
                            $arr_dep_aux[$arr_datos_aux[1]] += intval($arr_datos_aux[2], 10);
                        } else {
                            $arr_dep_aux[$arr_datos_aux[1]] = intval($arr_datos_aux[2], 10);
                        }
                        $mapa_casos_dep[$cod_dep] = $arr_dep_aux;
                        
                        if ($fecha_ini == "") {
                            $fecha_ini = $arr_datos_aux[1];
                        }
                    }
                    
                    $cont++;
                }
                
                fclose($arch_aux);
            }
            
            //Se cargan los nombres del archivo de defunciones
            $nombre_arch = $_FILES["fil_arch_defunciones"]["name"];
            $nombre_tmp = $_FILES["fil_arch_defunciones"]["tmp_name"];
            
            $extension_aux = strtolower($utilidades->get_extension_arch($nombre_arch));
            
            $mapa_defunciones = array();
            $mapa_defunciones_dep = array();
            if ($extension_aux === "csv") {
                //Se establece la zona horaria
                date_default_timezone_set("America/Bogota");
                
                //Se abre el archivo csv
                $arch_aux = fopen($nombre_tmp, "r") or die("Problem open file");
                $cont = 0;
                $cod_mun_ant = "";
                while (($arr_datos_aux = fgetcsv($arch_aux, 100000, ";")) !== false) {
                    $arr_datos_aux = array_map("utf8_encode", $arr_datos_aux);
                    
                    if ($cont > 0) {
                        //Se halla el codigo del municipios
                        $cod_mun = $arr_datos_aux[0];
                        if ($cod_mun == "") {
                            $cod_mun = $cod_mun_ant;
                        } else {
                            $cod_mun_ant = $cod_mun;
                        }
                        $cod_dep = substr($cod_mun, 0, 2);
                        $cod_dep .= " - " . $mapa_departamentos[$cod_dep];
                        
                        if (isset($mapa_defunciones[$cod_mun])) {
                            $arr_mun_aux = $mapa_defunciones[$cod_mun];
                        } else {
                            $arr_mun_aux = array();
                        }
                        
                        if (isset($mapa_defunciones_dep[$cod_dep])) {
                            $arr_dep_aux = $mapa_defunciones_dep[$cod_dep];
                        } else {
                            $arr_dep_aux = array();
                        }
                        
                        $arr_mun_aux[$arr_datos_aux[1]] = intval($arr_datos_aux[2], 10);
                        $mapa_defunciones[$cod_mun] = $arr_mun_aux;
                        
                        if (isset($arr_dep_aux[$arr_datos_aux[1]])) {
                            $arr_dep_aux[$arr_datos_aux[1]] += intval($arr_datos_aux[2], 10);
                        } else {
                            $arr_dep_aux[$arr_datos_aux[1]] = intval($arr_datos_aux[2], 10);
                        }
                        $mapa_defunciones_dep[$cod_dep] = $arr_dep_aux;
                    }
                    
                    $cont++;
                }
                
                fclose($arch_aux);
            }
            
            if (count($mapa_casos) > 0 && count($mapa_defunciones) > 0) {
                //Se obtiene el listad completo de fechas desde la fecha inicial de los archivos hasta la fecha actual
                $lista_fechas = $dbVariables->getListaFechas($fecha_ini);
                
                //Se realizan los calculos de casos por municipio
                $mapa_casos_rev = calcular_alertas($mapa_casos, $lista_fechas);
                
                //Se realizan los calculos de casos por departamento
                $mapa_casos_dep_rev = calcular_alertas($mapa_casos_dep, $lista_fechas);
                
                //Se realizan los calculos de defunciones por municipio
                $mapa_defunciones_rev = calcular_alertas($mapa_defunciones, $lista_fechas);
                
                //Se realizan los calculos de defunciones por departamento
                $mapa_defunciones_dep_rev = calcular_alertas($mapa_defunciones_dep, $lista_fechas);
                
                //Se crea el archivo de salida
                $doc_salida = new Spreadsheet();
                $doc_salida
                        ->getProperties()
                        ->setCreator("MSPS")
                        ->setLastModifiedBy("MSPS") // última vez modificado por
                        ->setTitle("Boletín semanal de alertas")
                        ->setSubject("Alertas")
                        ->setDescription("Boletín semanal de alertas")
                        ->setKeywords("Alertas")
                        ->setCategory("Carga masiva");
                
                /*********************/
                /*Casos por municipio*/
                /*********************/
                $doc_salida->setActiveSheetIndex(0)->setTitle("Alertas mun");
                $doc_salida = crear_hoja_alertas($doc_salida, $mapa_casos_rev);
                
                /************************/
                /*Casos por departamento*/
                /************************/
                $hoja_aux = new Worksheet($doc_salida, "Alertas dep");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(1);
                $doc_salida = crear_hoja_alertas($doc_salida, $mapa_casos_dep_rev);
                
                /***************************/
                /*Defunciones por municipio*/
                /***************************/
                $hoja_aux = new Worksheet($doc_salida, "Alertas mun def");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(2);
                $doc_salida = crear_hoja_alertas($doc_salida, $mapa_defunciones_rev);
                
                /******************************/
                /*Defunciones por departamento*/
                /******************************/
                $hoja_aux = new Worksheet($doc_salida, "Alertas dep def");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(3);
                $doc_salida = crear_hoja_alertas($doc_salida, $mapa_defunciones_dep_rev);
                
                //Se crea el archivo
                $doc_salida->setActiveSheetIndex(0);
                $xlsxWrite = new XlsxWrite($doc_salida);
                
                //Ruta de guardado
                $nombre_arch_salida = "./tmp/alertas_" . $id_usuario . ".xlsx";
                $xlsxWrite->save($nombre_arch_salida);
            } else {
                if (count($mapa_casos) == 0) {
                    echo("No se encontraron casos.<br/>");
                }
                if (count($mapa_defunciones) == 0) {
                    echo("No se encontraron defunciones.<br/>");
                }
            }
            ?>
        </div>
        <?php
        if ($nombre_arch_salida != "") {
            ?>
            <form name="frm_reporte_alertas" id="frm_reporte_alertas" method="post" action="<?= $nombre_arch_salida ?>">
            </form>
            <script id="ajax" type="text/javascript">
                document.getElementById("frm_reporte_alertas").submit();
            </script>
            <?php
        }
        break;
}
?>
