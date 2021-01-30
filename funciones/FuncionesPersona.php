<?php
	class FuncionesPersona {
		public function obtenerNombreCompleto($nombre1, $nombre2, $apellido1, $apellido2) {
			$nombreAux = $nombre1;
			if (trim($nombre2) != "") {
				$nombreAux .= " ".$nombre2;
			}
			$nombreAux .= " ".$apellido1;
			if (trim($apellido2) != "") {
				$nombreAux .= " ".$apellido2;
			}
			
			return $nombreAux;
		}
		
		public function obtenerEdad($edad, $unidadEdad) {
			if ($edad != "" && $unidadEdad != "") {
				$unidadAux = "";
				switch ($unidadEdad) {
					case "D": //Días
						if ($edad == "1") {
							$unidadAux = "D&iacute;a";
						} else {
							$unidadAux = "D&iacute;as";
						}
						break;
					case "M": //Meses
						if ($edad == "1") {
							$unidadAux = "Mes";
						} else {
							$unidadAux = "Meses";
						}
						break;
					case "A": //Años
						if ($edad == "1") {
							$unidadAux = "A&ntilde;o";
						} else {
							$unidadAux = "A&ntilde;os";
						}
						break;
				}
			}
			return $edad." ".$unidadAux;
		}
		
		public function obtenerEdad2($edad, $unidadEdad) {
			if ($edad != "" && $unidadEdad != "") {
				$unidadAux = "";
				switch ($unidadEdad) {
					case "3": //Días
						if ($edad == "1") {
							$unidadAux = "D&iacute;a";
						} else {
							$unidadAux = "D&iacute;as";
						}
						break;
					case "2": //Meses
						if ($edad == "1") {
							$unidadAux = "Mes";
						} else {
							$unidadAux = "Meses";
						}
						break;
					case "1": //Años
						if ($edad == "1") {
							$unidadAux = "A&ntilde;o";
						} else {
							$unidadAux = "A&ntilde;os";
						}
						break;
				}
			}
			return $edad." ".$unidadAux;
		}
		
		public function obtenerFecha($strFecha) {
			$diaAux = substr($strFecha, 0, 2);
			$mesAux = substr($strFecha, 3, 2);
			$anoAux = substr($strFecha, 6);
			
			return $anoAux."-".$mesAux."-".$diaAux;
		}
		
		public function obtenerFecha2($dia, $mes, $ano) {
			if ($dia < 10) {
				$dia = "0".intval($dia, 10);
			}
			if ($mes < 10) {
				$mes = "0".intval($mes, 10);
			}
			
			return $ano."-".$mes."-".$dia;
		}
		
        public function obtenerFecha3($dia, $mes, $ano) {//Retorna la fecha - ejemplo: 02 de Enero del 2014
			$mes_aux = '';
			if ($mes < 10) {
				$mes = "0".$mes;
			}
			$mes_aux = $this->obtenerNombreMes($mes, 1);
			
			return $dia." de ".$mes_aux." de ".$ano;
		}
		
        public function obtenerFecha4($fecha) {//Retorna la fecha - ejemplo: Miércoles 04 Dic/13
			$anio = date("y", $fecha);
			$mes = date("m", $fecha);
			$dia = date("d", $fecha);
			$dia_sem = date("w", $fecha);
			
			$mes_aux = '';
			if ($mes < 10) {
				$mes = "0".$mes;
			}
			$mes_aux = $this->obtenerNombreMes($mes, 2);
			$dia_sem_aux = $this->obtenerNombreDia($dia_sem, 1);
			
			return $dia_sem_aux." ".$dia." ".$mes_aux."/".$anio;
		}
		
        public function obtenerFecha5($fecha) {//Retorna la fecha - ejemplo: Miércoles 04 de Diciembre de 2013
			$anio = date("Y", $fecha);
			$mes = date("m", $fecha);
			$dia = date("d", $fecha);
			$dia_sem = date("w", $fecha);
			
			$mes_aux = '';
			if ($mes < 10) {
				$mes = "0".$mes;
			}
			$mes_aux = $this->obtenerNombreMes($mes, 1);
			$dia_sem_aux = $this->obtenerNombreDia($dia_sem, 1);
			
			return $dia_sem_aux.", ".$dia." de ".$mes_aux." de ".$anio;
		}
		
		/**
		 * Formato (1: Largo - 2: Corto)
		 */
		private function obtenerNombreDia($dia_sem, $formato) {
			$dia_sem_aux = "";
			switch ($dia_sem) {
				case "0":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Domingo';
							break;
						case 2:
							$dia_sem_aux = 'Dom';
							break;
					}
					break;
				case "1":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Lunes';
							break;
						case 2:
							$dia_sem_aux = 'Lun';
							break;
					}
					break;
				case "2":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Martes';
							break;
						case 2:
							$dia_sem_aux = 'Mar';
							break;
					}
					break;
				case "3":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Mi&eacute;rcoles';
							break;
						case 2:
							$dia_sem_aux = 'Mi&eacute;';
							break;
					}
					break;
				case "4":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Jueves';
							break;
						case 2:
							$dia_sem_aux = 'Jue';
							break;
					}
					break;
				case "5":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Viernes';
							break;
						case 2:
							$dia_sem_aux = 'Vie';
							break;
					}
					break;
				case "6":
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'S&aacute;bado';
							break;
						case 2:
							$dia_sem_aux = 'S&aacute;b';
							break;
					}
					break;
				default:
					switch ($formato) {
						case 1:
							$dia_sem_aux = 'Error';
							break;
						case 2:
							$dia_sem_aux = 'Err';
							break;
					}
					break;
			}
			
			return $dia_sem_aux;
		}
		
		/**
		 * Formato (1: Largo - 2: Corto)
		 */
		private function obtenerNombreMes($mes, $formato) {
			$mes_aux = "";
			switch ($mes) {
				case '01':
					switch ($formato) {
						case 1:
							$mes_aux = 'Enero';
							break;
						case 2:
							$mes_aux = 'Ene';
							break;
					}
					break;
				case '02':
					switch ($formato) {
						case 1:
							$mes_aux = 'Febrero';
							break;
						case 2:
							$mes_aux = 'Feb';
							break;
					}
					break;
				case '03':
					switch ($formato) {
						case 1:
							$mes_aux = 'Marzo';
							break;
						case 2:
							$mes_aux = 'Mar';
							break;
					}
					break;
				case '04':
					switch ($formato) {
						case 1:
							$mes_aux = 'Abril';
							break;
						case 2:
							$mes_aux = 'Abr';
							break;
					}
					break;
				case '05':
					switch ($formato) {
						case 1:
							$mes_aux = 'Mayo';
							break;
						case 2:
							$mes_aux = 'May';
							break;
					}
					break;
				case '06':
					switch ($formato) {
						case 1:
							$mes_aux = 'Junio';
							break;
						case 2:
							$mes_aux = 'Jun';
							break;
					}
					break;
				case '07':
					switch ($formato) {
						case 1:
							$mes_aux = 'Julio';
							break;
						case 2:
							$mes_aux = 'Jul';
							break;
					}
					break;
				case '08':
					switch ($formato) {
						case 1:
							$mes_aux = 'Agosto';
							break;
						case 2:
							$mes_aux = 'Ago';
							break;
					}
					break;
				case '09':
					switch ($formato) {
						case 1:
							$mes_aux = 'Septiembre';
							break;
						case 2:
							$mes_aux = 'Sep';
							break;
					}
					break;
				case '10':
					switch ($formato) {
						case 1:
							$mes_aux = 'Octubre';
							break;
						case 2:
							$mes_aux = 'Oct';
							break;
					}
					break;
				case '11':
					switch ($formato) {
						case 1:
							$mes_aux = 'Noviembre';
							break;
						case 2:
							$mes_aux = 'Nov';
							break;
					}
					break;
				case '12':
					switch ($formato) {
						case 1:
							$mes_aux = 'Diciembre';
							break;
						case 2:
							$mes_aux = 'Dic';
							break;
					}
					break;
				default:
					switch ($formato) {
						case 1:
							$mes_aux = 'Error';
							break;
						case 2:
							$mes_aux = 'Err';
							break;
					}
					break;
			}
			
			return $mes_aux;
		}
	}
?>
