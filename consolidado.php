<?php

ini_set("memory_limit", "164M");
$origen = "http://www.datos.gov.co/api/views/gt2j-8ykr/rows.csv?accessType=DOWNLOAD";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $origen);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
//curl_setopt($ch, CURLOPT_SSLVERSION,3);
//$data = curl_exec($ch);
//$error = curl_error($ch);
//curl_close($ch);
$destino = "archivos/archivo1.csv";
$archivo = fopen($destino, "w");

curl_setopt($ch, CURLOPT_FILE, $archivo);
curl_exec ($ch);

//fputs($archivo, $data);
//fclose($archivo);
	
	
?>



