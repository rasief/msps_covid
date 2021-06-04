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
        $cont_hojas++;
    }
    
    return $lista_entes;
}

function validar_incluir_territorio($nom_dep, $nom_mun, $territorio, $lista_territorios) {
    $bol_incluir = false;
    if ($territorio["tipo"] == "D") {
        if ($nom_dep === $territorio["nom_dep"]) {
            $bol_incluir = true;
            
            //Se verifica si el municipio es un territorio independiente (distrito)
            foreach ($lista_territorios as $territorio_aux) {
                if ($territorio_aux["tipo"] == "M" && $territorio_aux["nom_dep"] === $nom_dep && $territorio_aux["nom_mun"] === $nom_mun) {
                    $bol_incluir = false;
                    break;
                }
            }
        }
    } else if ($territorio["tipo"] == "M") {
        if ($nom_mun === $territorio["nom_mun"]) {
            $bol_incluir = true;
        }
    }
    
    //echo("#" . $territorio["tipo"] . "#" . $territorio["nom_dep"] . "#" . $territorio["nom_mun"] . "#" . $nom_dep . "#" . $nom_mun . "#" . ($bol_incluir ? 1 : 0) . "#<br>");
    return $bol_incluir;
}

function procesar_archivo_excel($nombre_arch, $nombre_tmp, $mapa_distritos, $ruta_base, $col_dep, $col_mun, $mapa_territorios_archivos) {
    //Se obtiene el listado de entes territoriales presentes
    //echo("#" . $nombre_arch . "#" . $nombre_tmp . "#" . $col_dep . "#" . $col_mun . "#<br>");
    $libro_des = IOFactory::load($nombre_tmp);
    $lista_territorios = obtener_entes_territoriales($libro_des, $col_dep, $col_mun, $mapa_distritos);
    $libro_des = null;
    
    foreach ($lista_territorios as $territorio_aux) {
        //Se ignoran los territorios vacíos
        if ($territorio_aux["nom_dep"] === "" || $territorio_aux["nom_dep"] === 0) {
            continue;
        }
        
        $libro_des = IOFactory::load($nombre_tmp);
        
        $lista_hojas = $libro_des->getSheetNames();
        
        foreach ($lista_hojas as $nombre_hoja_aux) {
            //Se renombra la hoja
            $hoja_aux = $libro_des->getSheetByName($nombre_hoja_aux);
            $nombre_hoja_ant = substr("ant-" . $nombre_hoja_aux, 0, 30);
            $hoja_aux->setTitle($nombre_hoja_ant);
            
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
                    
                    //Se aumenta el conteo de archivos y territorios
                    if ($territorio_aux["tipo"] == "D") {
                        $nom_territorio_aux = $territorio_aux["nom_dep"];
                    } else {
                        $nom_territorio_aux = $territorio_aux["nom_mun"];
                    }
                    if (isset($mapa_territorios_archivos[$nom_territorio_aux][$nombre_arch])) {
                        $mapa_territorios_archivos[$nom_territorio_aux][$nombre_arch] += 1;
                    } else {
                        $mapa_territorios_archivos[$nom_territorio_aux][$nombre_arch] = 1;
                    }
                    
                    $cont_fila_dest++;
                }
            }
            
            //Se borra la hoja base
            $indice_aux = $libro_des->getIndex(
                $libro_des->getSheetByName($nombre_hoja_ant)
            );
            $libro_des->removeSheetByIndex($indice_aux);
            
            $hoja_aux = null;
            $hoja_des_aux = null;
        }
        
        //Se crea la carpeta del territorio
        if ($territorio_aux["tipo"] == "D") {
            $nombre_territorio = str_replace(",", "", str_replace(".", "", $territorio_aux["nom_dep"]));
        } else {
            $nombre_territorio = str_replace(",", "", str_replace(".", "", $territorio_aux["nom_mun"]));
        }
        
        //Se ajustan los nombres de algunos territorios para mantenerlos en la misma carpeta
        switch ($nombre_territorio) {
            case "BOGOTÁ D C":
                $nombre_territorio = "BOGOTÁ DC";
                break;
            case "CARTAGENA":
                $nombre_territorio = "CARTAGENA DE INDIAS";
                break;
            case "MOMPÓS":
                $nombre_territorio = "SANTA CRUZ DE MOMPOX";
                break;
            case "QUINDIO":
                $nombre_territorio = "QUINDÍO";
                break;
            case "VALLE":
                $nombre_territorio = "VALLE DEL CAUCA";
                break;
        }
        
        $ruta_territorio = $ruta_base . $nombre_territorio . "/";
        mkdir($ruta_territorio, 0777, true);
        
        //Se crea el archivo del territorio
        $writer = new XlsxWrite($libro_des);
        //echo("2 " . $ruta_territorio . $nombre_territorio . " " . $nombre_arch . "<br>");
        $writer->save($ruta_territorio . $nombre_territorio . " " . $nombre_arch);
        $libro_des = null;
    }
    
    return $mapa_territorios_archivos;
}

