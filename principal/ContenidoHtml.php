<?php

require_once("../db/DbUsuarios.php");
require_once("../db/DbMenus.php");
require_once("../funciones/FuncionesPersona.php");
require_once("../funciones/Class_Combo_Box.php");

class ContenidoHtml {
    /*
     * Funcion para generar el enbabezadao de la pagina
     */

    public function cabecera_html() {
		
        ///require_once("../funciones/get_idioma.php");
        $usuarios = new DbUsuarios();
        $menus = new DbMenus();
        $combo = new Combo_Box();

        //usuarios
        $usuarios_r = $usuarios->getUsuario($_SESSION["nomUsuario"]);
        ?>
        <div class="container">

            <div class="row">
                <div class="col-md-12" style='text-align:center;'>
                    <br/>
                    <br/>
                    <br/>
                    <a href="#" rel="home"><h1 class="ir logo">Logo</h1></a>
                </div>
                <div class="col-md-12">
                    <nav class="navbar navbar-inverse navbar-fixed-top">

                        <div class="container">

                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>

                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                <ul class="nav navbar-nav">
                                    <?php
                                    //Array que contendrá los accesos del usuario
                                    $arr_accesos_usuario = array();
                                    //Imprime el menu
                                    $menus_r = $menus->getListaMenus2($_SESSION["idUsuario"]);
                                    foreach ($menus_r as $value) {
                                        if ($value['id_menu_padre'] == 0) {
                                            ?>
                                            <li class="dropdown">
                                                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#" >
                                                    <?php echo($value['nombre_menu']); ?><span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <?php
                                                    for ($i = 0; $i <= count($menus_r) - 1; $i++) {
                                                        if ($menus_r[$i]['id_menu_padre'] == $value['id_menu']) {
                                                            ?>
                                                            <li>
                                                                <a href="#" onclick="enviar_credencial('<?php echo($menus_r[$i]['pagina_menu']); ?>', <?php echo($menus_r[$i]['id_menu']); ?>)"><?php echo($menus_r[$i]['nombre_menu']); ?></a>
                                                                <ul>
                                                                    <?php
                                                                    for ($e = 0; $e <= count($menus_r) - 1; $e++) {
                                                                        if ($menus_r[$e]['id_menu_padre'] == $menus_r[$i]['id_menu']) {
                                                                            ?>
                                                                            <li><a href="#" onclick="enviar_credencial('<?php echo($menus_r[$e]['pagina_menu']); ?>', <?php echo($menus_r[$e]['id_menu']); ?>)"><?php echo($menus_r[$e]['nombre_menu']); ?></a></li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </li>

                                            <?php
                                        }
                                        //Se agrega el menú con el tipo de acceso al array
                                        $arr_accesos_usuario[$value["id_menu"]] = intval($value["tipo_acceso"]);
                                    }
                                    //Se agregan los tipos de acceso a la sesión
                                    $_SESSION["accesos_usuario"] = $arr_accesos_usuario;
                                    ?>
                                </ul>
								
                                <ul class="nav navbar-nav navbar-right">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Opciones <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="enviar_credencial('../principal/pass.php', 0)">Cambiar contrase&ncaron;a</a></li>
                                            <li><a href="#" onclick="confirmar()">Salir</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div><!-- /.navbar-collapse -->
                        </div>
                    </nav>



                </div>
            </div>

        </div><!-- /.container-->

        <?php
    }

    /*
     * Funcion para generar el pie de pagina 
     */

    public function footer() {
        ?>
        <div class="container-fluid fondoFooter">
            <div class="container">
                <div class="row footer">
                    <div class="col-md-12">
                        <div class="clearfix">
                            <div class="wrapper">
                                <p class="left" style="text-align: left;">INS</p>
                                <p class="right"> - Todos los derechos reservados - <?php echo date("Y"); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="ventanaModal"></div>

            <div id="fondo_negro" class="d_fondo_negro"></div>
            <div class="div_centro" id="d_centro" style="display:none;">
                <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="cerrar_div_centro();"></a>
                <div class="div_interno" id="d_interno"></div>
            </div>
            <div id="d_impresion_hc" style="display:none;"></div>


        </div>

        <?php
    }

    /*
     * Pie de pagina pra las paginas iframe
     */

    public function footer_iframe() {
        ?>
        <div id="fondo_negro" class="d_fondo_negro"></div>
        <div class="div_centro" id="d_centro" style="display:none;">
            <a name="a_cierre_panel" id="a_cierre_panel" href="#" onclick="cerrar_div_centro();"></a>
            <div class="div_interno" id="d_interno"></div>
        </div>
        <div id="d_impresion_hc" style="display:none;"></div>
        <?php
    }

    /**
     * Funcion para validar si existe un a session o si la sesion expiro
     * $ajax= 1 : SI entro por ajax
     * $ajax= 0 : NO entro por ajax
     */
    public function validar_seguridad($ajax) {
        $id_usuario = $_SESSION["idUsuario"];
        @$credencial = $_POST["credencial"];
        @$id_menu = $_POST["hdd_numero_menu"];

        if (isset($_SESSION["idUsuario"])) {
            if ($credencial == '' && $ajax == 0) {
                $credencial = $id_usuario;
                //Se crear la variable para continuar con la credencial activa
                ?>
                <form name="frm_credencial" id="frm_credencial" method="post" action="index.php">
                    <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
                    <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
                </form>
                <script type="text/javascript">
                    document.frm_credencial.submit();
                </script>
                <?php
            } else if ($credencial == '' && $ajax == 1) {
                header("Location: ../principal/sesion_finalizada.html");
            } else if (($credencial == '' && $id_usuario == '') || ($credencial != $id_usuario)) {
                header("Location: ../principal/sesion_finalizada.html");
            } else if (($credencial == $id_usuario) && ($ajax == 0)) {
                ?>
                <form name="frm_credencial" id="frm_credencial" method="post" action="">
                    <input type="hidden" name="credencial" id="credencial" value="<?php echo($credencial); ?>" />
                    <input type="hidden" name="hdd_numero_menu" id="hdd_numero_menu" value="<?php echo($id_menu); ?>" />
                </form>
                <?php
            }
        } else {
            header("Location: ../principal/sesion_finalizada.html");
        }
    }

    function obtener_permisos_menu($id_menu) {
        $arr_accesos_usuario = $_SESSION["accesos_usuario"];
        $tipo_acceso = 0;
        if (isset($arr_accesos_usuario[$id_menu])) {
            $tipo_acceso = $arr_accesos_usuario[$id_menu];
        }

        return $tipo_acceso;
    }
	
	
	
	
	
	

}
?>
