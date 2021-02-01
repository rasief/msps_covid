<?php
session_start();

require_once("../db/DbDefunciones.php");
require_once("../db/DbMunicipios.php");
require_once("../db/DbVariables.php");
require_once("../funciones/Utilidades.php");

require_once("../funciones/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWrite;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$dbDefunciones = new DbDefunciones();
$dbMunicipios = new DbMunicipios();
$dbVariables = new DbVariables();
$utilidades = new Utilidades();

class CopyUtils {
    public static function copyRows(Worksheet $sheet, $srcRange, $dstCell, Worksheet $destSheet = null) {
        if( !isset($destSheet)) {
            $destSheet = $sheet;
        }

        if( !preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch) ) {
            // Invalid src range
            return;
        }

        if( !preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch) ) {
            // Invalid dest cell
            return;
        }

        $srcColumnStart = $srcRangeMatch[1];
        $srcRowStart = $srcRangeMatch[2];
        $srcColumnEnd = $srcRangeMatch[3];
        $srcRowEnd = $srcRangeMatch[4];

        $destColumnStart = $destCellMatch[1];
        $destRowStart = $destCellMatch[2];

        $srcColumnStart = Coordinate::columnIndexFromString($srcColumnStart);
        $srcColumnEnd = Coordinate::columnIndexFromString($srcColumnEnd);
        $destColumnStart = Coordinate::columnIndexFromString($destColumnStart);

        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;
            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $dataType = $cell->getDataType();
                $style = $sheet->getStyleByColumnAndRow($col, $row);
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $destSheet->setCellValue($dstCell, $cell->getValue());
                $destSheet->duplicateStyle($style, $dstCell);
                $destSheet->getCell($dstCell)->setDataType($dataType);
                
                // Set width of column, but only once per column
                if ($rowCount === 0) {
                    $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                    $destSheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setAutoSize(false);
                    $destSheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setWidth($w);
                }

                $colCount++;
            }

            $h = $sheet->getRowDimension($row)->getRowHeight();
            $destSheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);

            $rowCount++;
        }

        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));

            $relativeColStart = $mergeColSrcStart - $srcColumnStart;
            $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
            $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
            $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;

            if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                $targetRowStart = $destRowStart + $relativeRowStart;
                $targetRowEnd = $destRowStart + $relativeRowEnd;

                $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                //Merge target cells
                $destSheet->mergeCells($merge);
            }
        }
    }

    public static function copyStyleXFCollection(Spreadsheet $sourceSheet, Spreadsheet $destSheet) {
        $collection = $sourceSheet->getCellXfCollection();

        foreach ($collection as $key => $item) {
            $destSheet->addCellXf($item);
        }
    }
}

function obtener_entes_territoriales($libro_excel, $col_dep, $col_mun, $mapa_distritos) {
    $utilidades = new Utilidades();
    
    $lista_hojas = $libro_excel->getSheetNames();
    
    $lista_entes = array();
    foreach ($lista_hojas as $nombre_hoja_aux) {
        //Se crea la hoja de destino
        $hoja_aux = $libro_excel->getSheetByName($nombre_hoja_aux);
        
        //Se obtiene el número de columnas
        $i = 1;
        while ($hoja_aux->getCellByColumnAndRow($i, 1)->getValue() != null) {
            $i++;
        }
        $num_cols = $i - 1;
        
        //Se obtiene el número de filas
        $i = 1;
        while ($hoja_aux->getCellByColumnAndRow(1, $i)->getValue() != null) {
            $i++;
        }
        $num_filas = $i - 1;
        
        if ($num_cols >= 23) {
            for ($i = 2; $i <= $num_filas; $i++) {
                $nombre_celda_dep = $col_dep . (string)($i);
                $nombre_celda_mun = $col_mun . (string)($i);
                
                $nom_dep = $hoja_aux->getCell($nombre_celda_dep)->getValue();
                $nom_mun = $hoja_aux->getCell($nombre_celda_mun)->getValue();
                $nom_dep_aux = $utilidades->simplificar_texto($nom_dep);
                $nom_num_aux = $utilidades->simplificar_texto($nom_mun);
                
                //Se verifica si se trata de un distrito
                $bol_distrito = false;
                if (isset($mapa_distritos[$nom_dep_aux])) {
                    $arr_aux = $mapa_distritos[$nom_dep_aux];
                    if (in_array($nom_num_aux, $arr_aux)) {
                        $bol_distrito = true;
                    }
                }
                
                if ($bol_distrito) {
                    if (!isset($lista_entes[$nom_mun])) {
                        $lista_entes[$nom_mun]["tipo"] = "M";
                        $lista_entes[$nom_mun]["nom_dep"] = $nom_dep;
                        $lista_entes[$nom_mun]["nom_mun"] = $nom_mun;
                    }
                } else {
                    if (!isset($lista_entes[$nom_dep])) {
                        $lista_entes[$nom_dep]["tipo"] = "D";
                        $lista_entes[$nom_dep]["nom_dep"] = $nom_dep;
                        $lista_entes[$nom_dep]["nom_mun"] = "";
                    }
                }
            }
        }
        $cont_hojas++;
    }
    
    return $lista_entes;
}

