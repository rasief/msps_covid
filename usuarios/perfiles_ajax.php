<?php
session_start();

header('Content-Type: text/xml; charset=UTF-8');

require_once("../db/DbPerfiles.php");
require_once("../db/DbMenus.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");
require_once("../db/DbListas.php");

$dbPerfiles = new DbPerfiles();
$dbMenus = new DbMenus();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
$dbListas = new DbListas();
$contenido->validar_seguridad(1);
$tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);

$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": //Formulario de creacion de perfiles
        $combo = new Combo_Box();
        $tipo_accion = '';
        if (isset($_POST['id_perfil'])) {
            $titulo_formulario = 'Editar perfil';
            $id_perfil = $utilidades->str_decode($_POST['id_perfil']);
            $tabla_perfil = $dbPerfiles->getUnPerfil($id_perfil);
            $tabla_permisos = $dbPerfiles->getPermisosMenus($id_perfil);
            $txt_nombre_perfil = $tabla_perfil['nombre_perfil'];
            $txt_desc_perfil = $tabla_perfil['descripcion'];
            $cmb_menu_inicio = $tabla_perfil['id_menu_inicio'];
            //$cod_departamento = $tabla_perfil['cod_departamento'];
            $cmd_estado = $tabla_perfil['ind_activo'];
			$cmd_colegio = $tabla_perfil['tipo_colegio'];
            $tipo_accion = 2; //Editar Perfil
        } else {
            $tabla_permisos = array();
            $titulo_formulario = 'Crear nuevo perfil';
            $txt_nombre_perfil = '';
            $txt_desc_perfil = '';
            $cmb_atiende_consulta = '';
            $cmb_recibe_citas = '';
            $cmd_cirugia = '';
            $cmb_menu_inicio = '';
			$cmd_colegio = '';
            //$cod_departamento = '';
            $id_perfil = '';
            $tipo_accion = 1; //Crear Perfil
			
        }

        $lista_si_no = array();
        $lista_si_no[0][0] = '1';
        $lista_si_no[0][1] = 'SI';
        $lista_si_no[1][0] = '0';
        $lista_si_no[1][1] = 'NO';
        ?>
        
        <input type="hidden" value="0" name="hdd_exito" id="hdd_exito" />
        <input type="hidden" value="<?php echo $id_perfil; ?>" name="hdd_id_perfil" id="hdd_id_perfil" />
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading"><?php echo $titulo_formulario; ?></div>
                    <div class="panel-body">
        
			        <form id="frm_perfiles" name="frm_perfiles" method="post">
			        	
			        	<div class="form-group">
                            <label for="txt_nombre_perfil">Nombre del perfil *</label>
                            <input type="text" class="form-control" name="txt_nombre_perfil" id="txt_nombre_perfil"  placeholder="Nombres del perfil" value="<?php echo $txt_nombre_perfil; ?>" onblur="trim_cadena(this);" />
                        </div>
                        
                        <div class="form-group">
                            <label for="txt_desc_perfil">Descripci&oacute;n del perfil *</label>
                            <input type="text" class="form-control" name="txt_desc_perfil" id="txt_desc_perfil" placeholder="Descripci&oacute;n del perfil" value="<?php echo $txt_desc_perfil; ?>" onblur="trim_cadena(this);" />
                        </div>
                        
                        
                        <!-- <div class="form-group">
                            <label for="cod_departamento">Departamento</label>
                            <?php
	                        $lista_departamentos = $dbPerfiles->getDepartamentos();
	                        $combo->getComboDb("cod_departamento", $cod_departamento, $lista_departamentos, "cod_dep, nom_dep", "--Sin departamento--", '', '', 'width:350px;', '', 'form-control');
	                        ?>
                        </div>-->
                        
                        
                        <div class="form-group">
                            <label for="cmb_menu_inicio">Men&uacute; inicial</label>
                            <?php
	                        $lista_menus = $dbMenus->getListaMenusVisibles();
	                        $combo->getComboDb('cmb_menu_inicio', $cmb_menu_inicio, $lista_menus, 'id_menu, nombre_completo', '--Seleccione--', '', '', 'width:350px;', '', 'form-control');
	                        ?>
                        </div>
                        
                        <?php
		                if ($tipo_accion == 2) {
		                    ?>
		                    <div class="form-group">
                                <label for="cmb_activo">Activo *</label>
                                <?php
	                            $combo->get('cmb_activo', $cmd_estado, $lista_si_no, '--Seleccione--', '', '', 'width:350px;', '', 'form-control');
	                            ?>
		                        
                    		</div>
		                    <?php
		                }
		                ?>
						
						
                                <!--<div class="form-group">
                                    <label for="cmb_activo">Tipo Intituci&oacute;n *</label>
                                    <?php
                                    $lista_tipo_intituciones = $dbListas->getListaDetalles(9);
                                    $combo->getComboDb('cmb_colegio', $cmd_colegio, $lista_tipo_intituciones, 'id_detalle, nombre_detalle', '--Seleccione--', '', '', 'width:350px;', '', 'form-control');
                                    ?>
                                </div>-->
								
                                <input type="hidden" name="cod_departamento" id="cod_departamento" value="1" />
                                <input type="hidden" name="cmb_colegio" id="cmb_colegio" value="1" />
													
		                
		                
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
			                            	<tr><th colspan='2'>Permisos de menus</th></tr>
			                                <tr class='headegrid'>
			                                    <th class="headegrid" align="center" >Menus</th>	
			                                    <th class="headegrid" align="center" style="width: 180px;">Permisos</th>
			                                </tr>
			                            </thead>
			                            <?php
			                            $lista_permiso = array();
			                            $lista_permiso[0][0] = '1';
			                            $lista_permiso[0][1] = 'CONSULTA';
			                            $lista_permiso[1][0] = '2';
			                            $lista_permiso[1][1] = 'COMPLETO';
			                            $tabla_menus = $dbMenus->getMenusRuta();
			                            $ids_menus = '';
			                            foreach ($tabla_menus as $fila_menus) {
			                                $id_menu = $fila_menus['id_menu'];
			                                $menu_principal = $fila_menus['nombre_menu'];
			                                $menu_padre2 = $fila_menus['nombre_2'];
			                                $menu_padre3 = $fila_menus['nombre_3'];
			                                $ruta_menu = $menu_principal;
			                                $ids_menus.=$id_menu . "-";
			
			                                if ($menu_padre2 <> '') {
			                                    $ruta_menu = $menu_padre2 . "->" . $ruta_menu;
			                                }
			                                if ($menu_padre3 <> '') {
			                                    $ruta_menu = $menu_padre3 . "->" . $ruta_menu;
			                                }
			
			                                $valor_permiso = '';
			                                if (count($tabla_permisos) > 0) {
			
			                                    foreach ($tabla_permisos as $fila_permisos) {
			                                        $id_menu_permiso = $fila_permisos['id_menu'];
			                                        $tipo_acceso = $fila_permisos['tipo_acceso'];
			                                        if ($id_menu_permiso == $id_menu) {
			                                            $valor_permiso = $tipo_acceso;
			                                        }
			                                    }
			                                }
			                                ?>
			                                <tr class='celdagrid'>
			                                    <td align="left"><?php echo $ruta_menu; ?></td>	
			                                    <td align="left">
			                                        <?php
			                                        $combo->get('cmb_permiso_' . $id_menu, $valor_permiso, $lista_permiso, '--Ninguno--', '', '', 'width:170px;');
			                                        ?>	
			                                    </td>
			                                </tr>
			                                <?php
			                            }
			                            ?>
			                        </table>	
			
			                        <script id='ajax'>
							            //<![CDATA[ 
							            $(function () {
							                $('.paginated', 'table').each(function (i) {
							                    $(this).text(i + 1);
							                });
							
							                $('table.paginated').each(function () {
							                    var currentPage = 0;
							                    var numPerPage = 5;
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
				                    
				                    
				                </div>
				            </div>
				        </div>    
				        
				        
				        <div class="centrar">
				        	
				        	<?php
			                if ($tipo_accion == 2) {
			                    ?>
		                            <input type="hidden"  id="hdd_idmenus" nombre="hdd_idmenus" value="<?php echo $ids_menus; ?>" />
		                            <?php
		                            if ($tipo_acceso_menu == 2) {
		                                ?>
		                                <button type="submit" class="btn btn-success" id="btn_editar_perfil" nombre="btn_editar_perfil" onclick="validar_editar_perfil();">Guardar</button>
		                                <?php
		                            }
		                            ?>
		                            <button type="button" class="btn btn-default" id="btn_cancelar" nombre="btn_cancelar" onclick="cargar_perfiles();">Cancelar</button>
		                            
			                    <?php
			                } else {
			                    ?>
		                            <input type="hidden"  id="hdd_idmenus" nombre="hdd_idmenus" value="<?php echo $ids_menus; ?>" />
		                            <?php
		                            if ($tipo_acceso_menu == 2) {
		                                ?>
		                                <button type="submit" class="btn btn-success" id="btn_crear_perfil" nombre="btn_crear_perfil" onclick="validar_crear_perfil();">Crear</button>
		                                
		                                <?php
		                            }
		                            ?>
		                            <button type="button" class="btn btn-default" id="btn_cancelar" nombre="btn_cancelar" onclick="cargar_perfiles();">Cancelar</button>

			                    <?php
			                }
			                ?>
                           
                        </div>
				        
			            
			            <br />
			        </form>
			        
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case "2": //Listado de los perfiles
        $tabla_perfiles = $dbPerfiles->getListaPerfiles();
        ?>
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
			                <tr><th colspan='4'>Perfiles de usuarios</th></tr>	
			                <tr>
			                    <th style="width: 5%;">Id</th>	
			                    <th style="width: 23%;">Perfil</th>	
			                    <th style="width: 32%;">Descripci&oacute;n</th>                                            
			                    <th style="width: 15%;">Estado</th>
			                </tr>
			            </thead>
			            <?php
			            foreach ($tabla_perfiles as $fila_perfiles) {
			                $id_perfil = $fila_perfiles['id_perfil'];
			                $nombre_perfil = $fila_perfiles['nombre_perfil'];
			                $descripcion = $fila_perfiles['descripcion'];
			                $ind_activo = $fila_perfiles['ind_activo'];
                                        $tipo_institucion = $fila_perfiles['tipo_institucion'];
			                
			
			                if ($ind_activo == 1) {
			                    $class_estado = 'activo';
			                    $texto_estado = 'Activo';
			                } else {
			                    $class_estado = 'inactivo';
			                    $texto_estado = 'Inactivo';
			                }
							
			                ?>
			                <tr onclick='cargar_formulario_editar(<?php echo $id_perfil; ?>);' style="cursor: pointer;">
			                    <td align="center"><?php echo $id_perfil; ?></td>
			                    <td align="left"><?php echo $nombre_perfil; ?></td>
			                    <td align="left"><?php echo $descripcion; ?></td>                                            
			                    <td align="center"><span class="<?php echo $class_estado; ?>"><?php echo $texto_estado; ?></span></td>
			                </tr>
			                <?php
			            }
			            ?>
			        </table>
			        </div>
            	</div>
        </div>
        <br/>
        <script id='ajax'>
           //<![CDATA[ 
            $(function () {
                $('.paginated', 'table').each(function (i) {
                    $(this).text(i + 1);
                });

                $('table.paginated').each(function () {
                    var currentPage = 0;
                    var numPerPage = 10;
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
        <?php
        break;

    case "3": //Crear perfil
        $id_usuario = $_SESSION["idUsuario"];

        @$txt_nombre_perfil = $utilidades->str_decode($_POST["txt_nombre_perfil"]);
        @$txt_desc_perfil = $utilidades->str_decode($_POST["txt_desc_perfil"]);
        @$cmb_menu_inicio = $utilidades->str_decode($_POST["cmb_menu_inicio"]);
        @$cod_departamento = $utilidades->str_decode($_POST["cod_departamento"]);
        @$hdd_idmenus = $utilidades->str_decode($_POST["hdd_idmenus"]);		
        @$cmb_colegio = $utilidades->str_decode($_POST["cmb_colegio"]);

        $array_menus = explode("-", $hdd_idmenus);
        $menus_permisos = '';
        foreach ($array_menus as $fila_menus) {
            if ($fila_menus != '') {
                $val_permiso = $_POST["cmb_permiso_" . $fila_menus];
                $menus_permisos = $menus_permisos . $fila_menus . "," . $val_permiso . "-";
            }
        }

        $valor_exito = $dbPerfiles->crearPerfil($txt_nombre_perfil, $txt_desc_perfil, $cmb_menu_inicio, $id_usuario, $menus_permisos, $cod_departamento, $cmb_colegio);
        ?>
        <input type="hidden" value="<?php echo $valor_exito; ?>" name="hdd_exito" id="hdd_exito" />
        <?php
        break;

    case "4": //Editar perfil
        $id_usuario = $_SESSION["idUsuario"];

        @$hdd_id_perfil = $utilidades->str_decode($_POST["hdd_id_perfil"]);
        @$txt_nombre_perfil = $utilidades->str_decode($_POST["txt_nombre_perfil"]);
        @$txt_desc_perfil = $utilidades->str_decode($_POST["txt_desc_perfil"]);
        @$cmb_menu_inicio = $utilidades->str_decode($_POST["cmb_menu_inicio"]);
        @$cmb_activo = $utilidades->str_decode($_POST["cmb_activo"]);
        @$hdd_idmenus = $utilidades->str_decode($_POST["hdd_idmenus"]);
        @$cod_departamento = $utilidades->str_decode($_POST["cod_departamento"]);
		@$cmb_colegio = $utilidades->str_decode($_POST["cmb_colegio"]);


        $array_menus = explode("-", $hdd_idmenus);
        $menus_permisos = '';
        foreach ($array_menus as $fila_menus) {
            if ($fila_menus != '') {
                $val_permiso = $utilidades->str_decode($_POST["cmb_permiso_" . $fila_menus]);
                $menus_permisos = $menus_permisos . $fila_menus . "," . $val_permiso . "-";
            }
        }

        $valor_exito = $dbPerfiles->editarPerfil($hdd_id_perfil, $txt_nombre_perfil, $txt_desc_perfil, $cmb_menu_inicio, $cmb_activo, $id_usuario, $menus_permisos, $cod_departamento, $cmb_colegio);
        ?>
        <input type="hidden" value="<?php echo $valor_exito; ?>" name="hdd_exito" id="hdd_exito" />
        <?php
    break;
	
	
	 case "5"://Modal, Confirmacion crear/editar
        ?>
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <?php
            @$titulo = $_POST["titulo"];
            @$funcion = $_POST["funcion"];
            ?>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo $titulo; ?></h4>
                    </div>
                    <div class="modal-body centrar">
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="<?php echo $funcion; ?>">Si</button>
                    </div>

                </div>
            </div>
        </div>
        <?php
        break;
	
	
	
	
}
?>
