function CMLOC_Index_Map(mapId, locations) {
	
	var $ = jQuery;
	
	this.mapElement = document.getElementById(mapId);
	this.mapElement.mapObj = this;
	this.containerElement = $(this.mapElement).parents('.cmloc-locations-archive').first();
	this.isFullscreen = false;
	
	var mapObj = this;
	
	CMLOC_Map.call(this, mapId, locations);
	if (locations.length < 2) {
		setTimeout(function() {
			if (locations.length == 0) {
				mapObj.map.panTo(new google.maps.LatLng(0,0));
				mapObj.map.setZoom(2);
			} else {
				mapObj.map.setZoom(12);
			}
		}, 500);
	}
	
	
	// Display overview path
	if (this.containerElement.data('showParamOverviewPath') == 1) {
		setTimeout(function() {
			for (var i=0; i<locations.length; i++) {
				var location = locations[i];
				if (location.path) {
					locations[i].pathPolyline = new google.maps.Polyline({
						path: google.maps.geometry.encoding.decodePath(location.path),
						strokeColor: (location.pathColor ? location.pathColor : '#3377FF'),
						opacity: 0.1,
						map: mapObj.map
					});
				}
			}
		}, 500);
	}
	
	// Display map thumbs on the routes list
	for (var i=0; i<locations.length; i++) {
		break;
		var location = locations[i];
		var image = this.containerElement.find('.cmloc-location-snippet[data-route-id='+ location.id +'] .cmloc-location-featured-image img');
		if (image.length == 1) {
			var pathParams = {weight: 3, color: location.pathColor, enc: location.path};
			var pathParamsVal = [];
			for (var name in pathParams) {
				pathParamsVal.push(name +':'+ pathParams[name]);
			}
			pathParamsVal = pathParamsVal.join('|');
			console.log(pathParamsVal);
			var url = 'https://maps.googleapis.com/maps/api/staticmap?path='+ encodeURIComponent(pathParamsVal)
				+'&size='+ image.width() +'x'+ image.height() +'&maptype=roadmap&key='+ CMLOC_Map_Settings.googleMapAppKey;
			image.attr('src', url);
		}
	}
	
	
	$('.cmloc-show-terrain input', this.containerElement).change(function(ev) {
		mapObj.map.setMapTypeId(this.checked ? google.maps.MapTypeId.TERRAIN : google.maps.MapTypeId.ROADMAP);
	});
	
	if (CMLOC_Index_Map_Settings.itemClickAction == 'tooltip') {
		$('.cmloc-location-link', this.containerElement).click(function(ev) {
			var wrapper = $(this).parents('.cmloc-location-snippet').first();
			var i = mapObj.getLocationIndexById(wrapper.data('routeId'));
			if (i !== false && typeof mapObj.locations[i] == 'object') {
				ev.stopPropagation();
				ev.preventDefault();
				mapObj.openTooltip(mapObj.locations[i]);
			}
		});
	}
	
	var fullscreen = $('<div/>', {"class":"cmloc-fullscreen"}).hide().appendTo($('body'));
	fullscreen.height($(window).height());
	$('.cmloc-map-fullscreen-btn', this.containerElement).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		mapObj.isFullscreen = true;
		jQuery('html, body').scrollTop(0);
		fullscreen.show();
		var obj = $(mapObj.mapElement);
		obj.data('height', obj.height());
		obj.height('100%');
		obj.appendTo(fullscreen);
		google.maps.event.trigger(mapObj.map, "resize");
		mapObj.center();
	});
	$(window).keydown(function(ev) { // Close fullscreen
		if (mapObj.isFullscreen && ev.keyCode == 27) {
			mapObj.isFullscreen = false;
			var obj = fullscreen.children().first();
			obj.appendTo(mapObj.containerElement.find('.cmloc-location-map-canvas-outer'));
			obj.height(obj.data('height'));
			fullscreen.hide();
			google.maps.event.trigger(mapObj.map, "resize");
			mapObj.center();
		}
	});
	
	
	$(this.mapElement).trigger('MapObject:ready');
	
	
};


CMLOC_Index_Map.prototype = Object.create(CMLOC_Map.prototype);
CMLOC_Index_Map.prototype.contructor = CMLOC_Index_Map;





CMLOC_Index_Map.prototype.addLocation = function(location, index) {
	var mapObj = this;
	if (location.full_address && !location.lat && !location.long) {
		var loc = this.findLocationByAddress(location.full_address, function(loc) {
			location.lat = loc.lat();
			location.long = loc.lng();
			CMLOC_Map.prototype.addLocation.call(mapObj, location, index);
			mapObj.createViewPointBound();
			mapObj.center();
		});
	} else {
		CMLOC_Map.prototype.addLocation.call(this, location, index);
	}
};




CMLOC_Index_Map.prototype.createMarker = function(location) {
	
	var marker = new CMLOC_Marker(this, new google.maps.LatLng(location.lat, location.long),
			   {draggable: false, style: 'cursor:pointer;', color: location.pathColor,icon: location.icon},
			   {text: CMLOC_Index_Map_Settings.showLabels=='1' ? location.name : '', style: 'cursor:pointer;'}
			 );
	
//	var marker = CMLOC_Map.prototype.createMarker.call(this, location);
	var mapObj = this;
	
//	google.maps.event.addDomListener(marker.get('container'), 'click'
	
	google.maps.event.addListener(marker, 'click', function() {
		var index = mapObj.getLocationIndexByMarker(marker);
	    if (index !== false) {
	    	var loc = mapObj.locations[index];
	    	mapObj.markerClickAction(loc);
	    }
	});
	
	return marker;
	
};

CMLOC_Index_Map.prototype.markerClickAction = function(location) {
	var action;
	if (!this.containerElement.hasClass('cmloc-business-shortcode')) {
		action = CMLOC_Index_Map_Settings.markerClickAction;
	} else {
		action = 'open';
	}
	switch (action) {
		case 'tooltip':
			this.openTooltip(location);
			break;
		default:
			window.location.href = location.permalink;
	}
};


CMLOC_Index_Map.prototype.openTooltip = function(location) {
	var infowindow = new google.maps.InfoWindow({
		content: location.infowindow,
	});
	infowindow.setZIndex(99999);
	infowindow.open(this.map, location.marker);
};