function validar_incluir_territorio($nom_dep, $nom_mun, $territorio, $lista_territorios) {
    $bol_incluir = false;
    if ($territorio["tipo"] == "D") {
        if ($nom_dep == $territorio["nom_dep"]) {
            $bol_incluir = true;
            
            //Se verifica si el municipio es un territorio independiente (distrito)
            foreach ($lista_territorios as $territorio_aux) {
                if ($territorio_aux["tipo"] == "M" && $territorio_aux["nom_dep"] == $nom_dep && $territorio_aux["nom_mun"] == $nom_mun) {
                    $bol_incluir = false;
                    break;
                }
            }
        }
    } else {
        if ($nom_mun == $territorio["nom_mun"]) {
            $bol_incluir = true;
        }
    }
    
    return $bol_incluir;
}

function procesar_monitoreo_aleatorio($nombre_arch, $nombre_tmp, $mapa_distritos, $fecha) {
    $col_dep = "V";
    $col_mun = "W";
    
    //Se crea la ruta que contendrá los archivos a generar
    $arr_aux = explode("-", $fecha);
    $ruta_base = "arch_excel/" . $arr_aux[0] . "/" . $arr_aux[1] . "/" . $arr_aux[2] . "/";
    
    //Se obtiene el listado de entes territoriales presentes
    $lista_territorios = obtener_entes_territoriales(IOFactory::load($nombre_tmp), $col_dep, $col_mun, $mapa_distritos);
    
    foreach ($lista_territorios as $territorio_aux) {
        $libro_des = IOFactory::load($nombre_tmp);
        
        $lista_hojas = $libro_des->getSheetNames();
        
        foreach ($lista_hojas as $nombre_hoja_aux) {
            //Se renombra la hoja
            $hoja_aux = $libro_des->getSheetByName($nombre_hoja_aux);
            $hoja_aux->setTitle("ant-" . $nombre_hoja_aux);
            
            //Se crea la hoja de destino
            $hoja_des_aux = new Worksheet($libro_des, $nombre_hoja_aux);
            $libro_des->addSheet($hoja_des_aux);
            
            //Se obtiene el número de filas
            $i = 1;
            while ($hoja_aux->getCellByColumnAndRow(1, $i)->getValue() != null) {
                $i++;
            }
            $num_filas = $i - 1;
            
            //Se obtiene el número de columnas
            $i = 1;
            while ($hoja_aux->getCellByColumnAndRow($i, 1)->getValue() != null) {
                $i++;
            }
            $num_cols = $i - 1;
            
            //Se copia la celda de encabezado
            $nombre_celda_fin = Coordinate::stringFromColumnIndex($num_cols) . "1";
            CopyUtils::copyRows($hoja_aux, "A1:" . $nombre_celda_fin, "A1", $hoja_des_aux);
            
            $cont_fila_dest = 2;
            for ($i = 2; $i <= $num_filas; $i++) {
                //Se verifica si la celda corresponde al territorio
                $nom_dep_aux = $hoja_aux->getCell($col_dep . $i)->getValue();
                $nom_mun_aux = $hoja_aux->getCell($col_mun . $i)->getValue();
                $bol_incluir = validar_incluir_territorio($nom_dep_aux, $nom_mun_aux, $territorio_aux, $lista_territorios);
                
                if ($bol_incluir) {
                    $nombre_celda_ini = "A" . (string)($i);
                    $nombre_celda_fin = Coordinate::stringFromColumnIndex($num_cols) . (string)($i);
                    $nombre_celda_dest_ini = "A" . $cont_fila_dest;
                    CopyUtils::copyRows($hoja_aux, $nombre_celda_ini . ":" . $nombre_celda_fin, $nombre_celda_dest_ini, $hoja_des_aux);
                    
                    $cont_fila_dest++;
                }
            }
            
            //Se borra la hoja base
            $indice_aux = $libro_des->getIndex(
                $libro_des->getSheetByName("ant-" . $nombre_hoja_aux)
            );
            $libro_des->removeSheetByIndex($indice_aux);
            
            $cont_hojas++;
        }
        
        //Se crea la carpeta del territorio
        if ($territorio_aux["tipo"] == "D") {
            $nombre_territorio = str_replace(",", "", str_replace(".", "", $territorio_aux["nom_dep"]));
        } else {
            $nombre_territorio = str_replace(",", "", str_replace(".", "", $territorio_aux["nom_mun"]));
        }
        $ruta_territorio = $ruta_base . $nombre_territorio . "/";
        mkdir($ruta_territorio, 0777, true);
        
        //Se crea el archivo del territorio
        $writer = new XlsxWrite($libro_des);
        $writer->save($ruta_territorio . $nombre_territorio . " " . $nombre_arch);
    }
    //Se crea el archivo zip
    /*$zip = new ZipArchive();
    
    $nombre_arch_zip = $ruta_base . "Salida.zip";
    
    if (file_exists($nombre_arch_zip)) {
        unlink($nombre_arch_zip); 
    }
    
    if ($zip->open($nombre_arch_zip, ZIPARCHIVE::CREATE) != TRUE) {
        die ("No se pudo crear el archivo");
    }
    
    $zip->addFromString($nombre_arch, file_get_contents($ruta_base . $nombre_arch));
    $zip->setEncryptionName($nombre_arch, ZipArchive::EM_AES_256, '1234');
    
    $zip->close();*/
}

