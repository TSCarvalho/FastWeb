Number.prototype.toRadians = function() {
   return this * Math.PI / 180;
}

Number.prototype.toDegrees = function() {
	   return this * 180 /  Math.PI;
}


function CMLOC_Map(mapId, locations) {
	
	this.mapId = mapId;
	this.map = new google.maps.Map(this.mapElement);
	this.map.setMapTypeId(CMLOC_Map_Settings.mapType);
	this.locations = [];
	this.directionsService = this.createDirectionsService();
	this.directionsDisplay = this.createDirectionsRenderer();
	this.elevationService = new google.maps.ElevationService();
	this.trailPolylines = [];
	this.trailResponse = null;
	this.totalDistance = 0;
	this.totalDuration = 0;
	this.travelMode = google.maps.TravelMode.WALKING;
	this.maxElevation = 0;
	this.minElevation = 0;
	this.elevationGain = 0;
	this.elevationDescent = 0;
	this.requestsCount = 0;
	this.geocoder = new google.maps.Geocoder;
	
	var mapObj = this;
	
	// Add locations
	for (var i=0; i<locations.length; i++) {
		this.addLocation(locations[i]);
	}
	
	this.createViewPointBound();
	setTimeout(function() {
		mapObj.center();
	}, 500);
	
};


CMLOC_Map.prototype.createDirectionsService = function() {
	return new google.maps.DirectionsService;
};


CMLOC_Map.prototype.createDirectionsRenderer = function() {
	var directionsDisplay = new google.maps.DirectionsRenderer;
	directionsDisplay.setMap(this.map);
	directionsDisplay.setOptions({suppressMarkers: true, preserveViewport: true, suppressBicyclingLayer: true, draggable: false});
	return directionsDisplay;
};


CMLOC_Map.prototype.createViewPointBound = function() {
//	console.log(this.locations);
	this.bounds = new google.maps.LatLngBounds();
	for (var i=0; i<this.locations.length; i++) {
		this.bounds.extend(new google.maps.LatLng(this.locations[i].lat, this.locations[i].long));
	}
};


CMLOC_Map.prototype.addLocation = function(location, index) {
//	location.marker = this.createMarker(location);
	if (location.type == 'location') { // Location
		location.marker = this.createMarker(location);
	} else { // Waypoint
		location.marker = this.createWaypointMarker(location);
	}
	this.pushLocation(location, index);
};


CMLOC_Map.prototype.pushLocation = function(location, index) {
	if (typeof index == 'undefined') {
		this.locations.push(location);
	} else {
		this.locations.splice(index, 0, location);
	}
};


CMLOC_Map.prototype.requestLocationWeather = function(location) {
	if (!CMLOC_Map_Settings.openweathermapAppKey) return;
	var units = ('temp_f' == CMLOC_Map_Settings.temperatureUnits ? 'imperial' : 'metric');
	var url = '//api.openweathermap.org/data/2.5/weather?APPID='+ CMLOC_Map_Settings.openweathermapAppKey
				+'&lat='+ location.lat + '&lon=' + location.long + '&units=' + units;
	this.pushRequest(url, function(response) {
//		console.log(response);
		if (200 == response.cod) {
			var iconUrl = 'http://openweathermap.org/img/w/'+ response.weather[0].icon +'.png';
			var container = location.container.find('.cmloc-weather');
			var tempUnit = ('temp_f' == CMLOC_Map_Settings.temperatureUnits ? 'F' : 'C');
			container.attr('href', 'http://openweathermap.org/city/' + response.id);
			container.append(jQuery('<img/>', {src: iconUrl}));
			container.append(jQuery('<div/>', {"class" : "cmlocr-weather-temperature"}).html(Math.round(response.main.temp) + "&deg;"+ tempUnit));
			container.append(jQuery('<div/>', {"class" : "cmlocr-weather-pressure"}).html(Math.round(response.main.pressure) + " hPa"));
		}
	});
};


