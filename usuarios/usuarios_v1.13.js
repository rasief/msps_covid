
function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) { //mostrar
        $('#fondo_negro').css('display', 'block');
        $('#d_centro').slideDown(400).css('display', 'block');
    } else if (tipo == 0) { //Ocultar
        $('#fondo_negro').css('display', 'none');
        $('#d_centro').slideDown(400).css('display', 'none');
    }
}

function reducir_formulario_flotante(ancho, alto) {
    $('.div_centro').width(ancho);
    $('.div_centro').css('top', '20%');
    $('.div_interno').width(ancho/*-15*/);
}

function mostrar_formulario(tipo) {
    if (tipo == 1) { //mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    } else if (tipo == 0) { //Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

function ver_todos_usuarios() {


    $('#txt_busca_usuario').val("");

    var params = 'opcion=1&txt_busca_usuario=' + $('#txt_busca_usuario').val();

    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1)");
}

function buscar_usuarios() {
    $("#listado_usuarios").validate({
        rules: {
            txt_busca_usuario: {
                required: true,
            }
        },
        submitHandler: function () {
            var txt_busca_usuario = $('#txt_busca_usuario').val();
            var params = 'opcion=1&txt_busca_usuario=' + txt_busca_usuario;
            llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1)");
            return false;
        },
    });
}

function llamar_crear_usuarios() {
    var params = 'opcion=2&txt_busca_usuario=' + txt_busca_usuario;

    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1)");
}

function validar_usuario_existente(nombre) {
    nombre = $(nombre).val();
    var params = 'opcion=3&nombre_usuario=' + nombre;

    llamarAjax("usuarios_ajax.php", params, "div_usuario_existe", "");
}

function validar_documento_existente(documento, tipo, id_usuario) {
    documento = $(documento).val();
    if (tipo == 1) {
        var params = 'opcion=5&documento_usuario=' + documento + '&tipo=' + tipo + '&id_usuario=' + id_usuario;

        llamarAjax("usuarios_ajax.php", params, "div_documento_existe", "");
    } else if (tipo == 2) {
        var params = 'opcion=5&documento_usuario=' + documento + '&tipo=' + tipo + '&id_usuario=' + id_usuario;

        llamarAjax("usuarios_ajax.php", params, "div_documento_existe", "");
    }
}

/*
 jQuery.validator.addMethod(
 "usuarioexistente",
 function (elementValue, element, param) {
 var validar_existe_usuario = $('#hdd_usuario_existe').val();
 if (validar_existe_usuario == 'true') {
 return true;
 } else {
 return false;
 }
 },
 "El usuario ya esxite"
 );
 
 jQuery.validator.addMethod(
 "documentoexistente",
 function (elementValue, element, param) {
 var validar_existe_documento = $('#hdd_documento_existe').val();
 if (validar_existe_documento == 'true') {
 return true;
 } else {
 return false;
 }
 },
 "El Documento ya esxite"
 );*/

function validar_crear_usuarios() {
    $("#frmCrearUsuario").validate({
        rules: {
            txt_nombre_usuario: {
                required: true,
            },
            txt_apellido_usuario: {
                required: true,
            },
            cmb_tipo_documento: {
                required: true,
            },
            txt_numero_documento: {
                required: true,
            },
            txt_usuario: {
                required: true,
            },
            txt_clave: {
                required: true,
            }

        },
        submitHandler: function () {

            var categorias = new Array();
            $('input:checkbox').each(function () {
                if (this.checked) {
                    categorias.push($(this).val());
                }
            });

            if (categorias == '') {
                mensajeError('Debe seleccionar al menos un perfil.');
            } else {
                //confirmarEdicion("¿Realmente desea crear el usuario?", "crear_usuarios");
                modalConfirmarGuardar("¿Realmente desea crear el usuario?", "crear_usuarios()");
            }
        },
    });
}

function modalConfirmarGuardar(titulo, funcion) {
    var params = 'opcion=9&titulo=' + titulo + '&funcion=' + funcion;
    llamarAjax("usuarios_ajax.php", params, "ventanaModal", "mostrarModalConfirmacion();");
}

function mostrarModalConfirmacion() {
    $('#modalConfirmacion').modal();
}

function validar_editar_usuarios() {
    $("#frmCrearUsuario").validate({
        rules: {
            txt_nombre_usuario: {
                required: true,
            },
            txt_apellido_usuario: {
                required: true,
            },
            cmb_tipo_documento: {
                required: true,
            },
            txt_numero_documento: {
                required: true,
            },
            txt_usuario: {
                required: true,
            },
            txt_clave: {
                required: true,
            }

        },
        submitHandler: function () {
            var categorias = new Array();
            $('input:checkbox').each(function () {
                if (this.checked) {
                    categorias.push($(this).val());
                }
            });

            if (categorias == '') {
                mensajeError('Debe seleccionar al menos un perfil.');
            } else {
                confirmarEdicion("¿Realmente desea guardar la edición?", "editar_usuarios");
            }
        },
    });
}

function confirmarEdicion(titulo, funcion) {
    var params = 'opcion=8&titulo=' + titulo + '&funcion=' + funcion;
    llamarAjax("usuarios_ajax.php", params, "ventanaModal", "mostrarVentana();");
}

function mostrarVentana() {
    $('#myModal').modal();
}


/*
 * Tipo
 * 1=crear
 * 2=editar
 */
