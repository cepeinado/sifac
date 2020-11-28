// JavaScript Document
var map;
var tiles;

var marcadores;
var marcadores2;
var Icon1;
var Icon2;
var Icon3;
var Icon4;
var masivasControl;
var searchControl;
//var showMasivasControl = false;
var $dialog_esperar;
var $dialog_config;
var $dialog_resultados;

var opcActual = 0;
var vistaActual = 0;
var minutoActual = minutos_refresh;
var segundoActual = 0;

$(document).ready(function () {
    if (server == "::1") server = "localhost";

    $(window).resize(function () {
        $("#mainId").css("height", $(window).height());
    });
    $(window).resize();

    var latlng;
    coordInicial = coordenadaInicial.split(",");
    if (coordInicial.length != 2 || regionActual == 0) {
        // Coordenadas incorrectas o Region 0
        latlng = L.latLng(24.291024, -100.988266);
    } else {
        latlng = L.latLng(coordInicial[0], coordInicial[1]);
    }
    

    if (RestringirMovimiento) {
        map = L.map("mainId", {
            minZoom: ZoomMin,
            maxZoom: ZoomMax,
            maxBounds: [
                [Bottom, Right],
                [Top, Left]
            ],
            zoomControl: false
        }).setView(latlng, ZoomInicial);
    } else {
        map = L.map("mainId", {
            minZoom: ZoomMin,
            maxZoom: ZoomMax,
            zoomControl: false
        }).setView(latlng, ZoomInicial);
    }

    map.attributionControl
        .addAttribution("<img src=\"images/z" + map.getZoom() + ".png\" id=\"imgZoom\">&copy; Gerencia de Operaci&oacute;n y Mantenimiento - <span class=\"resaltar-text\">Usuario: " + usr + "</span>")
        .setPrefix(false);

    var Logout = L.Control.extend({
        options: {
            position: "bottomright"
        },

        onAdd: function () {//map) {
            var container = L.DomUtil.create("div", "logout-control");
            $(container).attr("id", "logout-control");
            $(container)
                //.addClass('leaflet-control-layers')
                .html($("#logout").html());
            $("#logout").html("");
            return container;
        }
    });
    map.addControl(new Logout());
    $("#logout-button").button({
        icons: {
            primary: "ui-icon-power"
        },
        text: false
    });
    $("#logout-control")
        .hover(
            function () {
                $("#logout-button").button("option", "text", true);
            },
            function () {
                $("#logout-button").button("option", "text", false);
            }
        );

    var MyNav = L.Control.extend({
        options: {
            position: "topright"
        },

        onAdd: function () {//map) {
            var container = L.DomUtil.create("div", "my-nav-control");
            $(container).attr("id", "my-nav-control");
            $(container).addClass("leaflet-control-layers").html($("#herramientas").html());
            $("#herramientas").html("");
            return container;
        }
    });
    map.addControl(new MyNav());
    $("#recargar").button({
        icons: {
            primary: "ui-icon-refresh"
        },
        text: false
    });
    $("#show-earth").button().click(function () {
        ShowEarth();
        checkbox($(this));
    });

    var Config = L.Control.extend({
        options: {
            position: "topright"
        },

        onAdd: function () {//map) {
            var container = L.DomUtil.create("div", "config-control");
            $(container).attr("id", "config-control");
            $(container)
                .html($("#configuracion").html());
            $("#configuracion").html("");
            return container;
        }
    });
    map.addControl(new Config());

    L.control.zoom({
        position: "topright"
    }).addTo(map);

    var CSearchControl = L.Control.extend({
        options: {
            position: "topleft"
        },
        onAdd: function () {//map) {
            var container = L.DomUtil.create("div", "my-search-control");
            $(container).attr("id", "my-search-control")
                .addClass("leaflet-control-layers")
                .html($("#search").html());
            $("#search").html("");
            return container;
        },
        onRemove: function () {//map) {
            $("#search").html($(this.getContainer()).html());
        }
    });
    searchControl = new CSearchControl();
    searchControl.addTo(map);
    $("#search_text").tooltip();
    $("#search_button").button({
        icons: {
            primary: "ui-icon-search"
        },
        text: false
    });

    var CMasivasControl = L.Control.extend({
        options: {
            position: "topleft"
        },
        onAdd: function () {//map) {
            var container = L.DomUtil.create("div", "my-masivas-control");
            $(container).attr("id", "my-masivas-control")
                //.addClass('leaflet-control-layers')
                .html($("#masivas").html());
            $("#masivas").html("");
            return container;
        },
        onRemove: function () {//map) {
            $("#masivas").html($(this.getContainer()).html());
        }
    });
    masivasControl = new CMasivasControl();
    masivasControl.addTo(map);

    if (mapas.indexOf("http") == -1) {
        tiles = L.tileLayer("https://" + server + ":" + puerto + ruta + mapas).addTo(map);
    } else {
        tiles = L.tileLayer(mapas).addTo(map);
    }
    

    Icon1 = L.icon({
        iconUrl: "./images/marker1.png",
        iconSize: [20, 20],
        iconAnchor: [10, 20],
        popupAnchor: [-0, -20],
        shadowUrl: "./images/marker_s.png",
        shadowSize: [20, 20],
        shadowAnchor: [3, 20]
    });

    Icon2 = L.icon({
        iconUrl: "./images/marker2.png",
        iconSize: [20, 20],
        iconAnchor: [10, 20],
        popupAnchor: [-0, -20],
        shadowUrl: "./images/marker_s.png",
        shadowSize: [20, 20],
        shadowAnchor: [3, 20]
    });

    Icon3 = L.icon({
        iconUrl: "./images/marker3.png",
        iconSize: [20, 20],
        iconAnchor: [10, 20],
        popupAnchor: [-0, -20],
        shadowUrl: "./images/marker_s.png",
        shadowSize: [20, 20],
        shadowAnchor: [3, 20]
    });

    Icon4 = L.icon({
        iconUrl: "./images/marker4.png",
        iconSize: [20, 20],
        iconAnchor: [10, 20],
        popupAnchor: [-0, -20],
        shadowUrl: "./images/marker_s.png",
        shadowSize: [20, 20],
        shadowAnchor: [3, 20]
    });

    IconMark = L.icon({
        iconUrl: "./images/marker.png",
        iconSize: [20, 20],
        iconAnchor: [10, 20],
        popupAnchor: [-0, -20],
        shadowUrl: "./images/marker_s.png",
        shadowSize: [20, 20],
        shadowAnchor: [3, 20]
    });

    marcadores = L.layerGroup();
    marcadores2 = L.layerGroup();
    marcadores.addTo(map);
    marcadores2.addTo(map);

    map.on("viewreset", function () {
        $("#imgZoom").attr("src", "images/z" + map.getZoom() + ".png");
    });

    $dialog_esperar = $("#dialog_esperar").dialog({
        title: "Espere...",
        resizable: false,
        width: 250,
        height: 100,
        position: ["center", "center"], 
        autoOpen: false,
        modal: false,
        dialogClass: "dialogWithDropShadow"
    });

    $dialog_config = $("#dialog-config").dialog({
        dialogClass: "dialogWithDropShadow",													
        title: "Configuracion",
        resizable: false,
        width: 440,
        position: ["center", "center"], 
        autoOpen: false,
        modal: true
    });

    $dialog_resultados = $("#dialog-resultados").dialog({
        dialogClass: "dialogWithDropShadow",													
        title: "Informacion",
        resizable: false,
        width: 640,
        position: ["center", "center"], 
        autoOpen: false,
        modal: true
    });

    $(document.formSearch).on("submit", function (e) {
        e.preventDefault(); // <-- important

        texto = $("#search_text").val();
        if (texto != "") {
            coord = texto.match(/[-|+]*\d+[\.\d+]*/g);
            if (coord.length == 2) {
                point_latlng = coord[0] + "," + coord[1];
                latlng = L.latLng(coord[0], coord[1]);
                map.setView(latlng, 15);
                CargarAfectaciones();
            } else {
                alert("Formato incorrecto.");
            }
        } else {
            point_latlng = "";
            CargarAfectaciones();
        }
    
        return false;
    });

    CargarAfectaciones();
    if (usrg == "CCR") setTimeout(mueveCuentaAtras, 1000);
});

