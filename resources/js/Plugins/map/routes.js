//Функции для отрисовки маршрутов, маркеров, БС на карте

/**
 * Отрисовка БС из JSON-объекта
 * @param {int} routeID     id маршрута для отрисовка
 * @param {int} geoID       id маркера
 * @param {float} lat       широта
 * @param {float} lon       долгота
 * @param {str} toolTipText текст подсказки
 */	
function DrawBS(routeID, geoID, lat, lon, toolTipText)
{
	if (lat != 0 && lon != 0) {
		var basestation = {
			"type": "Feature",
			"properties": {
				"name": "BS",
				"geoID": geoID,
				"routeID": routeID,
                "filtered": false,
				"popupContent": toolTipText
				//"visible": visible
			},
			"geometry": {
				"type": "Point",
				"coordinates": [lon, lat]
			}
		};
		CoverLayers[routeID].addData(basestation);	
	}	
}

/**
 * Создает и добавляет маркер БС к кластеру маркеров
 * @param {float} lat       широта
 * @param {float} lon       долгота
 * @param {str} toolTipText текст подсказки
 */
function AddBSToCluster(lat, lon, toolTipText) {
	if (lat != 0 && lon != 0) {
		var basestation = {
			"type": "Feature",
			"properties": {
				"name": "BS",
                "filtered": false,
				"popupContent": toolTipText
			},
			"geometry": {
				"type": "Point",
				"coordinates": [lon, lat]
			}
		};
		bsClusterLayer.addData(basestation);
	}	
}

/**
 * Отрисовывает БС кластер
 */
function DrawBSCluster()
{
	if (!(bsClusterLayer == null)) {
		bscluster.addLayer(bsClusterLayer);
	}
}

/**
 * Отрисовывает маркеры маршрута из JSON-объекта 
 * @param {str} sJSON   JSON-строка
 * @param {int} routeID id маршрута
 */
function DrawMarkers(sJSON, routeID)
{
	tmpLayer.bringToFront();
	//Десериализация JSON:
	var positions = JSON.parse(sJSON);
	//dprint(sJSON);
 	for (var key in positions)
	{
		if (key != "$type") {
			var pos = positions[key];
			var marker = {
					"type": "Feature",
					"properties": {
						"name": "Marker",
						"geoID": pos.GeoID,
						"routeID": pos.RouteID,
						"popupContent": pos.ToolTipText,
						"color": pos.Color,
						"rang": pos.Rang,
						"filtered": pos.Filtered,
						"selected": pos.Selected
					},
					"geometry": {
						"type": "Point",
						"coordinates": [pos.Lon, pos.Lat]
					}
				};
			MarkerLayers[routeID].addData(marker);		
		}
	}
}

/**
 * Рисует покрытие из JSON-объекта для указанного маршрута
 * @param {int} routeID  	id маршрута
 * @param {str} sJSON    	JSON-строка
 * @param {str} color    	цвет
 * @param {float} opacity  	видимость (0 - 1)
 * @param {bool} filtered 	отфильтровано ли
 */
function DrawCovering(routeID, sJSON, color, opacity, filtered)
{
	if (!sJSON || sJSON == "") 
		return;

	var rawCover = JSON.parse(sJSON);
	if (!(rawCover.type == "MultiPolygon" ||  rawCover.type == "Polygon"))
		return;
	var cover;
	if (rawCover.type == "MultiPolygon") {
		cover = {
			"type": "Feature",
			"properties": {
				"name": "Cover",
				"color": color,
                "opacity": opacity,
				"filtered": filtered
			},
			"geometry": {
				"type": "MultiPolygon",
				"coordinates": rawCover.coordinates
			}
		};
	} else if (rawCover.type == "Polygon") {
		cover = {
			"type": "Feature",
			"properties": {
				"name": "Cover",
				"color": color,
                "opacity": opacity,
				"filtered": filtered
			},
			"geometry": {
				"type": "Polygon",
				"coordinates": rawCover.coordinates
			}
		};
	}
	CoverLayers[routeID].addData(cover);
}

/**
 * Отрисовывает маршрут (линии) полученный из сериализованного json объекта
 * @param {str} sJSON   JSON-строка
 * @param {int} routeID id маршрута для отрисовки
 */
function DrawRoute(sJSON, routeID)
{
	var lastPos;
	//Десериализация JSON:
	var positions = JSON.parse(sJSON);
	//dprint(sJSON);
 	for (var key in positions)
	{
		if (key != "$type") {
			var pos = positions[key];
			//dprint(key + " " + pos.GeoID);
			if ( !(typeof(lastPos) == "undefined")) {
				var route = {
					"type": "Feature",
					"properties": {
						"name": "Line",
						"color": ColorHexes[pos.Color],
						"filtered": pos.Filtered
					},
					"geometry": {
						"type": "LineString",
						"coordinates": [[lastPos.Lon, lastPos.Lat],[pos.Lon, pos.Lat]]
					}
				};
				RouteLayers[routeID].addData(route);
			}
			lastPos = pos;
			pos = null;
			route = null;
			
		}
	}
	positions = null;
}
