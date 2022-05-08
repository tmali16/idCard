//Функции для создания-очистки слоев, кластеров карты

/**
 * Очистка карты полностью
 */
function ClearMap()
{
	//dprint(">>>ClearMapStarted");
	ClosePopup();
	for (var i = 0; i < MarkerLayers.length; ++i) {
		if (MarkerLayers[i] != null) {
			//dprint("Delete i = " + i);
			map.removeLayer(MarkerLayers[i]);
			MarkerLayers[i]._layers = null;
			MarkerLayers[i].options = null;
			MarkerLayers[i] = null;
		}
		if (RouteLayers[i] != null) {
			map.removeLayer(RouteLayers[i]);
			RouteLayers[i]._layers = null;
			RouteLayers[i].options = null;
			RouteLayers[i] = null;
		}
		if (CoverLayers[i] != null) {
			map.removeLayer(CoverLayers[i]);
			CoverLayers[i]._layers = null;
			CoverLayers[i].options = null;
			CoverLayers[i] = null;
		}
	}

	if (!(LayersControl == null)) {
		map.removeControl(LayersControl);
		LayersControl = null;				//removeControl() не обнуляет переменную!!!
	}
    DeleteBSCluster();
	MarkerLayers.length = 0;
	RouteLayers.length = 0;
	CoverLayers.length = 0;
}

/**
 * Очищает слой покрытий маршрута
 * @param {int} routeID id маршрута
 */
function ClearCoverLayer(routeID)
{
	if (routeID in CoverLayers && CoverLayers[routeID] != null) {
		if (!(LayersControl == null)) LayersControl.removeLayer(CoverLayers[routeID]);
		map.removeLayer(CoverLayers[routeID]);
		CoverLayers[routeID] = null;
	}
    CreateNewLayer(routeID, "cover");
    if (!(LayersControl == null)) LayersControl.addOverlay(CoverLayers[routeID], "Покрытия");
}

/**
 * Создает слой маркеров, путей или покрытий для нового маршрута
 * @param {int} routeID   id маршрута
 * @param {str} layerType тип слоя
 */
function CreateNewLayer(routeID, layerType)
{
	switch (layerType) {
	case "marker":
		if (routeID in MarkerLayers && MarkerLayers[routeID] != null)
			return; 	
		MarkerLayers[routeID] = L.geoJson(undefined, {
			style: function (feature) {
				return {color: feature.properties.color};
			},
			onEachFeature: function (feature, layer) {
				if (feature.properties.popupContent && feature.properties.filtered == false) {
					layer.bindPopup(feature.properties.popupContent);
				}
				layer.on({
					popupopen: PopupOpen,	//выделять на маркеры на временной шкале
					popupclose: PopupClose
				});
			},
			pointToLayer: function (feature, latlng) {
				//For filtering:
				var newIcon;
				if (feature.properties.filtered == true) {
					newIcon = L.icon({
						iconSize: [25, 41],
						shadowSize:   [41, 41],
						iconAnchor: [12, 41],
						shadowAnchor: [10, 41],
						popupAnchor:  [1, -24],
						iconUrl: 'leaflet/markers/' + feature.properties.rang + '-' + 'filtered.png',
					});
				}
				else {
					if (feature.properties.selected == true) 
					{
						newIcon = L.icon({
								iconSize: [25, 41],
								shadowSize:   [41, 41],
								iconAnchor: [12, 41],
								shadowAnchor: [10, 41],
								popupAnchor:  [1, -24],
								iconUrl: 'leaflet/markers/' + feature.properties.rang + '-' + "yellow" + '.png',
								shadowUrl: 'leaflet/markers/marker-shadow.png'
						});
					}	
					else {
						newIcon = L.icon({
								iconSize: [25, 41],
								shadowSize:   [41, 41],
								iconAnchor: [12, 41],
								shadowAnchor: [10, 41],
								popupAnchor:  [1, -24],
								iconUrl: 'leaflet/markers/' + feature.properties.rang + '-' + feature.properties.color + '.png',
								shadowUrl: 'leaflet/markers/marker-shadow.png'
						});
					}
				}
				return L.marker(latlng, {icon: newIcon});
			}
		}).addTo(map);
		break;
	case "route":
		if (routeID in RouteLayers && RouteLayers[routeID] != null)
			return;
		RouteLayers[routeID] = L.geoJson(undefined, {
			style: function (feature) {
				if (feature.properties.filtered == true) {
						var NewColor = "gray";
						var NewOpacity = 0.4;
				} else {
						var NewColor = feature.properties.color;
						var NewOpacity = 0.6;
                }
				return {
					color: NewColor,
					opacity: NewOpacity
					};
			}
		}).addTo(map);
		break;
	case "cover":
		if (routeID in CoverLayers && CoverLayers[routeID] != null)
			return;
		CoverLayers[routeID] = L.geoJson(undefined, {
			style: function (feature) {
				if (feature.properties.name == "Cover") {
					if (feature.properties.filtered == true) {
							var NewColor = "gray";
							var NewOpacity = 0.2;
					} else {
							var NewColor = feature.properties.color;
							var NewOpacity = feature.properties.opacity;
					}
					return {
						color: NewColor,
						opacity: NewOpacity,
						weight: 2
					};
				}
				return {};
			},
            onEachFeature: function (feature, layer) {
				if (feature.properties.popupContent && feature.properties.filtered == false) {
					layer.bindPopup(feature.properties.popupContent);
				}
			},
			pointToLayer: function (feature, latlng) {
				if (feature.properties.name == "BS") {
					var newIcon = L.icon({
							iconSize: [48, 48],
							iconAnchor: [24, 48],
							popupAnchor:  [-2, -24],
							iconUrl: 'leaflet/markers/antenna.png',
					});
					return L.marker(latlng, {icon: newIcon});
				}
				return L.marker(latlng);
			}
		}).addTo(map);
		break;
	}
	//alert("Layer " + layerType + " created!");
}

