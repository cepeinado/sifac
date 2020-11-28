<?php
/**
 * Configuracion para la conexion a base de datos Mysql
 * PHP Version 7
 *
 * @category DB
 * @package  Connections
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
$dsn_ccr_telcel = 'mysql:host=localhost;port=3305;dbname=ccr_telcel';
$username_ccr_telcel = 'ccr_telcel';
$password_ccr_telcel = 'telcel';
$options_ccr_telcel = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
$ccr_telcel = new PDO($dsn_ccr_telcel, $username_ccr_telcel, $password_ccr_telcel, $options_ccr_telcel);
?>