function validar_exito() {

    var hdd_exito = $('#hdd_exito').val();
	
    //$('.formulario').css('display', 'none')
    if (hdd_exito > 0) {
        $("#contenedor_exito").addClass("contenedor_exito_visible");
        $('#contenedor_exito').html('Datos guardados correctamente');
        window.scroll(0, 0);
        setTimeout(
                function () {
                    $('#contenedor_exito').slideUp(200).removeClass("contenedor_exito_visible").addClass("contenedor_exito");
                }, 2000);
        volver_inicio();
    } else {
        $("#contenedor_error").addClass("contenedor_error_visible");
        $('#contenedor_error').html('Error al guardar usuarios');

        window.scroll(0, 0);
        setTimeout("$('#contenedor_error').slideUp(200).removeClass('contenedor_error_visible').addClass('contenedor_error')", 3000);
    }

}

function volver_inicio() {
    //Si hay algo en la casilla de buscar usuarios busca por esta opcion
    if ($('#txt_busca_usuario').val() != '') {
        $("#listado_usuarios").submit();
    } else {//De lo contrario muestra todos los usuarios
        ver_todos_usuarios();
    }
}

function crear_usuarios() {

    var txt_nombre_usuario = $('#txt_nombre_usuario').val();
    var txt_apellido_usuario = $('#txt_apellido_usuario').val();
    var cmb_tipo_documento = $('#cmb_tipo_documento').val();
    var txt_numero_documento = $('#txt_numero_documento').val();
    var txt_usuario = $('#txt_usuario').val();
    var txt_clave = $('#txt_clave').val();
    var perfiles_usuario = new Array();

    var txt_telefono = $('#txt_telefono').val();
    var txt_email = $('#txt_email').val();

    $("input[name='check_pefiles']:checked").each(function () {
        perfiles_usuario.push($(this).val());
    });
    var params = 'opcion=4&txt_nombre_usuario=' + txt_nombre_usuario +
            '&txt_apellido_usuario=' + txt_apellido_usuario +
            '&cmb_tipo_documento=' + cmb_tipo_documento +
            '&txt_numero_documento=' + txt_numero_documento +
            '&txt_usuario=' + txt_usuario +
            '&txt_clave=' + txt_clave +
            '&array_perfiles=' + perfiles_usuario +
            '&txt_telefono=' + txt_telefono +
            '&txt_email=' + txt_email;
    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "validar_exito();volver_inicio();");
    //llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "validar_exito(); volver_inicio()");


}

function editar_usuarios() {
	
	
    var txt_nombre_usuario = $('#txt_nombre_usuario').val();
    var txt_apellido_usuario = $('#txt_apellido_usuario').val();
    var cmb_tipo_documento = $('#cmb_tipo_documento').val();
    var txt_numero_documento = $('#txt_numero_documento').val();
    var hdd_id_usuario = $('#hdd_id_usuario').val();
    var check_estado = $('#check_estado').is(':checked') ? 1 : 0;
    var perfiles_usuario = new Array();

    var txt_telefono = $('#txt_telefono').val();
    var txt_email = $('#txt_email').val();

    $("input[name='check_pefiles']:checked").each(function () {
        perfiles_usuario.push($(this).val());
    });

    var params = 'opcion=6&txt_nombre_usuario=' + txt_nombre_usuario +
            '&txt_apellido_usuario=' + txt_apellido_usuario +
            '&cmb_tipo_documento=' + cmb_tipo_documento +
            '&txt_numero_documento=' + txt_numero_documento +
            '&array_perfiles=' + perfiles_usuario +
            '&hdd_id_usuario=' + hdd_id_usuario +
            '&check_estado=' + check_estado +
            '&txt_telefono=' + txt_telefono +
            '&txt_email=' + txt_email;
    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "validar_exito();volver_inicio()");
	
	//llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "");
	


}

function seleccionar_usuarios(id_usuario) {
    var params = 'opcion=2&id_usuario=' + id_usuario;
    llamarAjax("usuarios_ajax.php", params, "principal_usuarios", "mostrar_formulario(1);");
}

function confirmar_guardar() {
    $('#d_interno').html(
            '<table border="0" cellpadding="5" cellspacing="0" align="center" style="width:100%">' +
            '<tr>' +
            '<th align="center">' +
            '<h4>&iquest;Est&aacute; seguro de guardar esta informaci&oacute;n?</h4>' +
            '</th>' +
            '</tr>' +
            '<tr>' +
            '<th align="center">' +
            '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_usuarios();"/>\n' +
            '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/> ' +
            '</th>' +
            '</tr>' +
            '</table>');
}

//Resetea la contraseña
function confirmar_resetear_pass(usuario) {
    confirmarEdicion('La contrase\u00f1a del usuario: ' + usuario + ' sera reemplazada por: ' + usuario + ' ¿Realmente desea realizar la acci\u00f3n?', "resetear_pass()");
}

function resetear_pass() {
    var id_usuario = $('#hdd_id_usuario').val();
    var params = 'opcion=7&id_usuario=' + id_usuario;
    llamarAjax("usuarios_ajax.php", params, "rtaResetearPass", "postResetearPass();");
}

//Funcion que verifica que la funcion: resetear_pass, se ejecute sin problemas
function postResetearPass() {
    var rtaResetearPass = $('#rtaResetearPass').text();
    if (rtaResetearPass == '1') {
        mensajeExitoso('La contrase\u00f1a ha sido reseteada');
    } else {
        mensajeError('Error al intentar resetear la contrase\u00f1a. Vuelve a intentarlo');
    }
}
