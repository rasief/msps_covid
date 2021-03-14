<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../principal/ContenidoHtml.php");

$dbVariables = new Dbvariables();

$contenido = new ContenidoHtml();

$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

//variables
$titulo = $dbVariables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos_1.css" rel="stylesheet" type="text/css" />
        <link href="../css/bootstrap/bootstrap.css" rel="stylesheet" type="text/css" />

        <script type='text/javascript' src='procesamiento_viajeros.js'></script>
    </head>
    <body onload="cargar_fecha_viaje(); cargar_filtros_indicadores();">
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 fondoAzul">
                    <ol class="breadcrumb">
                        <li>Procesamiento de viajeros</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="d_contenedor_error" class="alert alert-danger" role="alert" style="display:none;"></div>
                    <div id="d_contenedor_exito" class="alert alert-success" role="alert" style="display:none;"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="form-group">
                                <div id="d_fecha_viaje" class="col-md-3">
                                </div>
                                <div class="col-md-6">
                                    <input type="file" class="form-control-file" id="fil_arch" name="fil_arch" accept=".xlsx"/>                                
                                </div>    
                                <div class="col-md-3">
                                    <div id="d_boton_cargar_datos">
                                        <button type="button" id="btn_cargar_datos" class="btn btn-success btn btn-block" onclick="cargar_archivo();">Cargar archivo</button>
                                        <div id="d_carga_archivo" style="display:none;"></div>
                                    </div>
                                    <div id="d_espera_cargar_datos" style="display:none;">
                                        <img src="../imagenes/cargando_gif.gif" />
                                    </div>
                                    <div id="img_cargando" class="img_cargando"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary">
                <br/>
                <div class="form-group">
                    <div id="d_filtros_indicadores" class="row"></div>
                    <div class="row">
                        <div id="d_indicadores" class="col-xs-12"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $contenido->footer();
        ?>
        <script type='text/javascript' src='charts_loader.js'></script>
        <script type='text/javascript' src='download2.js'></script>
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/bootstrap/bootstrap.js'></script>
    </body>
</html>
