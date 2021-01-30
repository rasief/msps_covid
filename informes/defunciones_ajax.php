<?php
session_start();

require_once("../db/DbDefunciones.php");

$dbDefunciones = new DbDefunciones();

$opcion = $_POST['opcion'];

switch ($opcion) {
    case "1": //Carga y procesamiento del archivo adjunto - Citas por dia
        @$nombre_tmp = $_FILES["fil_arch"]["tmp_name"];
        @$nombre_ori = $_FILES["fil_arch"]["name"];
        
        //Se verifica que la extensión del archivo sea .csv
        $pos_aux = strrpos($nombre_ori, ".");
        $cantidad_registros = 0;
        $cant_datos = 0;
        $ind_resultado = 0;
        if ($cantidad_registros == 0) {

            if ($pos_aux !== false) {
                $extension_arch = strtolower(substr($nombre_ori, $pos_aux + 1));

                if ($extension_arch == "csv") {
                    //Se asigna nombre al archivo
                    $nombre_arch = "tmp/carga_defunciones.csv";

                    //Se copia el archivo
                    copy($nombre_tmp, $nombre_arch);

                    //Se establece la zona horaria
                    date_default_timezone_set("America/Bogota");

                    //Se abre el archivo csv
                    $source = fopen($nombre_arch, "r") or die("Problem open file");

                    $arr_columnas = array("departamento" => -1, "municipio" => -1, "dia" => -1, "cantidad" => -1);
                    
                    $cont_filas = 0;
                    $cant_datos = 0;
                    
                    $array_datos_insert = array();
                    $cont = 0;
                    $sql_insert = "";
                    $departamento_ant = "";
                    $municipio_ant = "";
                    while (($arr_datos_aux = fgetcsv($source, 100000, ";")) !== false) {
                        $arr_datos_aux = array_map("utf8_encode", $arr_datos_aux);
                        if ($ind_resultado == 0) {
                            if ($cont_filas == 0) {
                                //Encabezado
                                //Se valida que se tengan los datos necesarios

                                foreach ($arr_datos_aux as $llave_aux => $valor_aux) {
                                    $valor_aux = utf8_encode($valor_aux);

                                    //echo $valor_aux."<br />";

                                    if (isset($arr_columnas[$valor_aux])) {
                                        //echo $valor_aux." == ".$llave_aux.  "<br />";	
                                        $arr_columnas[$valor_aux] = $llave_aux;
                                    }
                                }
                                //var_dump($arr_columnas);

                                foreach ($arr_columnas as $col_aux) {
                                    //echo $col_aux."<br />";									
                                    if ($col_aux < 0) {
                                        $ind_resultado = -4;
                                        break;
                                    }
                                }
                            } else {

                                $departamento = $arr_datos_aux[$arr_columnas["departamento"]];
                                if ($departamento_ant == "") {
                                    $departamento_ant = $departamento;
                                } else if ($departamento == "") {
                                    $departamento = $departamento_ant;
                                } else {
                                    $departamento_ant = $departamento;
                                }


                                $municipio = $arr_datos_aux[$arr_columnas["municipio"]];
                                if ($municipio_ant == "") {
                                    $municipio_ant = $municipio;
                                } else if ($municipio == "") {
                                    $municipio = $municipio_ant;
                                } else {
                                    $municipio_ant = $municipio;
                                }

                                $dia = $arr_datos_aux[$arr_columnas["dia"]];
                                $cantidad = $arr_datos_aux[$arr_columnas["cantidad"]];

                                $departamento = explode("-", $departamento);
                                $municipio = explode("-", $municipio);


                                //echo $departamento[0]." -- ".$departamento[1]." -- ".$municipio[0]." -- ".$municipio[1]." -- ".$dia." -- ".$cantidad."<br />";

                                if ($cont_filas == 1) {
                                    $delete_datos_mes = $dbDefunciones->EliminarDatos();
                                }

                                $array_datos_insert[$cant_datos]['cod_departamento'] = trim($departamento[0]);
                                $array_datos_insert[$cant_datos]['nom_departamento'] = trim($departamento[1]);
                                $array_datos_insert[$cant_datos]['cod_municipio'] = trim($municipio[0]);
                                $array_datos_insert[$cant_datos]['nom_municipio'] = trim($municipio[1]);
                                $array_datos_insert[$cant_datos]['dia'] = $dia;
                                $array_datos_insert[$cant_datos]['cantidad'] = $cantidad;


                                //Insertar registros citas por dia
                                if ($cont > 0 && $cont % 500 == 0) {
                                    $insert_def = $dbDefunciones->guardarDatos($array_datos_insert);
                                    $array_datos_insert = array();

                                    if ($insert_def == 0) {
                                        $ind_resultado = -6;
                                        break;
                                    }
                                }

                                $cant_datos++;
                                $cont++;
                            }
                        } else {
                            break;
                        }

                        $cont_filas++;
                    }


                    if (count($array_datos_insert) > 0) {
                        $insert_def = $dbDefunciones->guardarDatos($array_datos_insert);

                        if ($insert_def == 0) {
                            $ind_resultado = -6;
                            break;
                        } else {
                            $ind_resultado = 1;
                        }
                    }

                    fclose($source);
                } else {
                    //La extensión del archivo no es csv
                    $ind_resultado = -3;
                }
            } else {
                //El archivo no tiene extensión
                $ind_resultado = -2;
            }
        } else {
            //El archivo ya tiene informacion del mes selccionado
            $ind_resultado = -1;
        }

        /* }	
          else {
          //El archivo no tiene extensión
          $ind_resultado = -7;
          } */



        //Se envía el resultado
        if ($ind_resultado == 1) {
            ?>

            <script id="ajax" type="text/javascript">
                window.parent.finalizar_cargar_datos(<?php echo($ind_resultado); ?>);
            </script>
            <?php
        } else {
            ?>
            <script id="ajax" type="text/javascript">
                //finalizar_cargar_datos(<?php echo($ind_resultado); ?>);
                window.parent.finalizar_cargar_datos(<?php echo($ind_resultado); ?>);
            </script>

            <?php
        }

        break;
}
?>
