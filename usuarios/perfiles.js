function mostrar_formulario(tipo) {
    if (tipo == 1) {//mostrar
        $('.formulario').slideDown(600).css('display', 'block')
    }
    else if (tipo == 0) {//Ocultar
        $('.formulario').slideUp(600).css('display', 'none')
    }
}

/**
 *Evento para cargar el listado de los perfiles 
 */
function cargar_perfiles() {
    var params = 'opcion=2';
    llamarAjax("perfiles_ajax.php", params, "principal_perfiles", "mostrar_formulario(1)");
}


/**
 *Evento para cargar el listado de los perfiles 
 */
function cargar_formulario_crear() {
    var params = 'opcion=1';
    llamarAjax("perfiles_ajax.php", params, "principal_perfiles", "mostrar_formulario(1)");
}

function cargar_formulario_editar(id_perfil) {
    var params = 'opcion=1&id_perfil=' + id_perfil;
    llamarAjax("perfiles_ajax.php", params, "principal_perfiles", "mostrar_formulario(1)");
}


function mostrar_formulario_flotante(tipo) {
    if (tipo == 1) {//mostrar
        $('#fondo_negro').css('display', 'block');
        $('#d_centro').slideDown(400).css('display', 'block');
    }
    else if (tipo == 0) {//Ocultar
        $('#fondo_negro').css('display', 'none');
        $('#d_centro').slideDown(400).css('display', 'none');
    }
}

function reducir_formulario_flotante(ancho, alto) {
    $('.div_centro').width(ancho);
    //$('.div_centro').height(alto);
    $('.div_centro').css('top', '20%');
    $('.div_interno').width(ancho/*-15*/);
    //$('.div_interno').height(alto-35);
}

/*
 * Tipo
 * 1=crear
 * 2=editar
 */
function validar_exito() {

    var hdd_exito = $('#hdd_exito').val();
    if (hdd_exito > 0) {
        $("#contenedor_exito").css("display", "block");
        $('#contenedor_exito').html('Datos guardados correctamente');
        setTimeout("$('#contenedor_exito').slideUp(200).css('display', 'none')", 5000);
        setTimeout(cargar_perfiles(), 5000);

    }
    else {
        $("#contenedor_error").css("display", "block");
        $('#contenedor_error').html('Error al guardar usuarios');
        setTimeout("$('#contenedor_error').slideUp(200).css('display', 'none')", 5000);
        setTimeout(cargar_perfiles(), 5000);
    }
}


function validar_crear_perfil() {
    $("#frm_perfiles").validate({
        rules: {
            txt_nombre_perfil: {
                required: true,
            },
            txt_desc_perfil: {
                required: true,
            },
            cmb_atiende_consulta: {
                required: true,
            },
            cmb_atiende_cirugias: {
                required: true,
            }
        },
        submitHandler: function() {
            var hdd_idmenus = $('#hdd_idmenus').val();
            var array_menus = hdd_idmenus.split('-');
            var bandera = false; //Para saber si hay un menu selccionado
            for (var k in array_menus)
            {
                var menu = array_menus[k];
                if (menu != '') {
                    var cmb_permiso = $('#cmb_permiso_' + menu).val();
                    if (cmb_permiso != '') {
                        bandera = true;
                        break;
                    }
                }
            }
            if (bandera == false) {
                $("#contenedor_error").css("display", "block");
                $('#contenedor_error').html('Debe seleccionar habilitar al menos un menu');
                return false;
            }
            crear_perfil();
        },
    });
}