CMLOC_Map.prototype.pushRequest = function(url, callback) {
	var callbackName = 'cmloc_callback_' + Math.floor(Math.random()*99999999);
	window[callbackName] = callback;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url + '&callback=' + callbackName;
	document.getElementsByTagName('body')[0].appendChild(script);
};


CMLOC_Map.prototype.createMarker = function(location) {
	
	return new CMLOC_Marker(this, new google.maps.LatLng(location.lat, location.long),
			   {draggable: false, style: 'cursor:pointer;',icon: location.icon},
			   {text: location.name, style: 'cursor:pointer;'}
			 );
	
//	var marker = new MarkerWithLabel({
//		   position: new google.maps.LatLng(location.lat, location.long),
//		   draggable: false,
////		   raiseOnDrag: true,
//		   map: this.map,
//		   cursor: 'pointer',
//		   labelContent: location.name,
//		   labelAnchor: new google.maps.Point(this.getTextWidth(location.name, 10), 0),
//		   labelClass: "cmloc-map-label" // the CSS class for the label
//		 });
	
	
	
	return marker;
};


CMLOC_Map.prototype.createWaypointMarker = function(location) {
	
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(location.lat, location.long),
		map: this.map,
		icon: 'https://maps.gstatic.com/mapfiles/dd-via.png',
		draggable: false,
	});
	
	return marker;
	
};


CMLOC_Map.prototype.getLocationIndexByMarker = function(marker) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i].marker == marker) {
			return i;
		}
	}
	return false;
};


CMLOC_Map.prototype.getLocationIndexByItem = function(item) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i].item == item) {
			return i;
		}
	}
	return false;
};


CMLOC_Map.prototype.getLocationIndexById = function(id) {
	for (var i=0; i<this.locations.length; i++) {
		if (this.locations[i].id == id) {
			return i;
		}
	}
	return false;
};


CMLOC_Map.prototype.center = function() {
	if (this.locations.length > 0) {
		if (this.locations.length == 1) {
			this.map.panTo(new google.maps.LatLng(this.locations[0].lat, this.locations[0].long));
			this.map.setZoom(15);
		} else {
			this.map.fitBounds(this.bounds);
		}
	}
};


CMLOC_Map.prototype.getMapElement = function() {
	return jQuery(this.mapElement);
};


CMLOC_Map.prototype.getTextWidth = function(text, fontSize) {
	var narrow = '1tiIfjJl';
	var wide = 'WODGKXZBM';
	var result = 0;
	for (var i=0; i<text.length; i++) {
		var letter = text.substr(i, 1);
		var rate = 1.0 + (0.5*(wide.indexOf(letter) >= 0 ? 1 : 0)) - (0.5*(narrow.indexOf(letter) >= 0 ? 1 : 0));
//		console.log(letter +' : '+ rate);
		result += rate;
	}
	return result * fontSize*0.7/2;
};



CMLOC_Map.prototype.requestTrail = function() {
	
	this.requestsCount++;
	
	this.removeTrailPolylines();
	this.removeElevationGraph();
	
	if (this.locations.length < 2) return false;
	
	if (this.travelMode == 'DIRECT') {
		this.requestTrailDirect();
		return;
	}
	
	var mapObj = this;
	
	var waypoints = [];
	for (var i=1; i<this.locations.length-1; i++) {
		var location = this.locations[i];
		waypoints.push({
			location: new google.maps.LatLng(location.lat, location.long),
			stopover: true,
		});
	}
	
	this.directionsService.route({
		origin: new google.maps.LatLng(this.locations[0].lat, this.locations[0].long),
		destination: new google.maps.LatLng(this.locations[this.locations.length-1].lat, this.locations[this.locations.length-1].long),
		waypoints: waypoints,
		travelMode: mapObj.travelMode,
		optimizeWaypoints: false
	  }, function(response, status) {
		  mapObj.requestTrailCallback(mapObj.travelMode, response, status);
	});
	
};


