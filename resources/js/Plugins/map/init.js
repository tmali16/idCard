//Инициализация карты

var debugMode = false;		//set this to true if you want debugMode:
//--------------------
var map;
var osm;
var tmpLayer;
var SendRect = false;
var SendCircle = false;
var SelectionStarted = false;
var LayersControl;
var CurZoom;
var Shape;
var StartPoint;
var EndPoint;
var Radius = 0.0;
var SendMapLoaded = false;
var TileLoadTime;

var bscluster = null;			//кластер БС
var bsClusterLayer = null;		//слой для отображения запроса по БС на текущем масштабе
var MarkerLayers;
var RouteLayers;
var CoverLayers;
var OpenPopups;
var AutoPopup = false;
//набор связок имени цвета и его hex кода:
var ColorHexes = {};
ColorHexes["blue"] = "blue";
ColorHexes["red"] = "red";
ColorHexes["green"] = "green";
ColorHexes["orange"] = "#ff8c00";
ColorHexes["brown"] = "#a52a2a";
ColorHexes["purple"] = "#8a2be2";
ColorHexes["gray"] = "#808080";
ColorHexes["lblue"] = "#30d5c8";
ColorHexes["dgreen"] = "#014421";
ColorHexes["dred"] = "#7b1113";
ColorHexes["yellow"] = "#ffe135";
ColorHexes["black"] = "#000000";

/**
 * Инициализация карты
 */
function initmap() {
	map = new L.Map('map', {
		attributionControl: false
	});
	//map.setView(new L.LatLng(59.93, 30.31), 10);		// Saint-Petersburg
	map.setView(new L.LatLng(66.416667, 94.25), 10);	// Center of Russia

	//Шкала масштаба:
	L.control.scale({
		imperial: false,
		position: 'bottomright'
	}).addTo(map);

	// L.Control.measureControl().addTo(map);

	map.on('popupclose', function(e) {
		ClearGeoMarkersSelection();
		ClearTimeMarkersSelection();
	});	//очистка выделения на шкале времени при закртии подсказок на карте

	//Обработка выделений для биллинга на карте:
	map.on("mousedown", function(e) {
		if ((SendCircle == true || SendRect == true) && e.originalEvent.shiftKey) {
			SelectionStarted = true;
			map.dragging.disable();
			StartPoint = e.latlng;
		}
	});

	map.on("mousemove", function(e) {
		if (SelectionStarted == true && e.originalEvent.shiftKey) {
			if (Shape != undefined) {
				map.removeLayer(Shape);
			}
			EndPoint = e.latlng;
			if (SendCircle == true) {
				Radius = EndPoint.distanceTo(StartPoint);
				Shape = L.circle(StartPoint, Radius).addTo(map);
			} else if (SendRect == true) {
				Shape = L.rectangle([StartPoint, EndPoint]).addTo(map);
			}
		}
	});

	map.on("mouseup", function(e) {
		if (SelectionStarted == true && Shape != undefined) {
			if (SendCircle == true) {
				window.external.TranslateCircle(StartPoint.lat, StartPoint.lng, Radius);
				SendCircle = false;
			} else if (SendRect == true) {
				var Bounds = Shape.getBounds();
				window.external.TranslateBounds(Bounds.getNorth(), Bounds.getSouth(), Bounds.getWest(), Bounds.getEast());
				SendRect = false;
			}
			map.removeLayer(Shape);
			SelectionStarted = false;
			map.dragging.enable();
			map.boxZoom.enable();
		}
	});

	MarkerLayers = new Array();
	RouteLayers = new Array();
	CoverLayers = new Array();
	OpenPopups = new Array();

}

//Для отправки выделенной области при биллинге:
/**
 * Посылать координаты выделенной области
 * @param {bool} value да/нет
 */
function EnableSendCoords(value)
{
	SendRect = value;
	map.boxZoom.disable();
}
/**
 * Посылать координаты выделенного круга
 * @param {bool} value да/нет
 */
function EnableSendCircle(value)
{
	SendCircle = value;
	map.boxZoom.disable();
}
//======================================

/**
 * Посылать оповещение о загрузке карты
 * @param {bool} value да/нет
 */
function EnableSendMapLoaded(value)
{
	SendMapLoaded = value;
}

/**
 * Перерисовать тайлы карты
 */
function RedrawTiles()
{
	osm.redraw();
}

/**
 * Установить ip-сервера карт
 * @param {str} ip сервера
 */
function InitMapServer(ip) {
	if (!(document.all && !document.querySelector)) {		//проверка на IE8+
		if ( !(typeof(osm) == "undefined") ) {
			map.removeLayer(osm);
		}


		//Вариации слоев для карты:
		// online osm:
		//var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		//var osmAttrib='&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>';
		//osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 17, attribution: osmAttrib});

		//For local tiles:
		//osm = L.tileLayer('file:///C:/tiles/{z}/{x}/{y}.png', { maxZoom: 13 });

		//Тайлы из папки:
		//osm = L.tileLayer('http://172.16.60.58/tilecache/Cache/osm/{z}/{x}/{y}.png', { maxZoom: 14 });

		//var tmpLayer = L.tileLayer('file:///C:/tiles/{z}/{x}/{y}.png', { maxZoom: 17 }).addTo(map);

		//Временный слой для гладкости переходов:
		tmpLayer = L.tileLayer.wms("http://" + ip + "/tilecache/tilecache.py", {
		layers: 'osm',
		format: 'image/png',
		transparent: true,
		attribution: " ",
		maxZoom: 17
		}).addTo(map);

		//наш IIS сервер:
		osm = L.tileLayer.wms("http://" + ip + "/tilecache/tilecache.py", {
		layers: 'osm',
		format: 'image/png',
		transparent: true,
		attribution: " ",
		maxZoom: 17
		});

		osm.on("loading", function(e) {
			//tmpLayer.bringToFront();
			if (debugMode) TileLoadTime = new Date();
		});

		osm.on("load", function(e) {
			osm.bringToFront();
			if (SendMapLoaded == true) {
				window.external.MapLoadedHandler();
				SendMapLoaded = false;
			}
			if (debugMode) {
				TileLoadTime = new Date() - TileLoadTime;
				tprint(TileLoadTime);
				updateTestCoords();
			}
		});
		map.addLayer(osm);
	}
}

/**
 * Добавить контрол слоев выбранного маршрута на карту
 * @param {int} routeID id маршрута
 */
function CreateLayersControl(routeID)
{
	LayersControl = L.control.layers().addTo(map);
	LayersControl.addOverlay(MarkerLayers[routeID], "Маркеры");
	LayersControl.addOverlay(RouteLayers[routeID], "Маршрут");
	LayersControl.addOverlay(CoverLayers[routeID], "Покрытия");
}

function dprint(log)
{
	var message = "";
	var elem = document.getElementById('test');

	if (log == null) message = "null";
	else message = log;

	if (elem.innerHTML.length > 0)
	{
		elem.innerHTML = elem.innerHTML + "<br>";
	}
	elem.innerHTML = elem.innerHTML + message;
}
