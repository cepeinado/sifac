<?php
require_once 'Connections/ccr_telcel.php';
// *** Validate request to login to this site.
if (session_id() == "") {
    session_cache_limiter('private_no_expire');
    session_start();
} else {
    session_regenerate_id(true);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = (get_magic_quotes_gpc()) ? $_GET['accesscheck'] : addslashes($_GET['accesscheck']);
}

$MM_redirectLoginFailed = false;
if (isset($_POST['usuario'])) {
    //var_dump($_POST);
    $loginUsername=$_POST['usuario'];
    $password=$_POST['password'];
    $MM_fldUserAuthorization = "";
    $MM_redirectLoginSuccess = "index.php?gmaps=" . $_POST['radio1'];
    //  $MM_redirectLoginFailed = $editFormAction;
    $MM_redirecttoReferrer = false;
  
    $LoginRS__query=sprintf(
        "SELECT * FROM mapa_usuarios WHERE usuario='%s' AND password='%s'",
        get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername),
        get_magic_quotes_gpc() ? $password : addslashes($password)
    );
   
    $LoginRS = $ccr_telcel->query($LoginRS__query) or die(print_r($ccr_telcel->errorInfo()));
    $loginFoundUser = $LoginRS->rowCount();
    if ($loginFoundUser) {
        $login_row = $LoginRS->fetch();
        $loginStrGroup = $login_row['grupo'];

        $_SESSION['MM_Username_Mapa'] = $loginUsername;
        $_SESSION['MM_UserGroup_Mapa'] = $loginStrGroup;
        $_SESSION['MM_UserRegion_Mapa'] = $login_row['region'];
    
        if (isset($_GET['accesscheck'])) {
            $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
        }

        header("Location: " . $MM_redirectLoginSuccess);
    } else {
        //header("Location: ". $MM_redirectLoginFailed );
        $MM_redirectLoginFailed = true;
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <title>SIFAC - Sistema de Información de Fallas para la Atención al Cliente</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="jquery-ui-1.10.4.min.css" />
    <script language="JavaScript" type="text/javascript" src="jquery-1.8.3.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="jquery-ui-1.10.4.min.js"></script>
    <script type="text/JavaScript">
        $(document).ready(function() {
    $('#submit').button();
    form1.usuario.focus();
    $('#loginDiv').css("margin-left", -($('#loginDiv').outerWidth() / 2));
    $('#loginDiv').css("margin-top", -($('#loginDiv').outerHeight() / 2));

    $( "#opciones_radio1" ).buttonset();
    $( "#opciones_radio1 input" ).click(function() { $( "#opciones_radio1 input" ).each(checkbox); } ).each(checkbox);
});

function checkbox() 
{
    var options;
    if ( $( this ).prop("checked") ) options = { icons: { primary: 'ui-icon-circle-check' } };
    else options = { icons: {} };
    $( this ).button( "option", options );
}
</script>
</head>

<body>
    <?php 
    if ($MM_redirectLoginFailed) {
        ?>
        <div id="loginError" class="ui-state-error">Usuario o Contrase&ntilde;a err&oacute;nea, intente de nuevo</div>
        <?php
    } ?>
    <div id="loginDiv">
        <form action="<?php echo $editFormAction; ?>" method="POST"
            name="form1" id="form1">
            <table border="0" align="center" cellspacing="0" cellpadding="4" width="320">
                <tr class="header1">
                    <td colspan="2">
                        <div align="center" style="font-size:x-large; font-weight:bold">SIFAC</div>
                    </td>
                </tr>
                <tr class="header">
                    <td colspan="2">
                        <div align="center" style="font-style:italic">Sistema de Información de Fallas para la Atención
                            al Cliente</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div align="right"><strong>Usuario:</strong></div>
                    </td>
                    <td><input name="usuario" type="text" id="usuario"></td>
                </tr>
                <tr>
                    <td>
                        <div align="right"><strong>Contrase&ntilde;a:</strong></div>
                    </td>
                    <td><input name="password" type="password" id="password"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div align="center">
                            <div id="opciones_radio1" style="font-size:smaller">
                                <input type="radio" name="radio1" value="0" id="radio1_0" <?php
                                if (isset($_POST['radio1'])) {
                                    if ($_POST['radio1'] == "0") {
                                        echo "checked=\"checked\"";
                                    }
                                } else {
                                    echo "checked=\"checked\"";
                                }
                                ?>/>
                                <label for="radio1_0">Mapas Locales</label>
                                <input type="radio" name="radio1" value="1" id="radio1_1" <?php
                                if (isset($_POST['radio1'])) {
                                    if ($_POST['radio1'] == "1") {
                                        echo "checked=\"checked\"";
                                    }
                                }
                                ?>/>
                                <label for="radio1_1">Mapas de Google</label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;<input name="google" type="hidden" id="google" value="0"></td>
                    <td><button type="submit" name="Submit" id="submit">Aceptar</button></td>
                </tr>
            </table>
        </form>
    </div>
    <div style="display:block" id="google-div"></div>
</body>

</html>