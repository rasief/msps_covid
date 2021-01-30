
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


function validar_generar_informe(){
    $('#img_cargando').css('display', 'none');
    $('#msg_cargado').css('display', 'block');
    
    cargar_informes();
    
}


function generar_reportes(val_fecha){
    $('#div_mostrar_reportes').html('<div id="cargando_informe" class="img_cargando"></div>');    
    $('#cargando_informe').css('display', 'block');    
    var params = 'opcion=3&val_fecha=' + val_fecha;  
    mostrarModalReportes();    
    llamarAjax("informes_ajax.php", params, "div_mostrar_reportes", "finalizar_reporte()");
    //llamarAjax("informes_ajax.php", params, "div_reportes", "");
}

function generar_reportes_municipal(val_fecha){
    $('#div_mostrar_reportes').html('<div id="cargando_informe" class="img_cargando"></div>');
    $('#cargando_informe').css('display', 'block');
    var params = 'opcion=5&val_fecha=' + val_fecha;
    
    mostrarModalReportes();    
    $('#cargando_informe').css('display', 'block');    
    llamarAjax("informes_ajax.php", params, "div_mostrar_reportes", "finalizar_reporte()");
}


function finalizar_reporte(){
    $('#cargando_informe').css('display', 'none');    
}

function mostrarModalReportes(){
    $('#modalReportes').modal();
}

function generar_reportes_ppt(){


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
    
    
    
   
    var params = 'opcion=4&val_fecha='+ val_fecha + '&img_1=' + img + '&img_fallecidos=' + img_fallecidos + '&img_muertes_gedad=' + img_muertes_gedad + '&img_contagios_gedad=' + img_contagios_gedad +
                 '&img_tasa_dpto=' + img_tasa_dpto + '&img_tasa_muni=' + img_tasa_muni + '&img_tasa_morta_dpto=' + img_tasa_morta_dpto + '&img_tasa_morta_muni=' + img_tasa_morta_muni + 
                 '&img_tasa_dpto_mes=' + img_tasa_dpto_mes + '&img_tasa_muni_mes=' + img_tasa_muni_mes + '&img_tasa_morta_dpto_mes=' + img_tasa_morta_dpto_mes + '&img_tasa_morta_muni_mes=' + img_tasa_morta_muni_mes +
                 '&img_letalidad=' + img_letalidad + '&img_muertes_covid_nocovid=' + img_muertes_covid_nocovid;
    llamarAjax("informes_ajax.php", params, "div_ppt", "");
}


function generar_reportes_municipal_ppt(array_municipios, array_municipios_gedad){
    
    $('#div_ppt').html('<div id="cargando_informe_municipal" class="img_cargando"></div>');
    $('#cargando_informe_municipal').css('display', 'block');
    var val_fecha = $('#val_fecha').val();
    var array = array_municipios.split(",");    
    
    var array_gedad = array_municipios_gedad.split(",");    
    
    
    
    var params = 'opcion=6&val_fecha='+ val_fecha;
    
    for (i = 0; i < array.length; i++) {
        //alert(array[i]);
        var img_casos = $('#img_casos_' + array[i]).val();
        var img_casos = base64Encode(img_casos);
        
        var img_fallecidos = $('#img_fallecidos_' + array[i]).val();
        var img_fallecidos = base64Encode(img_fallecidos);        
        
        var img_casos_gedad = $('#img_contagios_gedad_' + array[i] + 'casos').val();
        var img_casos_gedad = base64Encode(img_casos_gedad);
        
        var img_muertes_gedad = $('#img_contagios_gedad_' + array[i] + 'muertes').val();
        var img_muertes_gedad = base64Encode(img_muertes_gedad);
        
        var img_frecuencias = $('#img_frecuencias_' + array[i] + 'frecuencias').val();
        var img_frecuencias = base64Encode(img_frecuencias);
        
        
        var img_def_covid_otros = $('#img_def_covid_otros_' + array[i]).val();
        var img_def_covid_otros = base64Encode(img_def_covid_otros);
        
        
        
        if(array_gedad.indexOf(array[i]) >=0 ){
            var img_casos_15 = $('#img_casos_' + array[i] + 'menor_15').val();
            var img_casos_15 = base64Encode(img_casos_15);
            
            var img_casos_15_60 = $('#img_casos_' + array[i] + 'de_15_60').val();
            var img_casos_15_60 = base64Encode(img_casos_15_60);
            
            var img_casos_60 = $('#img_casos_' + array[i] + 'mayor_60').val();
            var img_casos_60 = base64Encode(img_casos_60);
            
            params = params + '&img_casos_15_' + array[i] + '=' + img_casos_15 + 
                              '&img_casos_15_60_' + array[i] + '=' + img_casos_15_60 +
                              '&img_casos_60_' + array[i] + '=' + img_casos_60;
            
            
        }
        
        
        
        
        
        
        
        params = params + '&img_casos_' + array[i] + '=' + img_casos + '&img_fallecidos_' + array[i] + '=' + img_fallecidos +
                          '&img_casos_gedad_' + array[i] + '=' + img_casos_gedad + '&img_muertes_gedad_' + array[i] + '=' + img_muertes_gedad +
                          '&img_frecuencias_' + array[i] + '=' + img_frecuencias + '&img_def_covid_otros_' + array[i] + '=' + img_def_covid_otros;
    }

    llamarAjax("informes_ajax.php", params, "div_ppt", "");
    
    
}



function getBinary(file){
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
            out += CHARS.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
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


