<?php
/**
 * Configuracion de usuario
 * PHP Version 7
 *
 * @category Usuario
 * @package  Configuracion
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
require "./Connections/access.php";
require "user.row.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Configuracion de usuario</title>
    <script type="text/javascript">
        var $ERROR = false;
        $(document).ready(function () {
            $("#config-tabs").tabs();
            $("#cambiar-preferencias").button({
                icons: {
                    primary: 'ui-icon-check'
                }
            });
            $("#cancelar-preferencias").button({
                icons: {
                    primary: 'ui-icon-close'
                }
            });
            $("#cambiar-password").button({
                icons: {
                    primary: 'ui-icon-check'
                }
            });
            $("#cancelar-password").button({
                icons: {
                    primary: 'ui-icon-close'
                }
            });

            $(document.formUserConfig).on('submit', function (e) {
                e.preventDefault(); // <-- importante
                $(this).ajaxSubmit({
                    target: "#dialog-resultados",
                    url: "user.update.proc.php",
                    beforeSubmit: function (arr, $form, options) {
                        $dialog_esperar.dialog('open');
                    },
                    success: function (responseText, statusText) {
                        $dialog_esperar.dialog('close');
                        if ($ERROR) {
                            $dialog_resultados.dialog('open');
                        } else {
                            $dialog_config.dialog('close');
                            CargarAfectaciones();
                        }
                    }
                });
                return false;
            });
        });

        function CambiarPreferencias() {
            document.formUserConfig.validaPwd.value = "0";
            $(document.formUserConfig).submit();
        }

        function CambiarPassword() {
            document.formUserConfig.validaPwd.value = "1";
            $(document.formUserConfig).submit();
        }
    </script>
</head>
<body>
<form class="forma" method="post" id="formUserConfig" name="formUserConfig" action="#">
    <div id="config-tabs">
        <ul style="font-size:9px">
            <li><a href="#tabs-1">Preferencias</a></li>
            <li><a href="#tabs-2">Contraseña</a></li>
        </ul>
        <div id="tabs-1">
            <table border="0" cellpadding="2" cellspacing="0" style="font-size:10px; width:100%;">
                <tr>
                    <td class="w100">Zona Horaria</td>
                    <td>
                        <select id="tz" name="tz">
                            <option value="America/Cancun"
                                <?php
                                if (!(strcmp("America/Cancun", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Cancun</option>
                            <option value="America/Chihuahua"
                                <?php
                                if (!(strcmp("America/Chihuahua", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Chihuahua</option>
                            <option value="America/Hermosillo"
                                <?php
                                if (!(strcmp("America/Hermosillo", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Hermosillo</option>
                            <option value="America/Matamoros"
                                <?php
                                if (!(strcmp("America/Matamoros", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Matamoros</option>
                            <option value="America/Mazatlan"
                                <?php
                                if (!(strcmp("America/Mazatlan", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Mazatlan</option>
                            <option value="America/Merida"
                                <?php
                                if (!(strcmp("America/Merida", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Merida</option>
                            <option value="America/Mexico_City"
                                <?php
                                if (!(strcmp("America/Mexico_City", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Mexico_City</option>
                            <option value="America/Monterrey"
                                <?php
                                if (!(strcmp("America/Monterrey", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Monterrey</option>
                            <option value="America/Tijuana"
                                <?php
                                if (!(strcmp("America/Tijuana", $row_user["tz"]))) {
                                    echo "selected=\" selected\"";
                                }
                                ?>
                            >America/Tijuana</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span style="float:right">
                            <span id="cambiar-preferencias" onClick="CambiarPreferencias()">Cambiar</span>&nbsp;
                            <span id="cancelar-preferencias" onClick="$dialog_config.dialog('close')">Cancelar</span>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tabs-2">
            <table border="0" cellpadding="2" cellspacing="0" style="font-size:10px; width:100%;">
                <tr>
                    <td class="w100">Actual</td>
                    <td>
                        <input name="PASS0" type="password" id="PASS0" size="10" />
                    </td>
                </tr>
                <tr>
                    <td>Nueva</td>
                    <td>
                        <input name="PASS1" type="password" id="PASS1" size="10" />
                    </td>
                </tr>
                <tr>
                    <td>Confirmar</td>
                    <td>
                        <input name="PASS2" type="password" id="PASS2" size="10" />
                        <input name="validaPwd" type="hidden" id="validaPwd" value="0" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span style="float:right">
                            <span id="cambiar-password" onClick="CambiarPassword()">Cambiar</span>&nbsp;
                            <span id="cancelar-password" onClick="$dialog_config.dialog('close')">Cancelar</span>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
<div id="bgprocess"></div>
</body>
</html>