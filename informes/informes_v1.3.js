
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

function cargar_informes() {
    var params = 'opcion=1';
    llamarAjax("informes_ajax.php", params, "principal_informes", "");
}

function llamar_generar_informe() {
    $('#btn_generar_informe').css('display', 'none');
    $('#img_cargando').css('display', 'block');
    var params = 'opcion=2';
    llamarAjax("informes_ajax.php", params, "div_subir_informes", "validar_generar_informe()");
}

function validar_generar_informe() {
    $('#img_cargando').css('display', 'none');
    $('#msg_cargado').css('display', 'block');

    cargar_informes();
}

function generar_reportes(val_fecha) {
    $('#div_mostrar_reportes').html('<div id="cargando_informe" class="img_cargando"></div>');
    $('#cargando_informe').css('display', 'block');
    var params = 'opcion=3&val_fecha=' + val_fecha;
    mostrarModalReportes();
    console.log(params);
    llamarAjax("informes_ajax.php", params, "div_mostrar_reportes", "finalizar_reporte()");
    //llamarAjax("informes_ajax.php", params, "div_reportes", "");
}

function generar_reportes_municipal(val_fecha) {
    $('#div_mostrar_reportes').html('<div id="cargando_informe" class="img_cargando"></div>');
    $('#cargando_informe').css('display', 'block');
    var params = 'opcion=5&val_fecha=' + val_fecha;

    mostrarModalReportes();
    $('#cargando_informe').css('display', 'block');
    llamarAjax("informes_ajax.php", params, "div_mostrar_reportes", "finalizar_reporte()");
}

function generar_reportes_departamental(val_fecha, indice) {
    if ($("#sel_departamento_" + indice).val() != "") {
        $('#div_mostrar_reportes').html('<div id="cargando_informe" class="img_cargando"></div>');
        $('#cargando_informe').css('display', 'block');
        var params = 'opcion=8&val_fecha=' + val_fecha +
                "&cod_dep=" + $("#sel_departamento_" + indice).val() +
                "&cod_mun_dane=" + $("#sel_municipio_" + indice).val();
        
        mostrarModalReportes();
        $('#cargando_informe').css('display', 'block');
        llamarAjax("informes_ajax.php", params, "div_mostrar_reportes", "finalizar_reporte()");
    } else {
        alert("Debe seleccionar un departamento");
    }
}

function finalizar_reporte() {
    $('#cargando_informe').css('display', 'none');
}

function mostrarModalReportes() {
    $('#modalReportes').modal();
}

