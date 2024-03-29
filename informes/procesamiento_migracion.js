function cargar_archivo() {
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
        
        var params = "opcion=1";
        
        llamarAjaxUploadFiles("procesamiento_migracion_ajax.php", params, "d_carga_archivo", "finalizar_cargar_archivo();", "", "fil_arch");
    } else {
        $("#d_contenedor_error").css("display", "block");
        $('#d_contenedor_error').html('Debe seleccionar un archivo para cargar');
        
        window.scroll(0, 0);
    }
}

function finalizar_cargar_archivo() {
    $("#d_contenedor_error").css("display", "none");
    $("#d_contenedor_error").css("display", "none");
    
    $("#btn_cargar_datos").removeAttr("disabled");
    $("#d_boton_cargar_datos").css("display", "block");
    $("#d_espera_cargar_datos").css("display", "none");
    
    var resultado = trim($("#d_carga_interna").html());
    
    if (resultado == "") {
        $("#d_contenedor_exito").html("Archivo procesado con &eacute;xito");
        $("#d_contenedor_exito").css("display", "block");
        $("#fil_arch").val("");
        
        cargar_fecha_viaje();
        cargar_filtros_indicadores();
    } else {
        $("#d_contenedor_error").html("Se present&oacute; el siguiente error:<br>" + resultado);
        $("#d_contenedor_error").css("display", "block");
    }
}
