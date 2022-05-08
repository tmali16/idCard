//Установка-извлечение свойств, событий карты

/**
 * Устанавливает вид карты
 * @param {float} lat       широта
 * @param {float} lon       долгота
 * @param {int} zoom 		масштаб
 */
function SetMapView(lat, lon, zoom)
{
	if (!(map == null)) {
		map.setView(new L.LatLng(lat, lon), zoom);
	}
}

/**
 * Вписать карту в рамку с координатами
 * @param {float} southB юг
 * @param {float} westB  запад
 * @param {float} northB север
 * @param {float} eastB  восток
 */
function FitMapBounds(southB, westB, northB, eastB)
{
	if (!(map == null)) {OpenPopups
		map.fitBounds(new L.LatLngBounds([southB, westB], [northB, eastB]),
			padding = new L.point(5,5),
			maxZoom = 14
		);
	}
}

/**
 * Возвращает масштаб карты
 */
function GetZoom()
{	
	if (!(map == null)) {
		var zoom = map.getZoom();
		if (!(zoom == null)) return zoom;	
	}
	return 1;	//default
}

/**
 * Возвращает широту центра карты
 */
function GetLat()
{
	if (!(map == null)) {
		return map.getCenter().lat;
	}
	return 0;	//default
}

/**
 * Возвращает долготу центра карты
 */
function GetLon()
{
	if (!(map == null)) {
		return map.getCenter().lng;
	}
	return 0;	//default
}

/**
 * Возвращает периметер карты в формате WKT
 */
function GetMapBoundsWkt()
{
    var bounds_wkt = "";
    if (!(map == null)) {
		var south = map.getBounds().getSouth().toString();
        var north = map.getBounds().getNorth().toString();
        var east = map.getBounds().getEast().toString();
        var west = map.getBounds().getWest().toString();
        var bounds_wkt = "POLYGON((" + 
            west + " " + north + "," +
            west + " " + south + "," +
            east + " " + south + "," +
            east + " " + north + "," +
            west + " " + north +
            "))";
	}
	return bounds_wkt;
}

//----------Markers SELECTION------------

/**
 * Выделяет маркер
 * @param {int} routeID id маршрута
 * @param {str} geoID   id маркера
 */
function SelectGeoMarker(routeID, geoID)
{
	if (!(MarkerLayers[routeID] == null)) {
		var layers = MarkerLayers[routeID].getLayers();
		for (i = 0; i < layers.length; i++)
		{
			if (layers[i].feature.properties.geoID == geoID)
			{
				SetMarkerIcon(layers[i], "selected");
			}
		}
	}
}

/**
 * Убирает выделение всех маркеров
 */
function ClearGeoMarkersSelection()
{
	for (var i = 0; i < MarkerLayers.length; i++) {
		if (i in MarkerLayers && MarkerLayers[i] != null) {
			var layers = MarkerLayers[i].getLayers();
			for (var j = 0; j < layers.length; j++)
			{	
				if (layers[j].feature.properties.filtered == false)
					SetMarkerIcon(layers[j], "default");
			}
		}
	}
}

/**
 * Устанавливает иконку маркера по типу
 * @param {object} marker   объект маркера
 * @param {str} iconType    тип иконки
 */
function SetMarkerIcon(marker, iconType)
{
	switch (iconType) {
	case "default":
		var newIcon = L.icon({
					iconSize: [25, 41],
					shadowSize:   [41, 41],
					iconAnchor: [12, 41],
					shadowAnchor: [10, 41],
					popupAnchor:  [1, -24],
					iconUrl: 'leaflet/markers/' + marker.feature.properties.rang + '-' + marker.feature.properties.color + '.png',
					shadowUrl: 'leaflet/markers/marker-shadow.png'
					});
		break;
	case "selected":
		var newIcon = L.icon({
					iconSize: [25, 41],
					shadowSize:   [41, 41],
					iconAnchor: [12, 41],
					shadowAnchor: [10, 41],
					popupAnchor:  [1, -24],
					iconUrl: 'leaflet/markers/' + marker.feature.properties.rang + '-' + "yellow" + '.png',
					shadowUrl: 'leaflet/markers/marker-shadow.png'
					});
		break;
	}
	marker.setIcon(newIcon);
}
//----------Markers SELECTION-----------

//---------POPUPS-----------------------
/**
 * Обработчик события открытия подсказки
 * @param {object} e событие
 */
function PopupOpen(e) {
	SetMarkerIcon(e.target, "selected");
	var lat = e.target.feature.geometry.coordinates[1];
	var lon = e.target.feature.geometry.coordinates[0];
	var popup = e.target.feature.properties.popupContent;
	if (popup.length >= 260) {
		SetMapView(lat, lon, GetZoom());	//если подсказка слишком длинная
	}
	if (AutoPopup == false) {
		var GeoID = e.target.feature.properties.geoID;
		var RouteID = parseInt(e.target.feature.properties.routeID);
		//Выделение маркеров на временной шкале:
		//window.external.SelectTimeMarkers(RouteID, GeoID);
		window.external.OnMarkerSelected(RouteID, GeoID);
	}
}

/**
 * Обработчик события закрытия подсказки
 * @param {object} e событие
 */
function PopupClose(e)
{
	SetMarkerIcon(e.target, "default");
}

/**
 * Очищает временную шкалу
 * @param {object} e событие
 */
function ClearTimeMarkersSelection(e) {
	window.external.ClearTimeMarkersSelection();
}

/**
 * Показывает popup в точке
 * @param {str} text 		текст подсказки
 * @param {float} lat       широта
 * @param {float} lon       долгота
 */
function ShowPopup(text, lat, lon)
{
	var popup = L.popup({
		offset: L.point(1, -19),
		autoPan: false
	});
    popup.setLatLng([lat, lon]);
    popup.setContent(text);
    popup.addTo(map);
	OpenPopups.push(popup);
	SetMapView(lat, lon, GetZoom());
}

/**
 * Закрывает предыдущий popup
 */
function ClosePopup()
{
	ClearGeoMarkersSelection();
	map.closePopup();
	if (OpenPopups.length > 0) {
		for (var i = 0; i < OpenPopups.length; i++)
			map.closePopup(OpenPopups[i]);
		OpenPopups.length = 0;
	}
}

//---------POPUPS-----------------------