function generar_reportes_ppt() {
    $('#div_ppt').html('<div id="cargando_informe_colom_ppt" class="img_cargando"></div>');
    $('#cargando_informe_colom_ppt').css('display', 'block');

    var val_fecha = $('#val_fecha').val();

    var img = $('#img_1').val();
    var img_fallecidos = $('#img_fallecidos').val();
    var img_muertes_gedad = $('#img_muertes_gedad').val();
    var img_contagios_gedad = $('#img_contagios_gedad').val();
    var img_tasa_dpto = $('#img_tasa_dpto').val();
    var img_tasa_muni = $('#img_tasa_muni').val();
    var img_tasa_morta_dpto = $('#img_tasa_morta_dpto').val();
    var img_tasa_morta_muni = $('#img_tasa_morta_muni').val();
    var img_tasa_dpto_mes = $('#img_tasa_dpto_mes').val();
    var img_tasa_muni_mes = $('#img_tasa_muni_mes').val();
    var img_tasa_morta_dpto_mes = $('#img_tasa_morta_dpto_mes').val();
    var img_tasa_morta_muni_mes = $('#img_tasa_morta_muni_mes').val();
    var img_letalidad = $('#img_letalidad').val();

    var img_muertes_covid_nocovid = $('#img_def_covid_otros_colom').val();

    var img = base64Encode(img);
    var img_fallecidos = base64Encode(img_fallecidos);
    var img_muertes_gedad = base64Encode(img_muertes_gedad);
    var img_contagios_gedad = base64Encode(img_contagios_gedad);
    var img_tasa_dpto = base64Encode(img_tasa_dpto);
    var img_tasa_muni = base64Encode(img_tasa_muni);
    var img_tasa_morta_dpto = base64Encode(img_tasa_morta_dpto);
    var img_tasa_morta_muni = base64Encode(img_tasa_morta_muni);
    var img_tasa_dpto_mes = base64Encode(img_tasa_dpto_mes);
    var img_tasa_muni_mes = base64Encode(img_tasa_muni_mes);
    var img_tasa_morta_dpto_mes = base64Encode(img_tasa_morta_dpto_mes);
    var img_tasa_morta_muni_mes = base64Encode(img_tasa_morta_muni_mes);

    var img_letalidad = base64Encode(img_letalidad);

    var img_muertes_covid_nocovid = base64Encode(img_muertes_covid_nocovid);

    var params = 'opcion=4&val_fecha=' + val_fecha + '&img_1=' + img + '&img_fallecidos=' + img_fallecidos + '&img_muertes_gedad=' + img_muertes_gedad + '&img_contagios_gedad=' + img_contagios_gedad +
            '&img_tasa_dpto=' + img_tasa_dpto + '&img_tasa_muni=' + img_tasa_muni + '&img_tasa_morta_dpto=' + img_tasa_morta_dpto + '&img_tasa_morta_muni=' + img_tasa_morta_muni +
            '&img_tasa_dpto_mes=' + img_tasa_dpto_mes + '&img_tasa_muni_mes=' + img_tasa_muni_mes + '&img_tasa_morta_dpto_mes=' + img_tasa_morta_dpto_mes + '&img_tasa_morta_muni_mes=' + img_tasa_morta_muni_mes +
            '&img_letalidad=' + img_letalidad + '&img_muertes_covid_nocovid=' + img_muertes_covid_nocovid;
    llamarAjax("informes_ajax.php", params, "div_ppt", "");
}

function generar_reportes_municipal_ppt(array_datos_seleccion) {
    $('#div_ppt').html('<div id="cargando_informe_municipal" class="img_cargando"></div>');
    $('#cargando_informe_municipal').css('display', 'block');
    var val_fecha = $('#val_fecha').val();
    var array_seleccion = array_datos_seleccion.split(";");

    var params = 'opcion=6&val_fecha=' + val_fecha;

    for (var i = 0; i < array_seleccion.length; i++) {
        var array_aux = array_seleccion[i].split(",");
        
        var img_casos = $('#img_casos_' + array_aux[0]).val();
        var img_casos = base64Encode(img_casos);

        var img_fallecidos = $('#img_fallecidos_' + array_aux[0]).val();
        var img_fallecidos = base64Encode(img_fallecidos);

        var img_casos_gedad = $('#img_contagios_gedad_' + array_aux[0] + 'casos').val();
        var img_casos_gedad = base64Encode(img_casos_gedad);

        var img_muertes_gedad = $('#img_contagios_gedad_' + array_aux[0] + 'muertes').val();
        var img_muertes_gedad = base64Encode(img_muertes_gedad);

        var img_frecuencias = $('#img_frecuencias_' + array_aux[0] + 'frecuencias').val();
        var img_frecuencias = base64Encode(img_frecuencias);

        var img_def_covid_otros = $('#img_def_covid_otros_' + array_aux[0]).val();
        var img_def_covid_otros = base64Encode(img_def_covid_otros);

        //if (array_aux[2] > 0) {
            var img_casos_15 = $('#img_casos_' + array_aux[0] + 'menor_15').val();
            var img_casos_15 = base64Encode(img_casos_15);

            var img_casos_15_60 = $('#img_casos_' + array_aux[0] + 'de_15_60').val();
            var img_casos_15_60 = base64Encode(img_casos_15_60);

            var img_casos_60 = $('#img_casos_' + array_aux[0] + 'mayor_60').val();
            var img_casos_60 = base64Encode(img_casos_60);

            params += '&img_casos_15_' + array_aux[0] + '=' + img_casos_15 +
                    '&img_casos_15_60_' + array_aux[0] + '=' + img_casos_15_60 +
                    '&img_casos_60_' + array_aux[0] + '=' + img_casos_60;
        //}

        params += '&img_casos_' + array_aux[0] + '=' + img_casos + '&img_fallecidos_' + array_aux[0] + '=' + img_fallecidos +
                '&img_casos_gedad_' + array_aux[0] + '=' + img_casos_gedad + '&img_muertes_gedad_' + array_aux[0] + '=' + img_muertes_gedad +
                '&img_frecuencias_' + array_aux[0] + '=' + img_frecuencias + '&img_def_covid_otros_' + array_aux[0] + '=' + img_def_covid_otros +
                '&cod_mun_dane_' + i + '=' + array_aux[0] + '&nom_mun_' + i + '=' + array_aux[1] + '&ind_gedad_' + i + '=' + array_aux[2];
    }
    params += '&cant_municipios=' + array_seleccion.length;

    llamarAjax("informes_ajax.php", params, "div_ppt", "");
}

