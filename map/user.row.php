<?php
/**
 * Obtener los datos del usuario de la sesion
 * PHP Version 7
 *
 * @category Info
 * @package  User
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
$user = $ccr_telcel->prepare("SELECT * FROM mapa_usuarios WHERE usuario=?");
$user->execute(array($_SESSION['MM_Username_Mapa']));
$row_user = $user->fetch(PDO::FETCH_ASSOC);
$user->closeCursor();
?>