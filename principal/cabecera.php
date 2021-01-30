<?php
require_once("/../db/DbVariables.php");
require_once("/../db/DbUsuarios.php");
require_once("/../db/Dbmenus.php");
$variables = new Dbvariables();
$usuarios = new DbUsuarios();
$menus = new DbMenus();

//variables
$titulo = $variables->getVariable(1);

//usuarios
$usuarios_r = $usuarios->getUsuario($_SESSION["nomUsuario"]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $titulo['valor_variable']; ?></title>
        <link href="../css/estilos.css" rel="stylesheet" type="text/css" />
        <script type='text/javascript' src='../js/jquery.js'></script>
        <script type='text/javascript' src='../js/jquery.validate.js'></script>
        <script type='text/javascript' src='../js/ajax.js'></script>
        <script type='text/javascript' src='../js/funciones.js'></script>

    </head>
    <body>



        <div>
            <div id="principal_contenedor">
                <div id="principal_header">
                    <div id="principal_header_div1"></div>
                    <div id="principal_header_div2">
                        <div class="img2">

                        </div>
                        <p style="margin-top: 8px;"><?php echo $_SESSION["nomUsuario"]; ?></p>
                        <div id="principal_header_div3">
                            <ul>
                                <li><a href="pass.php">Cambiar contrase&ntilde;a</a></li>
                                <li id="cerrar_sesion">Cerrar sesi&oacute;n</li>
                            </ul>
                        </div>

                    </div>

                    <div class="clear_both"></div>
                </div>
                <div style="background-color: #0C9BA0;width: 100%;height: 31px;">

                    <ul class="nav">
                        <?php
                        //Imprime el menu
                        $menus_r = $menus->getListaMenus2($_SESSION["idUsuario"]);

                        foreach ($menus_r as $value) {
                            if ($value['id_menu_padre'] == 0) {
                                echo '<li>';
                                echo '<a href="' . $value['pagina_menu'] . '">' . $value['nombre_menu'] . '</a>';

                                echo '<ul>';
                                for ($i = 0; $i <= count($menus_r) - 1; $i++) {
                                    if ($menus_r[$i]['id_menu_padre'] == $value['id_menu']) {
                                        echo '<li><a href="#">' . $menus_r[$i]['nombre_menu'] . '</a>';
                                        echo '<ul>';
                                        for ($e = 0; $e <= count($menus_r) - 1; $e++) {
                                            if ($menus_r[$e]['id_menu_padre'] == $menus_r[$i]['id_menu']) {
                                                echo '<li><a href="#">' . $menus_r[$e]['nombre_menu'] . '</a></li>';
                                            }
                                        }
                                        echo '</ul>';
                                        echo '</li>';
                                    }
                                }
                                echo '</ul>';
                                echo '</li>';
                            }
                        }
                        ?>
                    </ul>






                </div>
                <div class="clear_both"></div>
            </div>
        </div>