// TODO make a class and maybe use ES6 version of leaflet

/**
 * Locations separated by new line to array.
 * Removes spaces around new lines.
 */
function locationLinesToPoly(locationLines) {
	latlngs = locationLines
	.trim()
	.split('\n')
	.map(l=>l.trim())
	.filter(l=>l.length)
	.map(l=>l.split(','))
	return latlngs;
}

/**
 * Update PoC.
 */
function updateMap(map, locationLines) {
	// transform
	var latlngs = locationLinesToPoly(locationLines);
	
	// remove previous
	map.eachLayer(function (layer) {
		map.removeLayer(layer);
	});

	if (latlngs.length) {
		// add
		var polyline = L.polyline(latlngs, {color: 'red'}).addTo(map);
		var radius = 5;
		var first = latlngs.shift();
		L.circleMarker(first, {color: 'green', radius}).addTo(map);
		latlngs.forEach((latLng)=>{
			L.circleMarker(latLng, {color: 'black', radius}).addTo(map);
		});
		// zoom the map to the polyline
		map.fitBounds(polyline.getBounds());
	}
}

/**
 * Locations of a column.
 */
function locationsColumnData(column) {
	let locations = [];
	app.portalsViewModel.state.portals
		.filter(p=>p.col == column)
		.map(p=>{
			return {
				col: p.col,
				row: p.row,
				ll: p.getLatLon(),
			}
		})
		// sort by col, row (col 1 first)
		.sort(
			(a,b)=> a.col == b.col ? a.row - b.row : a.col - b.col
		)
		.forEach(p=>{
			locations.push(p.ll);
		})
	;
	//console.log(locations);
	return locations.join('\n');
}

//
// quick and dirty init
//
var map = L.map('main-char-map', {
	center: [0, 0],
	zoom: 13,
	attributionControl: false,
	zoomControl: false,
});

let actions = document.querySelectorAll('#passcode-columns .passcode-col-map button');
actions.forEach((button)=>{
	button.onclick = function() {
		let column = parseInt(button.getAttribute('data-col'));
		let newdata = locationsColumnData(column);
		updateMap(map, newdata);
	};
});
