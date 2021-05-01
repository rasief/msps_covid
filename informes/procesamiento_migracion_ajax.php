<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbListas.php");
require_once("../db/DbMunicipios.php");
require_once("../funciones/Utilidades.php");

require_once("../funciones/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWrite;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$dbVariables = new DbVariables();
$dbListas = new DbListas();
$dbMunicipios = new DbMunicipios();

$utilidades = new Utilidades();

$opcion = $_REQUEST['opcion'];

switch ($opcion) {
    case "1": //Procesamiento del archivo de migración
        $id_usuario = $_SESSION["idUsuario"];
        $nombre_arch_salida = "";
        ?>
        <div id="d_carga_interna">
            <?php
            //Se carga el listado de tipos de documento y se convierte en un mapa
            $lista_tipos_doc = $dbListas->getListaDetalles(1, 1);
            $mapa_tipos_doc = array();
            foreach ($lista_tipos_doc as $tipo_doc_aux) {
                $mapa_tipos_doc[$tipo_doc_aux["nombre_detalle"]] = $tipo_doc_aux["codigo_detalle"];
            }
            
            //Se carga el listado de municipios y se convierte en un mapa
            $lista_municipios = $dbMunicipios->getListaMunicipiosMayusc();
            $mapa_municipios = array();
            foreach ($lista_municipios as $municipio_aux) {
                $mapa_municipios[$utilidades->simplificar_texto($municipio_aux["nom_dep"] . "-" . $municipio_aux["nom_mun"])] = $municipio_aux["cod_mun_dane"];
            }
            
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
                $lista_valores = array();
                for ($i = 2; $i <= $num_filas; $i++) {
                    //Se cargan las variables que se consideran relevantes para la clasificación del registro
                    $tipo_doc_aux = trim($hoja_aux->getCell("A" . $i)->getValue());
                    if (isset($mapa_tipos_doc[$tipo_doc_aux])) {
                        $cod_tipo_doc_aux = $mapa_tipos_doc[$tipo_doc_aux];
                    } else {
                        $cod_tipo_doc_aux = "";
                    }
                    $num_doc_aux = $hoja_aux->getCell("B" . $i)->getValue();
                    $apellido_1_aux = $hoja_aux->getCell("D" . $i)->getValue();
                    $apellido_2_aux = $hoja_aux->getCell("E" . $i)->getValue();
                    $nombres_aux = trim($hoja_aux->getCell("F" . $i)->getValue());
                    $pos_aux = strpos($nombres_aux, " ");
                    if ($pos_aux !== false) {
                        $nombre_1_aux = substr($nombres_aux, 0, $pos_aux);
                        $nombre_2_aux = substr($nombres_aux, $pos_aux + 1);
                    } else {
                        $nombre_1_aux = $nombres_aux;
                        $nombre_2_aux = "";
                    }
                    $sexo_aux = $hoja_aux->getCell("K" . $i)->getValue();
                    $fecha_nac_aux = $hoja_aux->getCell("M" . $i)->getValue();
                    //$fecha_nac_aux = XlsxDate::excelToDateTimeObject($fecha_nac_obj)->format("d/m/Y");
                    $nom_dep_aux = $hoja_aux->getCell("N" . $i)->getValue();
                    $nom_mun_aux = $hoja_aux->getCell("O" . $i)->getValue();
                    if (isset($mapa_municipios[$utilidades->simplificar_texto($nom_dep_aux . "-" . $nom_mun_aux)])) {
                        $cod_mun_aux = $mapa_municipios[$utilidades->simplificar_texto($nom_dep_aux . "-" . $nom_mun_aux)];
                    } else {
                        $cod_mun_aux = "";
                    }
                    
                    $arr_aux = array(
                        "cod_tipo_doc" => $cod_tipo_doc_aux,
                        "num_doc" => $num_doc_aux,
                        "nombre_1" => $nombre_1_aux,
                        "nombre_2" => $nombre_2_aux,
                        "apellido_1" => $apellido_1_aux,
                        "apellido_2" => $apellido_2_aux,
                        "sexo" => $sexo_aux,
                        "fecha_nac" => $fecha_nac_aux,
                        "cod_mun" => $cod_mun_aux,
                    );
                    array_push($lista_valores, $arr_aux);
                    //echo("#" . $cod_tipo_doc_aux . "#" . $num_doc_aux . "#" . $apellido_1_aux . "#" . $apellido_2_aux . "#" . $nombre_1_aux . "#" . $nombre_2_aux . "#" . $sexo_aux . "#" . $fecha_nac_aux . "#" . $cod_mun_aux . "#" . $nom_dep_aux . "#" . $nom_mun_aux. "#<br>");
                }
                
                unset($libro_des);
                
                //Se crea el archivo de salida
                $doc_salida = new Spreadsheet();
                $doc_salida
                        ->getProperties()
                        ->setCreator("MSPS")
                        ->setLastModifiedBy("MSPS") // última vez modificado por
                        ->setTitle("Migrantes para SegCovid-19")
                        ->setSubject("Migrantes")
                        ->setDescription("Archivo de migrantes para carga en SegCovid-19")
                        ->setKeywords("Migrantes")
                        ->setCategory("Carga masiva");
                
                $doc_salida->setActiveSheetIndex(0)->setTitle("Hoja1");
                $doc_salida->getActiveSheet()->getColumnDimension("C")->setWidth(17);
                $doc_salida->getActiveSheet()->getColumnDimension("H")->setWidth(15);

                $doc_salida->getActiveSheet()
                        ->setCellValue("A1", "ID")
                        ->setCellValue("B1", "ID_TipoDocumento")
                        ->setCellValue("C1", "NumeroDocumento")
                        ->setCellValue("D1", "PrimerNombre")
                        ->setCellValue("E1", "SegundoNombre")
                        ->setCellValue("F1", "PrimerApellido")
                        ->setCellValue("G1", "SegundoApellido")
                        ->setCellValue("H1", "FechaNacimiento")
                        ->setCellValue("I1", "ID_Sexo")
                        ->setCellValue("J1", "asegurador codigo")
                        ->setCellValue("K1", "fuente inicial del caso")
                        ->setCellValue("L1", "asegurador codigo");
                
                $contador_linea = 2;
                foreach ($lista_valores as $valor_aux) {
                    $doc_salida->getActiveSheet()
                            ->setCellValue("A" . $contador_linea, "")
                            ->setCellValue("B" . $contador_linea, $valor_aux["cod_tipo_doc"])
                            ->setCellValueExplicit("C" . $contador_linea, $valor_aux["num_doc"], DataType::TYPE_STRING)
                            ->setCellValue("D" . $contador_linea, $valor_aux["nombre_1"])
                            ->setCellValue("E" . $contador_linea, $valor_aux["nombre_2"])
                            ->setCellValue("F" . $contador_linea, $valor_aux["apellido_1"])
                            ->setCellValue("G" . $contador_linea, $valor_aux["apellido_2"])
                            ->setCellValue("H" . $contador_linea, $valor_aux["fecha_nac"])
                            ->setCellValue("I" . $contador_linea, $valor_aux["sexo"])
                            ->setCellValue("J" . $contador_linea, "")
                            ->setCellValue("K" . $contador_linea, "01")
                            ->setCellValueExplicit("L" . $contador_linea, $valor_aux["cod_mun"], DataType::TYPE_STRING);
                    
                    //Se formatea la celda de la fecha de nacimiento
                    $doc_salida->getActiveSheet()
                            ->getStyle("H" . $contador_linea)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                    
                    $contador_linea++;
                }
                
                $xlsxWrite = new XlsxWrite($doc_salida);
                
                //Le pasamos la ruta de guardado
                $nombre_arch_salida = "./tmp/Migrantes_" . $id_usuario . ".xlsx";
                $xlsxWrite->save($nombre_arch_salida);
            }
            ?>
        </div>
        <?php
        if ($nombre_arch_salida != "") {
            ?>
            <form name="frm_reporte_migrantes" id="frm_reporte_migrantes" method="post" action="<?= $nombre_arch_salida ?>">
            </form>
            <script id="ajax" type="text/javascript">
                document.getElementById("frm_reporte_migrantes").submit();
            </script>
            <?php
        }
        break;
}
?>
