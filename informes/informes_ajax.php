<?php
require_once '../funciones/point/vendor/autoload.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\Chart\Gridlines;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Slide\Background\Color;
use PhpOffice\PhpPresentation\Style\Color as StyleColor;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Fill;

session_start();

require_once("../db/DbUsuarios.php");
require_once("../db/DbInformes.php");
require_once("../db/DbListas.php");
require_once("../db/DbPerfiles.php");
require_once("../db/DbMunicipios.php");
require_once("../db/DbVariables.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Utilidades.php");

require_once("ClassGraficos.php");

$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbPefiles = new DbPerfiles();
$dbMunicipios = new DbMunicipios();
$dbVariables = new DbVariables();
$dbInformes = new DbInformes();

$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$classGraficos = new ClassGraficos();
$combo = new Combo_Box();

if (isset($_POST["hdd_numero_menu"])) {
    $tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
}

function generar_graficas_municipios($val_fecha, $tabla_municipios) {
    $dbInformes = new DbInformes();
    $classGraficos = new ClassGraficos();
    ?>
    <input id="val_fecha" type="hidden" value="<?php echo($val_fecha) ?>">
    <?php
    //Se obtiene la etiqueta de años
    $ano_graf = substr($val_fecha, 0, 4);
    if ($ano_graf > "2020") {
        $ano_graf = "2020-" . $ano_graf;
    }
    
    $array_datos_seleccion = "";
    $i = 0;
    $j = 0;
    foreach ($tabla_municipios as $fila_municipio) {
        $codigo_municipio = $fila_municipio['cod_mun_dane'];
        $nombre_municipio = $fila_municipio['nom_mun'];
        $ind_gedad = $fila_municipio['ind_gedad'];

        if ($i == 0) {
            $array_datos_seleccion = $array_datos_seleccion . $codigo_municipio . "," . $nombre_municipio . "," . $ind_gedad;
        } else {
            $array_datos_seleccion = $array_datos_seleccion . ";" . $codigo_municipio . "," . $nombre_municipio . "," . $ind_gedad;
        }

        //Total de casos
        $tabla_informes = $dbInformes->getDatosSintomasMunicipal($val_fecha, $codigo_municipio);
        $titulo_img_casos = "Casos según fecha de síntomas, " . $nombre_municipio . " " . $ano_graf;
        $classGraficos->grafica_casos_generales($tabla_informes, $nombre_municipio, $codigo_municipio, $titulo_img_casos, "");

        //Casos Grupo de edad
        $titulo_img_casos_gedad = "Casos según edad y sexo, " . $nombre_municipio . " " . $ano_graf;
        $tabla_casos_gedad = $dbInformes->getDatosContagiosGrupoEdadMunicipal($val_fecha, $codigo_municipio);
        $classGraficos->graficas_casos_gedad_sexo($tabla_casos_gedad, $nombre_municipio, $codigo_municipio, $titulo_img_casos_gedad, "casos");

        //Frecuencia Casos Muertes                      
        $titulo_img_frecuencias = "Frecuencia de casos y muertes según mes de notificación, " . $nombre_municipio . " " . $ano_graf . " ";
        $tabla_frecuencias = $dbInformes->getFrecuenciaCasosMuertesMuni($val_fecha, $codigo_municipio);
        if (count($tabla_frecuencias) == 0) {
            $arr_aux = array();
            $arr_aux["ano_noti"] = "2020";
            $arr_aux["mes_noti"] = "Abril";
            $arr_aux["tipo_atencion"] = "Fallecidos";
            $arr_aux["cantidad"] = "0";

            $tabla_frecuencias = array();
            array_push($tabla_frecuencias, $arr_aux);
            $arr_aux["tipo_atencion"] = "Vivio";
            array_push($tabla_frecuencias, $arr_aux);
        }
        $classGraficos->graficas_frecuencias($tabla_frecuencias, $nombre_municipio, $codigo_municipio, $titulo_img_frecuencias, "frecuencias");

        //if ($ind_gedad == 1) {
        $j++;

        //Casos por grupos de edad
        //*Menores de 15
        $tabla_informes_15 = $dbInformes->getDatosSintomasMunicipalGedad($val_fecha, $codigo_municipio, 1);
        $titulo_casos_gedad_15 = "Casos según fecha de inicio de síntomas en menores de 15 años, " . $nombre_municipio . " " . $ano_graf . " ";
        $classGraficos->grafica_casos_generales($tabla_informes_15, $nombre_municipio, $codigo_municipio, $titulo_casos_gedad_15, "menor_15");

        //*De 15 a 60                
        $tabla_informes_15_60 = $dbInformes->getDatosSintomasMunicipalGedad($val_fecha, $codigo_municipio, 2);
        $titulo_casos_gedad_15_60 = "Casos según fecha de inicio de síntomas en personas de 15 a 60 años, " . $nombre_municipio . " " . $ano_graf . " ";
        $classGraficos->grafica_casos_generales($tabla_informes_15_60, $nombre_municipio, $codigo_municipio, $titulo_casos_gedad_15_60, "de_15_60");

        //*Mayores de 60                
        $tabla_informes_60 = $dbInformes->getDatosSintomasMunicipalGedad($val_fecha, $codigo_municipio, 3);
        $titulo_casos_gedad_60 = "Casos según fecha de inicio de síntomas en mayores de 60 años, " . $nombre_municipio . " " . $ano_graf . " ";
        $classGraficos->grafica_casos_generales($tabla_informes_60, $nombre_municipio, $codigo_municipio, $titulo_casos_gedad_60, "mayor_60");
        //}
        //Total Muertes
        $titulo_img_fallecidos = "Muertes Según fecha de fallecimiento, " . $nombre_municipio . " " . $ano_graf;
        $tabla_muertes = $dbInformes->getDatosMuertesMunicipal($val_fecha, $codigo_municipio);
        $classGraficos->graficas_muertes_generales($tabla_muertes, $nombre_municipio, $codigo_municipio, $titulo_img_fallecidos);

        //Muertes Grupo de edad
        $titulo_img_muertes_gedad = "Muertes según edad y sexo, " . $nombre_municipio . " " . $ano_graf;
        $tabla_muertes_gedad = $dbInformes->getDatosMuertesGrupoEdadMunicipal($val_fecha, $codigo_municipio);
        $classGraficos->graficas_casos_gedad_sexo($tabla_muertes_gedad, $nombre_municipio, $codigo_municipio, $titulo_img_muertes_gedad, "muertes");

        //Grafico muertes natirales vs muertes por covid
        $tabla_lista_fechas = $dbInformes->getListaFechas($val_fecha);
        $tabla_defunciones_otras = $dbInformes->getDatosDefuncionesMunicipal($val_fecha, $codigo_municipio);
        $titulo_muertes_covid_nocovid = 'Muertes naturales y muertes por Covid según fecha de defunción, ' . $nombre_municipio . ' ' . $ano_graf . ' ';
        $classGraficos->graficas_casos_def_covid_otros($tabla_lista_fechas, $tabla_muertes, $tabla_defunciones_otras, $codigo_municipio, $titulo_muertes_covid_nocovid);

        $i = $i + 1;
    }
    ?>
    <button type="button" id="btn_generar_informe_muni" class="btn btn-success btn-lg btn-block" onclick="generar_reportes_municipal_ppt('<?= $array_datos_seleccion ?>');">Descargar archivo .pptx</button>
    <br />       
    <div id="div_ppt"></div>
    <br />       
    <?php
}