/**
 * Создает все слои для нового маршрута
 * @param {int} routeID id нового маршрута
 */
function CreateRouteLayers(routeID)
{
    CreateNewLayer(routeID, "route");
    CreateNewLayer(routeID, "marker");
    CreateNewLayer(routeID, "cover");
}

/**
 * Удаляет все слои маршрута
 * @param {int} routeID id маршрута
 */
function DeleteRouteLayers(routeID)
{
	//dprint("Delete rID = " + routeID);
	ClosePopup();
	if (routeID in MarkerLayers && MarkerLayers[routeID] != null) {
		map.removeLayer(MarkerLayers[routeID]);
		MarkerLayers[routeID] = null;
	}
	if (routeID in RouteLayers && RouteLayers[routeID] != null) {
		map.removeLayer(RouteLayers[routeID]);
		RouteLayers[routeID] = null;
	}
	if (routeID in CoverLayers && CoverLayers[routeID] != null) {
		map.removeLayer(CoverLayers[routeID]);
		CoverLayers[routeID] = null;
	}
}

/**
 * Создает кластер БС
 */
function CreateBSCluster() {
    DeleteBSCluster();
    bscluster = L.markerClusterGroup();
	map.addLayer(bscluster)
    bsClusterLayer = L.geoJson(undefined, {
            onEachFeature: function (feature, layer) {
				if (feature.properties.popupContent && feature.properties.filtered == false) {
					layer.bindPopup(feature.properties.popupContent);
				}
			},
			pointToLayer: function (feature, latlng) {
				if (feature.properties.name == "BS") {
					var newIcon = L.icon({
							iconSize: [48, 48],
							iconAnchor: [24, 48],
							popupAnchor:  [-2, -24],
							iconUrl: 'leaflet/markers/antenna.png',
					});
					return L.marker(latlng, {icon: newIcon});
				}
				return L.marker(latlng);
			}
		});
}

/**
 * Удаляет кластер БС
 */
function DeleteBSCluster() {
    if (!(bscluster == null)) {    
        if (!(bsClusterLayer == null)) {
            bscluster.removeLayer(bsClusterLayer);
            bsClusterLayer = null;
        }
        map.removeLayer(bscluster);
        bscluster = null;
    }    
}

