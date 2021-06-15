function calcular_alertas() {
    $("#d_contenedor_exito").css("display", "none");
    $("#d_contenedor_error").css("display", "none");

    var result = 0;

    $("#fil_arch_casos").removeClass("borde_error");
    $("#fil_arch_defunciones").removeClass("borde_error");
    
    if (trim($('#fil_arch_casos').val()) == '') {
        $("#fil_arch_casos").addClass("borde_error");
        result = 1;
    }
    
    if (trim($('#fil_arch_defunciones').val()) == '') {
        $("#fil_arch_defunciones").addClass("borde_error");
        result = 1;
    }
    
    if (result == 0) {
        $("#btn_cargar_datos").attr("disabled", "disabled");
        $("#d_boton_cargar_datos").css("display", "none");
        $("#d_espera_cargar_datos").css("display", "block");
        
        var params = "opcion=1";
        
        llamarAjaxUploadFiles("alertas_bes_ajax.php", params, "d_carga_archivo", "finalizar_calcular_alertas();", "", "fil_arch_casos;fil_arch_defunciones");
    } else {
        $("#d_contenedor_error").css("display", "block");
        $('#d_contenedor_error').html('Debe seleccionar los dos archivos para cargar');
        
        window.scroll(0, 0);
    }
}

function finalizar_calcular_alertas() {
    $("#d_contenedor_error").css("display", "none");
    $("#d_contenedor_error").css("display", "none");
    
    $("#btn_cargar_datos").removeAttr("disabled");
    $("#d_boton_cargar_datos").css("display", "block");
    $("#d_espera_cargar_datos").css("display", "none");
    
    var resultado = trim($("#d_carga_interna").html());
    
    if (resultado == "") {
        $("#d_contenedor_exito").html("C&aacute;lculo realizado con &eacute;xito");
        $("#d_contenedor_exito").css("display", "block");
        $("#fil_arch_casos").val("");
        $("#fil_arch_defunciones").val("");
    } else {
        $("#d_contenedor_error").html("Se present&oacute; el siguiente error:<br>" + resultado);
        $("#d_contenedor_error").css("display", "block");
    }
}
