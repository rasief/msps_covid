
$(document).ready(function () {
    if ($('#caja_flotante').length) {
        var posicion = $("#caja_flotante").offset();
        var margenSuperior = 15;
        $(window).scroll(function () {
            if ($(window).scrollTop() > posicion.top) {
                $("#caja_flotante").stop().animate({
                    marginTop: $(window).scrollTop() - posicion.top + margenSuperior
                });
            } else {
                $("#caja_flotante").stop().animate({
                    marginTop: 0
                });
            }
            ;
        });
    }
});

posicionar_encabezado_hc();
$(window).scroll(function () {
    posicionar_encabezado_hc();
});

function mensajeError(mensaje) {
    $('#contenedor_error').slideUp(200).removeClass("contenedor_error").addClass("contenedor_error_visible");
    $('#contenedor_error').html(mensaje);
    window.scroll(0, 0);
    setTimeout(
            function () {
                $('#contenedor_error').slideUp(200).removeClass("contenedor_error_visible").addClass("contenedor_error");
                $('#contenedor_error').html('');
            }, 10000);
}

function mensajeExitoso(mensaje) {
    $('#contenedor_exito').slideUp(200).removeClass("contenedor_exito").addClass("contenedor_exito_visible");
    $('#contenedor_exito').html(mensaje);
    window.scroll(0, 0);
    setTimeout(
            function () {
                $('#contenedor_exito').slideUp(200).removeClass("contenedor_exito_visible").addClass("contenedor_exito");
                $('#contenedor_exito').html('');
            }, 10000);
}

function posicionar_encabezado_hc() {
    var altura_del_header = $('.title-bar').outerHeight(true) + $('.topbar').outerHeight(true);
    var altura_del_div = $('#encabezado_hc_principal').outerHeight(true);

    if ($(window).scrollTop() >= altura_del_header) {

        var position_left = ($(window).width() - 1017) / 2;
        $('#encabezado_hc_principal').addClass('fixed_hc');
        $('#encabezado_hc_principal').css('left', (position_left) + 'px');
        $('#id_contenedor_principal').css('margin-top', (altura_del_div) + 'px');
    } else {
        $('#encabezado_hc_principal').removeClass('fixed_hc');
        $('#encabezado_hc_principal').css('left', '0');
        $('#id_contenedor_principal').css('margin-top', '0');
    }
}

function isObject(v) {
    return (v != null && typeof (v) == 'object');
}

function solo_numeros(event, decReq) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var isNum = (key > 47 && key < 58) ? true : false;
    var dotOK = (key == 46 && decReq && (obj.value.indexOf(".") < 0 || obj.value.length == 0)) ? true : false;
    if (key != 0 && key != 8) {
        if (isIE) {
            window.event.keyCode = (!isNum && !dotOK) ? 0 : key;
        } else if (!isNum && !dotOK) {
            event.preventDefault();
        }
    }
    return (isNum || dotOK || key == 8 || key == 0);
}

function validarNro(e) {
    var key;
    if (window.event) // IE
    {
        key = e.keyCode;
        if (key == 8) {
            e.keyCode = 9;
            return (e.keyCode);
        }
    }
    else if (e.which) // Netscape/Firefox/Opera
    {
        key = e.which;
        if (key == 8) {
            e.keyCode = 9;
            return (e.keyCode);
        }
    }
    if (key < 48 || key > 57)
    {
        return false;
    }
    return true;
}


function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function solo_alfanumericos(event) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var isAlfanum = ((key > 47 && key < 58) || (key > 64 && key < 91) || (key > 96 && key < 123)) ? true : false;
    if (key != 0 && key != 8) {
        if (isIE) {
            window.event.keyCode = (!isAlfanum) ? 0 : key;
        } else if (!isAlfanum) {
            event.preventDefault();
        }
    }
    return (isAlfanum || key == 8 || key == 0);
}


