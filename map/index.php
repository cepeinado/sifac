<?php
/**
 * Carga el archivo indice, dependiendo del parametro gmaps
 * PHP Version 7
 *
 * @category Index
 * @package  App
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
if (isset($_GET['gmaps'])) {
    if ($_GET['gmaps'] == "1") {
        include "index_gmaps.php";
    } else {
        include "index_local.php";
    }
} else {
    include "index_local.php";
}
?>