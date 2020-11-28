<?php 
/**
 * Cargar valores de configuracion de la tabla 'configuracion' y
 * gererar variables globales.
 * PHP Version 7
 *
 * @category Configuracion
 * @package  App
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
if (!isset($GLOBALS['MAPA_RESTRINGIR_MOVIMIENTO'])) {
    include_once 'Connections/ccr_telcel.php';
    $query = "SELECT data,value FROM mapa_configuracion";
    $config = $ccr_telcel->query($query);
    if ($config) {
        if ($config->rowCount()) {
            $GLOBALS['MAPA_SITE_ID_FALLA_MASIVA'] = "";
            $GLOBALS['MAPA_COORDENADA_INICIAL'] = array();
            while ($row_config = $config->fetch()) {
                if (strpos($row_config['data'], 'MAPA_SITE_ID_FALLA_MASIVA') === false) {
                    if (strpos($row_config['data'], 'MAPA_COORDENADA_INICIAL') === false) {
                        $GLOBALS[$row_config['data']] = $row_config['value'];
                    } else {
                        $region = substr($row_config['data'], strlen($row_config['data']) - 1, 1);
                        $GLOBALS['MAPA_COORDENADA_INICIAL'][$region] = $row_config['value'];
                    }
                } else {
                    $GLOBALS['MAPA_SITE_ID_FALLA_MASIVA'] .= "[" . $row_config['value'] . "]";
                }
            }
        }
        $config->closeCursor();
    }
}
//print_r($GLOBALS);

/**
 * Convierte una fecha-hora de una zona horaria a otra
 *
 * @param string       $dt        DateTime string
 * @param DateTimeZone $tzOrigen  TimeZone origen
 * @param DateTimeZone $tzDestino TimeZone destino
 * 
 * @return string
 */
function changeTimeZone($dt, DateTimeZone $tzOrigen, DateTimeZone $tzDestino)
{
    if ($dt != "" && $dt != null) {
        global $MAPA_FORMATO_FECHA;
        $mDt = new DateTime($dt, $tzOrigen);
        $mDt->setTimezone($tzDestino);
        return $mDt->format($MAPA_FORMATO_FECHA);
    } else {
        return $dt;
    }
}
?>