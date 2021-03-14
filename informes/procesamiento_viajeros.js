function cargar_fecha_viaje() {
    var params = "opcion=1";
    
    llamarAjax("procesamiento_viajeros_ajax.php", params, "d_fecha_viaje", "");
}

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
        
        var params = "opcion=2&fecha_viaje=" + $("#hdd_fecha_viaje").val();
        
        llamarAjaxUploadFiles("procesamiento_viajeros_ajax.php", params, "d_carga_archivo", "finalizar_cargar_archivo();", "", "fil_arch");
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

function cargar_filtros_indicadores() {
    var params = "opcion=3";
    
    llamarAjax("procesamiento_viajeros_ajax.php", params, "d_filtros_indicadores", "");
}

function calcular_indicadores() {
    var params = "opcion=4&fecha_ini=" + $("#txt_fecha_ini").val() +
            "&fecha_fin=" + $("#txt_fecha_fin").val();
    
    llamarAjax("procesamiento_viajeros_ajax.php", params, "d_indicadores", "abrir_archivo_indicadores();");
}

function abrir_archivo_indicadores() {
    var ruta = $("#hdd_ruta_indicadores").val();
    if (ruta !== "") {
        window.open("../funciones/abrir_txt.php?ruta=" + ruta + "&nombre_arch=indicadores_viajeros.txt", "_blank");
    } else {
        $("#d_contenedor_error").html("Error - el archivo no pudo ser generado.");
        $("#d_contenedor_error").css("display", "block");
    }
}
