<?php
require_once("../funciones/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWrite;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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

$ruta_base = "C:/Users/adiaz/OneDrive/Documentos/Feisar/MinSalud/Separación de archivos/";
$nombre_arch_ori = "Monitoreo Aleatorio 20210124 - nopass.xlsx";
$nombre_arch_des = "Salida.xlsx";

copy($ruta_base . $nombre_arch_ori, $ruta_base . $nombre_arch_des);

$libro_des = IOFactory::load($ruta_base . $nombre_arch_des);
$libro_des->getSecurity()->setWorkbookPassword("1234");

/*$reader = new XlsxRead();
$reader->setReadDataOnly(false);
$libro_ori = $reader->load($ruta_base . $nombre_arch_ori);*/

$lista_hojas = $libro_des->getSheetNames();

foreach ($lista_hojas as $nombre_hoja_aux) {
    //Se crea la hoja de destino
    $hoja_des_aux = new Worksheet($libro_des, "A" . $nombre_hoja_aux);
    $libro_des->addSheet($hoja_des_aux);
    
    $hoja_aux = $libro_des->getSheetByName($nombre_hoja_aux);
    
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
    
    for ($i = 1; $i <= $num_filas; $i++) {
        $nombre_celda_ini = "A" . (string)($i);
        $nombre_celda_fin = Coordinate::stringFromColumnIndex($num_cols) . (string)($i);
        
        $nombre_celda_dest_ini = $nombre_celda_ini;
        
        CopyUtils::copyRows($hoja_aux, $nombre_celda_ini . ":" . $nombre_celda_fin, $nombre_celda_dest_ini, $hoja_des_aux);
        /*for ($j = 1; $j <= $num_cols; $j++) {
            $celda_aux = $hoja_aux->getCellByColumnAndRow($j, $i);
            $estilo_aux = $hoja_aux->getStyleByColumnAndRow($j, $i);
            $nombre_celda_dest = Coordinate::stringFromColumnIndex($j) . (string)($i);
            //$hoja_des_aux->setCellValueByColumnAndRow($j, $i, $celda_aux->getValue());
            $hoja_des_aux->setCellValue($nombre_celda_dest, $celda_aux->getValue());
            $hoja_des_aux->duplicateStyle($estilo_aux, $nombre_celda_dest);
            //var_dump($estilo_aux);
            //echo($celda_aux->getValue() . " ");
        }*/
        //echo("<br>");
    }
    $cont_hojas++;
}

$writer = new XlsxWrite($libro_des);
$writer->save($ruta_base . $nombre_arch_des);

//Se crea el archivo zip
$zip = new ZipArchive();

$nombre_arch_zip = $ruta_base . "Salida.zip";

if (file_exists($nombre_arch_zip)) {
    unlink($nombre_arch_zip); 
}

if ($zip->open($nombre_arch_zip, ZIPARCHIVE::CREATE) != TRUE) {
    die ("No se pudo crear el archivo");
}

$zip->addFromString($nombre_arch_des, file_get_contents($ruta_base . $nombre_arch_des));
$zip->setEncryptionName($nombre_arch_des, ZipArchive::EM_AES_256, '1234');
$zip->addFromString($nombre_arch_ori, file_get_contents($ruta_base . $nombre_arch_ori));
$zip->setEncryptionName($nombre_arch_ori, ZipArchive::EM_AES_256, '1234');
//$zip->addFile($ruta_base . $nombre_arch_des, $nombre_arch_des);
//$zip->setEncryptionName($ruta_base . $nombre_arch_des, ZipArchive::EM_AES_256, '1234');

$zip->close();
?>
