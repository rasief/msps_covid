// JavaScript Document

function nuevoAjax() {
    var xmlhttp = false;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }

    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

var contAjax = 0;
function llamarAjax(strURL, strPARAM, objDestino, funcion, accion, idProgressBar) {
    strPARAM += "&credencial=" + document.getElementById("credencial").value +
            "&hdd_numero_menu=" + document.getElementById("hdd_numero_menu").value;
    try {
        eval("var xmlHttpReq" + contAjax + " = false;");
        eval("var self = this;");
        eval("self.xmlHttpReq" + contAjax + " = nuevoAjax();");
        eval("self.xmlHttpReq" + contAjax + ".open('POST', '" + strURL + "', true);");
        eval("self.xmlHttpReq" + contAjax + ".setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');");
        eval("self.xmlHttpReq" + contAjax + ".onreadystatechange = function() { if (self.xmlHttpReq" + contAjax + ".readyState == 4) { updateObj(self.xmlHttpReq" + contAjax + ".responseText, '" + objDestino + "'); eval('" + funcion + "'); } else { updateObj(\"<img src='../imagenes/ajax-loader.gif\'>\", '" + objDestino + "', self.xmlHttpReq" + contAjax + ".readyState); }}");
        /*Funcion para imprimir el porcentaje de carga del ajax*/
        if (typeof accion !== 'undefined' && typeof idProgressBar !== 'undefined') {
            globalIdProgressBar = idProgressBar;
            eval("self.xmlHttpReq" + contAjax + ".addEventListener('progress'," + barraProgreso + ", false);");
        }
        /*END*/
        eval("self.xmlHttpReq" + contAjax + ".send('" + strPARAM + "');");
        contAjax++;
    } catch (err) {
        //alert(err.message);
    }
}

function updateObj(str, objDestino) {
    try {
        if ("#" + objDestino == "#") {
        } else {
            document.getElementById(objDestino).innerHTML = str;
        }
        var elements = document.getElementsByTagName('script');
        var code = '';
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].id == "ajax") {
                code += elements[i].innerHTML;
                elements[i].id = "ajax_E" + contAjax;
            }
        }
        eval(code);
    } catch (err) {
        //alert(err.message);
    }
}

/*Función que */
function _(el) {
    return document.getElementById(el);
}

var globalIdBarraProgreso = "";
function mostrarBarraProgreso(event) {
	var idBarraProgresoInt = globalIdBarraProgreso + "_int";
	var percent = (event.loaded / event.total) * 100;
	document.getElementById(idBarraProgresoInt).style.backgroundColor = "#0081b7";
	document.getElementById(idBarraProgresoInt).style.width = percent + "%";
	document.getElementById(idBarraProgresoInt).innerHTML = Math.round(percent) + "%";
	document.getElementById(globalIdBarraProgreso).style.display = "block";
}

/*
 * idInputFiles: cadena de ids (separados por ";") de los <input type=file> a enviar.
 * Para inputs vectoriales (name="nombre[]") se debe enviar sólo el id de UNO de los inputs del vector 
 * Los input múltiples y vectoriales deben tener name (esto para poder conservar en el dataForm la misma agrupación de los <input>) 
 */
function llamarAjaxUploadFiles(strURL, strPARAM, objDestino, funcion, idBarraProgreso, idInputFiles) {
	strPARAM += "&credencial=" + document.getElementById("credencial").value +
				"&hdd_numero_menu=" + document.getElementById("hdd_numero_menu").value;
	try {
		//Transforma el urlencoded recibido
		var urlParams = strPARAM.split("&");
		
		//Asigna los valores en el array formData
		var formdata = new FormData();
		for (var i = 0; i < urlParams.length; i++) {
			var arrayDos = urlParams[i].split("=");
			formdata.append(arrayDos[0], arrayDos[1]);
		}
		
		//Archivos adjuntos
		if (idInputFiles != "") {
			var inputFiles = idInputFiles.split(";");
			
			var countInput = 0;
			for (var e = 0; e < inputFiles.length; e++) {
				//Comprueba si el input está vacio
				if ($("#" + inputFiles[e]).attr("name")) {
					keyFormData = $("#" + inputFiles[e]).attr("name");
					selector = "[name='" + keyFormData + "']";
				} else {
					keyFormData = inputFiles[e];
					selector = "#" + inputFiles[e];
				}
				
				nInputsName = $(selector).length;
				for (k = 0; k < nInputsName; k++) {
					input = $(selector).get(k);
					arrFilesInput = input.files;
					
					nFilesInput = arrFilesInput.length;
					for (f = 0; f < nFilesInput; f++) {
						formdata.append(keyFormData, arrFilesInput[f]);
						countInput++;
					}
				}
			}
			formdata.append("countFiles", countInput);
		}
		
		var ajax = new XMLHttpRequest();
		ajax.open("POST", strURL, "true");
		
		//Evento que se ejecuta al finalizar el ajax
		ajax.onreadystatechange = function () {
			if (ajax.readyState == 4) {
				updateObj(ajax.responseText, objDestino);
				eval(funcion);
				if (typeof idBarraProgreso !== "undefined" && idBarraProgreso != "") {
					document.getElementById(idBarraProgreso).style.display = "none";
				}
			}
		};
		globalIdBarraProgreso = idBarraProgreso;
		ajax.upload.addEventListener("progress", mostrarBarraProgreso, false);
		
		ajax.send(formdata);
	} catch (err) {
		console.log("llamarAjaxUploadFiles->" + err.message);
	}
}

/*END*/