function CargarAfectaciones() {
    minutoActual = minutos_refresh;
    segundoActual = 0;

    marcadores.clearLayers();
    marcadores2.clearLayers();
    var url = "afectaciones.json.php?opc=" + opcActual + "&region=" + regionActual;
    $.getJSON(url,
        function (data) {
            if (data.length == 1) {
                if (typeof data[0].session_ok !== "undefined") document.location = "login.php";
            }
            map.removeLayer(marcadores);
            map.removeLayer(marcadores2);
            var nMasivas = 0;
            $("#masivas-contenido").html("");
            $("#masivas-tips").html("");
            if (data.length == 0) {
                $("#masivas-contenido").append("<div class=\"ui-widget leaflet-control-layers\" style=\"margin:1px\"><div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em; cursor:help\"><div><p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span><strong>NO HAY AFECTACION</strong></p></div></div></div>");
            }
            for (var i = 0; i < data.length; i++) {
                //if (data[i].ID_SITIO == siteIdFallaMasiva) {
                if (siteIdFallaMasiva.indexOf("[" + data[i].ID_SITIO + "]") != -1) {
                    nMasivas++;
                    if (data[i].TIPO_AFECTACION != "SOLUCIONADA") {
                        $("#masivas-contenido").append("<div class=\"ui-widget leaflet-control-layers\" style=\"margin:1px\"><div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; cursor:help\"><div class=\"masivas-tip\" rel=\"get.masiva.php?op=0&id=" + data[i].ID_AFECTACION + "\" title=\"Afectaci&oacute;n Masiva\"><p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span><strong>REGION " + data[i].REGION + "<br>" + data[i].SERVICIOS_AFECTADOS + "</strong></p></div></div></div>");
                    } else {
                        $("#masivas-contenido").append("<div class=\"ui-widget leaflet-control-layers\" style=\"margin:1px\"><div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em; cursor:help\"><div class=\"masivas-tip\" rel=\"get.masiva.php?op=1&id=" + data[i].ID_AFECTACION + "\" title=\"Afectaci&oacute;n Masiva\"><p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span><strong>REGION " + data[i].REGION + "<br>" + data[i].SERVICIOS_AFECTADOS + "</strong></p></div></div></div>");
                    }
                } else {
                    var Icon = Icon1;
                    switch (data[i].TIPO_AFECTACION) {
                    case "TOTAL":
                        Icon = Icon1;
                        break;
                    case "PARCIAL":
                        Icon = Icon2;
                        break;
                    case "SOLUCIONADA":
                        Icon = Icon3;
                        break;
                    case "SUSPENDIDO":
                        Icon = Icon4;
                        break;
                    }
                    var marca;
                    if (data[i].TIPO_AFECTACION == "TOTAL" 
                        || data[i].TIPO_AFECTACION == "PARCIAL"
                        || data[i].TIPO_AFECTACION == "SUSPENDIDO"
                    ) {
                        marca = L.marker([data[i].LATITUD, data[i].LONGITUD], {
                            icon: Icon
                        }).bindPopup(
                            "<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\"><tr class=\"header\"><td><strong>" 
                            + data[i].ID_SITIO 
                            + "</strong></td><td><strong>" 
                            + data[i].NOMBRE_SITIO 
                            + "</strong></td></tr><tr class=\"tr0\"><td>Poblacion o Area afectada:</td><td>" 
                            + data[i].POBLACION_AFECTADA 
                            + "</td></tr><tr class=\"tr1\"><td>Tecnologias Afectadas:</td><td>" 
                            + data[i].TECNOLOGIA_AFECTADA 
                            + "</td></tr><tr class=\"tr0\"><td>Causa Probable:</td><td>" 
                            + data[i].CAUSA_PROBABLE 
                            + "</td></tr><tr class=\"tr1\"><td>Servicios Afectados:</td><td>" 
                            + data[i].SERVICIOS_AFECTADOS 
                            + "</td></tr><tr class=\"tr0\"><td>Fecha Inicio:</td><td>" 
                            + data[i].TIEMPO_INICIO 
                            + "</td></tr><tr class=\"tr1\"><td>Tiempo Afectacion:</td><td>" 
                            + data[i].TIEMPO_AFECTACION 
                            + "</td></tr><tr class=\"tr0\"><td>Tiempo de Respuesta:</td><td>" 
                            + data[i].TIEMPO_RESPUESTA 
                            + "</td></tr></table>", 
                            { maxWidth: 400 }
                        );
                    } else {
                        marca = L.marker([data[i].LATITUD, data[i].LONGITUD], {
                            icon: Icon
                        }).bindPopup(
                            "<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\"><tr class=\"header\"><td><strong>"
                            + data[i].ID_SITIO 
                            + "</strong></td><td><strong>" 
                            + data[i].NOMBRE_SITIO 
                            + "</strong></td></tr><tr class=\"tr0\"><td>Poblacion:</td><td>"
                            + data[i].POBLACION_AFECTADA 
                            + "</td></tr><tr class=\"tr1\"><td>Tecnologias Afectadas:</td><td>"
                            + data[i].TECNOLOGIA_AFECTADA 
                            + "</td></tr><tr class=\"tr0\"><td>Causa Probable:</td><td>"
                            + data[i].CAUSA_PROBABLE 
                            + "</td></tr><tr class=\"tr1\"><td>Servicios Afectados:</td><td>"
                            + data[i].SERVICIOS_AFECTADOS 
                            + "</td></tr><tr class=\"tr0\"><td>Fecha Inicio:</td><td>"
                            + data[i].TIEMPO_INICIO 
                            + "</td></tr><tr class=\"tr1\"><td>Tiempo Afectacion:</td><td>"
                            + data[i].TIEMPO_AFECTACION 
                            + "</td></tr><tr class=\"tr0\"><td>Fecha Solucion:</td><td>"
                            + data[i].TIEMPO_FIN 
                            + "</td></tr><tr class=\"tr1\"><td>Solucion:</td><td>"
                            + data[i].SOLUCION 
                            + "</td></tr></table>", 
                            { maxWidth: 400 }
                        );
                    }
                    if (data[i].TIPO_AFECTACION == "TOTAL" 
                        || data[i].TIPO_AFECTACION == "SOLUCIONADA"
                        || data[i].TIPO_AFECTACION == "SUSPENDIDO"
                    ) {
                        marcadores2.addLayer(marca);
                    } else {
                        marcadores.addLayer(marca);
                    }
                }
            }

            if (point_latlng != "") {
                coord = point_latlng.split(",");
                marca = L.marker([coord[0], coord[1]], { icon: IconMark })
                    .bindPopup("<strong>" + point_latlng + "</strong>", { maxWidth:400 });
                marcadores.addLayer(marca);
                marcadores2.addLayer(marca);
            }

            map.addLayer(marcadores);
            map.addLayer(marcadores2);

            sel = $(".masivas-tip");
            if (sel.val() != undefined) sel.cluetip({
                ajaxCache: false,
                cluezIndex: 10000,
                width: 400
            });
        }
    );
}

