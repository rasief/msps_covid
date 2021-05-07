<?php
session_start();

require_once("../db/DbInformes.php");
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

$dbInformes = new DbInformes();
$dbVariables = new DbVariables();
$dbListas = new DbListas();
$dbMunicipios = new DbMunicipios();

$utilidades = new Utilidades();

$opcion = $_REQUEST['opcion'];

switch ($opcion) {
    case "1": //Cálculo del índice de afectación
        $id_usuario = $_SESSION["idUsuario"];
        $nombre_arch_salida = "";
        ?>
        <div id="d_carga_interna">
            <?php
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
                $mapa_pruebas = array();
                for ($i = 2; $i <= $num_filas; $i++) {
                    //Se cargan las variables que se consideran relevantes para la clasificación del registro
                    $cod_mun_aux = substr("00" . $hoja_aux->getCell("C" . $i)->getValue(), -5);
                    $total_periodo_aux = $hoja_aux->getCell("D" . $i)->getValue();
                    $positivos_aux = $hoja_aux->getCell("E" . $i)->getValue();
                    $porcentaje_aux = $hoja_aux->getCell("F" . $i)->getValue();
                    if ($porcentaje_aux == "") {
                        $porcentaje_aux = 0;
                    }
                    
                    $arr_aux = array(
                        "total_periodo" => $total_periodo_aux,
                        "positivos" => $positivos_aux,
                        "porcentaje" => $porcentaje_aux
                    );
                    $mapa_pruebas[$cod_mun_aux] = $arr_aux;
                }
                
                unset($libro_des);
                
                //Se obtiene el listado de departamentos
                $lista_departamentos = $dbMunicipios->getListaDepartamentosPoblacion(2020);
                $mapa_departamentos = array();
                foreach ($lista_departamentos as $departamento_aux) {
                    $departamento_aux["total_casos"] = 0;
                    $departamento_aux["acumulado_4"] = 0;
                    $mapa_departamentos[$departamento_aux["cod_dep"]] = $departamento_aux;
                }
                
                //Se obtiene el listado de municipios
                $lista_municipios = $dbMunicipios->getListaMunicipiosPoblacion(2020);
                
                //Se obtiene el listado de casos y defunciones totales y de las últimas semanas
                $lista_casos_semanas = $dbInformes->getListaCasosUltimasSemanas();
                $mapa_casos_semanas = array();
                foreach ($lista_casos_semanas as $caso_aux) {
                    $mapa_casos_semanas[$caso_aux["cod_mun_dane"]] = $caso_aux;
                }
                
                //Se crea el archivo de salida de municipios
                $doc_salida = new Spreadsheet();
                $doc_salida
                        ->getProperties()
                        ->setCreator("MSPS")
                        ->setLastModifiedBy("MSPS") // última vez modificado por
                        ->setTitle("Índice de afectación por municipios")
                        ->setSubject("Afectación")
                        ->setDescription("Índice de afectación por municipios")
                        ->setKeywords("Afectación")
                        ->setCategory("Carga masiva");
                
                /******************/
                /*Totales de casos*/
                /******************/
                $doc_salida->setActiveSheetIndex(0)->setTitle("Conf");
                $doc_salida->getActiveSheet()->getColumnDimension("N")->setWidth(18);
                $doc_salida->getActiveSheet()->getColumnDimension("O")->setWidth(27);
                
                $doc_salida->getActiveSheet()
                        ->setCellValue("A1", "cod")
                        ->setCellValue("B1", "munic")
                        ->setCellValue("C1", "Total general")
                        ->setCellValue("D1", "Acumulado4")
                        ->setCellValue("E1", "sem1")
                        ->setCellValue("F1", "sem2")
                        ->setCellValue("G1", "sem3")
                        ->setCellValue("H1", "sem4")
                        ->setCellValue("I1", "sem5")
                        ->setCellValue("J1", "s1")
                        ->setCellValue("K1", "s2")
                        ->setCellValue("L1", "afectacion")
                        ->setCellValue("M1", "criterio")
                        ->setCellValue("N1", "Porcentaje de incremento semanas 2 con respecto a semana 3")
                        ->setCellValue("O1", "Incremento porcentual de los casos en las semanas 4 y 5 con respecto a semanas 2 y 3 hacia atrás, descontado la semana de medición");
                
                $contador_linea = 2;
                foreach ($lista_municipios as $municipio_aux) {
                    //Se buscan los totales de casos del municipio
                    if (isset($mapa_casos_semanas[$municipio_aux["cod_mun_dane"]])) {
                        $arr_aux = $mapa_casos_semanas[$municipio_aux["cod_mun_dane"]];
                        
                        $total_casos = intval($arr_aux["casos"], 10);
                        $semana1 = intval($arr_aux["casos_sem1"], 10);
                        $semana2 = intval($arr_aux["casos_sem2"], 10);
                        $semana3 = intval($arr_aux["casos_sem3"], 10);
                        $semana4 = intval($arr_aux["casos_sem4"], 10);
                        $semana5 = intval($arr_aux["casos_sem5"], 10);
                    } else {
                        $total_casos = 0;
                        $semana1 = 0;
                        $semana2 = 0;
                        $semana3 = 0;
                        $semana4 = 0;
                        $semana5 = 0;
                    }
                    
                    $s1 = $semana2 + $semana3;
                    $s2 = $semana4 + $semana5;
                    $acumulado4 = $s1 + $s2;
                    
                    //Se agregan los totales al mapa de departamentos
                    if (isset($mapa_departamentos[$municipio_aux["cod_dep"]])) {
                        $departamento_aux = $mapa_departamentos[$municipio_aux["cod_dep"]];
                        $departamento_aux["total_casos"] += $total_casos;
                        $departamento_aux["acumulado_4"] += $acumulado4;
                        
                        $mapa_departamentos[$municipio_aux["cod_dep"]] = $departamento_aux;
                    }
                    
                    if ($total_casos > 0) {
                        $afectacion = "Afectación";
                    } else {
                        $afectacion = "No Covid";
                    }
                    
                    if ($acumulado4 > 0) {
                        $criterio = "No Cumple";
                    } else {
                        $criterio = "Cumple";
                    }
                    
                    if ($semana3 > 0) {
                        $incremento_sem2_sem3 = ($semana2 - $semana3) / $semana3;
                    } else {
                        $incremento_sem2_sem3 = 0;
                    }
                    
                    if ($s2 > 0) {
                        $incremento_s1_s2 = ($s1 - $s2) / $s2;
                    } else {
                        $incremento_s1_s2 = 0;
                    }
                    
                    $doc_salida->getActiveSheet()
                            ->setCellValueExplicit("A" . $contador_linea, $municipio_aux["cod_mun_dane"], DataType::TYPE_STRING)
                            ->setCellValue("B" . $contador_linea, $municipio_aux["nom_mun"])
                            ->setCellValue("C" . $contador_linea, $total_casos)
                            ->setCellValue("D" . $contador_linea, $acumulado4)
                            ->setCellValue("E" . $contador_linea, $semana1)
                            ->setCellValue("F" . $contador_linea, $semana2)
                            ->setCellValue("G" . $contador_linea, $semana3)
                            ->setCellValue("H" . $contador_linea, $semana4)
                            ->setCellValue("I" . $contador_linea, $semana5)
                            ->setCellValue("J" . $contador_linea, $s1)
                            ->setCellValue("K" . $contador_linea, $s2)
                            ->setCellValue("L" . $contador_linea, $afectacion)
                            ->setCellValue("M" . $contador_linea, $criterio)
                            ->setCellValue("N" . $contador_linea, $incremento_sem2_sem3)
                            ->setCellValue("O" . $contador_linea, $incremento_s1_s2);
                    
                    $contador_linea++;
                }
                
                /************************/
                /*Totales de defunciones*/
                /************************/
                $hoja_aux = new Worksheet($doc_salida, "Mue");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(1);
                $doc_salida->getActiveSheet()->getColumnDimension("L")->setWidth(18);
                $doc_salida->getActiveSheet()->getColumnDimension("M")->setWidth(27);
                
                $doc_salida->getActiveSheet()
                        ->setCellValue("A1", "cod")
                        ->setCellValue("B1", "munic")
                        ->setCellValue("C1", "Total general")
                        ->setCellValue("D1", "Acumulado4")
                        ->setCellValue("E1", "sem1")
                        ->setCellValue("F1", "sem2")
                        ->setCellValue("G1", "sem3")
                        ->setCellValue("H1", "sem4")
                        ->setCellValue("I1", "sem5")
                        ->setCellValue("J1", "s1")
                        ->setCellValue("K1", "s2")
                        ->setCellValue("L1", "Porcentaje de incremento semanas 2 con respecto a semana 3")
                        ->setCellValue("M1", "Incremento porcentual de los casos en las semanas 4 y 5 con respecto a semanas 2 y 3 hacia atrás, descontado la semana de medición");
                
                $contador_linea = 2;
                foreach ($lista_municipios as $municipio_aux) {
                    //Se buscan los totales de defunciones del municipio
                    if (isset($mapa_casos_semanas[$municipio_aux["cod_mun_dane"]])) {
                        $arr_aux = $mapa_casos_semanas[$municipio_aux["cod_mun_dane"]];
                        
                        $total_def = intval($arr_aux["defunciones"], 10);
                        $semana1_def = intval($arr_aux["defun_sem1"], 10);
                        $semana2_def = intval($arr_aux["defun_sem2"], 10);
                        $semana3_def = intval($arr_aux["defun_sem3"], 10);
                        $semana4_def = intval($arr_aux["defun_sem4"], 10);
                        $semana5_def = intval($arr_aux["defun_sem5"], 10);
                    } else {
                        $total_def = 0;
                        $semana1_def = 0;
                        $semana2_def = 0;
                        $semana3_def = 0;
                        $semana4_def = 0;
                        $semana5_def = 0;
                    }
                    
                    $s1_def = $semana2_def + $semana3_def;
                    $s2_def = $semana4_def + $semana5_def;
                    $acumulado4_def = $s1_def + $s2_def;
                    
                    if ($semana3_def > 0) {
                        $incremento_sem2_sem3 = ($semana2_def - $semana3_def) / $semana3_def;
                    } else {
                        $incremento_sem2_sem3 = 0;
                    }
                    
                    if ($s2_def > 0) {
                        $incremento_s1_s2 = ($s1_def - $s2_def) / $s2_def;
                    } else {
                        $incremento_s1_s2 = 0;
                    }
                    
                    $doc_salida->getActiveSheet()
                            ->setCellValueExplicit("A" . $contador_linea, $municipio_aux["cod_mun_dane"], DataType::TYPE_STRING)
                            ->setCellValue("B" . $contador_linea, $municipio_aux["nom_mun"])
                            ->setCellValue("C" . $contador_linea, $total_def)
                            ->setCellValue("D" . $contador_linea, $acumulado4_def)
                            ->setCellValue("E" . $contador_linea, $semana1_def)
                            ->setCellValue("F" . $contador_linea, $semana2_def)
                            ->setCellValue("G" . $contador_linea, $semana3_def)
                            ->setCellValue("H" . $contador_linea, $semana4_def)
                            ->setCellValue("I" . $contador_linea, $semana5_def)
                            ->setCellValue("J" . $contador_linea, $s1_def)
                            ->setCellValue("K" . $contador_linea, $s2_def)
                            ->setCellValue("L" . $contador_linea, $incremento_sem2_sem3)
                            ->setCellValue("M" . $contador_linea, $incremento_s1_s2);
                    
                    $contador_linea++;
                }
                
                /****************************/
                /*Criterios de clasificación*/
                /****************************/
                $hoja_aux = new Worksheet($doc_salida, "Crit_Clasf");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(2);
                $doc_salida->getActiveSheet()->getColumnDimension("A")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("B")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("C")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("D")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("E")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("F")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("G")->setWidth(22);
                $doc_salida->getActiveSheet()->getColumnDimension("H")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("I")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("J")->setWidth(15);
                $doc_salida->getActiveSheet()->getColumnDimension("K")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("L")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("M")->setWidth(13);
                $doc_salida->getActiveSheet()->getColumnDimension("N")->setWidth(13);
                $doc_salida->getActiveSheet()->getColumnDimension("O")->setWidth(13);
                $doc_salida->getActiveSheet()->getColumnDimension("P")->setWidth(17);
                $doc_salida->getActiveSheet()->getColumnDimension("Q")->setWidth(17);
                $doc_salida->getActiveSheet()->getColumnDimension("R")->setWidth(32);
                $doc_salida->getActiveSheet()->getColumnDimension("S")->setWidth(32);
                
                $doc_salida->getActiveSheet()
                        ->setCellValue("A1", "Cod_Departamento")
                        ->setCellValue("B1", "Departamento")
                        ->setCellValue("C1", "Cod_Municipio")
                        ->setCellValue("D1", "Municipio")
                        ->setCellValue("E1", "Población 2020")
                        ->setCellValue("F1", "Criterio 0 Municipios que no han presentado ningun caso durante toda la pandemia")
                        ->setCellValue("G1", "Criterio 0 plus: Indicador de 4 semanas sin casos Municipios que no han presentado casos confirmados de covid19 en las semanas de estudio (desde el 22 de marzo hasta el 18 de abril/21)")
                        ->setCellValue("H1", "Criterio 1: casos últimas 4 semanas (desde el 22 de marzo hasta el 18 de abril/21) (umbral=  1 por cada 1.000 Habitantes en el municipio)")
                        ->setCellValue("K1", "Criterio 2, positividad (acumulado a la fecha de corte 20210425)")
                        ->setCellValue("N1", "Criterio 3, Incremento de casos (Porcentaje de incremento de casos entre 2 semanas. Semana 2, del 12 - 18 de abril/21 con la semana 3, del 5 al 11 de abril/21)")
                        ->setCellValue("P1", "Criterio 4, Incremento mortalidad (Porcentaje de incremento de casos entre 2 semanas. Semana 2, del 12 - 18 de abril/21 con la semana 3, del 5 al 11 de abril/21)")
                        ->setCellValue("R1", "Comparación de casos acumulados del 5 al 18 de abril/21,  semanas 2 y 3 hacia atrás,  sin contar la semana de actualización. Acumulado del 22 de marzo al 4 de abril/21 , semanas 4 y 5 hacia atrás, sin contar la semana de actualización.")
                        ->setCellValue("S1", "Incremento de muertes  del 5 al 18 de abril/21,  semanas 2 y 3 hacia atrás,  sin contar la semana de actualización. Acumulado del 22 de marzo al 4 de abril/21 , semanas 4 y 5 hacia atrás, sin contar la semana de actualización.");
                
                $doc_salida->getActiveSheet()
                        ->mergeCells("A1:A2")
                        ->mergeCells("B1:B2")
                        ->mergeCells("C1:C2")
                        ->mergeCells("D1:D2")
                        ->mergeCells("E1:E2")
                        ->mergeCells("F1:F2")
                        ->mergeCells("G1:G2")
                        ->mergeCells("H1:J1")
                        ->mergeCells("K1:M1")
                        ->mergeCells("N1:O1")
                        ->mergeCells("P1:Q1");
                
                $doc_salida->getActiveSheet()
                        ->setCellValue("H2", "Casos acumulados últimas 4 semanas")
                        ->setCellValue("I2", "Tasa casos últimas 4 semanas por mil hab")
                        ->setCellValue("J2", "Indicador de tasas de casos acumulados 4 semanas de estudio por mil hab")
                        ->setCellValue("K2", "Pruebas realizadas")
                        ->setCellValue("L2", "% Positividad")
                        ->setCellValue("M2", "Indicador de positividad mayor a 20%")
                        ->setCellValue("N2", "Porcentaje de incremento semanas 2 con respecto a semana 3")
                        ->setCellValue("O2", "Indicador de cremiento porcentual superior a 10 % entre semana 2 y 3")
                        ->setCellValue("P2", "Porcentaje de incremento en las muertes semana 2 con respecto a semana 3")
                        ->setCellValue("Q2", "Indicador de porcentaje de incremento de muertes mayor a 10%")
                        ->setCellValue("R2", "Incremento porcentual de los casos en las semanas 4 y 5 con respecto a semanas 2 y 3 hacia atrás, descontado la semana de medición")
                        ->setCellValue("S2", "Incremento porcentual de las muertes ocurridas en las semanas 4 y 5 hacias atrás, con respecto a semanas 2 y 3 hacia atrás, descontado la semana de medición");
                
                $contador_linea = 3;
                $mapa_criterios_clasificacion = array();
                foreach ($lista_municipios as $municipio_aux) {
                    //Se buscan los totales de casos y defunciones del municipio
                    if (isset($mapa_casos_semanas[$municipio_aux["cod_mun_dane"]])) {
                        $arr_aux = $mapa_casos_semanas[$municipio_aux["cod_mun_dane"]];
                        
                        $total_casos = intval($arr_aux["casos"], 10);
                        $semana1 = intval($arr_aux["casos_sem1"], 10);
                        $semana2 = intval($arr_aux["casos_sem2"], 10);
                        $semana3 = intval($arr_aux["casos_sem3"], 10);
                        $semana4 = intval($arr_aux["casos_sem4"], 10);
                        $semana5 = intval($arr_aux["casos_sem5"], 10);
                        $total_def = intval($arr_aux["defunciones"], 10);
                        $semana1_def = intval($arr_aux["defun_sem1"], 10);
                        $semana2_def = intval($arr_aux["defun_sem2"], 10);
                        $semana3_def = intval($arr_aux["defun_sem3"], 10);
                        $semana4_def = intval($arr_aux["defun_sem4"], 10);
                        $semana5_def = intval($arr_aux["defun_sem5"], 10);
                    } else {
                        $total_casos = 0;
                        $semana1 = 0;
                        $semana2 = 0;
                        $semana3 = 0;
                        $semana4 = 0;
                        $semana5 = 0;
                        $total_def = 0;
                        $semana1_def = 0;
                        $semana2_def = 0;
                        $semana3_def = 0;
                        $semana4_def = 0;
                        $semana5_def = 0;
                    }
                    
                    if (isset($mapa_pruebas[$municipio_aux["cod_mun_dane"]])) {
                        $arr_aux = $mapa_pruebas[$municipio_aux["cod_mun_dane"]];
                        
                        $total_pruebas = intval($arr_aux["total_periodo"], 10);
                        $porcentaje_positivos = floatval($arr_aux["porcentaje"]) / 100;
                    } else {
                        $total_pruebas = 0;
                        $porcentaje_positivos = 0;
                    }
                    
                    $s1 = $semana2 + $semana3;
                    $s2 = $semana4 + $semana5;
                    $acumulado4 = $s1 + $s2;
                    
                    //Criterio 0
                    if ($total_casos > 0) {
                        $afectacion = "Afectación";
                    } else {
                        $afectacion = "No Covid";
                    }
                    
                    //Criterio 0 plus
                    if ($acumulado4 > 0) {
                        $criterio = "No Cumple";
                    } else {
                        $criterio = "Cumple";
                    }
                    
                    if ($semana3 > 0) {
                        $incremento_sem2_sem3 = ($semana2 - $semana3) / $semana3;
                    } else {
                        $incremento_sem2_sem3 = 0;
                    }
                    
                    if ($s2 > 0) {
                        $incremento_s1_s2 = ($s1 - $s2) / $s2;
                    } else {
                        $incremento_s1_s2 = 0;
                    }
                    
                    $s1_def = $semana2_def + $semana3_def;
                    $s2_def = $semana4_def + $semana5_def;
                    $acumulado4_def = $s1_def + $s2_def;
                    
                    if ($semana3_def > 0) {
                        $incremento_sem2_sem3_def = ($semana2_def - $semana3_def) / $semana3_def;
                    } else {
                        $incremento_sem2_sem3_def = 0;
                    }
                    
                    if ($s2_def > 0) {
                        $incremento_s1_s2_def = ($s1_def - $s2_def) / $s2_def;
                    } else {
                        $incremento_s1_s2_def = 0;
                    }
                    
                    //Variable para el conteo de los criterios que se superan
                    $conteo_criterios_aux = 0;
                    
                    //Criterio 1
                    if ($total_casos == 0) {
                        $tasa4 = "No Covid";
                        $indicador_tasa4 = "No Covid";
                    } else {
                        if ($municipio_aux["poblacion"] > 0) {
                            $tasa4 = ($acumulado4 / $municipio_aux["poblacion"]) * 1000;
                        } else {
                            $tasa4 = 0;
                        }
                        if ($tasa4 > 1) {
                            $indicador_tasa4 = "Mayor del umbral";
                            $conteo_criterios_aux++;
                        } else {
                            $indicador_tasa4 = "Menor del umbral";
                        }
                    }
                    
                    //Criterio 2
                    if ($porcentaje_positivos > 0.2) {
                        $positividad20 = "Mayor del umbral";
                        $conteo_criterios_aux++;
                    } else {
                        $positividad20 = "Menor del umbral";
                    }
                    
                    //Criterios 3 y 4
                    if ($total_casos == 0) {
                        $casos_sem2_sem3_10 = "No Covid";
                        $defun_sem2_sem3_10 = "No Covid";
                    } else {
                        if ($incremento_sem2_sem3 > 0.1) {
                            $casos_sem2_sem3_10 = "Mayor del umbral";
                            $conteo_criterios_aux++;
                        } else {
                            $casos_sem2_sem3_10 = "Menor del umbral";
                        }
                        if ($incremento_sem2_sem3_def > 0.1) {
                            $defun_sem2_sem3_10 = "Mayor del umbral";
                            $conteo_criterios_aux++;
                        } else {
                            $defun_sem2_sem3_10 = "Menor del umbral";
                        }
                    }
                    
                    $arr_clasificacion_aux = array(
                        "conteo_criterios" => $conteo_criterios_aux,
                        "incremento_casos" => $incremento_s1_s2,
                        "incremento_defun" => $incremento_s1_s2_def
                    );
                    $mapa_criterios_clasificacion[$municipio_aux["cod_mun_dane"]] = $arr_clasificacion_aux;
                    
                    $doc_salida->getActiveSheet()
                            ->setCellValueExplicit("A" . $contador_linea, $municipio_aux["cod_dep"], DataType::TYPE_STRING)
                            ->setCellValue("B" . $contador_linea, $municipio_aux["nom_dep"])
                            ->setCellValueExplicit("C" . $contador_linea, $municipio_aux["cod_mun_dane"], DataType::TYPE_STRING)
                            ->setCellValue("D" . $contador_linea, $municipio_aux["nom_mun"])
                            ->setCellValue("E" . $contador_linea, $municipio_aux["poblacion"])
                            ->setCellValue("F" . $contador_linea, $afectacion)
                            ->setCellValue("G" . $contador_linea, $criterio)
                            ->setCellValue("H" . $contador_linea, $acumulado4)
                            ->setCellValue("I" . $contador_linea, $tasa4)
                            ->setCellValue("J" . $contador_linea, $indicador_tasa4)
                            ->setCellValue("K" . $contador_linea, $total_pruebas)
                            ->setCellValue("L" . $contador_linea, $porcentaje_positivos)
                            ->setCellValue("M" . $contador_linea, $positividad20)
                            ->setCellValue("N" . $contador_linea, $incremento_sem2_sem3)
                            ->setCellValue("O" . $contador_linea, $casos_sem2_sem3_10)
                            ->setCellValue("P" . $contador_linea, $incremento_sem2_sem3_def)
                            ->setCellValue("Q" . $contador_linea, $defun_sem2_sem3_10)
                            ->setCellValue("R" . $contador_linea, $incremento_s1_s2)
                            ->setCellValue("S" . $contador_linea, $incremento_s1_s2_def);
                    
                    $contador_linea++;
                }
                
                //Se dibujan los bordes
                $doc_salida->getActiveSheet()
                        ->getStyle("A1:S" . ($contador_linea - 1))
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->setColor(new Color('00000000'));
                
                //Se formatea a un decimal la tasa de las últimas 4 semanas
                $doc_salida->getActiveSheet()
                        ->getStyle("I3:I" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode("0.0");
                
                //Se formatean a porcentaje las columnas que lo requieren
                $doc_salida->getActiveSheet()
                        ->getStyle("L3:L" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $doc_salida->getActiveSheet()
                        ->getStyle("N3:N" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $doc_salida->getActiveSheet()
                        ->getStyle("P3:P" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $doc_salida->getActiveSheet()
                        ->getStyle("R3:R" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $doc_salida->getActiveSheet()
                        ->getStyle("S3:S" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                
                //Se cambia el tamaño de letra de la hoja
                $doc_salida->getActiveSheet()->getStyle("A1:S" . ($contador_linea - 1))->getFont()->setSize(8);
                
                //Se ponen los títulos en negrilla
                $doc_salida->getActiveSheet()->getStyle("A1:S2")->getFont()->setBold(true);
                
                //Ajuste de línea para los títulos
                $doc_salida->getActiveSheet()->getStyle("A1:S2")->getAlignment()->setWrapText(true);
                
                //Se centra el texto horizontal y verticalmente
                $doc_salida->getActiveSheet()->getStyle("A1:S" . ($contador_linea - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $doc_salida->getActiveSheet()->getStyle("A1:S" . ($contador_linea - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                /**********************/
                /*Índice de afectación*/
                /**********************/
                $hoja_aux = new Worksheet($doc_salida, "ClasMpios_AfectaCOVID");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(3);
                $doc_salida->getActiveSheet()->getColumnDimension("A")->setWidth(11);
                $doc_salida->getActiveSheet()->getColumnDimension("B")->setWidth(11);
                $doc_salida->getActiveSheet()->getColumnDimension("C")->setWidth(11);
                $doc_salida->getActiveSheet()->getColumnDimension("D")->setWidth(11);
                $doc_salida->getActiveSheet()->getColumnDimension("E")->setWidth(21);
                $doc_salida->getActiveSheet()->getColumnDimension("F")->setWidth(34);
                $doc_salida->getActiveSheet()->getColumnDimension("G")->setWidth(34);
                
                $doc_salida->getActiveSheet()
                        ->setCellValue("A1", "Cod_Departamento")
                        ->setCellValue("B1", "Departamento")
                        ->setCellValue("C1", "Cod_Municipio")
                        ->setCellValue("D1", "Municipio")
                        ->setCellValue("E1", "Clasificación Final")
                        ->setCellValue("F1", "Municipios en descenso o ascenso en casos confirmados")
                        ->setCellValue("G1", "Municipios en descenso o ascenso en muertes confirmadas");
                
                $contador_linea = 2;
                foreach ($lista_municipios as $municipio_aux) {
                    //Se buscan los totales de casos del municipio
                    if (isset($mapa_casos_semanas[$municipio_aux["cod_mun_dane"]])) {
                        $arr_aux = $mapa_casos_semanas[$municipio_aux["cod_mun_dane"]];
                        
                        $total_casos = intval($arr_aux["casos"], 10);
                        $acumulado4 = intval($arr_aux["casos_sem2"], 10) + intval($arr_aux["casos_sem3"], 10) + intval($arr_aux["casos_sem4"], 10) + intval($arr_aux["casos_sem5"], 10);
                    } else {
                        $total_casos = 0;
                        $acumulado4 = 0;
                    }
                    
                    //Se buscan los criterios de clasificacipon del municipio
                    if (isset($mapa_criterios_clasificacion[$municipio_aux["cod_mun_dane"]])) {
                        $arr_aux = $mapa_criterios_clasificacion[$municipio_aux["cod_mun_dane"]];
                        
                        $conteo_criterios = $arr_aux["conteo_criterios"];
                        $incremento_casos = $arr_aux["incremento_casos"];
                        $incremento_defun = $arr_aux["incremento_defun"];
                    } else {
                        $conteo_criterios = 0;
                        $incremento_casos = 0;
                        $incremento_defun = 0;
                    }
                    
                    if ($total_casos > 0) {
                        if ($acumulado4 > 0) {
                            switch ($conteo_criterios) {
                                case 0:
                                    $clasificacion = "Afectación baja";
                                    break;
                                case 1:
                                    $clasificacion = "Afectación moderada";
                                    break;
                                default:
                                    $clasificacion = "Afectación alta";
                                    break;
                            }
                        } else {
                            $clasificacion = "Afectación baja";
                        }
                        
                        if ($incremento_casos > 0) {
                            $estado_casos = "Municipio en crecimiento";
                        } else {
                            $estado_casos = "Municipio en descenso";
                        }
                        
                        if ($incremento_defun > 0) {
                            $estado_defun = "Municipio en crecimiento";
                        } else {
                            $estado_defun = "Municipio en descenso";
                        }
                    } else {
                        $clasificacion = "No Covid";
                        $estado_casos = "No Covid";
                        $estado_defun = "No Covid";
                    }
                    
                    $doc_salida->getActiveSheet()
                            ->setCellValueExplicit("A" . $contador_linea, $municipio_aux["cod_dep"], DataType::TYPE_STRING)
                            ->setCellValue("B" . $contador_linea, $municipio_aux["nom_dep"])
                            ->setCellValueExplicit("C" . $contador_linea, $municipio_aux["cod_mun_dane"], DataType::TYPE_STRING)
                            ->setCellValue("D" . $contador_linea, $municipio_aux["nom_mun"])
                            ->setCellValue("E" . $contador_linea, $clasificacion)
                            ->setCellValue("F" . $contador_linea, $estado_casos)
                            ->setCellValue("G" . $contador_linea, $estado_defun);
                    
                    $contador_linea++;
                }
                
                //Se ponen los títulos en negrilla
                $doc_salida->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(true);
                
                //Ajuste de línea para los títulos
                $doc_salida->getActiveSheet()->getStyle("A1:G1")->getAlignment()->setWrapText(true);
                
                //Se centra el texto horizontal y verticalmente
                $doc_salida->getActiveSheet()->getStyle("A1:G" . ($contador_linea - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $doc_salida->getActiveSheet()->getStyle("A1:G" . ($contador_linea - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                /***************************/
                /*Consolidado departamental*/
                /***************************/
                $hoja_aux = new Worksheet($doc_salida, "Departamentos");
                $doc_salida->addSheet($hoja_aux);
                $doc_salida->setActiveSheetIndex(4);
                $doc_salida->getActiveSheet()->getColumnDimension("A")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("B")->setWidth(28);
                $doc_salida->getActiveSheet()->getColumnDimension("C")->setWidth(12);
                $doc_salida->getActiveSheet()->getColumnDimension("D")->setWidth(14);
                $doc_salida->getActiveSheet()->getColumnDimension("E")->setWidth(16);
                $doc_salida->getActiveSheet()->getColumnDimension("F")->setWidth(24);
                $doc_salida->getActiveSheet()->getColumnDimension("G")->setWidth(21);
                
                $doc_salida->getActiveSheet()
                        ->setCellValue("A1", "Cod_Depto")
                        ->setCellValue("B1", "Nombre_Depto")
                        ->setCellValue("C1", "Población 2020")
                        ->setCellValue("D1", "Casos acumulados Total general")
                        ->setCellValue("E1", "Tasas de casos Covid19 por 100 mil habitantes")
                        ->setCellValue("F1", "Casos en los acumulados 4 semanas de estudio, sin contar la última semana")
                        ->setCellValue("G1", "Tasa casos COVID19 últimas 4 semanas por 100 mil habitantes");
                
                $contador_linea = 2;
                foreach ($mapa_departamentos as $departamento_aux) {
                    //Se calculan las tasas
                    if ($departamento_aux["poblacion"] > 0) {
                        $tasa_general = ($departamento_aux["total_casos"] / $departamento_aux["poblacion"]) * 100000;
                        $tasa_acumulado_4 = ($departamento_aux["acumulado_4"] / $departamento_aux["poblacion"]) * 100000;
                    } else {
                        $tasa_general = 0;
                        $tasa_acumulado_4 = 0;
                    }
                    
                    $doc_salida->getActiveSheet()
                            ->setCellValueExplicit("A" . $contador_linea, $departamento_aux["cod_dep"], DataType::TYPE_STRING)
                            ->setCellValue("B" . $contador_linea, $departamento_aux["nom_dep"])
                            ->setCellValue("C" . $contador_linea, $departamento_aux["poblacion"])
                            ->setCellValue("D" . $contador_linea, $departamento_aux["total_casos"])
                            ->setCellValue("E" . $contador_linea, $tasa_general)
                            ->setCellValue("F" . $contador_linea, $departamento_aux["acumulado_4"])
                            ->setCellValue("G" . $contador_linea, $tasa_acumulado_4);
                    
                    $contador_linea++;
                }
                
                //Se formatean a enteros las columnas de valores
                $doc_salida->getActiveSheet()
                        ->getStyle("C2:G" . ($contador_linea - 1))
                        ->getNumberFormat()
                        ->setFormatCode("#,##0");
                
                //Se ponen los títulos en negrilla
                $doc_salida->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(true);
                
                //Ajuste de línea para los títulos
                $doc_salida->getActiveSheet()->getStyle("A1:G1")->getAlignment()->setWrapText(true);
                
                //Se centra el texto horizontal y verticalmente
                $doc_salida->getActiveSheet()->getStyle("A1:G" . ($contador_linea - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $doc_salida->getActiveSheet()->getStyle("A1:G" . ($contador_linea - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                //Se crea el archivo
                $doc_salida->setActiveSheetIndex(0);
                $xlsxWrite = new XlsxWrite($doc_salida);
                
                //Ruta de guardado
                $nombre_arch_salida = "./tmp/Afectación_" . $id_usuario . ".xlsx";
                $xlsxWrite->save($nombre_arch_salida);
            }
            ?>
        </div>
        <?php
        if ($nombre_arch_salida != "") {
            ?>
            <form name="frm_reporte_afectacion" id="frm_reporte_afectacion" method="post" action="<?= $nombre_arch_salida ?>">
            </form>
            <script id="ajax" type="text/javascript">
                document.getElementById("frm_reporte_afectacion").submit();
            </script>
            <?php
        }
        break;
}
?>
