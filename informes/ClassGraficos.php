<?php
require_once '../funciones/point/vendor/autoload.php';

/**
 * Header file
 */
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\Chart\Gridlines;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Bullet;
//use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Slide\Background\Color;
use PhpOffice\PhpPresentation\Style\Color as StyleColor;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Fill;

class ClassGraficos {

    public function obtener_nombre_mes($id_mes) {

        $array_mes = array();


        $array_mes[0][0] = 1;
        $array_mes[0][1] = "Enero";
        $array_mes[1][0] = 2;
        $array_mes[1][1] = "Febrero";
        $array_mes[2][0] = 3;
        $array_mes[2][1] = "Marzo";
        $array_mes[3][0] = 4;
        $array_mes[3][1] = "Abril";
        $array_mes[4][0] = 5;
        $array_mes[4][1] = "Mayo";
        $array_mes[5][0] = 6;
        $array_mes[5][1] = "Junio";
        $array_mes[6][0] = 7;
        $array_mes[6][1] = "Julio";
        $array_mes[7][0] = 8;
        $array_mes[7][1] = "Agosto";
        $array_mes[8][0] = 9;
        $array_mes[8][1] = "Spetiembre";
        $array_mes[9][0] = 10;
        $array_mes[9][1] = "Octubre";
        $array_mes[10][0] = 11;
        $array_mes[10][1] = "Noviembre";
        $array_mes[11][0] = 12;
        $array_mes[11][1] = "Diciembre";
        $nombre_mes = "";
        foreach ($array_mes as $fila) {
            $num = $fila[0];
            $mes = $fila[1];
            if ($num == $id_mes) {
                $nombre_mes = $mes;
                break;
            }
        }

        return $nombre_mes;
    }

    public function obtener_array_municipal($tabla_datos_generales, $cod_mun) {
        $array_muni = array();
        $i = 0;
        foreach ($tabla_datos_generales as $fila) {

            $tipo_atencion = $fila['tipo_atencion'];
            $codigo_divipola = $fila['codigo_divipola'];
            $cantidad = $fila['cantidad'];

            if ($cod_mun == $codigo_divipola) {
                $array_muni[$i]['tipo_atencion'] = $tipo_atencion;
                $array_muni[$i]['cantidad'] = $cantidad;
                $i = $i + 1;
            }
        }

        return $array_muni;
    }

    public function grafica_casos_generales($tabla_informes, $nombre_municipio, $cod_muni, $titulo, $clase) {

        $array_asintomaticos = array();
        $array_sintomaticos = array();
        $array_sintomas = array();
        foreach ($tabla_informes as $fila) {
            $tipo = $fila['tipo_estado'];
            $semana = $fila['fecha_notificacion'];
            $cantidad = $fila['cantidad'];
            if ($tipo == 'ASINTOMATICOS') {
                $array_sintomas[$semana]['asinto'] = $cantidad;
            } else if ($tipo == 'SINTOMATICOS') {
                $array_sintomas[$semana]['sinto'] = $cantidad;
            }
        }
        $datos_grafica = "['TIPO', 'SINTOMATICOS', 'ASINTOMATICOS', { role: 'annotation' }], ";
        $tot = count($array_sintomas);
        $num = 1;
        $h = 0;
        foreach ($array_sintomas as $key => $fila_sintomas) {
            @$asinto = @$fila_sintomas['asinto'];
            if ($asinto == '') {
                $asinto = 0;
            }
            $sinto = $fila_sintomas['sinto'];
            if ($sinto == '') {
                $sinto = 0;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_grafica = $datos_grafica . "['" . $key . "', " . $sinto . ", " . $asinto . ", ''] ";
            } else {
                $datos_grafica = $datos_grafica . "['" . $key . "', " . $sinto . ", " . $asinto . ", ''], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_casos_<?php echo($cod_muni . $clase); ?>" style="width: 100%; height: 500px; "></div>
        <input id="img_casos_<?php echo($cod_muni . $clase); ?>" type="hidden">
        <script id="ajax" type="text/javascript">
            google.charts.setOnLoadCallback(drawChart_<?php echo($cod_muni . $clase); ?>);
            var dataImg = "";

            function drawChart_<?php echo($cod_muni . $clase); ?>() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_grafica); ?>
                ]);

