<?php
session_start();
ini_set("memory_limit", "164M");

require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbInformes.php");

require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
require_once("ClassGraficos.php");

$variables = new Dbvariables();
$usuarios = new DbUsuarios();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();

$dbInformes = new DbInformes();

$classGraficos = new ClassGraficos();

$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

//variables
$titulo = $variables->getVariable(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title><?php echo $titulo['valor_variable']; ?></title>
            <link href="../css/estilos_1.css" rel="stylesheet" type="text/css" />
            <link href="../css/bootstrap/bootstrap.css" rel="stylesheet" type="text/css" />

            <script type='text/javascript' src='informes_v1.5.js'></script>

    </head>
    <!-- onload="cargar_informes();" -->
    <body onload="cargar_informes();">
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 fondoAzul">
                    <ol class="breadcrumb">
                        <li>Generar Informe</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger contenedor_error" role="alert" id='contenedor_error'></div>
                    <div class="alert alert-success contenedor_exito" role="alert" id='contenedor_exito'></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">

                        <div class="panel-body">
                            <form id="listado_usuarios" name="listado_usuarios">
                                <div class="form-group">
                                    <div class="col-md-6">                                        
                                        <input type="file" class="form-control-file" id="fil_arch" name="fil_arch" accept=".zip" />                                
                                    </div>    
                                    <div class="col-md-6">
                                        <button type="button" id="btn_generar_informe" class="btn btn-success btn-lg btn-block" onclick="llamar_generar_informe();">Cargar datos</button>
                                        <div id="img_cargando" class="img_cargando"></div>
                                        <div id="div_subir_informes" ></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="principal_informes" ></div>
                    <div id="div_reportes" ></div>
                </div>
            </div>
        </div>
        <div class="modal" id="modalReportes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width:80%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Informes</h4>
                    </div>
                    <div class="modal-body" id="div_mostrar_reportes">                        
                        <div id="cargando_informe" class="img_cargando"></div>                        
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

        <script type="text/javascript">
            google.charts.load('45', {'packages': ['corechart']});
            //google.charts.load("current", {packages:['corechart']});
        </script>	
    </body>
</html>
