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

function cargar_archivos() {
    $("#d_contenedor_exito").css("display", "none");
    $("#d_contenedor_error").css("display", "none");

    var result = 0;

    $("#fil_arch").removeClass("borde_error");

    if (trim($('#fil_arch').val()) == '') {
        $("#fil_arch").addClass("borde_error");
        result = 1;
    }

    if (result == 0) {
        $("#btn_cargar_datos").attr("disabled", "disabled");
        $("#d_boton_cargar_datos").css("display", "none");
        $("#d_espera_cargar_datos").css("display", "block");

        var params = "opcion=1&fecha=" + $("#txt_fecha").val();

        llamarAjaxUploadFiles("procesamiento_excel_ajax.php", params, "d_carga_archivos", "", "", "fil_arch");
    } else {
        $("#contenedor_error").addClass("contenedor_error_visible");
        $('#contenedor_error').html('Debe seleccionar un archivo para cargar');

        window.scroll(0, 0);
    }
}

function obtener_extension_archivo(nombre_archivo) {
    var extension = nombre_archivo.substring(nombre_archivo.lastIndexOf(".") + 1).toLowerCase();
    return extension;
}

function mostrarModalConfirmacion() {
    $('#modalConfirmacion').modal();
}

function mostrarVentana() {
    $('#myModal').modal();
}

function finalizar_cargar_datos(ind_resultado) {
    //alert(ind_resultado);
    $("#contenedor_error").css("display", "none");
    $("#contenedor_exito").css("display", "none");

    $("#btn_cargar_datos").removeAttr("disabled");
    $("#d_boton_cargar_datos").css("display", "block");
    $("#d_espera_cargar_datos").css("display", "none");

    if (ind_resultado > 0) {
        if (ind_resultado == 1) {
            $("#d_contenedor_exito").html("Archivo cargado con &eacute;xito");
            $("#d_contenedor_exito").css("display", "block");
        }
    } else if (ind_resultado == -1) {
        $("#d_contenedor_error").html("Error - El archivo esta vacio");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -2) {
        $("#d_contenedor_error").html("Error - El archivo no tiene extensión");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -3) {
        $("#d_contenedor_error").html("Error - El archivo cargado no tiene la extensión correcta .csv");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -4) {
        $("#d_contenedor_error").html("Error - El archivo cargado no tiene todas las columnas requeridas");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -5) {
        $("#d_contenedor_error").html("Error - El archivo no cumple con algunos de los parametro de inicio, Fecha de movimiento, Tipo de cargue, Sociedad");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -6) {
        $("#d_contenedor_error").html("Error interno al registrar");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -7) {
        $("#d_contenedor_error").html("Error - Error al Eliminar Datos del Mes");
        $("#d_contenedor_error").css("display", "block");
    } else if (ind_resultado == -8) {
        $("#d_contenedor_error").html("Error - La fecha seleccionada no corresponde a la Fecha del archivo");
        $("#d_contenedor_error").css("display", "block");
    } else {
        $("#d_contenedor_error").html("Error interno al tratar de cargar el archivo");
        $("#d_contenedor_error").css("display", "block");
    }
}