                var options = {
                    title: "<?php echo($titulo); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '75%'},
                    isStacked: true,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_casos_<?php echo($cod_muni . $clase); ?>"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_casos_<?php echo($cod_muni . $clase); ?>').val(dataImg);
            }

        </script>	
        <?php
    }

    public function graficas_muertes_generales($tabla_muertes, $nombre_municipio, $cod_muni, $titulo) {


        $datos_grafica_muertes = "['Fecha', 'FALLECIDOS', 'FALLECIDOS ACUMULADOS'], ";
        $tot = count($tabla_muertes);
        $num = 1;
        $h = 0;
        $acumulado_muertes = 0;
        foreach ($tabla_muertes as $fila) {
            $fecha_muerte = $fila['fecha_muerte'];
            $cantidad = $fila['cantidad'];
            $acumulado_muertes = $acumulado_muertes + $cantidad;
            if ($num == $tot) {//Ultimo valor
                $datos_grafica_muertes = $datos_grafica_muertes . "['" . $fecha_muerte . "', " . $cantidad . ", " . $acumulado_muertes . "] ";
            } else {
                $datos_grafica_muertes = $datos_grafica_muertes . "['" . $fecha_muerte . "', " . $cantidad . ", " . $acumulado_muertes . "], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_fallecidos_<?php echo($cod_muni); ?>" style="width: 100%; height: 500px"></div>
        <input id="img_fallecidos_<?php echo($cod_muni); ?>" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartFallecidos_<?php echo($cod_muni); ?>);
            var dataImg = "";
            function ChartFallecidos_<?php echo($cod_muni); ?>() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_grafica_muertes); ?>
                ]);

                var options = {
                    title: "<?php echo($titulo); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    seriesType: 'bars',
                    series: [{targetAxisIndex: 0}, {targetAxisIndex: 1, type: 'line'}]
                };

                var chart = new google.visualization.ComboChart(document.getElementById("chart_fallecidos_<?php echo($cod_muni); ?>"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_fallecidos_<?php echo($cod_muni); ?>').val(dataImg);
            }
        </script>	
        <?php
    }

    public function graficas_casos_gedad_sexo($tabla_contagios_gedad, $nombre_municipio, $cod_muni, $titulo, $tipo) {

        $array_contagios_gedad = array();
        foreach ($tabla_contagios_gedad as $fila) {
            $nombre_sexo = $fila['nombre_sexo'];
            $grupo_edad = $fila['grupo_edad'];
            $cantidad = $fila['cantidad'];
            if ($nombre_sexo == 'Hombres') {
                $array_contagios_gedad[$grupo_edad]['hombres'] = $cantidad;
            } else if ($nombre_sexo == 'Mujeres') {
                $array_contagios_gedad[$grupo_edad]['mujeres'] = $cantidad;
            }
        }

        $datos_contagios_gedad = "['SEXO', 'MUJERES', 'HOMBRES', { role: 'annotation' }], ";
        $tot = count($array_contagios_gedad);
        $num = 1;
        $h = 0;
        foreach ($array_contagios_gedad as $key => $fila_contagios_gedad) {

            @$mujeres = @$fila_contagios_gedad['mujeres'];
            if ($mujeres == '') {
                $mujeres = 0;
            }
            $hombres = $fila_contagios_gedad['hombres'];
            if ($hombres == '') {
                $hombres = 0;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_contagios_gedad = $datos_contagios_gedad . "['" . $key . "', " . $mujeres . ", " . $hombres . ", ''] ";
            } else {
                $datos_contagios_gedad = $datos_contagios_gedad . "['" . $key . "', " . $mujeres . ", " . $hombres . ", ''], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_contagios_gedad_<?php echo($cod_muni . $tipo); ?>" style="width: 50%; height: 400px"></div>
        <input id="img_contagios_gedad_<?php echo($cod_muni . $tipo); ?>" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartContagiosGedad_<?php echo($cod_muni . $tipo); ?>);
            var dataImg = "";
            function ChartContagiosGedad_<?php echo($cod_muni . $tipo); ?>() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_contagios_gedad); ?>
                ]);

                var options = {
                    title: "<?php echo($titulo); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '40%'},
                    isStacked: true,
                };
                var chart = new google.visualization.BarChart(document.getElementById("chart_contagios_gedad_<?php echo($cod_muni . $tipo); ?>"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_contagios_gedad_<?php echo($cod_muni . $tipo); ?>').val(dataImg);
            }
        </script>	
        <?php
    }

    public function graficas_frecuencias($tabla_frecuencias, $nombre_municipio, $cod_muni, $titulo, $tipo) {

        $array_frecuencias = array();
        foreach ($tabla_frecuencias as $fila) {
            $mes_noti = $fila['mes_noti'];
            $tipo_atencion = $fila['tipo_atencion'];
            $cantidad = $fila['cantidad'];

            if ($tipo_atencion == 'Fallecidos') {
                @$array_frecuencias[$mes_noti]['fallecido'] = @$array_frecuencias[$mes_noti]['fallecido'] + $cantidad;
                @$array_frecuencias[$mes_noti]['total'] = @$array_frecuencias[$mes_noti]['total'] + $cantidad;
            } else {
                @$array_frecuencias[$mes_noti]['total'] = @$array_frecuencias[$mes_noti]['total'] + $cantidad;
            }
        }

        $datos_frecuencia = "['MES', 'CASOS', { role: 'annotation' }, 'MUERTES', { role: 'annotation'}], ";
        $tot = count($array_frecuencias);
        $num = 1;
        $h = 0;
        foreach ($array_frecuencias as $key => $fila_frecuencia) {

            @$fallecido = @$fila_frecuencia['fallecido'];
            if ($fallecido == "") {
                $fallecido = 0;
            }
            $total = $fila_frecuencia['total'];
            if ($total == "") {
                $total = 0;
            }


            $nombre_mes = $this->obtener_nombre_mes($key);

            switch (strlen("" . $total)) {
                case 1:
                    $texto_total = "" . $total . "  ";
                    break;
                case 2:
                    $texto_total = "" . $total . "    ";
                    break;
                case 3:
                    $texto_total = "" . $total . "      ";
                    break;
                case 4:
                    $texto_total = "" . $total . "        ";
                    break;
                case 5:
                    $texto_total = "" . $total . "          ";
                    break;
                case 6:
                    $texto_total = "" . $total . "            ";
                    break;
                case 7:
                    $texto_total = "" . $total . "              ";
                    break;
                default:
                    $texto_total = "" . $total . "                ";
                    break;
            }

            switch (strlen("" . $total)) {
                case 1:
                    $texto_fallecido = "  " . $fallecido;
                    break;
                case 2:
                    $texto_fallecido = "    " . $fallecido;
                    break;
                case 3:
                    $texto_fallecido = "      " . $fallecido;
                    break;
                case 4:
                    $texto_fallecido = "        " . $fallecido;
                    break;
                case 5:
                    $texto_fallecido = "          " . $fallecido;
                    break;
                case 6:
                    $texto_fallecido = "            " . $fallecido;
                    break;
                case 7:
                    $texto_fallecido = "              " . $fallecido;
                    break;
                default:
                    $texto_fallecido = "                " . $fallecido;
                    break;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_frecuencia = $datos_frecuencia . "['" . $nombre_mes . "', " . $total . ", '" . $texto_total . "', " . $fallecido . ", '" . $texto_fallecido . "'] ";
            } else {
                $datos_frecuencia = $datos_frecuencia . "['" . $nombre_mes . "', " . $total . ", '" . $texto_total . "', " . $fallecido . ", '" . $texto_fallecido . "'], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_frecuencias_<?php echo($cod_muni . $tipo); ?>" style="width: 50%; height: 400px"></div>
        <input id="img_frecuencias_<?php echo($cod_muni . $tipo); ?>" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartFrecuencias_<?php echo($cod_muni . $tipo); ?>);
            var dataImg = "";
            function ChartFrecuencias_<?php echo($cod_muni . $tipo); ?>() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_frecuencia); ?>
                ]);

                var options = {
                    title: "<?php echo($titulo); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '40%'},

                    //seriesType: 'bars',
                    //series: {1: {type: 'line'}}

                    legend: {position: 'bottom', maxLines: 3},
                    seriesType: 'bars',
                    series: [{targetAxisIndex: 0}, {targetAxisIndex: 1, type: 'line'}]



                };
                var chart = new google.visualization.ComboChart(document.getElementById("chart_frecuencias_<?php echo($cod_muni . $tipo); ?>"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_frecuencias_<?php echo($cod_muni . $tipo); ?>').val(dataImg);
            }
        </script>	
        <?php
    }

    public function obtener_dato_defunciones_covid($fecha, $base) {
        //fecha_muerte, SUM(cantidad) AS cantidad
        $val_defunciones = 0;
        foreach ($base as $fila) {
            $fecha_muerte = $fila['fecha_muerte'];
            $cantidad = $fila['cantidad'];

            if ($fecha == $fecha_muerte) {
                $val_defunciones = $cantidad;
                break;
            }
        }

        return $val_defunciones;
    }

    public function obtener_dato_defunciones_otras($fecha, $base) {
        //fecha_def, sum(d.cantidad) cant_defunciones 
        $val_defunciones = 0;
        foreach ($base as $fila) {
            $fecha_def = $fila['fecha_def'];
            $cant_defunciones = $fila['cant_defunciones'];

            if ($fecha == $fecha_def) {
                $val_defunciones = $cant_defunciones;
                break;
            }
        }

        return $val_defunciones;
    }

    public function graficas_casos_def_covid_otros($tabla_fechas, $tabla_muertes_covid, $tabla_muertes_otras, $cod_grafico, $titulo) {

        $array_def_covid_otros = array();
        foreach ($tabla_fechas as $fila) {
            $fecha = $fila['fecha'];
            $defunciones_covid = $this->obtener_dato_defunciones_covid($fecha, $tabla_muertes_covid);
            $defunciones_otras = $this->obtener_dato_defunciones_otras($fecha, $tabla_muertes_otras);

            $defunciones_no_covid = $defunciones_otras - $defunciones_covid;
            if ($defunciones_no_covid < 0) {
                $defunciones_no_covid = 0;
            }

            $array_def_covid_otros[$fecha]['covid'] = $defunciones_covid;
            $array_def_covid_otros[$fecha]['no_covid'] = $defunciones_no_covid;
        }


        $datos_def_covid_otros = "['MUERTES', 'Muertes Covid', 'Muertes Naturales', { role: 'annotation' }], ";
        $tot = count($array_def_covid_otros);
        $num = 1;
        $h = 0;
        foreach ($array_def_covid_otros as $key => $fila_def) {

            @$covid = @$fila_def['covid'];
            if ($covid == '') {
                $covid = 0;
            }
            @$no_covid = @$fila_def['no_covid'];
            if ($no_covid == '') {
                $no_covid = 0;
            }

            if ($num == $tot) {//Ultimo valor
                $datos_def_covid_otros = $datos_def_covid_otros . "['" . $key . "', " . $covid . ", " . $no_covid . ", ''] ";
            } else {
                $datos_def_covid_otros = $datos_def_covid_otros . "['" . $key . "', " . $covid . ", " . $no_covid . ", ''], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_def_covid_otros_<?php echo($cod_grafico); ?>" style="width: 80%; height: 400px"></div>
        <input id="img_def_covid_otros_<?php echo($cod_grafico); ?>" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartDefCovidOtros_<?php echo($cod_grafico); ?>);
            var dataImg = "";
            function ChartDefCovidOtros_<?php echo($cod_grafico); ?>() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_def_covid_otros); ?>
                ]);

                var options = {
                    title: "<?php echo($titulo); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'bottom', maxLines: 3},
                    bar: {groupWidth: '60%'},
                    isStacked: true,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("chart_def_covid_otros_<?php echo($cod_grafico); ?>"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_def_covid_otros_<?php echo($cod_grafico); ?>').val(dataImg);
            }
        </script>	
        <?php
    }

    public function ppt_casos_generales($tipo, $objPHPPresentation, $val_fecha, $img_tbl_1, $img_tbl_2, $img_tbl_3, $tabla_datos_generales, $arr_valores) {
        $objPHPPresentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_CUSTOM, true)
                ->setCX(1600, DocumentLayout::UNIT_PIXEL)
                ->setCY(900, DocumentLayout::UNIT_PIXEL);
        
        //Se obtiene la etiqueta de años
        $ano_graf = substr($val_fecha, 0, 4);
        if ($ano_graf > "2020") {
            $ano_graf = "2020-" . $ano_graf;
        }
        
        if ($tipo == 1) {//Inicia ppt
            $currentSlide = $objPHPPresentation->getActiveSlide();
        } else if ($tipo == 2) { //add ppt
            $currentSlide = $objPHPPresentation->createSlide();
        }

        $oBkgColor = new Color();
        $oBkgColor->setColor(new StyleColor("c6d9f1"));
        $currentSlide->setBackground($oBkgColor);

        //Texto Tabla Contenido
        $shape = $currentSlide->createTableShape(2);
        $shape->setHeight(200);
        $shape->setWidth(260);
        $shape->setOffsetX(10);
        $shape->setOffsetY(100);
        
        $poblacion = 0;
        $cant_casos = 0;
        if (isset($arr_valores["poblacion"])) {
            $poblacion = $arr_valores["poblacion"];
            $cant_casos = $arr_valores["casos"];
        }
        
        foreach ($tabla_datos_generales as $value) {
            $tipo_atencion = $value['tipo_atencion'];
            $cantidad = $value['cantidad'];
            
            switch ($tipo_atencion) {
                case "Tasa de Contagio * 100.000":
                    if ($poblacion > 0) {
                        $cantidad = ($cant_casos / $poblacion) * 100000;
                    }
                    break;
            }
            if ($cantidad != "" && $cantidad != "-") {
                if ($tipo_atencion == "Tasa de Contagio * 100.000") {
                    $cantidad = number_format($cantidad, 2, ",", ".");
                } else {
                    $cantidad = number_format($cantidad, 0, ",", ".");
                }
            }

            $row = $shape->createRow();
            $row->setHeight(20);
            $oCell = $row->nextCell();
            $oCell->setWidth(150);
            $oCell->createTextRun($tipo_atencion)->getFont()->setBold(true)->setSize(16);
            $oCell = $row->nextCell();
            $oCell->setWidth(110);
            $oCell->createTextRun($cantidad)->getFont()->setSize(16);
            $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);
        }

        //Fuente
        $shape = $currentSlide
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(800);
        $textRun = $shape->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun->getFont()->setSize(14)->setBold(true);


        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_1)
                ->setResizeProportional(false)
                ->setHeight(500)
                ->setWidth(1300)
                ->setOffsetX(280)
                ->setOffsetY(5);
        $currentSlide->addShape($shape);

        if ($img_tbl_3 != "NULL") {
            $shape = new Drawing\Base64();
            $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                    ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                    ->setData($img_tbl_3)
                    ->setResizeProportional(false)
                    ->setHeight(390)
                    ->setWidth(550)
                    ->setOffsetX(350)
                    ->setOffsetY(508);
            $currentSlide->addShape($shape);
        }

        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_2)
                ->setResizeProportional(false)
                ->setHeight(390)
                ->setWidth(500)
                ->setOffsetX(930)
                ->setOffsetY(508);
        $currentSlide->addShape($shape);

        //******** Fin Hoja 1 **********
    }

    public function ppt_casos_generales_def($tipo, $objPHPPresentation, $val_fecha, $img_tbl_1, $img_tbl_2, $img_tbl_3, $tabla_datos_generales, $arr_valores) {
        $objPHPPresentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_CUSTOM, true)
                ->setCX(1600, DocumentLayout::UNIT_PIXEL)
                ->setCY(900, DocumentLayout::UNIT_PIXEL);
        
        //Se obtiene la etiqueta de años
        $ano_graf = substr($val_fecha, 0, 4);
        if ($ano_graf > "2020") {
            $ano_graf = "2020-" . $ano_graf;
        }
        
        if ($tipo == 1) {//Inicia ppt
            $currentSlide = $objPHPPresentation->getActiveSlide();
        } else if ($tipo == 2) { //add ppt
            $currentSlide = $objPHPPresentation->createSlide();
        }


        $oBkgColor = new Color();
        $oBkgColor->setColor(new StyleColor("c6d9f1"));
        $currentSlide->setBackground($oBkgColor);

        //Texto Tabla Contenido
        $shape = $currentSlide->createTableShape(2);
        $shape->setHeight(200);
        $shape->setWidth(260);
        $shape->setOffsetX(10);
        $shape->setOffsetY(100);

        $poblacion = 0;
        $cant_casos = 0;
        $cant_fallecidos = 0;
        $cant_casos_0_59 = 0;
        $cant_fallecidos_0_59 = 0;
        $cant_casos_60 = 0;
        $cant_fallecidos_60 = 0;
        if (isset($arr_valores["poblacion"])) {
            $poblacion = $arr_valores["poblacion"];
            $cant_casos = $arr_valores["casos"];
            $cant_fallecidos = $arr_valores["fallecidos"];
            $cant_casos_0_59 = $arr_valores["casos_0_59"];
            $cant_fallecidos_0_59 = $arr_valores["fallecidos_0_59"];
            $cant_casos_60 = $arr_valores["casos_60"];
            $cant_fallecidos_60 = $arr_valores["fallecidos_60"];
        }
        
        foreach ($tabla_datos_generales as $cont_aux => $value) {
            $tipo_atencion = $value['tipo_atencion'];
            $cantidad = $value['cantidad'];
            
            switch ($cont_aux) {
                case 0: //Fallecidos
                    $cantidad = $cant_fallecidos;
                    break;
                    
                case 1: //Tasa de Mortalidad * 100.000
                    if ($poblacion > 0) {
                        $cantidad = ($cant_fallecidos / $poblacion) * 100000;
                    }
                    break;
                    
                case 2: //Letalidad
                    if ($cant_casos > 0) {
                        $cantidad = ($cant_fallecidos / $cant_casos) * 100;
                    }
                    break;
                    
                case 3: //Letalidad menores de 60 años
                    if ($cant_casos_0_59 > 0) {
                        $cantidad = ($cant_fallecidos_0_59 / $cant_casos_0_59) * 100;
                    }
                    break;
                    
                case 4: //Letalidad 60 años y más
                    if ($cant_casos_60 > 0) {
                        $cantidad = ($cant_fallecidos_60 / $cant_casos_60) * 100;
                    }
                    break;
            }
            
            if ($cantidad != "" && $cantidad != "-") {
                if ($tipo_atencion == "Fallecidos") {
                    $cantidad = number_format($cantidad, 0, ",", ".");
                } else {
                    $cantidad = number_format($cantidad, 2, ",", ".");
                }
            }
            
            $row = $shape->createRow();
            $row->setHeight(20);
            $oCell = $row->nextCell();
            $oCell->setWidth(150);
            $oCell->createTextRun($tipo_atencion)->getFont()->setBold(true)->setSize(16);
            $oCell = $row->nextCell();
            $oCell->setWidth(110);
            $oCell->createTextRun($cantidad)->getFont()->setSize(16);
            $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);
        }

        //Fuente
        $shape = $currentSlide
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(800)
                ->setOffsetY(850);
        $textRun = $shape->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun->getFont()->setSize(14)->setBold(true);


        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_1)
                ->setResizeProportional(false)
                ->setHeight(500)
                ->setWidth(1300)
                ->setOffsetX(280)
                ->setOffsetY(5);
        $currentSlide->addShape($shape);

        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_3)
                ->setResizeProportional(false)
                ->setHeight(390)
                ->setWidth(900)
                ->setOffsetX(10)
                ->setOffsetY(508);
        $currentSlide->addShape($shape);



        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_2)
                ->setResizeProportional(false)
                ->setHeight(390)
                ->setWidth(500)
                ->setOffsetX(930)
                ->setOffsetY(508);
        $currentSlide->addShape($shape);

        //******** Fin Hoja 1 **********
    }

    //$tabla_datos_generales
    public function ppt_casos_generales_gedad($objPHPPresentation, $val_fecha, $img_tbl_1, $img_tbl_2, $img_tbl_3) {
        $objPHPPresentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_CUSTOM, true)
                ->setCX(1600, DocumentLayout::UNIT_PIXEL)
                ->setCY(900, DocumentLayout::UNIT_PIXEL);

        $currentSlide = $objPHPPresentation->createSlide();
        
        //Se obtiene la etiqueta de años
        $ano_graf = substr($val_fecha, 0, 4);
        if ($ano_graf > "2020") {
            $ano_graf = "2020-" . $ano_graf;
        }
        
        $oBkgColor = new Color();
        $oBkgColor->setColor(new StyleColor("c6d9f1"));
        $currentSlide->setBackground($oBkgColor);

        //Fuente
        $shape = $currentSlide
                ->createRichTextShape()
                ->setHeight(40)
                ->setWidth(700)
                ->setOffsetX(50)
                ->setOffsetY(800);
        $textRun = $shape->createTextRun('Fuente: MSPS – INS, Corte: ' . $val_fecha);
        $textRun->getFont()->setSize(14)->setBold(true);

        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_1)
                ->setResizeProportional(false)
                ->setHeight(450)
                ->setWidth(800)
                ->setOffsetX(10)
                ->setOffsetY(5);
        $currentSlide->addShape($shape);

        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_2)
                ->setResizeProportional(false)
                ->setHeight(450)
                ->setWidth(800)
                ->setOffsetX(800)
                ->setOffsetY(5);
        $currentSlide->addShape($shape);

        $shape = new Drawing\Base64();
        $shape->setName('Casos COVID-19, Colombia ' . $ano_graf)
                ->setDescription('Casos COVID-19, Colombia ' . $ano_graf)
                ->setData($img_tbl_3)
                ->setResizeProportional(false)
                ->setHeight(450)
                ->setWidth(800)
                ->setOffsetX(300)
                ->setOffsetY(450);
        $currentSlide->addShape($shape);

        //******** Fin Hoja 1 **********
    }

    public function graficas_tasas_covid($tabla_tasa, $titulo, $campo, $tipo, $clase) {

        $datos_tasa = "['" . $tipo . "', 'TASA', { role: 'annotation' }], ";
        $tot = count($tabla_tasa);
        $num = 1;
        $h = 0;
        $acumulado_tasa = 0;
        foreach ($tabla_tasa as $fila) {
            $nom_departamento = $fila[$campo];
            $tasa = $fila['tasa'];
            if ($num == $tot) {//Ultimo valor
                $datos_tasa = $datos_tasa . "['" . $nom_departamento . "', " . $tasa . ", " . $tasa . "] ";
            } else {
                $datos_tasa = $datos_tasa . "['" . $nom_departamento . "', " . $tasa . ", " . $tasa . "], ";
            }

            $num = $num + 1;
            $h++;
        }
        ?>
        <div id="chart_tasa_<?php echo($clase); ?>" style="width: 50%; height: 800px"></div>
        <input id="img_tasa_<?php echo($clase); ?>" type="hidden">
        <script id="ajax" type="text/javascript">

            google.charts.setOnLoadCallback(ChartTasa_<?php echo($clase); ?>);
            var dataImg = "";
            function ChartTasa_<?php echo($clase); ?>() {
                var data = google.visualization.arrayToDataTable([
        <?php echo($datos_tasa); ?>
                ]);

                var options = {
                    title: "<?php echo($titulo); ?>",
                    titleTextStyle: {
                        bold: true,
                        fontSize: 22,
                    },
                    backgroundColor: '#D3E1F4',
                    legend: {position: 'none', maxLines: 3},
                    bar: {groupWidth: '45%'},
                    isStacked: true,
                };
                var chart = new google.visualization.BarChart(document.getElementById("chart_tasa_<?php echo($clase); ?>"));
                chart.draw(data, options);

                dataImg = chart.getImageURI();
                dataImg = dataImg.replace("png", "jpeg")
                $('#img_tasa_<?php echo($clase); ?>').val(dataImg);
            }
        </script>	
        <?php
    }

    public function grafico_letalidad_colombia($tabla_informes, $tipo, $val_fecha) {
        //Se obtiene la etiqueta de años
        $ano_graf = substr($val_fecha, 0, 4);
        if ($ano_graf > "2020") {
            $ano_graf = "2020-" . $ano_graf;
        }
        
        $array_sintomas = array();
        foreach ($tabla_informes as $fila) {
            $tipo_estado = $fila['tipo_estado'];
            $estado_paciente = $fila['estado_paciente'];
            $fecha_notificacion = $fila['fecha_notificacion'];

            $cantidad = $fila['cantidad'];
            if ($tipo_estado == 'SINTOMATICOS') {

                if ($estado_paciente == 'Fallecido') {
                    //Sintomaticos
                    @$array_sintomas[$fecha_notificacion]['sinto']['fallecidos'] = @$array_sintomas[$fecha_notificacion]['sinto']['fallecidos'] + $cantidad;
                    @$array_sintomas[$fecha_notificacion]['sinto']['casos'] = @$array_sintomas[$fecha_notificacion]['sinto']['casos'] + $cantidad;

                    //Todos
                    @$array_sintomas[$fecha_notificacion]['todos']['fallecidos'] = @$array_sintomas[$fecha_notificacion]['todos']['fallecidos'] + $cantidad;
                    @$array_sintomas[$fecha_notificacion]['todos']['casos'] = @$array_sintomas[$fecha_notificacion]['todos']['casos'] + $cantidad;
                } else {
                    //Sintomaticos
                    @$array_sintomas[$fecha_notificacion]['sinto']['casos'] = @$array_sintomas[$fecha_notificacion]['sinto']['casos'] + $cantidad;
                    //Todos
                    @$array_sintomas[$fecha_notificacion]['todos']['casos'] = @$array_sintomas[$fecha_notificacion]['todos']['casos'] + $cantidad;
                }
            } else {

                if ($estado_paciente == 'Fallecido') {
                    //Todos
                    @$array_sintomas[$fecha_notificacion]['todos']['fallecidos'] = @$array_sintomas[$fecha_notificacion]['todos']['fallecidos'] + $cantidad;
                    @$array_sintomas[$fecha_notificacion]['todos']['casos'] = @$array_sintomas[$fecha_notificacion]['todos']['casos'] + $cantidad;
                } else {
                    //Todos
                    @$array_sintomas[$fecha_notificacion]['todos']['casos'] = @$array_sintomas[$fecha_notificacion]['todos']['casos'] + $cantidad;
                }
            }
        }
        $datos_grafica = "['Fecha', 'Sintomaticos', 'Sintomaticos + Asintomaticos'], ";
        $tot = count($array_sintomas);
        $num = 1;
        $h = 0;

        $acumulado_todos_fallecidos = 0;
        $acumulado_todos_casos = 0;

        $acumulado_sinto_fallecidos = 0;
        $acumulado_sinto_casos = 0;
        foreach ($array_sintomas as $key => $fila_sintomas) {

            //print_r($fila_sintomas['asinto']);
            $todos_fallecidos = $fila_sintomas['todos']['fallecidos'];
            $todos_casos = $fila_sintomas['todos']['casos'];

            $sinto_fallecidos = $fila_sintomas['sinto']['fallecidos'];
            $sinto_casos = $fila_sintomas['sinto']['casos'];


            $acumulado_todos_fallecidos = $acumulado_todos_fallecidos + $todos_fallecidos;
            $acumulado_todos_casos = $acumulado_todos_casos + $todos_casos;

            $acumulado_sinto_fallecidos = $acumulado_sinto_fallecidos + $sinto_fallecidos;
            $acumulado_sinto_casos = $acumulado_sinto_casos + $sinto_casos;

            if ($acumulado_sinto_casos == 0) {
                $por_sintomaticos = 0;
            } else {
                $por_sintomaticos = ($acumulado_sinto_fallecidos / $acumulado_sinto_casos) * 100;
            }

            if ($acumulado_todos_casos == 0) {
                $por_todos = 0;
            } else {
                $por_todos = ($acumulado_todos_fallecidos / $acumulado_todos_casos) * 100;
            }


            //echo $key." = Casos to:".$acumulado_todos_casos." Casos sinto: ".$acumulado_sinto_casos."<br />";

            if ($key >= '2020-03-01') {
                if ($num == $tot) {//Ultimo valor
                    $datos_grafica = $datos_grafica . "['" . $key . "', " . $por_sintomaticos . ", " . $por_todos . "] ";
                } else {
                    $datos_grafica = $datos_grafica . "['" . $key . "', " . $por_sintomaticos . ", " . $por_todos . "], ";
                }
                $num = $num + 1;
                $h++;
            }
        }

        //echo $datos_grafica;
        //echo "<br /> Total = ".$acumulado_todos_casos;
        ?>
        <div id="chart_<?php echo($tipo); ?>" style="width: 100%; height: 500px; "></div>
        <input id="img_<?php echo($tipo); ?>" type="hidden">
        <script id="ajax" type="text/javascript">

                    google.charts.setOnLoadCallback(Chart_<?php echo($tipo); ?>);
                    var dataImg = "";
                    function Chart_<?php echo($tipo); ?>() {
                        var data = google.visualization.arrayToDataTable([
            <?php echo($datos_grafica); ?>
                        ]);

                        var options = {
                            title: "Letalidad por COVID 19 con enfoque de cohorte según fecha de inicio de síntomas en Colombia, <?php echo($ano_graf); ?>",
                            titleTextStyle: {
                                bold: true,
                                fontSize: 22,
                            },
                            backgroundColor: '#D3E1F4',
                            curveType: 'function',
                            legend: {position: 'bottom', maxLines: 3},

                        };
                        var chart = new google.visualization.LineChart(document.getElementById("chart_<?php echo($tipo); ?>"));
                        chart.draw(data, options);

                        dataImg = chart.getImageURI();
                        dataImg = dataImg.replace("png", "jpeg")
                        $('#img_<?php echo($tipo); ?>').val(dataImg);
                    }
        </script>	
        <?php
    }

}