function soloLetras(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    especiales = "8-37-39-46";
    permitido = "45";

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if (key == 45) {
        return true;
    }
    else {
        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            return false;
        }
    }
}


function cant_caracteres(cant, id) {
    cadena_caracteres = $(id).val();
    if (cadena_caracteres.length < cant) {
        alert('Es campo debe tener ' + cant + ' Caracteres');
        $(id).val('');
    }

}


function trim(str) {
    return str.replace(/^\s*|\s*$/g, "");
}

function ir_al_inicio() {
    window.open('../index.php', '_parent');
}

function convertir_a_mayusculas(cajaTxt) {
    cajaTxt.value = cajaTxt.value.toUpperCase();
}

function convertir_a_minusculas(cajaTxt) {
    cajaTxt.value = cajaTxt.value.toLowerCase();
}

function convert_mayusc_inicial(caja_texto) {
    var texto_aux = caja_texto.value;
    var texto_rta = "";

    var ind_mayusc = true;
    for (var i = 0; i < texto_aux.length; i++) {
        var char_aux = texto_aux.charAt(i);
        if (ind_mayusc) {
            char_aux = char_aux.toUpperCase();
        } else {
            char_aux = char_aux.toLowerCase();
        }

        texto_rta += char_aux;

        if (char_aux == " " || char_aux == "." || char_aux == "(") {
            ind_mayusc = true;
        } else {
            ind_mayusc = false;
        }
    }

    caja_texto.value = texto_rta;
}

//quitar espacios de una cadena de caracteres 
function quitar_espacios(id_texto) {
    cadena_caracteres = $(id_texto).val();
    var array_cadena = cadena_caracteres.split(" ");
    i = array_cadena.length;
    while (i > 1) {
        cadena_caracteres = cadena_caracteres.replace(" ", "");
        array_cadena = cadena_caracteres.split(" ");
        i = array_cadena.length;
    }
    $(id_texto).val(cadena_caracteres);
}

var g_fecha_act = "";
function obtener_fecha_actual() {
    return g_fecha_act;
}

//Convertir una cadena a mayuscula
function convertirAMayusculas(cajaTxt) {
    $(cajaTxt).val($(cajaTxt).val().toUpperCase());
}

//Convertir una cadena a minuscula
function convertirAMinusculas(cajaTxt) {
    $(cajaTxt).val($(cajaTxt).val().toLowerCase());
}

/******Combinacion para hacer cambio de colores en las tablas******/
//Resaltar fila de una tabla
function resaltar_fila(fila) {
    fila.style.backgroundColor = "#E5E5E5";
}

//Opacar fila de una tabla
function opacar_fila(fila) {
    fila.style.backgroundColor = "#FFF";
}
/************************/

//unión de ambas funciones ltrim y rtrim
//function trim(id_texto, chars) {
function trim_cadena(id_texto) {
    var str = $(id_texto).val();
    var chars = '';
    var resul = ltrim(rtrim(str, chars), chars);
    $(id_texto).val(resul);
}

//ltrim quita los espacios o caracteres indicados al inicio de la cadena
function ltrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

//rtrim quita los espacios o caracteres indicados al final de la cadena 
function rtrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

//Funcion para cerrar el div del centro de ajax
function cerrar_div_centro() {
    $('#fondo_negro').css('display', 'none');
    $('#d_centro').slideDown(400).css('display', 'none');
}

//Funciones del Index
$(document).ready(function () {
    $('#principal_header_div2').hover(function () {
        //animacion icono de usuario
        $('.img2').animate({'background-position-y': '-32'}, 200, 'linear');

        //muestra el div escondido
        $('#principal_header_div3').slideDown(200).css('display', 'block')
    }, function () {
        //animacion icono de usuario
        $('.img2').animate({'background-position-y': '0'}, 200, 'linear');

        //muestra el div escondido
        $('#principal_header_div3').slideUp(200).css('display', 'none')
    })

    /*$('#cerrar_sesion').click(function() {
     confirmar()
     })*/
});
// End Funciones del index

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function enviar_credencial(action, id_menu) {
    $("#frm_credencial").attr("action", action);
    $("#hdd_numero_menu").val(id_menu);
    document.getElementById("frm_credencial").submit();
}

