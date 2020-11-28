<?php
/**
 * Obtener el listado de afectaciones
 * PHP Version 7
 *
 * @category Afectacion
 * @package  App
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: content="text/plain; charset=utf-8"');

require "./Connections/access.php";
require "user.row.php";
require 'config.php'; 

if (session_id() == "") {
    session_cache_limiter('private_no_expire');
    session_start();
} else {
    session_regenerate_id(true);
}
$session_ok = true;
if (!(isset($_SESSION['MM_Username_Mapa']))) {
    $session_ok = false;
}

$gmtTz = new DateTimeZone('GMT');
$myTz = new DateTimeZone($row_user['tz']);

if (!$session_ok) { 
    echo '[{"session_ok":"false"}]'; 
    exit; 
}

$opc = 0;
$region = "";
if (isset($_GET['opc'])) {
    $opc = $_GET['opc'];
}
if (isset($_GET['region'])) {
    if ($_GET['region'] != 0) {
        $region = "AND region = {$_GET['region']}";
    }
}

//-- INI - Buscar afectaciones TOTALES a cambiar a SUSPENDIDO --//
$segAfectados = $MAPA_DIAS_PARA_SUSPENDIDO * 86400;
$nowDt = new DateTime();
$now = $nowDt->format("Y-m-d H:i:s");
$ccr_telcel->query(
    "UPDATE mapa_afectaciones 
    SET tipo_afectacion = 'SUSPENDIDO'
    WHERE 
        ISNULL(tiempo_fin) AND tipo_afectacion = 'TOTAL'
        AND TIMESTAMPDIFF(SECOND, tiempo_inicio, '$now') > $segAfectados
        $region
    "
);
//-- FIN - Buscar afectaciones TOTALES a cambiar a SUSPENDIDO --//

if ($opc == 0) {
    //-- Sitios caidos o parcialmente afectados --//
    $ahora = new DateTime("now", $myTz);
    $ahora->setTimezone($gmtTz);
    $ahora = $ahora->format("Y-m-d H:i:s");
    $query_afectacion = sprintf(
        "SELECT 
            region,
            id_afectacion, tipo_afectacion, id_sitio, nombre_sitio, 
            longitud, latitud, poblacion_afectada, 
            tecnologia_afectada, causa_probable, servicios_afectados, 
            tiempo_inicio, 
            tiempo_fin, 
            tiempo_respuesta, 
            solucion,
            #SEC_TO_TIME(TIMESTAMPDIFF(SECOND, tiempo_inicio, '$ahora')) as tiempo_afectacion
            TIMESTAMPDIFF(SECOND, tiempo_inicio, '$ahora') as tiempo_afectacion
        FROM mapa_afectaciones 
        WHERE tipo_afectacion <> 'SOLUCIONADA' $region", 
        $MAPA_FORMATO_FECHA, $MAPA_FORMATO_FECHA, $MAPA_FORMATO_FECHA
    );
}
if ($opc == 1) {
    //-- Sitios con afectaciones resueltas --//
    $query_afectacion = sprintf(
        "SELECT 
            region,
            id_afectacion, tipo_afectacion, id_sitio, nombre_sitio, 
            longitud, latitud, poblacion_afectada, 
            tecnologia_afectada, causa_probable, servicios_afectados, 
            tiempo_inicio, 
            tiempo_fin, 
            tiempo_respuesta, 
            solucion,
            #SEC_TO_TIME(TIMESTAMPDIFF(SECOND, tiempo_inicio, tiempo_fin)) as tiempo_afectacion
            TIMESTAMPDIFF(SECOND, tiempo_inicio, tiempo_fin) as tiempo_afectacion
        FROM mapa_afectaciones 
        WHERE 
            tipo_afectacion = 'SOLUCIONADA' 
            AND TIMESTAMPDIFF(DAY, tiempo_fin, NOW()) <= $MAPA_RANGO_DIAS_SOLUCIONADOS $region
        ", 
        $MAPA_FORMATO_FECHA, $MAPA_FORMATO_FECHA, $MAPA_FORMATO_FECHA
    );
}
//echo $query_afectacion;
$afectacion = $ccr_telcel->query($query_afectacion) or var_dump($ccr_telcel->errorInfo());
$totalRows_afectacion = $afectacion->rowCount();

$contenido = array();
while ($row_afectacion = $afectacion->fetch(PDO::FETCH_ASSOC)) {
    $row = array();
    $row_afectacion['tiempo_inicio'] = changeTimeZone(
        $row_afectacion['tiempo_inicio'], $gmtTz, $myTz
    );
    $row_afectacion['tiempo_fin'] = changeTimeZone(
        $row_afectacion['tiempo_fin'], $gmtTz, $myTz
    );
    $row_afectacion['tiempo_respuesta'] = changeTimeZone(
        $row_afectacion['tiempo_respuesta'], $gmtTz, $myTz
    );

    $diasAfectados = floor($row_afectacion['tiempo_afectacion'] / 86400);
    $horasAfectadas = floor(
        ($row_afectacion['tiempo_afectacion'] % 86400) / 3600
    );
    $minutosAfectados = floor(
        (   
            $row_afectacion['tiempo_afectacion'] 
            - $diasAfectados * 86400 
            - $horasAfectadas * 3600
        ) / 60
    );

    if ($diasAfectados > 0) {
        $row_afectacion['tiempo_afectacion'] 
            = "$diasAfectados Dia(s), $horasAfectadas Hora(s) y $minutosAfectados Minuto(s)";
    } else {
        $row_afectacion['tiempo_afectacion']
            = "$horasAfectadas Hora(s) y $minutosAfectados Minuto(s)";
    }

    foreach ($row_afectacion as $key => $value) {
        $row[strtoupper($key)] = nl2br($value);
    }
    $contenido[] = $row;
}
echo json_encode($contenido);
$afectacion->closeCursor();
?>