CMLOC_Map.prototype.requestTrailDirect = function() {
	
	var overview_path = [];
	var legs = [];
	var newLeg = {duration: {value: 0}, distance: {value: 0}, steps: [{path: []}]};
	var leg = jQuery.extend(true, {}, newLeg);
	var step = null;
	var lastCoord = null;
	for (var i=0; i<this.locations.length; i++) {
		
		var location = this.locations[i];
		var coord = new google.maps.LatLng(location.lat, location.long);
		overview_path.push(coord);
		
		if (lastCoord) {
			var distance = this.calculateDistance(lastCoord, coord);
			leg.distance.value += distance;
			leg.duration.value += distance * (3600/4000);
		}
		
		if (i > 0) {
			leg.steps[0].path.push(coord);
			legs.push(leg);
		}
		leg = jQuery.extend(true, {}, newLeg);
		
		
		
		leg.steps[0].path.push(coord);
		lastCoord = coord;
		
	}
	
	var status = google.maps.DirectionsStatus.OK;
	var response = {
		routes: [{
		   overview_path: overview_path,
		   legs: legs
		}]
	};
	
	this.requestTrailCallback('DIRECT', response, status);
	
};


CMLOC_Map.prototype.requestTrailCallback = function(travelMode, response, status) {
	if (status === google.maps.DirectionsStatus.OK) {
		
		var init = (typeof this.trailResponse != 'object');
		
		this.trailResponse = response;
//		this.directionsDisplay.setDirections(response);
//		console.log(response);
//		if (this.trailPolyline) this.trailPolyline.setMap(null);
//		this.trailPolyline = this.createTrailPolyline(response.routes[0].overview_path);
		this.removeTrailPolylines();
		this.trailPolylines = this.createTrailPolylines(response);
		
		this.totalDistance = this.getTrailDistance(response);
		this.updateDistance(this.totalDistance);
		
		if (this.requestsCount > 1 ||  this.shouldRecalculate()) {
			this.totalDuration = this.getTrailDuration(response);
			this.updateDuration(this.totalDuration);
		}
 		
		if (!init) {
//			this.calculateElevationAlongPath(this.getPath(response));
			this.calculateElevationAlongPath(response.routes[0].overview_path);
		}
		
	} else {
	  alert('Directions request failed due to ' + status);
	}
};


CMLOC_Map.prototype.createTrailPolyline = function(path, legIndex) {
//	console.log(this.pathColor);
	var p = new google.maps.Polyline({
		path: path,
		strokeColor: (this.pathColor ? this.pathColor : '#3377FF'),
		opacity: 0.1,
		map: this.map
	});
	p.legIndex = legIndex;
	return p;
};


CMLOC_Map.prototype.createTrailPolylines = function(response) {
	var result = [];
	var legs = response.routes[0].legs;
	for (var legIndex=0; legIndex<legs.length; legIndex++) {
		var path = [];
		var steps = legs[legIndex].steps;
		for (var j=0; j<steps.length; j++) {
			path = path.concat(steps[j].path);
		}
		result.push(this.createTrailPolyline(path, legIndex));
	}
	return result;
};


CMLOC_Map.prototype.getPath = function(response) {
	var result = [];
	var legs = response.routes[0].legs;
	for (var legIndex=0; legIndex<legs.length; legIndex++) {
		var steps = legs[legIndex].steps;
		for (var j=0; j<steps.length; j++) {
			result = result.concat(steps[j].path);
		}
	}
	return result;
};


CMLOC_Map.prototype.removeTrailPolylines = function() {
	for (var i=0; i<this.trailPolylines.length; i++) {
		this.trailPolylines[i].setMap(null);
	}
	this.trailPolylines = [];
};


CMLOC_Map.prototype.calculateElevation = function() {
//	console.log('calc elev');
	var elevator = new google.maps.ElevationService;
	var path = this.trailResponse.routes[0].overview_path;
	var points = [];
	for (var i=0; i<path.length; i++) {
		points.push(path[i]);
	}
	
	var mapObj = this;
//	console.log('ele = '+ points.length);
	
	elevator.getElevationForLocations({
		'locations': points
	  }, function(results, status) {
		mapObj.calculateElevationCallback(results, status);
	  });
	
};