$opcion = $_POST['opcion'];

switch ($opcion) {
    case "1": //Procesamiento de archivos de Excel
        @$fecha = $utilidades->str_decode($_POST["fecha"]);
        
        //Se cargan los nombres de los archivos
        $arr_nombres_aux = $_FILES["fil_arch"]["name"];
        $arr_tmp_nombres_aux = $_FILES["fil_arch"]["tmp_name"];
        
        if (!is_array($arr_nombres_aux)) {
            $arr_nombres_aux = array($arr_nombres_aux);
            $arr_tmp_nombres_aux = array($arr_tmp_nombres_aux);
        }
        
        //Lista de entes territoriales especiales (distritos)
        $lista_distritos = $dbMunicipios->getListaDistritosClasificacion();
        $mapa_distritos = array();
        foreach ($lista_distritos as $distrito_aux) {
            $nom_dep_aux = $utilidades->simplificar_texto($distrito_aux["nom_dep"]);
            $nom_mun_aux = $utilidades->simplificar_texto($distrito_aux["nom_mun"]);
            if (isset($mapa_distritos[$nom_dep_aux])) {
                $arr_aux = $mapa_distritos[$nom_dep_aux];
            } else {
                $arr_aux = array();
            }
            if (!in_array($nom_mun_aux, $arr_aux)) {
                array_push($arr_aux, $nom_mun_aux);
            }
            $mapa_distritos[$nom_dep_aux] = $arr_aux;
        }
        
        foreach ($arr_nombres_aux as $k => $nombre_ori_aux) {
            $extension_aux = strtolower($utilidades->get_extension_arch($nombre_ori_aux));
            
            if ($extension_aux === "xlsx") {
                //Se obtiene el tipo de archivo cargado
                $tipo_arch = 0;
                if (strpos(strtolower($nombre_ori_aux), "monitoreo aleatorio") === 0) {
                    $tipo_arch = 1;
                }
                
                switch ($tipo_arch) {
                    case 1: //Monitoreo aleatorio
                        procesar_monitoreo_aleatorio($nombre_ori_aux, $arr_tmp_nombres_aux[$k], $mapa_distritos, $fecha);
                        break;
                }
            }
        }
        break;
}
?>