function crear_archivo_resumen($mapa_territorios_archivos, $ruta_base) {
    $doc_salida = new Spreadsheet();
    $doc_salida
            ->getProperties()
            ->setCreator("MSPS")
            ->setLastModifiedBy("MSPS")
            ->setTitle("Consolidado de registros por ente territorial")
            ->setSubject("Consolidado")
            ->setDescription("Consolidado de registros por ente territorial")
            ->setKeywords("Consolidado")
            ->setCategory("Carga masiva");
    
    $doc_salida->setActiveSheetIndex(0)->setTitle("Conf");
    $doc_salida->getActiveSheet()->getColumnDimension("A")->setWidth(30);
    $doc_salida->getActiveSheet()->getColumnDimension("B")->setWidth(40);
    $doc_salida->getActiveSheet()->getColumnDimension("C")->setWidth(20);
    
    $doc_salida->getActiveSheet()
            ->setCellValue("A1", "Ente territorial")
            ->setCellValue("B1", "Archivo")
            ->setCellValue("C1", "Cantidad de registros");
    
    $contador_linea = 2;
    $nom_dep_ant = "";
    foreach ($mapa_territorios_archivos as $nom_dep_aux => $mapa_archivos_aux) {
        foreach ($mapa_archivos_aux as $mom_arch_aux => $cantidad_aux) {
            if ($nom_dep_aux != $nom_dep_ant) {
                $nom_dep_act = $nom_dep_aux;
            } else {
                $nom_dep_act = "";
            }
            $doc_salida->getActiveSheet()
                    ->setCellValue("A" . $contador_linea, $nom_dep_act)
                    ->setCellValue("B" . $contador_linea, $mom_arch_aux)
                    ->setCellValue("C" . $contador_linea, $cantidad_aux);
            
            $nom_dep_ant = $nom_dep_aux;
            $contador_linea++;
        }
    }
    
    //Se crea el archivo
    $doc_salida->setActiveSheetIndex(0);
    $xlsxWrite = new XlsxWrite($doc_salida);
    
    //Ruta de guardado
    $nombre_arch_salida = $ruta_base . "consolidado.xlsx";
    $xlsxWrite->save($nombre_arch_salida);
}

function limpiar_directorio($directorio, $bol_borrar) {
    $it = new RecursiveDirectoryIterator($directorio, RecursiveDirectoryIterator::SKIP_DOTS);
    $archivos = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($archivos as $archivo_aux) {
        if ($archivo_aux->isDir()){
            rmdir($archivo_aux->getRealPath());
        } else {
            unlink($archivo_aux->getRealPath());
        }
    }
    
    if ($bol_borrar) {
        rmdir($directorio);
    }
}

function crear_paquete_zip($ruta_base, $contrasena) {
    $arr_carpetas = scandir($ruta_base);
    
    foreach ($arr_carpetas as $carpeta_aux) {
        if ($carpeta_aux != "." && $carpeta_aux != ".." && is_dir($ruta_base . $carpeta_aux)) {
            //Se crea un archivo zip con el contenido de la carpeta
            $zip = new ZipArchive();
            
            $nombre_arch_zip = $ruta_base . $carpeta_aux . ".zip";
            
            if (file_exists($nombre_arch_zip)) {
                unlink($nombre_arch_zip); 
            }
            
            if ($zip->open($nombre_arch_zip, ZIPARCHIVE::CREATE) != TRUE) {
                die ("No se pudo crear el archivo");
            }
            
            //Se agregan los archivos que se encuentran dentro de la carpeta
            $arr_archivos = scandir($ruta_base . $carpeta_aux);
            foreach ($arr_archivos as $archivo_aux) {
                if ($archivo_aux != "." && $archivo_aux != ".." && is_file($ruta_base . $carpeta_aux . "/" . $archivo_aux)) {
                    $zip->addFromString($archivo_aux, file_get_contents($ruta_base . $carpeta_aux . "/" . $archivo_aux));
                    $zip->setEncryptionName($archivo_aux, ZipArchive::EM_AES_256, $contrasena);
                }
            }
            $zip->close();
            
            //Se borra la carpeta de archivos
            limpiar_directorio($ruta_base . $carpeta_aux, true);
        }
    }
    
    //Se crea un único archivo zip con todo el contenido generado
    $arr_partes = explode("/", $ruta_base);
    
    if (count($arr_partes) >= 4) {
        $nombre_paquete = $arr_partes[0] . "/" . "archivos_" . $arr_partes[1] . "-" . $arr_partes[2] . "-" . $arr_partes[3] . ".zip";
        
        $zip = new ZipArchive();
        
        if (file_exists($nombre_paquete)) {
            unlink($nombre_paquete); 
        }
        
        if ($zip->open($nombre_paquete, ZIPARCHIVE::CREATE) != TRUE) {
            die ("No se pudo crear el archivo consolidado");
        }
        
        //Se agregan los archivos que se encuentran dentro de la carpeta
        $arr_archivos = scandir($ruta_base);
        foreach ($arr_archivos as $archivo_aux) {
            if ($archivo_aux != "." && $archivo_aux != ".." && is_file($ruta_base . $archivo_aux)) {
                $zip->addFromString($archivo_aux, file_get_contents($ruta_base . $archivo_aux));
            }
        }
        $zip->close();
        
        //Se borra la carpeta de archivos
        limpiar_directorio($arr_partes[0] . "/" . $arr_partes[1], true);
    } else {
        $nombre_paquete = "";
    }
    
    return $nombre_paquete;
}