function getBinary(file) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", file, false);
    xhr.overrideMimeType("text/plain; charset=x-user-defined");
    xhr.send(null);
    return xhr.responseText;
}

function base64Encode(str) {
    var CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var out = "", i = 0, len = str.length, c1, c2, c3;
    while (i < len) {
        c1 = str.charCodeAt(i++) & 0xff;
        if (i == len) {
            out += CHARS.charAt(c1 >> 2);
            out += CHARS.charAt((c1 & 0x3) << 4);
            out += "==";
            break;
        }
        c2 = str.charCodeAt(i++);
        if (i == len) {
            out += CHARS.charAt(c1 >> 2);
            out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
            out += CHARS.charAt((c2 & 0xF) << 2);
            out += "=";
            break;
        }
        c3 = str.charCodeAt(i++);
        out += CHARS.charAt(c1 >> 2);
        out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
        out += CHARS.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
        out += CHARS.charAt(c3 & 0x3F);
    }
    return out;
}

function mostrarModalConfirmacion() {
    $('#modalConfirmacion').modal();
}

function mostrarVentana() {
    $('#myModal').modal();
}

function seleccionar_departamento(cod_dep, indice) {
    var params = 'opcion=7&cod_dep=' + cod_dep + "&indice=" + indice;
    
    llamarAjax("informes_ajax.php", params, "d_municipio_" + indice, "");
}

function abrir_configurar_mun() {
    var params = 'opcion=9';
    
    $('#div_mostrar_reportes').html("");
    mostrarModalReportes();
    $('#cargando_informe').css('display', 'block');
    llamarAjax("informes_ajax.php", params, "div_mostrar_reportes", "");
}

function agregar_municipio_esp() {
    var cant_municipios = parseInt($("#hdd_cant_municipios").val(), 10);
    
    if (cant_municipios < 100) {
        $("#tr_municipio_esp_" + cant_municipios).css("display", "table-row");
        $("#hdd_cant_municipios").val(cant_municipios + 1);
    }
}

function mover_arriba_mun_esp(indice) {
    var fila = $("#tr_municipio_esp_" + indice);
    fila.prev().insertAfter(fila);
}

function mover_abajo_mun_esp(indice) {
    var fila = $("#tr_municipio_esp_" + indice);
    fila.insertAfter(fila.next());
}

function borrar_municipio_esp(indice) {
    $("#tr_municipio_esp_" + indice).css("display", "none");
}

function seleccionar_departamento_esp(cod_dep, indice) {
    var params = 'opcion=10&cod_dep=' + cod_dep + "&indice=" + indice;
    
    llamarAjax("informes_ajax.php", params, "d_municipio_esp_" + indice, "");
}

function guardar_municipios_esp() {
    var params = 'opcion=11';
    
    var cont_mun_esp = 0;
    $("#tbl_municipios_esp").children("tbody").children().each(function(i) {
        if ($(this).is(":visible") && $(this).attr("value") !== "") {
            var indice = $(this).attr("value");
            if ($("#cmb_municipio_esp_" + indice).val() !== "") {
                params += "&cod_mun_dane_" + cont_mun_esp + "=" + $("#cmb_municipio_esp_" + indice).val();
                cont_mun_esp++;
            }
        }
    });
    params += "&cant_mun_esp=" + cont_mun_esp;
    
    llamarAjax("informes_ajax.php", params, "d_guardar_mun_esp", "");
}
