
function validar_contrasena() {
    $("#frmPass").validate({
        rules: {
            txtpass2: {
                equalTo: "#txtpass"
            }
        },
        submitHandler: function () {
            modalConfirmarGuardar("¿Realmente desea cambiar la contraseña?", "cambiarCobntrasena()");
        },
    });
}

function cambiarCobntrasena() {
    var txtpass = $('#txtpass').val();
    var txtpass2 = $('#txtpass2').val();
    var txtpassa = $('#txtPassword').val();
    var params = 'opcion=1&txtpass=' + txtpass + '&txtpass2=' + txtpass2 + '&txtpassa=' + txtpassa;

    //$("#contenedor_error").css("display", "none");
    //$("#contenedor_exito").css("display", "none");

    llamarAjax("pass_ajax.php", params, "contenedor_error", "", "");
}


function modalConfirmarGuardar(titulo, funcion) {
    var params = 'opcion=3&titulo=' + titulo + '&funcion=' + funcion;
    llamarAjax("pass_ajax.php", params, "ventanaModal", "mostrarModalConfirmacion();");
}

function mostrarModalConfirmacion() {
    $('#modalConfirmacion').modal();
}