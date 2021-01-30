<?php
session_start();
require_once 'ContenidoHtml.php';
$contenidoHtml = new ContenidoHtml();
require_once("../db/DbVariables.php");
$variables = new Dbvariables();

require_once("../funciones/Button.php");
$button = new Button();

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
    <body>
        <?php
        $contenidoHtml->validar_seguridad(0);
        $contenidoHtml->cabecera_html();
        ?>
        <div class="container">

            <div class="row">
                <div class="col-md-12 fondoAzul">
                    <ol class="breadcrumb">
                        <li>Cambiar contrase&ntilde;a</li>
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
                <form id="frmPass">
                    <div class="col-md-12 form-group">
                        <label for="">Contrase&ntilde;a actual:*</label>
                        <input type="password" class="form-control" name="txtPassword" id="txtPassword" placeholder="" required>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="">Nueva contrase&ntilde;a:*</label>
                        <input type="password" class="form-control" name="txtpass" id="txtpass" placeholder="" required>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="">Repetir contrase&ntilde;a:*</label>
                        <input type="password" class="form-control" name="txtpass2" id="txtpass2" placeholder="" required>
                    </div>
                    <div class="col-md-12 form-group centrar">
                        <?php
                        $button->getButton(1, "Cambiar contrase&ntilde;a", "submit", "exito", "btn btn-success", "validar_contrasena();");
                        
                        ?>
                    </div>
                </form>
            </div>



        </div>
        <?php
        $contenidoHtml->footer();
        ?>


        
        <script type='text/javascript' src='../js/jquery.min.js'></script>
        <script type='text/javascript' src='pass.js'></script>
        <script type='text/javascript' src='../js/validaFecha.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.add.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>
        <script type='text/javascript' src='../js/bootstrap/bootstrap.js'></script>
    </body>
</html>
