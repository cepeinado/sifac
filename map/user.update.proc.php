<?php
/**
 * Actualizar preferecias o contraseña de usuario
 * PHP Version 7
 *
 * @category Update
 * @package  User
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
require "./Connections/access.php";
require "user.row.php";
//var_dump($_POST);

$error = "";
$res = true;
if ($_POST['validaPwd'] == "1") {
    //-- Cambiar Contraseña --//
    if ($_POST['PASS0'] == $row_user['password']) {
        if ($_POST['PASS1'] == $_POST['PASS2']) {
            $update = $ccr_telcel->prepare(
                "UPDATE mapa_usuarios SET password=? WHERE usuario=?"
            );
            $res = $update->execute(
                array(
                    $_POST['PASS1'],
                    $_SESSION['MM_Username_Mapa']
                )
            );
        } else {
            $error = "La nueva contraseña y su confirmación no son iguales";
        }
    } else {
        $error = "La contraseña actual no es correcta";
    }
} else {
    //-- Cambiar Preferencias --//
    $update = $ccr_telcel->prepare(
        "UPDATE mapa_usuarios SET tz=? WHERE usuario=?"
    );
    $res = $update->execute(
        array(
            $_POST['tz'],
            $_SESSION['MM_Username_Mapa']
        )
    );
}
if (!$res) {
    $error = sprintf(
        "Error:<br>Codigo: %s<br>Mensaje: %s<br>",
        $update->errorInfo()[1],
        $update->errorInfo()[2]
    );
}
if ($error == "") {
    echo '<script type="text/javascript">$ERROR = false;</script>';
} else {
    echo $error;
    echo '<script type="text/javascript">$ERROR = true;</script>';
}
?>