CMLOC_Map.prototype.calculateElevationAlongPath = function(path) {
	var elevator = new google.maps.ElevationService;
	var mapObj = this;
	var dist = 0;
	if (path.length > 1) dist = this.calculateDistance(path[0], path[path.length-1]);
	var samples = 450; //Math.min(450, Math.max(2, Math.floor(dist/5)));
//	console.log('dist = '+ dist + ' samples = '+ samples);
	
	this.elevationService.getElevationAlongPath({
		'path': path,
		'samples': samples,
	  }, function(results, status) {
//		  console.log(results);
		mapObj.calculateElevationCallback(results, status);
	  });
};


CMLOC_Map.prototype.calculateElevationCallback = function(results, status) {
	if (status === google.maps.ElevationStatus.OK) {
		
		this.showElevationGraph(results);
		
		this.maxElevation = 0;
		this.minElevation = 99999;
		this.elevationGain = 0;
		this.elevationDescent = 0;
		var prev = null;
		for (var i=0; i<results.length; i++) {
			var elevation = results[i].elevation;
			if (elevation > this.maxElevation) {
				this.maxElevation = elevation;
			}
			if (elevation < this.minElevation) {
				this.minElevation = elevation;
			}
//			console.log('elev '+ elevation +' --- '+(elevation-prev));
			if (typeof prev == 'number') {
				if (elevation-prev > 0) {
					this.elevationGain += (elevation-prev);
				} else {
					this.elevationDescent += (prev-elevation);
				}
			}
			prev = elevation;
		}
		
		if (this.requestsCount > 1 || this.shouldRecalculate()) {
			if (this.minElevation == 99999) this.minElevation = 0;
			this.updateMaxElevation(this.maxElevation);
			this.updateMinElevation(this.minElevation);
			this.updateElevationGain(this.elevationGain);
			this.updateElevationDescent(this.elevationDescent);
		}
		
	} else {
	  console.log('Elevation service failed due to: ' + status);
	}
};


CMLOC_Map.prototype.updateMaxElevation = function(maxElevation) {
	this.containerElement.find('.cmloc-max-elevation span').text(this.getElevationLabel(maxElevation));
};

CMLOC_Map.prototype.updateMinElevation = function(minElevation) {
	this.containerElement.find('.cmloc-min-elevation span').text(this.getElevationLabel(minElevation));
};


CMLOC_Map.prototype.updateElevationGain = function(elevationGain) {
	this.containerElement.find('.cmloc-elevation-gain span').text(this.getElevationLabel(elevationGain));
};

CMLOC_Map.prototype.updateElevationDescent = function(elevationDescent) {
	this.containerElement.find('.cmloc-elevation-descent span').text(this.getElevationLabel(elevationDescent));
};


CMLOC_Map.prototype.getTrailDistance = function(response) {
	var totalDistance = 0;
	var legs = response.routes[0].legs;
	for (var i=0; i<legs.length; ++i) {
		totalDistance += legs[i].distance.value;
	}
	return totalDistance;
};


CMLOC_Map.prototype.getDistanceLabel = function(distanceMeters, useMinorUnits) {
	
	if (typeof useMinorUnits == 'undefined') {
		useMinorUnits = false;
	}
	
	if ('feet' == CMLOC_Map_Settings.lengthUnits) {
		var num = distanceMeters/CMLOC_Map_Settings.feetToMeter;
		if (!useMinorUnits && num > CMLOC_Map_Settings.feetInMile) {
			return Math.round(num/CMLOC_Map_Settings.feetInMile) +' miles';
		} else {
			return Math.floor(num) + ' ft';
		}
	} else {
	
		var dist = distanceMeters;
		var distLabel = '' + Math.round(dist) + ' m';
		if (!useMinorUnits && dist > 2000) {
			distLabel = '' + Math.round(dist/1000) + ' km';
		}
		return distLabel;
		
	}
	
};


