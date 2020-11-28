// JavaScript Document

var map;
var marcadores = [];
var opcActual = 0;
var minutoActual = minutos_refresh;
var segundoActual = 0;
var mapaIniciado = false;
var Icon1;
var Icon2;
var Icon3;

var $dialog_esperar;
var $dialog_config;
var $dialog_resultados;

$(document).ready(function () {
    //initMap();
});

function TerminarCarga() {
    if (server == "::1") server = "localhost";

    $("#recargar").button({ icons: { primary: "ui-icon-refresh" }, text: false });
    $("#logout-button").button({ icons: { primary: "ui-icon-power" }, text: false });
    $("#logout-div")
        .hover(
            function () {
                $("#logout-button").button('option', 'text', true);
            },
            function () {
                $("#logout-button").button('option', 'text', false);
            }
        );
}

function initMap() {
    TerminarCarga();

    var lat, lng;
    coordInicial = coordenadaInicial.split(",");
    if (coordInicial.length != 2 || regionActual == 0) {
        // Coordenadas incorrectas o Region 0
        lat = 24.291024;
        lng = -100.988266;
    } else {
        lat = coordInicial[0].valueOf();
        lng = coordInicial[1].valueOf();
    }

    map = new google.maps.Map(document.getElementById('map'), {
        center: new google.maps.LatLng(lat, lng),
        zoom: 6,
        zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_TOP },
        scaleControl: true
    });

    Icon1 = {
        url: './images/marker1_gmaps.png',
        anchor: new google.maps.Point(8, 20)
    };
    Icon2 = {
        url: './images/marker2_gmaps.png',
        anchor: new google.maps.Point(8, 20)
    };
    Icon3 = {
        url: './images/marker3_gmaps.png',
        anchor: new google.maps.Point(8, 20)
    };
    Icon4 = {
        url: './images/marker4_gmaps.png',
        anchor: new google.maps.Point(8, 20)
    };
    Icon0 = {
        url: './images/marker-icon.png',
        anchor: new google.maps.Point(13, 41)
    };


    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    map.addListener('bounds_changed', function () {
        searchBox.setBounds(map.getBounds());
    });

    var markers = [];
    // [START region_getplaces]
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener('places_changed', function () {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // Clear out the old markers.
        markers.forEach(function (marker) {
            marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function (place) {
            var icon = {
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
                map: map,
                icon: icon,
                title: place.name,
                position: place.geometry.location
            }));

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });
    // [END region_getplaces]

    CreateControl("herramientas", map, google.maps.ControlPosition.TOP_RIGHT);
    CreateControl("configuracion", map, google.maps.ControlPosition.RIGHT_TOP);
    CreateControl("logout-div", map, google.maps.ControlPosition.RIGHT_BOTTOM);
    CreateControl("masivas", map, google.maps.ControlPosition.LEFT_TOP);

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

    CargarAfectaciones();
    if (usrg == "CCR") setTimeout('mueveCuentaAtras()', 1000);
}

function CreateControl(fromId, map, position) {
    var controlDiv = document.createElement('div');
    var controlUI = document.createElement('div');
    document.getElementById(fromId).style.display = "block";
    controlUI.appendChild(document.getElementById(fromId));
    controlDiv.appendChild(controlUI);

    controlDiv.index = 1;
    map.controls[position].push(controlDiv);
}