function confirmar() {
    //var r = confirm("\xbfDesea cerrar la sesion?")
    //if (r == true) {
    //cerrar_sesion_ajax();
    url = "../principal/cerrar_sesion.php";
    $(location).attr('href', url);
    //} else {

    //}
}

function cerrar_sesion_ajax() {
    $.ajax({
        url: "../principal/cerrar_sesion.php",
        type: "POST",
        data: {id: 1},
        dataType: "html"
    });
    url = "../index.php";
    $(location).attr('href', url);

}

function salir() {
    window.open('../index.php', '_parent');
}

/**
 * Función que procesa la lectura de códigos de barras de las cédulas
 * Tipos:
 * 1 - Solo número de cédula (Buscar paciente)
 * 2 - Datos completos (Admisiones)
 */
var bol_modo_lectura_cod = false;
var texto_lectura_cod = "";
var n1;
function leer_codigo_cedula(event, tipo) {
    var isIE = document.all ? true : false;
    var key = (isIE) ? window.event.keyCode : event.which;
    var obj = (isIE) ? window.event.srcElement : event.target;
    var ind_retorno = true;

    if (key == 58) {
        if (bol_modo_lectura_cod) {
            //Si se encuentra en modo lectura, se finaliza
            bol_modo_lectura_cod = false;
            var arr_datos = texto_lectura_cod.split("|");

            //Se asignan los valores leidos
            switch (tipo) {
                case 1: //Solo número de cédula (Buscar paciente)
                    var cedula_aux = trim(arr_datos[0]);
                    if (cedula_aux.length > 0) {
                        $("#txt_identificacion_interno").val(parseInt(cedula_aux, 10));
                        buscar_paciente_cont();
                    }
                    break;
                case 2: //Datos completos (Admisiones)
                    var cedula_aux = trim(arr_datos[0]);
                    if (cedula_aux.length > 0) {
                        $("#txt_id").val(parseInt(cedula_aux, 10));
                        $('#hdd_evento_pistola').val(arr_datos);
                        verificaEventoPistola();
                    }
                    break;
                default:
                    alert("Modo no soportado.");
                    break;
            }
            //alert(arr_datos[0] + "-" + texto_lectura_cod);
        } else {
            //Se inicia el modo lectura
            bol_modo_lectura_cod = true;
            texto_lectura_cod = "";
        }

        if (isIE) {
            window.event.keyCode = 0;
        } else {
            event.preventDefault();
        }
        ind_retorno = false;
    } else {
        if (bol_modo_lectura_cod) {
            //Se almacenan los caracteres
            if (key != 59) {
                texto_lectura_cod += String.fromCharCode(key);
            } else {
                texto_lectura_cod += "|";
            }

            if (key != 0 && key != 8) {
                if (isIE) {
                    window.event.keyCode = 0;
                } else {
                    event.preventDefault();
                }
            }
            ind_retorno = false;
        } else {
            //No está en modo lectura, se procesan los caracteres de forma normal
            ind_retorno = true;
        }
    }

    return ind_retorno;
}

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
    $('.div_centro').height(alto);
    $('.div_centro').css('top', '20%');
    $('.div_interno').width(ancho - 15);
    $('.div_interno').height(alto - 35);
    $('.div_interno').css('height', 'auto');
}

//Funcion que centra el div flotante respecto a la pantalla
function posicionarDivFlotante(id) {
    //Centrar el div flotante en la pantalla
    //$("#" + id).css({'top': Math.max(0, (($(window).height() - $("#" + id).outerHeight()) / 2) + $(window).scrollTop()) + "px"});
    $("#" + id).css({'top': Math.max(0, 100 + $(window).scrollTop()) + "px"});

}

