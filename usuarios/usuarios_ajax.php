<?php
session_start();

require_once("../db/DbUsuarios.php");
require_once("../db/DbListas.php");
require_once("../db/DbPerfiles.php");
require_once("../funciones/Utilidades.php");
require_once("../funciones/Class_Combo_Box.php");
require_once("../principal/ContenidoHtml.php");
require_once("../funciones/Utilidades.php");

$dbUsuarios = new DbUsuarios();
$dbListas = new DbListas();
$dbPefiles = new DbPerfiles();
$utilidades = new Utilidades();
$contenido = new ContenidoHtml();
//$contenido->validar_seguridad(1);

if (isset($_POST["hdd_numero_menu"])) {
    $tipo_acceso_menu = $contenido->obtener_permisos_menu($_POST["hdd_numero_menu"]);
}

$opcion = $_POST["opcion"];
if ($opcion != "4" && $opcion != "6") {
    header('Content-Type: text/xml; charset=UTF-8');
}

switch ($opcion) {
    case "1": //Opcion para buscar usuarios
        $txt_busca_usuario = urldecode($_POST["txt_busca_usuario"]);
        $tabla_busca_persona = $dbUsuarios->getListaUsuariosBuscar($txt_busca_usuario);
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
                            <tr><th colspan='5' style="text-align: center;">Usuarios del Sistema</th></tr>
                            <tr>
                                <th style="width:5%;">Id</th>
                                <th style="width:36%;">Nombre</th>
                                <th style="width:20%;">Usuario</th>
                                <th style="width:24%;">Perfil</th>
                                <th style="width:15%;">Estado</th>
                            </tr>
                        </thead>
                        <?php
                        $cantidad_registro = count($tabla_busca_persona);
                        $i = 1;
                        if ($cantidad_registro > 0) {
                            foreach ($tabla_busca_persona as $fila_usuario) {
                                @$id_usuario = $fila_usuario['id_usuario'];
                                @$nombre_usuario = $fila_usuario['nombre_usuario'] . " " . $fila_usuario['apellido_usuario'];
                                @$usuario = $fila_usuario['login_usuario'];
                                @$estado = $fila_usuario['ind_activo'];
                                if ($estado == 1) {
                                    $estado = 'Activo';
                                    $class_estado = 'activo';
                                } else if ($estado == 0) {
                                    $estado = 'No Activo';
                                    $class_estado = 'inactivo';
                                }
                                $tabla_perfil_persona = $dbUsuarios->getListaPerfilUsuarios($id_usuario);
                                $cantidad_perfiles = count($tabla_perfil_persona);
                                $perfiles_usuarios = '';
                                $contador = 1;
                                foreach ($tabla_perfil_persona as $fila_perfil) {
                                    if ($contador == $cantidad_perfiles) {
                                        $perfiles_usuarios = $perfiles_usuarios . $fila_perfil['nombre_perfil'];
                                    } else {
                                        $perfiles_usuarios = $perfiles_usuarios . $fila_perfil['nombre_perfil'] . ', ';
                                    }
                                    $contador = $contador + 1;
                                }
                                ?>
                                <tr style="cursor:pointer;" onclick="seleccionar_usuarios('<?php echo $id_usuario; ?>');">
                                    <td align="center"><?php echo $id_usuario; ?></td>
                                    <td align="left"><?php echo $nombre_usuario; ?></td>
                                    <td align="left"><?php echo $usuario; ?></td>
                                    <td align="left"><?php echo $perfiles_usuarios; ?></td>
                                    <td align="left"><span class="<?php echo $class_estado; ?>"><?php echo $estado; ?></span></td>

                                </tr>
                                <?php
                                $i = $i + 1;
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
        <br />
        <?php
        break;

    case "2": //Opcion para crear el formulario de crear usuarios
        $combo = new Combo_Box();
        

        $id_usuario = '';
        $tabla_perfiles_usuario = array();
        $txt_nombre_usuario = '';
        $txt_apellido_usuario = '';
        $cmb_tipo_documento = '';
        $txt_numero_documento = '';
        $txt_usuario = '';
        $txt_clave = '';
        $tipo_accion = 1; //Crear usuario
        $titulo_formulario = 'Crear nuevo usuario';
        $txt_email = '';
        $txt_telefono = '';

        if (isset($_POST['id_usuario'])) {
            $tabla_usuario = $dbUsuarios->getUsuario($_POST['id_usuario']);
            $id_usuario = $_POST['id_usuario'];
            $tabla_perfiles_usuario = $dbUsuarios->getListaPerfilUsuarios($_POST['id_usuario']);
            $txt_nombre_usuario = $tabla_usuario['nombre_usuario'];
            $txt_apellido_usuario = $tabla_usuario['apellido_usuario'];
            $cmb_tipo_documento = $tabla_usuario['id_tipo_documento'];
            $txt_numero_documento = $tabla_usuario['numero_documento'];
            $estado_usuario = $tabla_usuario['ind_activo'];
            $tipo_accion = 2; //Editar usuario
            $titulo_formulario = 'Editar usuario';
            $nombre_de_usuario = $tabla_usuario['login_usuario'];
            $txt_email = $tabla_usuario['email_usuario'];
            $txt_telefono = $tabla_usuario['telefono_usuario'];
        }
        ?>
        <div id="div_usuario_existe"><input type="hidden" value="true" name="hdd_usuario_existe" id="hdd_usuario_existe" /></div>
        <div id="div_documento_existe"><input type="hidden" value="true" name="hdd_documento_existe" id="hdd_documento_existe" /></div>
        <input type="hidden" value="<?php echo $id_usuario; ?>" name="hdd_id_usuario" id="hdd_id_usuario" />
        <!-- <input type="text" value="0" name="hdd_exito" id="hdd_exito" /> -->

        </br>
        </br>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading"><?php echo $titulo_formulario; ?></div>
                    <div class="panel-body">
                        <form id="frmCrearUsuario" name="frmCrearUsuario">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Nombres del usuario</label>
                                <input type="text" class="form-control" name="txt_nombre_usuario" id="txt_nombre_usuario" placeholder="Nombres del usuario" value="<?php echo $txt_nombre_usuario; ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Apellidos del usuario</label>
                                <input type="text" class="form-control" name="txt_apellido_usuario" id="txt_apellido_usuario" placeholder="Apellidos del usuario" value="<?php echo $txt_apellido_usuario; ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Tipo de documento</label>
                                <?php
                                $lista_tipo_documento = $dbListas->getListaDetalles(1);
                                $combo->getComboDb('cmb_tipo_documento', $cmb_tipo_documento, $lista_tipo_documento, 'id_detalle, nombre_detalle', '--Seleccione--', '', '', 'width:350px;', '', 'form-control');
                                ?>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">N&uacute;mero de documento</label>
                                <input type="text" class="form-control" name="txt_numero_documento" id="txt_numero_documento" placeholder="N&uacute;mero de documento" value="<?php echo $txt_numero_documento; ?>" onblur="trim_cadena(this);
                                                validar_documento_existente(this, '<?php echo $tipo_accion; ?>', '<?php echo $id_usuario; ?>');
                                                quitar_espacios(this);">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tel&eacute;fono</label>
                                <input type="text" class="form-control" name="txt_telefono" id="txt_telefono" placeholder="Nombres del usuario" value="<?php echo $txt_telefono; ?>" onblur="trim_cadena(this);
                                                quitar_espacios(this);">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email</label>
                                <input type="email" class="form-control" name="txt_email" id="txt_email" placeholder="Nombres del usuario" value="<?php echo $txt_email; ?>" onblur="trim_cadena(this);
                                                quitar_espacios(this);">
                            </div>



                            <?php
                            if ($tipo_accion == 1) { //Solo se muestra si se va a crear un nuevo usuario
                                ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Usuario</label>
                                    <input type="text" class="form-control" name="txt_usuario" id="txt_usuario" placeholder="Usuario" value="<?php echo $txt_nombre_usuario; ?>" onblur="trim_cadena(this);
                                                        quitar_espacios(this);">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Contrase&ntilde;a</label>
                                    <input type="password" class="form-control" name="txt_clave" id="txt_clave" placeholder="Usuario"  onblur="convertirAMinusculas(this);
                                                        trim_cadena(this);
                                                        validar_usuario_existente(this);
                                                        quitar_espacios(this);">
                                </div>
                                <?php
                            } else if ($tipo_accion == 2) {//Solo se muestra si se va a editar un nuevo usuario
                                if ($estado_usuario == 1) {
                                    $checked_estado = 'checked';
                                } else {
                                    $checked_estado = '';
                                }
                                ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Estado</label>
                                </div>     
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="check_estado" id="check_estado" <?php echo $checked_estado; ?>> Usuario activo
                                    </label>
                                </div>

                                <div class="centrar panel panel-primary">
                                    <div class="panel-heading">Usuario del sistema</div>
                                    </br>
                                    <div class="form-group">
                                        <div>
                                            <label for="exampleInputEmail1">Usuario: <span class="label label-primary"><?php echo $nombre_de_usuario; ?></span></label>
                                        </div>
                                        </br>
                                        <div>
                                            <button type="button" id="btnResetearPass" name="btnResetearPass" class="btn btn-danger" onclick="confirmar_resetear_pass('<?php echo $nombre_de_usuario; ?>');">Reiniciar contraseña</button>
                                        </div>
                                    </div>
                                </div>

                                </br>

                                <?php
                            }
                            ?>
                            <div class="centrar panel panel-primary">
                                <div class="panel-heading">Perfiles</div>
                                </br>
                                <?php
                                $tabla_pefiles = $dbPefiles->getListaPerfiles();

                                $i = 1;
                                foreach ($tabla_pefiles as $fila_perfiles) {
                                    $id_perfil = $fila_perfiles['id_perfil'];
                                    $nombre_perfil = $fila_perfiles['nombre_perfil'];

                                    $checked = '';
                                    //Se recorre el array donde tien los perfiles encontrados
                                    if (count($tabla_perfiles_usuario) != 0) {
                                        foreach ($tabla_perfiles_usuario as $fila_perfiles_usuario) {
                                            $id_perfil_usuario = $fila_perfiles_usuario['id_perfil'];
                                            if ($id_perfil == $id_perfil_usuario) {
                                                $checked = 'checked';
                                            }
                                        }
                                    }
                                    if ($i == 1) {
                                        ?>
                                        <input type="checkbox" name="check_pefiles" id="check_pefiles_<?php echo $id_perfil; ?>" value="<?php echo $id_perfil; ?>" <?php echo $checked; ?>> <?php echo $nombre_perfil; ?>
                                        <?php
                                        $i = 0;
                                    } else {
                                        ?>
                                        <input type="checkbox" name="check_pefiles" id="check_pefiles_<?php echo $id_perfil; ?>" value="<?php echo $id_perfil; ?>" <?php echo $checked; ?>> <?php echo $nombre_perfil; ?>	
                                        <?php
                                        $i = $i + 1;
                                    }
                                }
                                ?>

                            </div>



                            <div class="centrar">
                                <button type="button" class="btn btn-default" id="btn_cancelar" nombre="btn_cancelar" onclick="volver_inicio();">Cancelar</button>
                                <?php
                                if ($tipo_accion == 1) {//Boton para crear usuario
                                    if ($tipo_acceso_menu == 2) {
                                        ?>
                                        <button type="submit" class="btn btn-success" id="btn_crear" nombre="btn_crear" onclick="validar_crear_usuarios();">Crear</button>
                                        <?php
                                    }
                                } else if ($tipo_accion == 2) {//Boton para editar usuario
                                    if ($tipo_acceso_menu == 2) {
                                        ?>
                                        <button type="submit" class="btn btn-success" id="btn_editar" nombre="btn_editar" onclick="validar_editar_usuarios();">Guardar</button>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </form>



                    </div>
                </div>
            </div>
        </div>




        <br />
        <?php
        break;

    case "3": //Opcion para validar usuario existente
        $txt_busca_usuario = urldecode($_POST["nombre_usuario"]);
        $tabla_busca_persona = $dbUsuarios->getNombreUsuariosBuscar($txt_busca_usuario);
        $cantidad = count($tabla_busca_persona);
        if ($cantidad >= 1) {
            ?>
            <input type="hidden" value="false" name="hdd_usuario_existe" id="hdd_usuario_existe" />
            <?php
        } else if ($cantidad == 0) {
            ?>
            <input type="hidden" value="true" name="hdd_usuario_existe" id="hdd_usuario_existe" />
            <?php
        }
        break;

    case "4": //Opcion para crear nuevo usuarios
        $id_usuario_crea = $_SESSION["idUsuario"];
        @$txt_nombre_usuario = urldecode($_POST["txt_nombre_usuario"]);
        @$txt_apellido_usuario = urldecode($_POST["txt_apellido_usuario"]);
        @$cmb_tipo_documento = $_POST["cmb_tipo_documento"];
        @$txt_numero_documento = $_POST["txt_numero_documento"];
        @$txt_usuario = urldecode($_POST["txt_usuario"]);
        @$txt_clave = urldecode($_POST["txt_clave"]);
        @$perfiles_usuarios = $_POST["array_perfiles"];
        @$txt_email = $utilidades->limpiar_tags($_POST["txt_email"]);
        @$txt_telefono = $utilidades->limpiar_tags($_POST["txt_telefono"]);
        $resultado = $dbUsuarios->InsertUsuario($txt_nombre_usuario, $txt_apellido_usuario, $cmb_tipo_documento, $txt_numero_documento, $txt_usuario, $txt_clave, $perfiles_usuarios, $id_usuario_crea, $txt_email, $txt_telefono);
        ?>
        <input type="hidden" value="<?php echo $resultado; ?>" name="hdd_exito" id="hdd_exito" />
        <?php
        break;

    case "5": //Opcion para validar documento existente
        $txt_documento_usuario = $_POST["documento_usuario"];
        $tipo = $_POST["tipo"];
        $id_usuario = $_POST["id_usuario"];

        if ($tipo == 1) {
            $tabla_busca_documento = $dbUsuarios->getBuscarDocumento($txt_documento_usuario, $id_usuario);
        } else if ($tipo == 2) {
            $tabla_busca_documento = $dbUsuarios->getBuscarDocumento($txt_documento_usuario, $id_usuario);
        }
        $cantidad = count($tabla_busca_documento);
        if ($cantidad >= 1) {
            ?>
            <input type="hidden" value="false" name="hdd_documento_existe" id="hdd_documento_existe" />
            <?php
        } else if ($cantidad == 0) {
            ?>
            <input type="hidden" value="true" name="hdd_documento_existe" id="hdd_documento_existe" />
            <?php
        }
        break;

    case "6": //Opcion para editar nuevo usuarios
        $id_usuario_crea = $_SESSION["idUsuario"];
        @$hdd_id_usuario = $_POST["hdd_id_usuario"];
        @$txt_nombre_usuario = urldecode($_POST["txt_nombre_usuario"]);
        @$txt_apellido_usuario = urldecode($_POST["txt_apellido_usuario"]);
        @$cmb_tipo_documento = $_POST["cmb_tipo_documento"];
        @$txt_numero_documento = $_POST["txt_numero_documento"];

        @$check_estado = $_POST['check_estado'];
        @$perfiles_usuarios = $_POST["array_perfiles"];

        @$txt_email = $utilidades->limpiar_tags($_POST["txt_email"]);
        @$txt_telefono = $utilidades->limpiar_tags($_POST["txt_telefono"]);

        $msg_resultado = $dbUsuarios->UpdateUsuario($hdd_id_usuario, $txt_nombre_usuario, $txt_apellido_usuario, $cmb_tipo_documento, $txt_numero_documento, $check_estado, $perfiles_usuarios, $id_usuario_crea, $txt_email, $txt_telefono);
        ?>
		<input type="hidden" value="<?php echo $msg_resultado; ?>" name="hdd_exito" id="hdd_exito" />
        <?php
        ?>
        <?php
        break;

    case "7": //Resetea la contarseña del usuario
        $idUsuario = $_POST['id_usuario'];

        $rta_aux = $dbUsuarios->resetearPass($idUsuario);

        echo $rta_aux;

        break;

    case "8"://Modal, Confirmacion crear/editar
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
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="<?php echo $funcion; ?>();">Si</button>
                    </div>

                </div>
            </div>
        </div>
        <?php
        break;
    
    
    
    case "9"://Modal, Confirmacion
        ?>
        <!-- Modal -->
        <div class="modal fade" id="modalConfirmacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="<?php echo $funcion; ?>;">Si</button>
                    </div>

                </div>
            </div>
        </div>
        <?php
        break;
}
?>