function mueveCuentaAtras() {
    if (segundoActual > 0) {
        segundoActual--;
    }
    else {
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

    //document.getElementById("CuentaRegresiva").innerHTML = horaImprimible;
    $("#CuentaRegresiva").html(horaImprimible);
    setTimeout('mueveCuentaAtras()', 1000);
}

function CargarAfectaciones() {
    minutoActual = minutos_refresh;
    segundoActual = 0;

    if ($("#masivas-contenido").html() == undefined) {
        setTimeout('CargarAfectaciones()', 1000);
        return;
    }

    var url = 'afectaciones.json.php?opc=' + opcActual + '&region=' + regionActual;
    $.getJSON(url,
        function (data) {
            if (data.length == 1) {
                if (typeof data[0].session_ok !== 'undefined') document.location = "login.php";
            }
            deleteMarkers();

            var nMasivas = 0;
            $("#masivas-contenido").html("");
            $("#masivas-tips").html("");
            if (data.length == 0) {
                $("#masivas-contenido").append('<div class="ui-widget afectacion-masiva"><div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; cursor:help"><div><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>NO HAY AFECTACION</strong></p></div></div></div>');
            }
            for (var i = 0; i < data.length; i++) {
                if (siteIdFallaMasiva.indexOf("[" + data[i].ID_SITIO + "]") != -1) {
                    nMasivas++;
                    if (data[i].TIPO_AFECTACION != "SOLUCIONADA") {
                        $("#masivas-contenido").append('<div class="ui-widget afectacion-masiva"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em; cursor:help"><div class="masivas-tip" rel="get.masiva.php?op=0&id=' + data[i].ID_AFECTACION + '" title="Afectaci&oacute;n Masiva"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>REGION ' + data[i].REGION + "<br>" + data[i].SERVICIOS_AFECTADOS + '</strong></p></div></div></div>');
                    } else {
                        $("#masivas-contenido").append('<div class="ui-widget afectacion-masiva"><div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; cursor:help"><div class="masivas-tip" rel="get.masiva.php?op=1&id=' + data[i].ID_AFECTACION + '" title="Afectaci&oacute;n Masiva"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>REGION ' + data[i].REGION + "<br>" + data[i].SERVICIOS_AFECTADOS + '</strong></p></div></div></div>');
                    }
                } else {
                    var marca;
                    var Icon = Icon1;
                    var zIndex = 0;
                    var pos = new google.maps.LatLng(data[i].LATITUD, data[i].LONGITUD);
                    var infowindow;
                    switch (data[i].TIPO_AFECTACION) {
                    case "TOTAL":
                        Icon = Icon1;
                        zIndex = 2;
                        break;
                    case "PARCIAL":
                        Icon = Icon2;
                        zIndex = 1;
                        break;
                    case "SOLUCIONADA":
                        Icon = Icon3;
                        break;
                    case "SUSPENDIDO":
                        Icon = Icon4;
                        break;
                    }
                    if (data[i].TIPO_AFECTACION == "TOTAL" || data[i].TIPO_AFECTACION == "PARCIAL") {
                        var contentString = '<table border="0" cellspacing="1" cellpadding="1"><tr class="header"><td><strong>' + data[i].ID_SITIO + '</strong></td><td><strong>' + data[i].NOMBRE_SITIO + '</strong></td></tr><tr class="tr0"><td>Poblacion o Area afectada:</td><td>' + data[i].POBLACION_AFECTADA + '</td></tr><tr class="tr1"><td>Tecnologias Afectadas:</td><td>' + data[i].TECNOLOGIA_AFECTADA + '</td></tr><tr class="tr0"><td>Causa Probable:</td><td>' + data[i].CAUSA_PROBABLE + '</td></tr><tr class="tr1"><td>Servicios Afectados:</td><td>' + data[i].SERVICIOS_AFECTADOS + '</td></tr><tr class="tr0"><td>Fecha Inicio:</td><td>' + data[i].TIEMPO_INICIO + '</td></tr><tr class="tr1"><td>Tiempo Afectacion:</td><td>' + data[i].TIEMPO_AFECTACION + '</td></tr><tr class="tr0"><td>Tiempo de Respuesta:</td><td>' + data[i].TIEMPO_RESPUESTA + '</td></tr></table>';
                        infowindow = new google.maps.InfoWindow({ content: contentString });
                        marca = new google.maps.Marker({
                            position: pos,
                            map: map,
                            icon: Icon,
                            zIndex: zIndex,
                            title: data[i].ID_SITIO + ' - ' + data[i].NOMBRE_SITIO,
                            infoWindow: infowindow
                        });
                    } else {
                        var contentString = '<table border="0" cellspacing="1" cellpadding="1"><tr class="header"><td><strong>' + data[i].ID_SITIO + '</strong></td><td><strong>' + data[i].NOMBRE_SITIO + '</strong></td></tr><tr class="tr0"><td>Poblacion:</td><td>' + data[i].POBLACION_AFECTADA + '</td></tr><tr class="tr1"><td>Tecnologias Afectadas:</td><td>' + data[i].TECNOLOGIA_AFECTADA + '</td></tr><tr class="tr0"><td>Causa Probable:</td><td>' + data[i].CAUSA_PROBABLE + '</td></tr><tr class="tr1"><td>Servicios Afectados:</td><td>' + data[i].SERVICIOS_AFECTADOS + '</td></tr><tr class="tr0"><td>Fecha Inicio:</td><td>' + data[i].TIEMPO_INICIO + '</td></tr><tr class="tr1"><td>Tiempo Afectacion:</td><td>' + data[i].TIEMPO_AFECTACION + '</td></tr><tr class="tr0"><td>Fecha Solucion:</td><td>' + data[i].TIEMPO_FIN + '</td></tr><tr class="tr1"><td>Solucion:</td><td>' + data[i].SOLUCION + '</td></tr></table>';
                        infowindow = new google.maps.InfoWindow({ content: contentString });
                        marca = new google.maps.Marker({
                            position: pos,
                            map: map,
                            icon: Icon,
                            title: data[i].ID_SITIO + ' - ' + data[i].NOMBRE_SITIO,
                            infoWindow: infowindow
                        });
                    }
                    marca.addListener('click', function () { this.infoWindow.open(map, this); });
                    marcadores.push(marca);
                }
            }
            setMapOnAll(map);

            if ($("#masivas-contenido").html() != "")
                $("#masivas").show();
            else
                $("#masivas").hide();
            sel = $(".masivas-tip");
            if (sel.val() != undefined) sel.cluetip({ ajaxCache: false, cluezIndex: 10000, width: 400 });
        }
    );
}

function setMapOnAll(map) {
    for (var i = 0; i < marcadores.length; i++) {
        marcadores[i].setMap(map);
    }
}

function clearMarkers() {
    setMapOnAll(null);
}

function deleteMarkers() {
    clearMarkers();
    marcadores = [];
}

function AbrirConfiguracion() {
    $dialog_esperar.dialog("open");
    $dialog_config.load("user.config.php", { "random": Math.random() }, function () {
        $dialog_esperar.dialog("close");
        $dialog_config.dialog("open");
    });

}