CMLOC_Map.prototype.getElevationLabel = function(elev) {
	if ('feet' == CMLOC_Map_Settings.lengthUnits) {
		var num = elev/CMLOC_Map_Settings.feetToMeter;
		return Math.floor(num) + ' ft';
	} else {
		return '' + Math.round(elev) + ' m';
	}
};


CMLOC_Map.prototype.updateDistance = function(dist) {
	var elem = this.containerElement.find('.cmloc-route-distance span');
	var useMinorUnits = (1 == elem.parents('.cmloc-location-params').first().data('useMinorLengthUnits'));
	elem.text(this.getDistanceLabel(dist, useMinorUnits));
};


CMLOC_Map.prototype.updateAvgSpeed = function(meterPerSec) {
	this.avgSpeed = meterPerSec;
	var elem = this.containerElement.find('.cmloc-route-avg-speed span');
	elem.text(this.getSpeedLabel(meterPerSec));
};


CMLOC_Map.prototype.getTrailDuration = function(response) {
	var totalDuration = 0;
	var legs = response.routes[0].legs;
	for (var i=0; i<legs.length; ++i) {
		totalDuration += legs[i].duration.value;
	}
	return totalDuration; // seconds
};


CMLOC_Map.prototype.getSpeedLabel = function(meterPerSec) {
	if ('feet' == CMLOC_Map_Settings.lengthUnits) {
		return '' + Math.round(meterPerSec/CMLOC_Map_Settings.feetToMeter/CMLOC_Map_Settings.feetInMile*3600) + ' mph';
	} else {
		return '' + Math.round(meterPerSec * 3.6) + ' km/h';
	}
};


CMLOC_Map.prototype.getDurationLabel = function(durationSec) {
		
	var durationNumber = Math.ceil(durationSec);
	var durationLabel = '' + durationNumber +' s';
	if (durationNumber > 60) {
		durationNumber /= 60;
		var min = Math.ceil(durationNumber);
		if (min > 0) {
			durationLabel = '' + min + ' min';
		}
	}
	if (durationNumber > 60) {
		durationLabel = '' + Math.floor(durationNumber/60) + ' h '+ Math.floor(durationNumber)%60 +' min';
	}
	return durationLabel;
		
};

CMLOC_Map.prototype.updateDuration = function(duration) {
	this.containerElement.find('.cmloc-route-duration span').text(this.getDurationLabel(duration));
	this.updateAvgSpeed(duration > 0 ? this.totalDistance/duration : 0);
};