$opcion = $_POST['opcion'];

switch ($opcion) {
    case "1": //Procesamiento de archivos de Excel
        ?>
        <div id="d_carga_interna">
            <?php
            $fecha = $utilidades->str_decode($_POST["fecha"]);
            
            //Se crea la ruta que contendrá los archivos a generar
            $arr_aux = explode("-", $fecha);
            $ruta_base = "arch_excel/" . $arr_aux[0] . "/" . $arr_aux[1] . "/" . $arr_aux[2] . "/";
            mkdir($ruta_base, 0777, true);
            limpiar_directorio($ruta_base, false);
            
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
            
            //Mapa en el que se guardarán los totales de archvos y registros por territorio
            $mapa_territorios_archivos = array();
            
            foreach ($arr_nombres_aux as $k => $nombre_ori_aux) {
                $extension_aux = strtolower($utilidades->get_extension_arch($nombre_ori_aux));
                
                if ($extension_aux === "xlsx") {
                    //Se obtiene el tipo de archivo cargado
                    $tipo_arch = 0;
                    if (strpos(strtolower($nombre_ori_aux), "monitoreo aleatorio") === 0) {
                        $tipo_arch = 1;
                        $col_dep = "V";
                        $col_mun = "W";
                    } else if (strpos(strtolower($nombre_ori_aux), "monitoreo estado de salud") === 0) {
                        $tipo_arch = 2;
                        $col_dep = "R";
                        $col_mun = "S";
                    } else if (strpos(strtolower($nombre_ori_aux), "monitoreo viajeros") === 0) {
                        $tipo_arch = 3;
                        $col_dep = "P";
                        $col_mun = "Q";
                    } else if (strpos(strtolower($nombre_ori_aux), "necesidades alojamiento") === 0) {
                        $tipo_arch = 4;
                        $col_dep = "I";
                        $col_mun = "H";
                    } else if (strpos(strtolower($nombre_ori_aux), "necesidades transferencia") === 0) {
                        $tipo_arch = 5;
                        $col_dep = "I";
                        $col_mun = "H";
                    } else if (strpos(strtolower($nombre_ori_aux), "no asegurados") === 0) {
                        $tipo_arch = 6;
                        $col_dep = "I";
                        $col_mun = "H";
                    } else if (strpos(strtolower($nombre_ori_aux), "no contactados") === 0) {
                        $tipo_arch = 7;
                        $col_dep = "J";
                        $col_mun = "I";
                    } else if (strpos(strtolower($nombre_ori_aux), "viajeros no contactados") === 0) {
                        $tipo_arch = 8;
                        $col_dep = "O";
                        $col_mun = "P";
                    } else if (strpos(strtolower($nombre_ori_aux), "viajeros") === 0) {
                        $tipo_arch = 9;
                        $col_dep = "P";
                        $col_mun = "Q";
                    } else if (strpos(strtolower($nombre_ori_aux), "reporte nacional de salud") === 0) {
                        $tipo_arch = 10;
                        $col_dep = "N";
                        $col_mun = "O";
                    }
                    
                    if ($tipo_arch > 0) {
                        $mapa_territorios_archivos = procesar_archivo_excel($nombre_ori_aux, $arr_tmp_nombres_aux[$k], $mapa_distritos, $ruta_base, $col_dep, $col_mun, $mapa_territorios_archivos);
                    }
                }
            }
            ?>
        </div>
        <?php
        crear_archivo_resumen($mapa_territorios_archivos, $ruta_base);
        
        $nombre_paquete = crear_paquete_zip($ruta_base, "1234");
        
        if ($nombre_paquete != "") {
            ?>
            <script id="ajax">
                window.open("<?= $nombre_paquete ?>", "_blank");
            </script>
            <?php
        }
        break;
}
?>
