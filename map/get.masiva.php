<?php
/**
 * Obtener datos de una afectacion masiva
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

require "./Connections/access.php";
require "user.row.php";
require 'config.php';

$gmtTz = new DateTimeZone('GMT');
$myTz = new DateTimeZone($row_user['tz']);

if ($_GET['op'] == 0) {
    $ahora = new DateTime("now", $myTz);
    $ahora->setTimezone($gmtTz);
    $ahora = $ahora->format("Y-m-d H:i:s");
    $query_afectacion = sprintf(
        "SELECT 
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
        WHERE id_afectacion = '%s'", 
        $_GET['id']
    );
} else {
    $query_afectacion = sprintf(
        "SELECT 
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
        WHERE id_afectacion = '%s'", 
        $_GET['id']
    );
}
//echo $query_afectacion;
$afectacion = $ccr_telcel->query($query_afectacion);
$totalRows_afectacion = $afectacion->rowCount();
$row_afectacion = $afectacion->fetch();
if ($totalRows_afectacion) {
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
    ?>
    <table border="0" cellspacing="1" cellpadding="1" width="100%">
        <tr class="tr1">
            <td>Afectaci&oacute;n:</td>
            <td><?php echo $row_afectacion['servicios_afectados'] ?></td>
        </tr>
        <tr class="tr0">
            <td>Fecha Inicio:</td>
            <td><?php echo $row_afectacion['tiempo_inicio'] ?></td>
        </tr>
        <tr class="tr1">
            <td>Tiempo Afectacion:</td>
            <td><?php echo $row_afectacion['tiempo_afectacion'] ?></td>
        </tr>
        <?php 
        if ($_GET['op'] == 0) {
            ?>
            <tr class="tr0">
                <td>Tiempo de Respuesta:</td>
                <td><?php echo $row_afectacion['tiempo_respuesta'] ?></td>
            </tr>
            <?php
        } else {
            ?>
            <tr class="tr0">
                <td>Solucion:</td>
                <td><?php echo $row_afectacion['solucion'] ?></td>
            </tr>
            <?php
        } ?>
    </table>
    <?php
}
$afectacion->closeCursor();
?>