CMLOC_Map.prototype.calculateDistance = function(p1, p2) {
	
	var R = 6371000; // metres
	var k = p1.lat().toRadians();
	var l = p2.lat().toRadians();
	var m = (p2.lat() - p1.lat()).toRadians();
	var n = (p2.lng() - p1.lng()).toRadians();
	
	var a = Math.sin(m/2) * Math.sin(m/2) +
    	Math.cos(k) * Math.cos(l) *
    	Math.sin(n/2) * Math.sin(n/2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	
	return R * c;
	
};


CMLOC_Map.prototype.calculateMidpoint = function(p1, p2) {
	
	var lat1 = p1.lat().toRadians();
	var lon1 = p1.lng().toRadians();
	var lat2 = p2.lat().toRadians();
	var lon2 = p2.lng().toRadians();
	
	var bx = Math.cos(lat2) * Math.cos(lon2 - lon1);
	var by = Math.cos(lat2) * Math.sin(lon2 - lon1);
	var lat3 = Math.atan2(Math.sin(lat1) + Math.sin(lat2), Math.sqrt((Math.cos(lat1) + bx) * (Math.cos(lat1) + bx) + by*by));
	var lon3 = lon1 + Math.atan2(by, Math.cos(lat1) + Bx);
	
	return new google.maps.LatLng(lat3.toDegrees(), lon3.toDegrees());
	
};


CMLOC_Map.prototype.calculateMidpoints = function(p1, p2, maxDist) {
	var dist = this.calculateDistance(p1, p2);
	if (dist <= maxDist) return [];
	var num = dist / maxDist;
	
	
	
};


CMLOC_Map.prototype.showElevationGraph = function(elevations) {
//	console.log('showElevationGraph');
	var graphDiv = this.containerElement.find('.cmloc-elevation-graph');
	if (graphDiv.length == 0 || typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.ColumnChart == 'undefined') return;
	var graph = new google.visualization.ColumnChart(graphDiv[0]);
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Sample');
	data.addColumn('number', 'Elevation');
	for (var i = 0; i < elevations.length; i++) {
		data.addRow(['', elevations[i].elevation]);
	}
	graph.draw(data, {
	    height: 150,
	    legend: 'none',
	    titleY: 'Elevation (m)'
	  });
	
	var marker = new google.maps.Marker({
//		position: new google.maps.LatLng(location.lat, location.long),
//		map: this.map,
		icon: 'https://maps.gstatic.com/mapfiles/dd-via.png',
		draggable: false,
	});
	
	var mapObj = this.map;
	google.visualization.events.addListener(graph, 'onmouseover', function(ev) {
		if (typeof elevations[ev.row] != 'undefined') {
			marker.setMap(mapObj);
			marker.setPosition(elevations[ev.row].location);
		}
	});
	
	graphDiv.mouseout(function() {
		marker.setMap(null);
	});
	
};


CMLOC_Map.prototype.removeElevationGraph = function() {
	this.containerElement.find('.cmloc-elevation-graph').html('');
};


CMLOC_Map.prototype.parseDuration = function(val) {
	val = val.replace(/[^0-9hms]/g, '').match(/([0-9]+h)?([0-9]+m)?([0-9]+s)?/);
	for (var i=1; i<=3; i++) {
		val[i] = parseInt(val[i]);
		if (isNaN(val[i])) val[i] = 0;
	}
	console.log(val);
	return val[1] * 3600 + val[2] * 60 + val[3];
};


CMLOC_Map.prototype.findAddress = function(pos, successCallback) {
	this.geocoder.geocode({'location': pos}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			
			var findPostalCode = function(results) {
				for (var j=0; j<results.length; j++) {
					var address = results[j];
					var components = address.address_components;
//					console.log(components);
					for (var i=0; i<components.length; i++) {
						var component = components[i];
						if (component.types[0]=="postal_code"){
					        return component.short_name;
					    }
					}
				}
				return "";
			};
			
			if (results.length > 0) {
				var address = results[0];
				successCallback({
					results: results,
					postal_code: findPostalCode(results),
					formatted_address: address.formatted_address,
				});
			}
		}
	});
};


CMLOC_Map.prototype.findLocationByAddress = function(full_address, successCallback) {
	
	jQuery.getJSON('http://maps.googleapis.com/maps/api/geocode/json?address='+ encodeURIComponent(full_address) +'&sensor=false', null, function (data) {
		if (data.results.length > 0) {
			var p = data.results[0].geometry.location;
			successCallback(new google.maps.LatLng(p.lat, p.lng));
		}
	});
	
	return;
	
	this.geocoder.geocode({'address': full_address}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
			successCallback(results[0].geometry.location);
		}
	});
};


CMLOC_Map.prototype.shouldRecalculate = function() {
	return (location.search.indexOf('recalculate=1') >= 0);
};


CMLOC_Map.prototype.geolocationGetPosition = function(callback, errorCallback, highAccuracy) {
	if ("geolocation" in navigator) {
		if (typeof highAccuracy != 'boolean') highAccuracy = true;
		var geo_options = {
				  enableHighAccuracy: highAccuracy, 
				  maximumAge        : 60, 
				  timeout           : 600
				};
		if (typeof errorCallback != 'function') errorCallback = function(err) {
			console.log(err);
			window.CMLOC.Utils.toast('Geolocation error: [' + err.code + '] ' + err.message, null, Math.ceil(err.message.length/5));
		};
		return navigator.geolocation.getCurrentPosition(callback, errorCallback, geo_options);
	}
};

