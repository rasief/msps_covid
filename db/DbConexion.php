<?php

require_once("Configuracion.php");

class DbConexion {

    protected $conexion;
    private $servidor;
    private $puerto;
    private $baseDatos;
    private $usuario;
    private $clave;

    public function __construct() {
        $this->servidor = Configuracion::$SERVIDOR;
        $this->puerto = Configuracion::$PUERTO;
        $this->baseDatos = Configuracion::$BASE_DATOS;
        $this->usuario = Configuracion::$USUARIOS;
        $this->clave = Configuracion::$PASS;
    }

    /**
     * Método que crea una conexión con la base de datos
     */
    protected function crearConexion() {
        //echo("#" . $this->servidor . "#" . $this->puerto . "#" . $this->usuario . "#" . $this->clave . "#" . $this->baseDatos . "#");
        $this->conexion = new mysqli($this->servidor . ":" . $this->puerto, $this->usuario, $this->clave, $this->baseDatos);
        //$this->conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->baseDatos);
        $this->conexion->set_charset("utf8");
    }

    /**
     * Método que cierra la conexión con la base de datos
     */
    protected function cerrarConexion() {
        $this->conexion->close();
    }

    /**
     * Metodo que realiza consultas en la base de datos
     * @param $sql SQL con la consulta a realizar
     * @return Resultado de la consulta SQL
     */
    public function getDatos($sql) {
        $tabla = array();

        try {
            $this->crearConexion();

            $resultado = $this->conexion->query($sql);

            if ($resultado) {
                $numFilas = $resultado->num_rows;
                $fila = 0;
                while ($fila < $numFilas) {
                    $aux = $resultado->fetch_array(MYSQLI_ASSOC);
                    $tabla[$fila] = $aux;
                    $fila++;
                }
            }

            $this->cerrarConexion();
            return $tabla;
        } catch (Exception $e) {
            $this->cerrarConexion();
            return array();
        }
    }

    /**
     * Metodo que realiza consultas en la base de datos que retornan un único registro
     * @param $sql SQL con la consulta a realizar
     * @return Resultado de la consulta SQL
     */
    public function getUnDato($sql) {
        $fila = null;

        try {
            $this->crearConexion();

            $resultado = $this->conexion->query($sql);

            if ($resultado) {
                $numFilas = $resultado->num_rows;
                if ($numFilas > 0) {
                    $fila = $resultado->fetch_array(MYSQLI_ASSOC);
                }
            }

            $this->cerrarConexion();
            return $fila;
        } catch (Exception $e) {
            $this->cerrarConexion();
            return null;
        }
    }

    /**
     * Metodo que ejecuta una sentencia SQL
     * @param $sql SQL con la sentencia a ejecutar
     * @param $arrCampos array con los campos de salida
     * @return Resultado de la sentencia SQL
     */
    public function ejecutarSentencia($sql, $arrCampos) {
        $resultado = 0;
        try {
            $this->crearConexion();

            $resulAux = $this->conexion->query($sql);

            if ($resulAux) {
                if (count($arrCampos) > 0) {
                    //Se ejecuta la segunda sentencia
                    $cadenaAux = "";
                    foreach ($arrCampos as $campoAux) {
                        if ($cadenaAux != "") {
                            $cadenaAux .= ", ";
                        }
                        $cadenaAux .= $campoAux;
                    }

                    $fila = null;
                    $resulAux = $this->conexion->query("SELECT " . $cadenaAux);
                    if ($resulAux) {
                        $numFilas = $resulAux->num_rows;
                        if ($numFilas > 0) {
                            $resultado = $resulAux->fetch_array(MYSQLI_ASSOC);
                        }
                    }
                } else {
                    $resultado = 1;
                }
            } else {
                $resultado = 0;
            }

            $this->cerrarConexion();
        } catch (Exception $e) {
            $this->cerrarConexion();
            $resultado = -1;
        }

        return $resultado;
    }

}

?>
