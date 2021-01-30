<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
$variables = new Dbvariables();
$usuarios = new DbUsuarios();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
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
    </head>
    <body onload="ver_todos_usuarios();">
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
        ?>

        <div class="container">
            <div class="row">
                <div class="col-md-12 fondoAzul">
                    <ol class="breadcrumb">
                        <li>Administraci&oacute;n de usuarios</li>
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
                        <div class="panel-heading">Buscar</div>
                        <div class="panel-body">
                            <form id="listado_usuarios" name="listado_usuarios">
                                <div class="form-group">
                                    <div class="col-md-2">
                                        <label class="left inline">Nombre de usuario</label>
                                    </div>
                                    <div class="col-md-6">                   
                                        <input type="text" class="form-control" id="txt_busca_usuario" name="txt_busca_usuario" placeholder="Nombre de usuario" onblur="trim_cadena(this);">   
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" id="btn_crear_usuario" class="btn btn-default" onclick="buscar_usuarios();">Buscar</button>
                                        <button type="button" id="btn_crear_usuario" class="btn btn-primary" onclick="ver_todos_usuarios();">Ver todos</button>
                                        <?php
                                        if ($tipo_acceso_menu == 2) {
                                            ?>
                                            <button type="button" id="btn_crear_usuario" class="btn btn-success" onclick="llamar_crear_usuarios();">Nuevo usuario</button>
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <div id="principal_usuarios" ></div>
                </div>

            </div>

        </div>
        <?php
        $contenido->footer();
        ?>


        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='usuarios_v1.13.js'></script>
        <script type='text/javascript' src='../js/bootstrap/bootstrap.js'></script>
    </body>
</html>