//Funion que agrega las unidades de mil a un valor númerico
function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function str_encode(texto) {
    var texto = texto.replace(/\+/g, "|PLUS|");
    var texto = texto.replace(/&/g, "|AMP|");
    var texto = texto.replace(/"/g, "|DQUOT|");
    var texto = texto.replace(/'/g, "");
    var texto_rta = "";
    for (var i = 0; i < texto.length; i++) {
        if (texto.charCodeAt(i) != 10) {
            texto_rta += texto.charAt(i);
        } else {
            texto_rta += "|ENTER|";
        }
    }
    return texto_rta;
}

function ver_hc_panel() {
    $('#caja_flotante').width('380');
    $('#ocultar_hc_panel').css('display', 'block');
    $('#ver_hc_panel').css('display', 'none');
    $('#detalle_hc').slideDown(400).css('display', 'block');
}

function ocultar_hc_panel() {
    $('#caja_flotante').width('165');
    $('#ocultar_hc_panel').css('display', 'none');
    $('#ver_hc_panel').css('display', 'block');
    $('#detalle_hc').slideDown(400).css('display', 'none');
}

function mostrar_consultas_div(id_paciente, nombre_paciente, id_admision, pagina_consulta, id_hc, credencial, menu) {
    mostrar_formulario_flotante(1);
    $("#d_interno").empty();
    posicionarDivFlotante('d_centro');
    reducir_formulario_flotante('1050', '700');
    $('.div_centro').height(700);
    $('.div_interno').height(700 - 35);

    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_consulta + '?hdd_id_paciente=' + id_paciente +
            '&hdd_nombre_paciente=' + nombre_paciente +
            '&hdd_id_admision=' + id_admision +
            '&hdd_id_hc=' + id_hc +
            '&credencial=' + credencial +
            '&hdd_numero_menu=' + menu + '&tipo_entrada=1';
    HcFrame.setAttribute("src", ruta);
    HcFrame.style.height = '100%';
    HcFrame.style.width = '1050px';
    var control = document.getElementById("HcFrame")
    $("#d_interno").append(HcFrame);
}





function mostrar_consulta_iframe(id_paciente, nombre_paciente, id_admision, pagina_consulta, id_hc, credencial, menu, id_div) {
    var HcFrame = document.createElement("iframe");
    HcFrame.id = "HcFrame";
    ruta = pagina_consulta + '?hdd_id_paciente=' + id_paciente +
            '&hdd_nombre_paciente=' + nombre_paciente +
            '&hdd_id_admision=' + id_admision +
            '&hdd_id_hc=' + id_hc +
            '&credencial=' + credencial +
            '&hdd_numero_menu=' + menu + '&tipo_entrada=2';
    HcFrame.setAttribute("src", ruta);
    HcFrame.setAttribute("scrolling", 'no');
    HcFrame.style.height = '100%';
    HcFrame.style.width = '100%';
    HcFrame.style.border = '1px solid #CCC';
    HcFrame.style.margin = '0 px';

    var control = document.getElementById("HcFrame")
    $("#div_consulta_optometria").append(HcFrame);
}



function imprSelec(muestra) {
    var ficha = document.getElementById(muestra);
    var ventimp = window.open(' ', 'popimpr');
    ventimp.document.write(ficha.innerHTML);
    ventimp.document.close();
    ventimp.print();
    ventimp.close();
}

function imprimir_reg_hc(id_hc) {
    var params = "id_hc=" + id_hc;

    llamarAjax("../historia_clinica/impresion_historia_clinica.php", params, "d_impresion_hc", "imprSelec(\"d_impresion_hc\")");
}

function imprimir_div(id_div) {
    var div_obj = document.getElementById(id_div);
    var vent_imp = window.open(" ", "popimpr");
    vent_imp.document.write(div_obj.innerHTML);
    vent_imp.document.close();
    vent_imp.print();
    vent_imp.close();
}
