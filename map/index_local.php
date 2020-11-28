<?php
/**
 * Archivo indice para uso de mapas Locales
 * PHP Version 7
 *
 * @category Index
 * @package  App
 * @author   Carlos Peinado <carlos.peinado@mail.telcel.com>
 * @license  licencia del archivo
 * @link     link
 */
require "Connections/access.php";
require "config.php";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SIFAC - Sistema de Informaci&oacute;n de Fallas para la Atenci&oacute;n al Cliente</title>
<link rel="stylesheet" href="leaflet.css" />
<link rel="stylesheet" href="styles.css" />
<link rel="stylesheet" href="jquery-ui-1.10.4.min.css" />
<link rel="stylesheet" href="jquery.cluetip.css" />
<script type="text/javascript" src="jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="jquery.cluetip.min.js"></script>
<script type="text/javascript" src="jquery.form.js"></script>
<script src="leaflet.js"></script>
<script type="text/javascript">
    var Left = <?php echo isset($MAPA_LONGITUD_IZQUIERDA)?$MAPA_LONGITUD_IZQUIERDA:"-109.69"; ?>;
    var Right = <?php echo isset($MAPA_LONGITUD_DERECHA)?$MAPA_LONGITUD_DERECHA:"-101.25"; ?>;
    var Top = <?php echo isset($MAPA_LATITUD_ARRIBA)?$MAPA_LATITUD_ARRIBA:"36"; ?>;
    var Bottom = <?php echo isset($MAPA_LATITUD_ABAJO)?$MAPA_LATITUD_ABAJO:"21.94"; ?>;
    var ZoomMin = <?php echo isset($MAPA_ZOOM_MIN)?$MAPA_ZOOM_MIN:"6"; ?>;
    var ZoomMax = <?php echo isset($MAPA_ZOOM_MAX)?$MAPA_ZOOM_MAX:"15"; ?>;
    var ZoomInicial = <?php echo isset($MAPA_ZOOM_ARRANQUE)?$MAPA_ZOOM_ARRANQUE:"6"; ?>;
    var RestringirMovimiento = <?php echo isset($MAPA_RESTRINGIR_MOVIMIENTO)?$MAPA_RESTRINGIR_MOVIMIENTO:0; ?>;
    var mapas = "<?php echo isset($MAPA_RUTA_IMAGENES)?$MAPA_RUTA_IMAGENES:""; ?>";
    var minutos_refresh = "<?php echo isset($MAPA_TIEMPO_ACTUALIZACION)?$MAPA_TIEMPO_ACTUALIZACION:"10"; ?>";
    var mapas_satelite = "<?php echo isset($MAPA_RUTA_IMAGENES_SATELITE)?$MAPA_RUTA_IMAGENES_SATELITE:""; ?>";
    //var mapas_satelite = "https://b.tile.openstreetmap.org/{z}/{x}/{y}.png";
    var siteIdFallaMasiva = "<?php echo isset($MAPA_SITE_ID_FALLA_MASIVA)?$MAPA_SITE_ID_FALLA_MASIVA:"NO_DEFINIDO"; ?>";
    var vista_actual = 0;
    
    var server = "<?php echo $_SERVER['SERVER_ADDR']; ?>";
    var puerto = "<?php echo $_SERVER['SERVER_PORT']; ?>";
    var ruta = "<?php echo substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>";
    var usr = "<?php echo $_SESSION['MM_Username_Mapa']; ?>";
    var usrg = "<?php echo $_SESSION['MM_UserGroup_Mapa']; ?>";
    <?php
    $region = (int)$_SESSION['MM_UserRegion_Mapa'];
    if ($region < 1 || $region > 8) {
        $region = 0;
    }
    ?>
    var regionActual = "<?php echo $region; ?>";
    <?php 
    if ($region < 1 || $region > 8 || !isset($MAPA_COORDENADA_INICIAL[$region])) {
        ?>
        var coordenadaInicial = "24.291024,-100.988266";
        <?php
    } else {
        ?>
        var coordenadaInicial = "<?php echo $MAPA_COORDENADA_INICIAL[$region]; ?>";
        <?php
    }
    ?>
    var point_latlng = "<?php 
    if (isset($_GET['latlng'])) {
        echo $_GET['latlng'];
    } 
    ?>";
</script>
<script src="index_local.js"></script>
</head>