function generar_ppt_municipios($val_fecha, $tabla_municipios) {
    $dbInformes = new DbInformes();
    $classGraficos = new ClassGraficos();

    $tabla_array_datos = array();
    $tabla_array_datos[0]['tipo_atencion'] = "Tasa de Contagio * 100.000";
    $tabla_array_datos[0]['cantidad'] = "-";
    $tabla_array_datos[1]['tipo_atencion'] = "Positividad (%)";
    $tabla_array_datos[1]['cantidad'] = "-";
    $tabla_array_datos[2]['tipo_atencion'] = "No Camas UCI REPS";
    $tabla_array_datos[2]['cantidad'] = "-";
    $tabla_array_datos[3]['tipo_atencion'] = "Camas Confirmados COVID";
    $tabla_array_datos[3]['cantidad'] = "-";
    $tabla_array_datos[4]['tipo_atencion'] = "Camas Sospechosas COVID";
    $tabla_array_datos[4]['cantidad'] = "-";
    $tabla_array_datos[5]['tipo_atencion'] = "Camas No COVID";
    $tabla_array_datos[5]['cantidad'] = "-";
    $tabla_array_datos[6]['tipo_atencion'] = "% ocupación camas REPS";
    $tabla_array_datos[6]['cantidad'] = "-";

    $tabla_array_muertes = array();
    $tabla_array_muertes[0]['tipo_atencion'] = "Fallecidos";
    $tabla_array_muertes[0]['cantidad'] = "-";
    $tabla_array_muertes[1]['tipo_atencion'] = "Tasa de Mortalidad * 100.000";
    $tabla_array_muertes[1]['cantidad'] = "-";
    $tabla_array_muertes[2]['tipo_atencion'] = "Letalidad (%)";
    $tabla_array_muertes[2]['cantidad'] = "-";
    $tabla_array_muertes[3]['tipo_atencion'] = "Letalidad menores de 60 años (%)";
    $tabla_array_muertes[3]['cantidad'] = "-";
    $tabla_array_muertes[4]['tipo_atencion'] = "Letalidad 60 años y más (%)";
    $tabla_array_muertes[4]['cantidad'] = "-";

    //Se obtienen los totales
    $tabla_datos_cantidades = $dbInformes->getDatosGeneralesMunicipalCantidades($val_fecha);
    $mapa_datos_cantidades = array();
    foreach ($tabla_datos_cantidades as $dato_aux) {
        $mapa_datos_cantidades[$dato_aux["codigo_divipola"]] = $dato_aux;
    }

    $nombre_archivo = time();
    $tabla_datos_generales = $dbInformes->getDatosGeneralesMunicipal($val_fecha);
    $tabla_datos_generales_muertes = $dbInformes->getDatosGeneralesDefMunicipal($val_fecha);
    $objPHPPresentation = new PhpPresentation();

    foreach ($tabla_municipios as $i => $fila_municipio) {
        $codigo_municipio = $fila_municipio['cod_mun_dane'];
        $nombre_municipio = $fila_municipio['nom_mun'];
        $ind_gedad = $fila_municipio['ind_gedad'];

        //Datos generales
        $array_casos_municpal = $classGraficos->obtener_array_municipal($tabla_datos_generales, $codigo_municipio);
        $tabla_datos_casos_generales = array_merge($array_casos_municpal, $tabla_array_datos);

        //Datos Muertes
        $tabla_datos_muertes_generales = $tabla_array_muertes;

        $img_casos = base64_decode(urldecode($_POST['img_casos_' . $codigo_municipio]));
        $img_fallecidos = base64_decode(urldecode($_POST['img_fallecidos_' . $codigo_municipio]));

        $img_casos_gedad = base64_decode(urldecode($_POST['img_casos_gedad_' . $codigo_municipio]));
        $img_muertes_gedad = base64_decode(urldecode($_POST['img_muertes_gedad_' . $codigo_municipio]));

        $img_frecuencias = base64_decode(urldecode($_POST['img_frecuencias_' . $codigo_municipio]));

        $img_def_covid_otros = base64_decode(urldecode($_POST['img_def_covid_otros_' . $codigo_municipio]));

        $cod_aux = "" . intval($codigo_municipio, 10);
        if (isset($mapa_datos_cantidades[$cod_aux])) {
            $arr_cantidades_aux = $mapa_datos_cantidades[$cod_aux];
        } else {
            $arr_cantidades_aux = array();
        }

        $classGraficos->ppt_casos_generales(2, $objPHPPresentation, $val_fecha, $img_casos, $img_casos_gedad, $img_frecuencias, $tabla_datos_casos_generales, $arr_cantidades_aux);

        //if ($ind_gedad == 1) {
        $img_casos_15 = base64_decode(urldecode($_POST['img_casos_15_' . $codigo_municipio]));
        $img_casos_15_60 = base64_decode(urldecode($_POST['img_casos_15_60_' . $codigo_municipio]));
        $img_casos_60 = base64_decode(urldecode($_POST['img_casos_60_' . $codigo_municipio]));
        $classGraficos->ppt_casos_generales_gedad($objPHPPresentation, $val_fecha, $img_casos_15, $img_casos_15_60, $img_casos_60);
        //}

        $classGraficos->ppt_casos_generales_def(2, $objPHPPresentation, $val_fecha, $img_fallecidos, $img_muertes_gedad, $img_def_covid_otros, $tabla_datos_muertes_generales, $arr_cantidades_aux);
    }

    $nombre_total_archivo = 'informe_muni_' . $nombre_archivo . '.pptx';

    $write = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
    $write->save('archivos/' . $nombre_total_archivo);

    return $nombre_total_archivo;
}

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Opcion para buscar usuarios
        $tabla_informes = $dbInformes->getListaInformes();
        ?>
        <br />

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">

                    <div id="paginador" class="centrar">
                        <nav>
                            <ul class="pagination">
                            </ul>
                        </nav>
                    </div>

                    <table class="table table-bordered paginated">
                        <thead>
                            <tr><th colspan='5' style="text-align: center;">Listado</th></tr>
                            <tr>
                                <th style="width:15%; text-align: center;">Fecha</th>
                                <th style="width:15%; text-align: center;">Cantidad de registros</th>
                                <th style="width:20%; text-align: center;">Nacional</th>
                                <th style="width:20%; text-align: center;">Municipal</th>
                                <th style="width:30%; text-align: center;">Departamental</th>
                            </tr>
                        </thead>
                        <?php
                        $cantidad_registro = count($tabla_informes);
                        if ($cantidad_registro > 0) {
                            foreach ($tabla_informes as $i => $fila_informe) {
                                @$fecha_hora = $fila_informe['fecha_cargue_archivo'];
                                @$nombre_archivo = $fila_informe['nombre_archivo'];
                                @$cantidad_registros = $fila_informe['cantidad'];
                                ?>
                                <tr>
                                    <td align="center"><?php echo $fecha_hora; ?></td>
                                    <td align="center"><?php echo number_format($cantidad_registros, 0, ",", "."); ?></td>
                                    <td align="center">
                                        <button type="button" class="btn btn-danger" onclick="generar_reportes('<?php echo($fecha_hora); ?>')">Generar gr&aacute;ficas</button>
                                    </td>
                                    <td align="center">
                                        <button type="button" class="btn btn-danger" onclick="generar_reportes_municipal('<?php echo($fecha_hora); ?>')">Generar gr&aacute;ficas</button>
                                        <br><br>
                                        <button class="btn btn-danger" onclick="abrir_configurar_mun();"><img src="../imagenes/ver_lista.png" title="Configurar municipios"/></button>
                                    </td>
                                    <td align="center">
                                        <select id="sel_departamento_<?= $i ?>" class="form-control" onchange="seleccionar_departamento(this.value, <?= $i ?>);">
                                            <option value="">-Selecciones un departamento-</option>
                                            <?php
                                            $lista_departamentos = $dbMunicipios->getListaDepartamentos();
                                            foreach ($lista_departamentos as $departamento_aux) {
                                                ?>
                                                <option value="<?= $departamento_aux["cod_dep"] ?>"><?= $departamento_aux["nom_dep"] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <div id="d_municipio_<?= $i ?>">
                                            <select id="sel_municipio_<?= $i ?>" class="form-control">
                                                <option value="">-Municipio-</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-danger" onclick="generar_reportes_departamental('<?= $fecha_hora; ?>', <?= $i ?>)">Generar gr&aacute;ficas</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td align="center" colspan="5">No se encontraron datos</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <script id='ajax'>
            //<![CDATA[ 
            $(function () {
                $('.paginated', 'table').each(function (i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function () {
                    var currentPage = 0;
                    var numPerPage = 20;
                    var $table = $(this);
                    $table.bind('repaginate', function () {
                        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
                    });
                    $table.trigger('repaginate');
                    var numRows = $table.find('tbody tr').length;
                    var numPages = Math.ceil(numRows / numPerPage);
                    var $pager = $('.pagination');
                    for (var page = 0; page < numPages; page++) {

                        $('<li><a href="#">' + (page + 1) + '</a></li>').bind('click', {
                            newPage: page
                        }, function (event) {
                            currentPage = event.data['newPage'];
                            $table.trigger('repaginate');
                            $(this).addClass('active').siblings().removeClass('active');

                        }).appendTo($pager);

                    }
                    $pager.appendTo('#paginador').find('li:first').addClass('active');
                });
            });
            //]]>
        </script>
        <br />
        <?php
        break;

    case "2": //Carga de datos del INS
        @$nombre_tmp = $_FILES["fil_arch"]["tmp_name"];
        @$nombre_ori = $_FILES["fil_arch"]["name"];
        
        //Se verifica que la extensión del archivo sea .zip
        $pos_aux = strrpos($nombre_ori, ".");
        if ($pos_aux !== false) {
            $extension_arch = strtolower(substr($nombre_ori, $pos_aux + 1));
            
            if ($extension_arch == "zip") {
                //Se asigna nombre al archivo
                $nombre_arch = "archivos/casos_covid.csv";
                
                //Se borra la carga anterior
                unlink($nombre_arch);
                
                //Se establece la zona horaria
                date_default_timezone_set("America/Bogota");
                
                $zip = new ZipArchive;
                $zip->open($nombre_tmp);
                
                if ($zip->numFiles == 1) {
                    //Se extrae el archivo
                    $zip->extractTo("archivos/", array($zip->getNameIndex(0)));
                    rename("archivos/" . $zip->getNameIndex(0), $nombre_arch);
                    
                    $fecha_cargue_archivo = date("Y-m-d H:i:s");

                    //Se establece la zona horaria
                    date_default_timezone_set("America/Bogota");

                    //Se abre el archivo csv
                    $source = fopen($nombre_arch, "r") or die("Problem open file");

                    $cont_filas = 0;
                    $cant_datos = 0;

                    $array_datos_insert = array();
                    $cont = 0;
                    $sql_insert = "";
                    $bol_dividir = false;

                    $dbInformes->borrarDatosInformes();

                    while (($arr_datos_aux = fgetcsv($source, 100000, ",")) !== false) {
                        //$arr_datos_aux = array_map("utf8_encode", $arr_datos_aux);
                        if ($ind_resultado == 0) {
                            if ($cont_filas == 0) {

                            } else {
                                //Cuerpo del archivo             
                                $id_caso = $arr_datos_aux[1]; //0
                                $fecha_notificacion = $arr_datos_aux[2]; //1
                                $codigo_divipola = $arr_datos_aux[5]; //2
                                $ciudad_ubicacion = $arr_datos_aux[6]; //3
                                $departamento_distrito = $arr_datos_aux[4]; //4
                                $atencion = $arr_datos_aux[15]; //5
                                $edad = $arr_datos_aux[7]; //6
                                $unidad_edad = $arr_datos_aux[8];
                                $sexo = $arr_datos_aux[9]; //7
                                $tipo = $arr_datos_aux[10]; //8
                                $estado = $arr_datos_aux[12]; //9
                                $pais_procedencia = $arr_datos_aux[14]; //10
                                $fecha_inicio_sintomas = $arr_datos_aux[16]; //11
                                $fecha_muerte = $arr_datos_aux[17]; //12
                                $fecha_diagnostico = $arr_datos_aux[18]; //13
                                $fecha_recuperado = $arr_datos_aux[19]; //14
                                $fecha_reporte_web = $arr_datos_aux[0]; //15
                                $tipo_recuperacion = $arr_datos_aux[20]; //16
                                $codigo_departamento = $arr_datos_aux[3]; //17
                                $codigo_pais = $arr_datos_aux[13]; //18
                                $pertenencia_etnica = $arr_datos_aux[21]; //19
                                $nombre_grupo_etnico = $arr_datos_aux[22]; //20
                                $ubicacion_recuperado = $arr_datos_aux[11]; //21
                                //Se ajusta la edad
                                if ($cont_filas == 1) {
                                    $bol_dividir = ($edad >= 100);
                                }
                                if ($bol_dividir) {
                                    $edad = round($edad / 100, 0);
                                }
                                switch ($unidad_edad) {
                                    case "2": //Meses
                                        $edad = floor($edad / 12);
                                        break;
                                    case "3": //Días
                                        $edad = 0;
                                        break;
                                }

                                $array_datos_insert[$cant_datos]['id_caso'] = $id_caso;
                                $array_datos_insert[$cant_datos]['fecha_notificacion'] = $fecha_notificacion;
                                $array_datos_insert[$cant_datos]['codigo_divipola'] = $codigo_divipola;
                                $array_datos_insert[$cant_datos]['ciudad_ubicacion'] = $ciudad_ubicacion;
                                $array_datos_insert[$cant_datos]['departamento_distrito'] = $departamento_distrito;
                                $array_datos_insert[$cant_datos]['atencion'] = $atencion;
                                $array_datos_insert[$cant_datos]['edad'] = $edad;
                                $array_datos_insert[$cant_datos]['sexo'] = $sexo;
                                $array_datos_insert[$cant_datos]['tipo'] = $tipo;
                                $array_datos_insert[$cant_datos]['estado'] = $estado;
                                $array_datos_insert[$cant_datos]['pais_procedencia'] = $pais_procedencia;
                                $array_datos_insert[$cant_datos]['fecha_inicio_sintomas'] = $fecha_inicio_sintomas;
                                $array_datos_insert[$cant_datos]['fecha_muerte'] = $fecha_muerte;
                                $array_datos_insert[$cant_datos]['fecha_diagnostico'] = $fecha_diagnostico;
                                $array_datos_insert[$cant_datos]['fecha_recuperado'] = $fecha_recuperado;
                                $array_datos_insert[$cant_datos]['fecha_reporte_web'] = $fecha_reporte_web;
                                $array_datos_insert[$cant_datos]['tipo_recuperacion'] = $tipo_recuperacion;
                                $array_datos_insert[$cant_datos]['codigo_departamento'] = $codigo_departamento;
                                $array_datos_insert[$cant_datos]['codigo_pais'] = $codigo_pais;
                                $array_datos_insert[$cant_datos]['pertenencia_etnica'] = $utilidades->str_encode($pertenencia_etnica);
                                $array_datos_insert[$cant_datos]['nombre_grupo_etnico'] = $utilidades->str_encode($nombre_grupo_etnico);
                                $array_datos_insert[$cant_datos]['ubicacion_recuperado'] = $utilidades->str_encode($ubicacion_recuperado);
                                $array_datos_insert[$cant_datos]['fecha_cargue_archivo'] = $fecha_cargue_archivo;
                                $array_datos_insert[$cant_datos]['nombre_archivo'] = $destino;

                                //Insertar registros citas por dia
                                if ($cont > 0 && $cont % 200 == 0) {
                                    $insert_citas = $dbInformes->guardarDatosInformes($array_datos_insert);
                                    $array_datos_insert = array();

                                    if ($insert_citas == 0) {
                                        $ind_resultado = -6;
                                        break;
                                    }
                                }

                                $cant_datos++;
                                $cont++;
                            }
                        } else {
                            break;
                        }

                        $cont_filas++;
                    }

                    if (count($array_datos_insert) > 0) {
                        $insert_citas = $dbInformes->guardarDatosInformes($array_datos_insert);
                        if ($insert_citas == 0) {
                            $ind_resultado = -6;
                            break;
                        } else {
                            $ind_resultado = 1;
                        }
                    }

                    fclose($source);
                    ?>
                    <div class="alert alert-success" role="alert" id='msg_cargado' style="display: none;" >Archivo cargado a la base de atos con &eacute;xito</div> 
                    <?php
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert" id='msg_cargado' style="display: none;">Contenido de archivo zip no v&aacute;lido</div> 
                    <?php
                }
                
                $zip->close();
            } else {
                ?>
                <div class="alert alert-danger" role="alert" id='msg_cargado' style="display: none;">Tipo de archivo no v&aacute;lido</div> 
                <?php
            }
        } else {
            ?>
            <div class="alert alert-danger" role="alert" id='msg_cargado' style="display: none;">Tipo de archivo no v&aacute;lido</div> 
            <?php
        }
        break;

    case "3": //Generar reportes
        $val_fecha = $_POST['val_fecha'];
        
        //Se obtiene la etiqueta de años
        $ano_graf = substr($val_fecha, 0, 4);
        if ($ano_graf > "2020") {
            $ano_graf = "2020-" . $ano_graf;
        }
        ?>
        <input id="val_fecha" type="hidden" value="<?php echo($val_fecha) ?>">
        <?php
        //Casos Colombia
        $tabla_informes = $dbInformes->getDatosSintomas($val_fecha);

        $array_asintomaticos = array();
        $array_sintomaticos = array();
        $array_sintomas = array();
        foreach ($tabla_informes as $fila) {
            $tipo = $fila['tipo_estado'];
            $semana = $fila['fecha_notificacion'];
            $cantidad = $fila['cantidad'];
            if ($tipo == 'ASINTOMATICOS') {
                $array_sintomas[$semana]['asinto'] = $cantidad;
            } else if ($tipo == 'SINTOMATICOS') {
                $array_sintomas[$semana]['sinto'] = $cantidad;
            }
        }
        $datos_grafica = "['TIPO', 'SINTOMATICOS', 'ASINTOMATICOS', { role: 'annotation' }], ";
        $tot = count($array_sintomas);
        $num = 1;
        $h = 0;
        foreach ($array_sintomas as $key => $fila_sintomas) {
            //echo $key."<br />";
            //print_r($fila_sintomas['asinto']);
            @$asinto = @$fila_sintomas['asinto'];
            if ($asinto == '') {
                $asinto = 0;
            }
            $sinto = $fila_sintomas['sinto'];
            if ($sinto == '') {
                $sinto = 0;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_grafica = $datos_grafica . "['" . $key . "', " . $sinto . ", " . $asinto . ", ''] ";
            } else {
                $datos_grafica = $datos_grafica . "['" . $key . "', " . $sinto . ", " . $asinto . ", ''], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="curve_chart" style="width: 100%; height: 500px; "></div>
        <input id="img_1" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(drawChart);
            var dataImg = "";
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_grafica); ?>
                ]);

                var options = {
                    title: "Situación COVID-19, Colombia <?php echo($ano_graf); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '75%'},
                    isStacked: true,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("curve_chart"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_1').val(dataImg);
            }
        </script>	
        <?php
        //Muertes
        $tabla_muertes = $dbInformes->getDatosMuertes($val_fecha);

        $datos_grafica_muertes = "['Fecha', 'FALLECIDOS', 'FALLECIDOS-ACUMULADOS'], ";
        $tot = count($tabla_muertes);
        $num = 1;
        $h = 0;
        $acumulado_muertes = 0;
        foreach ($tabla_muertes as $fila) {
            $fecha_muerte = $fila['fecha_muerte'];
            $cantidad = $fila['cantidad'];
            $acumulado_muertes = $acumulado_muertes + $cantidad;
            if ($num == $tot) {//Ultimo valor
                $datos_grafica_muertes = $datos_grafica_muertes . "['" . $fecha_muerte . "', " . $cantidad . ", " . $acumulado_muertes . "] ";
            } else {
                $datos_grafica_muertes = $datos_grafica_muertes . "['" . $fecha_muerte . "', " . $cantidad . ", " . $acumulado_muertes . "], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_fallecidos" style="width: 100%; height: 500px"></div>
        <input id="img_fallecidos" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartFallecidos);
            var dataImg = "";
            function ChartFallecidos() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_grafica_muertes); ?>
                ]);

                var options = {
                    title: "Muertes Según fecha de fallecimiento, Colombia <?php echo($ano_graf); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    seriesType: 'bars',
                    series: [{targetAxisIndex: 0}, {targetAxisIndex: 1, type: 'line'}]};

                var chart = new google.visualization.ComboChart(document.getElementById("chart_fallecidos"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_fallecidos').val(dataImg);
            }
        </script>	
        <?php
        //Contagios por grupo de edad
        $tabla_contagios_gedad = $dbInformes->getDatosContagiosGrupoEdad($val_fecha);

        $array_contagios_gedad = array();
        foreach ($tabla_contagios_gedad as $fila) {
            $nombre_sexo = $fila['nombre_sexo'];
            $grupo_edad = $fila['grupo_edad'];
            $cantidad = $fila['cantidad'];
            if ($nombre_sexo == 'Hombres') {
                $array_contagios_gedad[$grupo_edad]['hombres'] = $cantidad;
            } else if ($nombre_sexo == 'Mujeres') {
                $array_contagios_gedad[$grupo_edad]['mujeres'] = $cantidad;
            }
        }

        $datos_contagios_gedad = "['SEXO', 'MUJERES', 'HOMBRES', { role: 'annotation' }], ";
        $tot = count($array_contagios_gedad);
        $num = 1;
        $h = 0;
        foreach ($array_contagios_gedad as $key => $fila_contagios_gedad) {

            $mujeres = $fila_contagios_gedad['mujeres'];
            if ($mujeres == '') {
                $mujeres = 0;
            }
            $hombres = $fila_contagios_gedad['hombres'];
            if ($hombres == '') {
                $hombres = 0;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_contagios_gedad = $datos_contagios_gedad . "['" . $key . "', " . $mujeres . ", " . $hombres . ", ''] ";
            } else {
                $datos_contagios_gedad = $datos_contagios_gedad . "['" . $key . "', " . $mujeres . ", " . $hombres . ", ''], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_contagios_gedad" style="width: 50%; height: 800px"></div>
        <input id="img_contagios_gedad" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartContagiosGedad);
            var dataImg = "";
            function ChartContagiosGedad() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_contagios_gedad); ?>
                ]);

                var options = {
                    title: "Casos según sexo y grupo de edad, Colombia <?php echo($ano_graf); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '75%'},
                    isStacked: true,
                };
                var chart = new google.visualization.BarChart(document.getElementById("chart_contagios_gedad"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_contagios_gedad').val(dataImg);
            }
        </script>	
        <?php
        //Muertes por grupo de edad
        $tabla_muertes_gedad = $dbInformes->getDatosMuertesGrupoEdad($val_fecha);

        $array_muertes_gedad = array();
        foreach ($tabla_muertes_gedad as $fila) {
            $nombre_sexo = $fila['nombre_sexo'];
            $grupo_edad = $fila['grupo_edad'];
            $cantidad = $fila['cantidad'];
            if ($nombre_sexo == 'Hombres') {
                $array_muertes_gedad[$grupo_edad]['hombres'] = $cantidad;
            } else if ($nombre_sexo == 'Mujeres') {
                $array_muertes_gedad[$grupo_edad]['mujeres'] = $cantidad;
            }
        }

        $datos_muertes_gedad = "['SEXO', 'MUJERES', 'HOMBRES', { role: 'annotation' }], ";
        $tot = count($array_muertes_gedad);
        $num = 1;
        $h = 0;
        foreach ($array_muertes_gedad as $key => $fila_mujertes_gedad) {
            $mujeres = $fila_mujertes_gedad['mujeres'];
            if ($mujeres == '') {
                $mujeres = 0;
            }
            $hombres = $fila_mujertes_gedad['hombres'];
            if ($hombres == '') {
                $hombres = 0;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_muertes_gedad = $datos_muertes_gedad . "['" . $key . "', " . $mujeres . ", " . $hombres . ", ''] ";
            } else {
                $datos_muertes_gedad = $datos_muertes_gedad . "['" . $key . "', " . $mujeres . ", " . $hombres . ", ''], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_muertes_gedad" style="width: 50%; height: 800px"></div>
        <input id="img_muertes_gedad" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartMuertesGedad);
            var dataImg = "";
            function ChartMuertesGedad() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_muertes_gedad); ?>
                ]);

                var options = {
                    title: "Muertes según sexo y grupo de edad, Colombia <?php echo($ano_graf); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '75%'},
                    isStacked: true,
                };
                var chart = new google.visualization.BarChart(document.getElementById("chart_muertes_gedad"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_muertes_gedad').val(dataImg);
            }
        </script>	
        <?php
        //Grafico muertes natirales vs muertes por covid
        $tabla_lista_fechas = $dbInformes->getListaFechas($val_fecha);
        $tabla_defunciones_otras = $dbInformes->getDatosDefunciones($val_fecha);
        $classGraficos->graficas_casos_def_covid_otros($tabla_lista_fechas, $tabla_muertes, $tabla_defunciones_otras, 'colom', 'Muertes naturales y muertes por Covid según fecha de defunción, Colombia');
        
        //Graficos de letalidad
        $tabla_leta = $dbInformes->getTasaLetalidad($val_fecha);
        $classGraficos->grafico_letalidad_colombia($tabla_leta, 'letalidad', $val_fecha);
        
        //Tasa de Contagio departamental
        $tabla_tasa_dpto = $dbInformes->getTasaContagioDpto($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_dpto, 'Tasa de contagio COVID-19 según Departamento', 'nom_departamento', 'DEPARTAMENTO', 'dpto');
        
        //Tasa de Contagio Municipal
        $tabla_tasa_muni = $dbInformes->getTasaContagioMuni($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_muni, 'Tasa de contagio COVID-19 según Municipio', 'nom_municipio', 'MUNICIPIO', 'muni');
        
        //Tasa de Mortalidad departamental
        $tabla_tasa_morta_dpto = $dbInformes->getTasaMortaDpto($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_morta_dpto, 'Tasa de Mortalidad COVID-19 según Departamento', 'nom_departamento', 'DEPARTAMENTO', 'morta_dpto');
        
        //Tasa de Mortalidad Municipal
        $tabla_tasa_morta_muni = $dbInformes->getTasaMortaMuni($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_morta_muni, 'Tasa de Mortalidad COVID-19 según Municipio', 'nom_municipio', 'MUNICIPIO', 'morta_muni');

        //Tasa de Contagio departamental ultimo mes
        $tabla_tasa_dpto_mes = $dbInformes->getTasaContagioDptoMes($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_dpto_mes, 'Tasa de contagio para el último mes valido según departamento, Colombia ' . $ano_graf, 'nom_departamento', 'DEPARTAMENTO', 'dpto_mes');

        //Tasa de Contagio Municipal ultimo mes
        $tabla_tasa_muni_mes = $dbInformes->getTasaContagioMuniMes($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_muni_mes, 'Tasa de contagio para el último mes valido según municipio, Colombia ' . $ano_graf, 'nom_municipio', 'MUNICIPIO', 'muni_mes');

        //Tasa de Mortalidad departamental Ultimo mes Valido
        $tabla_tasa_morta_dpto_mes = $dbInformes->getTasaMortaDptoMes($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_morta_dpto_mes, 'Tasa de mortalidad para el último mes valido según departamento, Colombia ' . $ano_graf, 'nom_departamento', 'DEPARTAMENTO', 'morta_dpto_mes');

        //Tasa de Mortalidad Municipal ultimo mes valido
        $tabla_tasa_morta_muni_mes = $dbInformes->getTasaMortaMuniMes($val_fecha);
        $classGraficos->graficas_tasas_covid($tabla_tasa_morta_muni_mes, 'Tasa de mortalidad para el último mes valido según municipio, Colombia ' . $ano_graf, 'nom_municipio', 'MUNICIPIO', 'morta_muni_mes');
        
        ?>
        <button type="button" id="btn_generar_informe" class="btn btn-success btn-lg btn-block" onclick="generar_reportes_ppt();">Descargar archivo .pptx</button>
        <br />       
        <div id="div_ppt"></div>
        <br />       
        <?php
        break;

    case "4": //PPTX Nacional
        $nombre_archivo = time();

        $val_fecha = $_POST['val_fecha'];
        $img = base64_decode(urldecode($_POST['img_1']));
        $img_fallecidos = base64_decode(urldecode($_POST['img_fallecidos']));
        $img_muertes_gedad = base64_decode(urldecode($_POST['img_muertes_gedad']));
        $img_contagios_gedad = base64_decode(urldecode($_POST['img_contagios_gedad']));
        $img_tasa_dpto = base64_decode(urldecode($_POST['img_tasa_dpto']));
        $img_tasa_muni = base64_decode(urldecode($_POST['img_tasa_muni']));
        $img_tasa_morta_dpto = base64_decode(urldecode($_POST['img_tasa_morta_dpto']));
        $img_tasa_morta_muni = base64_decode(urldecode($_POST['img_tasa_morta_muni']));
        $img_tasa_dpto_mes = base64_decode(urldecode($_POST['img_tasa_dpto_mes']));
        $img_tasa_muni_mes = base64_decode(urldecode($_POST['img_tasa_muni_mes']));

        $img_tasa_morta_dpto_mes = base64_decode(urldecode($_POST['img_tasa_morta_dpto_mes']));
        $img_tasa_morta_muni_mes = base64_decode(urldecode($_POST['img_tasa_morta_muni_mes']));

        $img_letalidad = base64_decode(urldecode($_POST['img_letalidad']));

        $img_muertes_covid_nocovid = base64_decode(urldecode($_POST['img_muertes_covid_nocovid']));
        
        //Se obtiene la etiqueta de años
        $ano_graf = substr($val_fecha, 0, 4);
        if ($ano_graf > "2020") {
            $ano_graf = "2020-" . $ano_graf;
        }
        
        $tabla_array_datos = array();
        $tabla_array_datos[0]['tipo_atencion'] = "Tasa de Contagio * 100.000";
        $tabla_array_datos[0]['cantidad'] = "-";
        $tabla_array_datos[1]['tipo_atencion'] = "R(t)";
        $tabla_array_datos[1]['cantidad'] = "-";
        $tabla_array_datos[2]['tipo_atencion'] = "No Camas UCI REPS";
        $tabla_array_datos[2]['cantidad'] = "-";
        $tabla_array_datos[3]['tipo_atencion'] = "Camas Confirmados COVID";
        $tabla_array_datos[3]['cantidad'] = "-";
        $tabla_array_datos[4]['tipo_atencion'] = "Camas Sospechosas COVID";
        $tabla_array_datos[4]['cantidad'] = "-";
        $tabla_array_datos[5]['tipo_atencion'] = "Camas No COVID";
        $tabla_array_datos[5]['cantidad'] = "-";
        $tabla_array_datos[6]['tipo_atencion'] = "% ocupación camas REPS";
        $tabla_array_datos[6]['cantidad'] = "-";

        $tabla_array_datos_def = array();
        $tabla_array_datos_def[0]['tipo_atencion'] = "Tasa de Mortalidad";
        $tabla_array_datos_def[0]['cantidad'] = "-";
        $tabla_array_datos_def[1]['tipo_atencion'] = "Letalidad Total (%)";
        $tabla_array_datos_def[1]['cantidad'] = "-";
        $tabla_array_datos_def[2]['tipo_atencion'] = "Letalidad menores de 60 años (%)";
        $tabla_array_datos_def[2]['cantidad'] = "-";
        $tabla_array_datos_def[3]['tipo_atencion'] = "Letalidad 60 años y más (%)";
        $tabla_array_datos_def[3]['cantidad'] = "-";

        $tabla_datos_generales = $dbInformes->getDatosGenerales($val_fecha, 1);
        $tabla_datos_generales_def = $dbInformes->getDatosGeneralesDef($val_fecha);
        $tabla_datos_generales_edades = $dbInformes->getDatosGeneralesEdades($val_fecha);

        //Creación del objeto pptx
        $objPHPPresentation = new PhpPresentation();
        $objPHPPresentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_CUSTOM, true)
                ->setCX(1600, DocumentLayout::UNIT_PIXEL)
                ->setCY(900, DocumentLayout::UNIT_PIXEL);

        //******** Hoja 1 **********        
        $currentSlide = $objPHPPresentation->getActiveSlide();

        $oBkgColor = new Color();
        $oBkgColor->setColor(new StyleColor("c6d9f1"));
        $currentSlide->setBackground($oBkgColor);

        //Texto Tabla Contenido
        $shape = $currentSlide->createTableShape(2);
        $shape->setHeight(200);
        $shape->setWidth(260);
        $shape->setOffsetX(10);
        $shape->setOffsetY(100);

        $total_casos = 0;
        $total_fallecidos = 0;
        foreach ($tabla_datos_generales as $value) {
            $cantidad = $value['cantidad'];
            $total_casos = $total_casos + $cantidad;
            if ($value["tipo_atencion"] == "Fallecidos") {
                $total_fallecidos = $cantidad;
            }
        }

        //Se obtiene el año actual
        $obj_aux = $dbVariables->getAnoMesDia();
        $anio_actual = "2020"; //$obj_aux["anio_actual"];

        //Se obtiene la población total del país
        $obj_aux = $dbMunicipios->getPoblacionPais($anio_actual);
        $poblacion = $obj_aux["cantidad"];

        //Se calculan las tasas
        $tasa_contagio = round(($total_casos / $poblacion) * 100000, 2);
        $tabla_array_datos[0]['cantidad'] = $tasa_contagio;

        $tasa_mortalidad = round(($total_fallecidos / $poblacion) * 100000, 2);
        $tabla_array_datos_def[0]['cantidad'] = $tasa_mortalidad;

        $tasa_letalidad = round(($total_fallecidos / $total_casos) * 100, 2);
        $tabla_array_datos_def[1]['cantidad'] = $tasa_letalidad;

        $tasa_letalidad_0_59 = round(($tabla_datos_generales_edades["fallecidos_0_59"] / $tabla_datos_generales_edades["casos_0_59"]) * 100, 2);
        $tabla_array_datos_def[2]['cantidad'] = $tasa_letalidad_0_59;

        $tasa_letalidad_60 = round(($tabla_datos_generales_edades["fallecidos_60"] / $tabla_datos_generales_edades["casos_60"]) * 100, 2);
        $tabla_array_datos_def[3]['cantidad'] = $tasa_letalidad_60;

        $tabla_datos_casos_generales = array_merge($tabla_datos_generales, $tabla_array_datos);

        $row = $shape->createRow();
        $row->setHeight(20);
        $oCell = $row->nextCell();
        $oCell->setWidth(150);
        $oCell->createTextRun('Casos')->getFont()->setBold(true)->setSize(16);
        $oCell = $row->nextCell();
        $oCell->setWidth(110);
        $oCell->createTextRun(number_format($total_casos, 0, ",", "."))->getFont()->setSize(16);
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);

        foreach ($tabla_datos_casos_generales as $value) {
            $tipo_atencion = $value['tipo_atencion'];
            $cantidad = $value['cantidad'];

            if ($tipo_atencion != 'Fallecidos' && $tipo_atencion != 'Otros') {
                if ($cantidad != "" && $cantidad != "-") {
                    if ($tipo_atencion == "Tasa de Contagio * 100.000") {
                        $cantidad = number_format($cantidad, 2, ",", ".");
                    } else {
                        $cantidad = number_format($cantidad, 0, ",", ".");
                    }
                }
                $row = $shape->createRow();
                $row->setHeight(20);
                $oCell = $row->nextCell();
                $oCell->createTextRun($tipo_atencion)->getFont()->setBold(true)->setSize(16);
                $oCell = $row->nextCell();
                $oCell->createTextRun($cantidad)->getFont()->setSize(16);
                $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);
            }
        }

        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img)
                ->setResizeProportional(false)
                ->setHeight(500)
                ->setWidth(1300)
                ->setOffsetX(280)
                ->setOffsetY(20);
        $currentSlide->addShape($shape);

        //Fuente
        $shape = $currentSlide
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun = $shape->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun->getFont()->setSize(14)->setBold(true);
        //******** Fin Hoja 1 **********
        //******** Hoja 2 **********
        $currentSlide2 = $objPHPPresentation->createSlide();
        $currentSlide2->setName('Diapositiva 2');
        $currentSlide2->setBackground($oBkgColor);

        //Texto Tabla Contenido
        $shape2 = $currentSlide2->createTableShape(2);
        $shape2->setHeight(200);
        $shape2->setWidth(260);
        $shape2->setOffsetX(10);
        $shape2->setOffsetY(100);
        $tabla_datos_casos_def = array_merge($tabla_datos_generales_def, $tabla_array_datos_def);

        foreach ($tabla_datos_casos_def as $value) {
            $tipo_atencion = $value['tipo_atencion'];
            $cantidad = $value['cantidad'];

            if ($tipo_atencion != 'Otros') {
                if ($cantidad != "" && $cantidad != "-") {
                    if ($tipo_atencion == "Fallecidos") {
                        $cantidad = number_format($cantidad, 0, ",", ".");
                    } else {
                        $cantidad = number_format($cantidad, 2, ",", ".");
                    }
                }

                $row = $shape2->createRow();
                $row->setHeight(20);
                $oCell = $row->nextCell();
                $oCell->setWidth(150);
                $oCell->createTextRun($tipo_atencion)->getFont()->setBold(true)->setSize(16);
                $oCell = $row->nextCell();
                $oCell->setWidth(110);
                $oCell->createTextRun($cantidad)->getFont()->setSize(16);
                $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);
            }
        }

        $shape2 = new Drawing\Base64();
        $shape2->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_fallecidos)
                ->setResizeProportional(false)
                ->setHeight(500)
                ->setWidth(1300)
                ->setOffsetX(280)
                ->setOffsetY(5);
        $currentSlide2->addShape($shape2);

        $shape2 = new Drawing\Base64();
        $shape2->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_muertes_covid_nocovid)
                ->setResizeProportional(false)
                ->setHeight(390)
                ->setWidth(900)
                ->setOffsetX(10)
                ->setOffsetY(508);
        $currentSlide2->addShape($shape2);

        $shape2 = new Drawing\Base64();
        $shape2->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_muertes_gedad)
                ->setResizeProportional(false)
                ->setHeight(390)
                ->setWidth(500)
                ->setOffsetX(930)
                ->setOffsetY(508);
        $currentSlide2->addShape($shape2);

        //Fuente
        $shape2 = $currentSlide2
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(800)
                ->setOffsetY(850);
        $textRun2 = $shape2->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun2->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 2 **********
        //******** Hoja 3 **********
        $currentSlide3 = $objPHPPresentation->createSlide();
        $currentSlide3->setName('Diapositiva 3');
        $currentSlide3->setBackground($oBkgColor);


        $shape3 = new Drawing\Base64();
        $shape3->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_muertes_gedad)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(0)
                ->setOffsetY(20);
        $currentSlide3->addShape($shape3);

        $shape3 = new Drawing\Base64();
        $shape3->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_contagios_gedad)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(800)
                ->setOffsetY(20);
        $currentSlide3->addShape($shape3);

        //Fuente
        $shape3 = $currentSlide3
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun3 = $shape3->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun3->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 3 **********
        //******** Hoja 3-2 **********
        $currentSlide3_2 = $objPHPPresentation->createSlide();
        $currentSlide3_2->setName('Diapositiva 3-2');
        $currentSlide3_2->setBackground($oBkgColor);

        $shape3_2 = new Drawing\Base64();
        $shape3_2->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_letalidad)
                ->setResizeProportional(false)
                ->setHeight(600)
                ->setWidth(1500)
                ->setOffsetX(90)
                ->setOffsetY(5);

        $currentSlide3_2->addShape($shape3_2);

        //Fuente
        $shape3_2 = $currentSlide3_2
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun3_2 = $shape3_2->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun3_2->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 3-2 **********
        //******** Hoja 4 **********
        $currentSlide4 = $objPHPPresentation->createSlide();
        $currentSlide4->setName('Diapositiva 4');
        $currentSlide4->setBackground($oBkgColor);

        $shape4 = new Drawing\Base64();
        $shape4->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_dpto)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(0)
                ->setOffsetY(20);
        $currentSlide4->addShape($shape4);

        $shape4 = new Drawing\Base64();
        $shape4->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_muni)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(800)
                ->setOffsetY(20);
        $currentSlide4->addShape($shape4);

        //Fuente
        $shape4 = $currentSlide4
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun4 = $shape4->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun4->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 4 **********
        //******** Hoja 5 **********
        $currentSlide5 = $objPHPPresentation->createSlide();
        $currentSlide5->setName('Diapositiva 5');
        $currentSlide5->setBackground($oBkgColor);

        $shape5 = new Drawing\Base64();
        $shape5->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_morta_dpto)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(0)
                ->setOffsetY(20);
        $currentSlide5->addShape($shape5);

        $shape5 = new Drawing\Base64();
        $shape5->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_morta_muni)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(800)
                ->setOffsetY(20);
        $currentSlide5->addShape($shape5);

        //Fuente
        $shape5 = $currentSlide5
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun5 = $shape5->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun5->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 5 **********
        //******** Hoja 6 **********
        $currentSlide6 = $objPHPPresentation->createSlide();
        $currentSlide6->setName('Diapositiva 6');
        $currentSlide6->setBackground($oBkgColor);

        $shape6 = new Drawing\Base64();
        $shape6->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_dpto_mes)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(0)
                ->setOffsetY(20);
        $currentSlide6->addShape($shape6);

        $shape6 = new Drawing\Base64();
        $shape6->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_muni_mes)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(800)
                ->setOffsetY(20);
        $currentSlide6->addShape($shape6);

        //Fuente
        $shape6 = $currentSlide6
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun6 = $shape6->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun6->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 6 **********
        //******** Hoja 7 **********
        $currentSlide7 = $objPHPPresentation->createSlide();
        $currentSlide7->setName('Diapositiva 7');
        $currentSlide7->setBackground($oBkgColor);

        $shape7 = new Drawing\Base64();
        $shape7->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_morta_dpto_mes)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(0)
                ->setOffsetY(20);
        $currentSlide7->addShape($shape7);

        $shape7 = new Drawing\Base64();
        $shape7->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tasa_morta_muni_mes)
                ->setResizeProportional(false)
                ->setHeight(800)
                ->setWidth(800)
                ->setOffsetX(800)
                ->setOffsetY(20);
        $currentSlide7->addShape($shape7);

        //Fuente
        $shape7 = $currentSlide7
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(850);
        $textRun7 = $shape7->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun7->getFont()->setSize(14)->setBold(true);

        //******** Fin Hoja 7 **********

        $nombre_total_archivo = 'informe_' . $nombre_archivo . '.pptx';

        $write = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
        //header('Content-Disposition: attachment; filename=archivos/export1.pptx');
        //$write->save('php://output');
        $write->save('archivos/' . $nombre_total_archivo);
        ?>
        <script id='ajax'>
            var url = "archivos/<?php echo($nombre_total_archivo); ?>";
            window.open(url);
        </script>

        <?php
        break;

    case "5":
        $val_fecha = $_POST['val_fecha'];

        $tabla_municipios = $dbInformes->getListaMunicipios();

        generar_graficas_municipios($val_fecha, $tabla_municipios);
        break;

    case "6": //PPTX Municipal
        @$val_fecha = $_REQUEST["val_fecha"];
        @$cant_municipios = intval($_REQUEST["cant_municipios"], 10);

        $tabla_municipios = array();
        for ($i = 0; $i < $cant_municipios; $i++) {
            $arr_aux = array();
            $arr_aux["cod_mun_dane"] = $_REQUEST["cod_mun_dane_" . $i];
            $arr_aux["nom_mun"] = $_REQUEST["nom_mun_" . $i];
            $arr_aux["ind_gedad"] = $_REQUEST["ind_gedad_" . $i];

            array_push($tabla_municipios, $arr_aux);
        }

        //var_dump($tabla_municipios);
        $nombre_total_archivo = generar_ppt_municipios($val_fecha, $tabla_municipios);
        ?>
        <script id='ajax'>
            var url = "archivos/<?php echo($nombre_total_archivo); ?>";
            window.open(url);
        </script>
        <?php
        break;

    case "7": //Select de municipios
        @$cod_dep = $_REQUEST["cod_dep"];
        @$indice = $_REQUEST["indice"];

        $lista_municipios = $dbMunicipios->getListaMunicipiosDepartamento($cod_dep);
        ?>
        <select id="sel_municipio_<?= $indice ?>" class="form-control">
            <?php
            if (count($lista_municipios) > 0) {
                ?>
                <option value="<?= $cod_dep ?>">(Consolidado departamental)</option>
                <option value="-<?= $cod_dep ?>">(Todos los municipios)</option>
                <?php
                foreach ($lista_municipios as $municipio_aux) {
                    ?>
                    <option value="<?= $municipio_aux["cod_mun_dane"] ?>"><?= $municipio_aux["nom_mun"] ?></option>
                    <?php
                }
            } else {
                ?>
                <option value="">-Municipio-</option>
                <?php
            }
            ?>
        </select>
        <?php
        break;

    case "8": //Reporte departamental
        @$val_fecha = $_REQUEST["val_fecha"];
        @$cod_dep = $_REQUEST["cod_dep"];
        @$cod_mun_dane = intval($_REQUEST["cod_mun_dane"], 10);

        //Se obtiene el listado de los entes territoriales
        if ($cod_mun_dane < 0) {
            //Todos los municipios
            $tabla_municipios = $dbMunicipios->getListaMunicipiosDepartamento(-$cod_mun_dane);
            for ($i = 0; $i < count($tabla_municipios); $i++) {
                $municipio_aux = $tabla_municipios[$i];
                if ($municipio_aux["cod_mun_dane"] % 100 == 1) {
                    //Capital
                    $municipio_aux["ind_gedad"] = "1";
                } else {
                    $municipio_aux["ind_gedad"] = "0";
                }
                $tabla_municipios[$i] = $municipio_aux;
            }
        } else if ($cod_mun_dane < 100) {
            //Consolidado departamental
            $departamento_obj = $dbMunicipios->getDepartamento($cod_dep);

            $fila_municipio = array();
            $fila_municipio['cod_mun_dane'] = $departamento_obj["cod_dep"];
            $fila_municipio['nom_mun'] = $departamento_obj["nom_dep"];
            $fila_municipio['ind_gedad'] = "1";
            $tabla_municipios = array();
            $tabla_municipios[0] = $fila_municipio;
        } else {
            //Municipio específico
            $municipio_obj = $dbMunicipios->getMunicipio($cod_mun_dane);

            $municipio_obj['ind_gedad'] = "1";
            $tabla_municipios = array();
            $tabla_municipios[0] = $municipio_obj;
        }

        //var_dump($tabla_municipios);
        generar_graficas_municipios($val_fecha, $tabla_municipios);
        break;

    case "9": //Mostrar listado de municipios configurados para generación
        $lista_municipios_esp = $dbInformes->getListaMunicipios();

        $lista_departamentos = $dbMunicipios->getListaDepartamentos();
        ?>
        <input type="hidden" id="hdd_cant_municipios" name="hdd_cant_municipios" value="<?= count($lista_municipios_esp) ?>"/>
        <div style="text-align:center;">
            <button type="button" id="btn_guardar_mun_esp" class="btn btn-success btn-lg" onclick="guardar_municipios_esp();">Guardar cambios</button>
            <br><br>
            <div id="d_guardar_mun_esp"></div>
        </div>
        <table id="tbl_municipios_esp" class="table table-bordered" style="width:80%" align="center">
            <thead>
                <tr>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:35%; text-align: center;">Departamento</th>
                    <th style="width:50%; text-align: center;">Municipio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < 100; $i++) {
                    $display_aux = "none";

                    $bol_habilitado = isset($lista_municipios_esp[$i]);

                    if ($bol_habilitado) {
                        $display_aux = "table-row";

                        $municipio_esp_aux = $lista_municipios_esp[$i];
                        $cod_dep = $municipio_esp_aux["cod_dep"];
                        if (strlen($cod_dep) < 2) {
                            $cod_dep = "0" . $cod_dep;
                        }
                        $cod_mun_dane = $municipio_esp_aux["cod_mun_dane"];
                        if (strlen($cod_mun_dane) < 5) {
                            $cod_mun_dane = "0" . $cod_mun_dane;
                        }
                    } else {
                        $cod_dep = "";
                        $cod_mun_dane = "";
                    }
                    ?>
                    <tr id="tr_municipio_esp_<?= $i ?>" style="display:<?= $display_aux ?>" value="<?= $i ?>">
                        <td style="text-align: center; cursor:pointer;">
                            <img src="../imagenes/icon-blue-up.png" title="Subir en el orden" onclick="mover_arriba_mun_esp(<?= $i ?>);"/>
                        </td>
                        <td style="text-align: center; cursor:pointer;">
                            <img src="../imagenes/icon-blue-down.png" title="Bajar en el orden" onclick="mover_abajo_mun_esp(<?= $i ?>);"/>
                        </td>
                        <td style="text-align: center; cursor:pointer;">
                            <img src="../imagenes/icon-error.png" title="Borrar" onclick="borrar_municipio_esp(<?= $i ?>);"/>
                        </td>
                        <td>
                            <?php
                            $combo->getComboDb("cmb_departamento_esp_" . $i, $cod_dep, $lista_departamentos, "cod_dep, nom_dep", "--Seleccione--", 'seleccionar_departamento_esp(this.value, ' . $i . ')', '', '', '', 'form-control');
                            ?>
                        </td>
                        <td>
                            <div id="d_municipio_esp_<?= $i ?>">
                                <?php
                                if ($bol_habilitado) {
                                    $lista_municipios_aux = $dbMunicipios->getListaMunicipiosDepartamento($cod_dep);
                                } else {
                                    $lista_municipios_aux = array();
                                }
                                $combo->getComboDb("cmb_municipio_esp_" . $i, $cod_mun_dane, $lista_municipios_aux, "cod_mun_dane, nom_mun", "--Seleccione--", '', '', '', '', 'form-control');
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr id="tr_agregar_mun_esp" value="">
                    <td style="text-align: center; cursor:pointer;"><img src="../imagenes/add_elemento.png" title="Agregar municipio" onclick="agregar_municipio_esp();"/></td>
                </tr>
            </tbody>
        </table>
        <?php
        break;

    case "10": //Select de municipios especiales
        @$cod_dep = $_REQUEST["cod_dep"];
        @$indice = $_REQUEST["indice"];

        $lista_municipios = $dbMunicipios->getListaMunicipiosDepartamento($cod_dep);
        $combo->getComboDb("cmb_municipio_esp_" . $indice, "", $lista_municipios, "cod_mun_dane, nom_mun", "--Seleccione--", '', '', '', '', 'form-control');
        break;

    case "11": //Guardar municipios especiales
        @$cant_mun_esp = intval($_REQUEST["cant_mun_esp"], 10);
        $lista_municipios = array();
        for ($i = 0; $i < $cant_mun_esp; $i++) {
            array_push($lista_municipios, $_REQUEST["cod_mun_dane_" . $i]);
        }

        $resultado = $dbInformes->guardarMunicipiosEspeciales($lista_municipios);
        if ($resultado > 0) {
            ?>
            <div class="alert alert-success" role="alert">
                Datos guardados con &eacute;xito
            </div>
            <?php
        } else {
            ?>
            <div class="alert alert-danger" role="alert">
                Error al modificar el listado de municipios
            </div>
            <?php
        }
        break;
}
?>