function mueveCuentaAtras() {
    if (segundoActual > 0) {
        segundoActual--;
    } else {
        segundoActual = 59;
        if (minutoActual > 0) {
            minutoActual--;
        }
    }
    if (segundoActual == 0 && minutoActual == 0) {
        minutoActual = minutos_refresh;
        segundoActual = 0;
        CargarAfectaciones();
    }

    str_segundo = new String(segundoActual);
    if (str_segundo.length == 1)
        str_segundo = "0" + str_segundo;

    str_minuto = new String(minutoActual);
    if (str_minuto.length == 1)
        str_minuto = "0" + str_minuto;

    horaImprimible = str_minuto + ":" + str_segundo;

    document.getElementById("CuentaRegresiva").innerHTML = horaImprimible;
    setTimeout(mueveCuentaAtras, 1000);
}

function ShowEarth() {
    if (vistaActual == 1) {
        tiles.setUrl(mapas);
        vistaActual = 0;
    } else {
        tiles.setUrl(mapas_satelite);
        vistaActual = 1;
    }
}

function checkbox(cb) {
    var options;
    if (cb.prop("checked")) {
        options = {
            icons: {
                primary: "ui-icon-circle-check"
            }
        };
    } else {
        options = {
            icons: {}
        };
    }
    cb.button("option", options);
}

function AbrirConfiguracion() {
    $dialog_esperar.dialog("open");
    $dialog_config.load("user.config.php", {"random": Math.random()}, function() {
        $dialog_esperar.dialog("close");
        $dialog_config.dialog("open");
    });

}