function crear_perfil() {

    var txt_nombre_perfil = $('#txt_nombre_perfil').val();
    var txt_desc_perfil = $('#txt_desc_perfil').val();
    var cmb_atiende_consulta = $('#cmb_atiende_consulta').val();
    var hdd_idmenus = $('#hdd_idmenus').val();
    var cmb_atiende_cirugias = $('#cmb_atiende_cirugias').val();
    var array_menus = hdd_idmenus.split('-');
    

    var params = 'opcion=3&txt_nombre_perfil=' + txt_nombre_perfil +
            '&txt_desc_perfil=' + txt_desc_perfil +
            '&cmb_atiende_consulta=' + cmb_atiende_consulta +
            '&hdd_idmenus=' + hdd_idmenus +
            '&cmb_atiende_cirugias=' + cmb_atiende_cirugias;
    
    for (var k in array_menus)
    {
        var menu = array_menus[k];
        if (menu != '') {
            var cmb_permiso = $('#cmb_permiso_' + menu).val();
            params = params + '&cmb_permiso_' + menu + '=' + cmb_permiso;
        }
    }
    llamarAjax("perfiles_ajax.php", params, "principal_perfiles", "validar_exito()");

}


function validar_editar_perfil() {
    $("#frm_perfiles").validate({
        rules: {
            txt_nombre_perfil: {
                required: true,
            },
            txt_desc_perfil: {
                required: true,
            },
            cmb_atiende_consulta: {
                required: true,
            },
            cmb_activo: {
                required: true,
            },
            cmb_atiende_cirugias: {
                required: true,
            }
        },
        submitHandler: function() {
            var hdd_idmenus = $('#hdd_idmenus').val();
            var array_menus = hdd_idmenus.split('-');
            var bandera = false; //Para saber si hay un menu selccionado
            for (var k in array_menus)
            {
                var menu = array_menus[k];
                if (menu != '') {
                    var cmb_permiso = $('#cmb_permiso_' + menu).val();
                    if (cmb_permiso != '') {
                        bandera = true;
                        break;
                    }
                }
            }
            if (bandera == false) {
                $("#contenedor_error").css("display", "block");
                $('#contenedor_error').html('Debe seleccionar habilitar al menos un menu');
                return false;
            }
            //editar_perfil();
            mostrar_formulario_flotante(1);
            reducir_formulario_flotante(400, 250);
            posicionarDivFlotante('d_centro');
            confirmar_guardar();
        },
    });
}

function editar_perfil() {

    var hdd_id_perfil = $('#hdd_id_perfil').val();
    var txt_nombre_perfil = $('#txt_nombre_perfil').val();
    var txt_desc_perfil = $('#txt_desc_perfil').val();
    var cmb_atiende_consulta = $('#cmb_atiende_consulta').val();
    var cmb_activo = $('#cmb_activo').val();
    var hdd_idmenus = $('#hdd_idmenus').val();
    var cmb_atiende_cirugias = $('#cmb_atiende_cirugias').val();
    var array_menus = hdd_idmenus.split('-');

    var params = 'opcion=4&txt_nombre_perfil=' + txt_nombre_perfil +
            '&txt_desc_perfil=' + txt_desc_perfil +
            '&cmb_atiende_consulta=' + cmb_atiende_consulta +
            '&cmb_activo=' + cmb_activo +
            '&hdd_id_perfil=' + hdd_id_perfil +
            '&hdd_idmenus=' + hdd_idmenus +
            '&cmb_atiende_cirugias=' + cmb_atiende_cirugias;
    
    for (var k in array_menus)
    {
        var menu = array_menus[k];
        if (menu != '') {
            var cmb_permiso = $('#cmb_permiso_' + menu).val();
            params = params + '&cmb_permiso_' + menu + '=' + cmb_permiso;
        }
    }
    llamarAjax("perfiles_ajax.php", params, "principal_perfiles", "cerrar_div_centro(); validar_exito();");
    //llamarAjax("perfiles_ajax.php", params, "principal_perfiles", "");
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
            '<input type="button" id="btn_cancelar_si" nombre="btn_cancelar_si" class="btnPrincipal" value="Aceptar" onclick="editar_perfil();"/>\n' +
            '<input type="button" id="btn_cancelar_no" nombre="btn_cancelar_no" class="btnSecundario" value="Cancelar" onclick="cerrar_div_centro();"/>' +
            '</th>' +
            '</tr>' +
            '</table>');
}
