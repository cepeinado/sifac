<?php
/**
 * Manejo de sesion de usuario
 * PHP Version 7
 *
 * @category DB
 * @package  Connections
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
require_once 'ccr_telcel.php'; 
if (session_id() == "") {
    session_cache_limiter('private_no_expire');
    session_start();
} else {
    session_regenerate_id(true);
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";
$MM_restrictGoTo = "login.php";
if (!(isset($_SESSION['MM_Username_Mapa']))) {   
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) {
        $MM_qsChar = "&";
    }
    if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) {
        $MM_referrer .= "?" . $QUERY_STRING;
    }
    $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);

    header("Location: ". $MM_restrictGoTo); 
    exit;
}
?>