<body style="margin:0; padding:0; overflow:hidden;">
<div id="mainId">
</div>
<div id="herramientas" style="display:none">
    <form name="form1" method="post" action="">
        <?php 
        if ($region == 0 || $_SESSION['MM_Username_Mapa'] == 'ccr3') {
            ?>
            <strong>Region:</strong>
            <select name="menuRegion" id="menuRegion" onChange="regionActual=this.value; CargarAfectaciones();">
                <option value="0" <?php 
                if ($region == 0) {
                    echo 'selected="selected"';
                } ?>>Todas</option>
                <option value="1" <?php 
                if ($region == 1) {
                    echo 'selected="selected"';
                } ?>>1</option>
                <option value="2" <?php 
                if ($region == 2) {
                    echo 'selected="selected"';
                } ?>>2</option>
                <option value="3" <?php 
                if ($region == 3) {
                    echo 'selected="selected"';
                } ?>>3</option>
                <option value="4" <?php 
                if ($region == 4) {
                    echo 'selected="selected"';
                } ?>>4</option>
                <option value="5" <?php 
                if ($region == 5) {
                    echo 'selected="selected"';
                } ?>>5</option>
                <option value="6" <?php 
                if ($region == 6) {
                    echo 'selected="selected"';
                } ?>>6</option>
                <option value="7" <?php 
                if ($region == 7) {
                    echo 'selected="selected"';
                } ?>>7</option>
                <option value="8" <?php 
                if ($region == 8) {
                    echo 'selected="selected"';
                } ?>>8</option>
            </select><br />
            <?php
        } ?>
        <strong>Afectaciones:</strong><br />
        <select style="width:100%" name="menuOpciones" size="2" id="menuOpciones" 
            onChange="opcActual=this.value; CargarAfectaciones();"
        >
            <option value="0" selected>Presentes</option>
            <option value="1">Solucionadas</option>
        </select><br>
        <div align="center" style="margin-top:2px; margin-bottom:6px;">
            <?php 
            if (strpos("CCR", $_SESSION['MM_UserGroup_Mapa']) !== false) {
                ?>
                [<span id="CuentaRegresiva"><?php echo $MAPA_TIEMPO_ACTUALIZACION; ?>:00</span>]
                <?php
            } else {
                ?>Actualizar<?php
            } ?>
            <div class="resaltar-div leaflet-control-layers" style="float:right;">
                <span class="mini-button resaltar-div" id="recargar" onClick="CargarAfectaciones();" style="font-size:10px; float:right">Refrescar</span>
            </div>
        </div>
        <?php 
        if ($MAPA_HABILITAR_SATELITE && strpos("CCR JEFE", $_SESSION['MM_UserGroup_Mapa']) !== false) {
            ?>
            <input id="show-earth" name="show-earth" type="checkbox" value="1">
            <label for="show-earth">Sat&eacute;lite</label>
            <?php
        } ?>
    </form>
</div>
<div id="masivas" style="display:none">
    <div style="margin:0px; font-size:10px; width:200px">
        <div id="masivas-contenido">
        </div>
    </div>
</div>
<div id="configuracion" style="display:none">
    <div class="controlButton leaflet-control-layers" 
        onClick="AbrirConfiguracion()" title="Configuración"
    >
        <img src="images/settings-work-tool.png" width="16" height="16" />
    </div>
</div>
<div id="logout" style="display:none">
    <div class="resaltar-div leaflet-control-layers" onClick="location='logout.php'">
        <span id="logout-button">Cerrar Sesi&oacute;n</span>
    </div>
</div>
<div id="masivas-tips" style="display:none">
<div id="search" style="display:none">
    <div style="padding:2px;">
        <form name="formSearch">
            <input style="width:150px; border-top:none; border-left:none; border-right:none;" id="search_text" name="search_text" type="text" 
            class="search_text" value="" 
            title="Coordenada absoluta, ej. 28.628751,-106.0728707">
            <button type="submit" class="mini-button" style="margin-bottom:3px;" id="search_button">Buscar Coordenada</button>
        </form>
    </div>
</div>
<div id="dialog-config" style="display:none;"></div>
<div id="dialog-resultados" style="display:none;"></div>
<div id="dialog_esperar" style="display:none;">
       <div align="center" style="font-size:10px;"><p>
           <img src="./images/loader.gif" alt=" " width="16" height="16" />&nbsp;Cargando...
       </p></div>
</div>
</body>
</html>
