<?php
session_start();

require_once("../db/DbVariables.php");
require_once("../db/DbPerfiles.php");
require_once("../db/DbListas.php");
require_once("../funciones/Utilidades.php");
require_once("../principal/ContenidoHtml.php");
$variables = new Dbvariables();
$perfiles = new DbPerfiles();
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
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos_1.css" rel="stylesheet" type="text/css" />
        <link href="../css/bootstrap/bootstrap.css" rel="stylesheet" type="text/css" />		
        
        
    </head>
    <body onload="cargar_perfiles();">
        <?php
        $contenido->validar_seguridad(0);
        $contenido->cabecera_html();
        ?>
		
		
		<div class="container">
			
			<div class="row">
                <div class="col-md-12 fondoAzul">
                    <ol class="breadcrumb">
                        <li>Administraci&oacute;n de perfiles</li>
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
                    <div class="panel panel-success">
                        <div class="panel-heading">Perfiles</div>
                        <div class="panel-body">
                            <form id="listado_usuarios" name="listado_usuarios">
                                <div class="form-group">
                                    <div class="col-md-5">
                                        <button type="button" id="btn_lista_perfil" class="btn btn-primary" onclick="cargar_perfiles();">Ver todos</button>
                                        <?php
                                        if ($tipo_acceso_menu == 2) {
                                            ?>
                                            <button type="button" id="btn_crear_usuario" class="btn btn-success" onclick="cargar_formulario_crear();">Nuevo Perfil</button>
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
                    <div id="principal_perfiles" ></div>
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
        <script type='text/javascript' src='perfiles_v1.3.js'></script>
		<script type='text/javascript' src='../js/bootstrap/bootstrap.js'></script>
		
		
		
		

    </body>
</html>
