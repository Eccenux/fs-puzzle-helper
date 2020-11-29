/**
 * Column's map.
 * 
 * Maps for characters of columns.
 */
class ColumnsMapViewModel {
	/**
	 * Init interactions.
	 */
	init() {
		this.initDialogMap();
		this.initLocationView();
		this.initMainMap();
	}

	/**
	 * Locations separated by new line to array.
	 * Removes spaces around new lines.
	 * @private
	 */
	locationLinesToPoly(locationLines) {
		let latlngs = locationLines
		.trim()
		.split('\n')
		.map(l=>l.trim())
		.filter(l=>l.length)
		.map(l=>l.split(','))
		return latlngs;
	}

	/**
	 * Update map with locations.
	 * @private
	 */
	updateMap(map, locationLines) {
		// transform
		let latlngs = this.locationLinesToPoly(locationLines);
		
		// remove previous
		map.eachLayer(function (layer) {
			map.removeLayer(layer);
		});

		if (latlngs.length > 1) {
			// add layers
			let polyline = L.polyline(latlngs, {color: 'red'}).addTo(map);
			let radius = 5;
			let first = latlngs.shift();
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
	 * @private
	 */
	locationsColumnData(column) {
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

	/**
	 * Show location dialog.
	 * @private
	 */
	showLocationDialog(column, locationLines) {
		let dialog = document.querySelector('#locations-dialog');
		dialog.title = `Locations list (${column})`;
		dialog.querySelector('textarea').value = locationLines;
		$(dialog).dialog({
			title: dialog.title,
		});
	}

	dialogMap = null;

	/**
	 * Show map dialog.
	 * @private
	 */
	showMapDialog(column, locationLines) {
		let dialog = document.querySelector('#column-map-dialog');
		dialog.title = `Map (${column})`;
		$(dialog).dialog({
			title: dialog.title,
			width: 120,
			height: 150,
			position: { my: "right bottom", at: "right bottom" },
		});
		// prepare map
		if (this.dialogMap == null) {
			let mapEl = dialog.querySelector('.char-map');
			this.dialogMap = L.map(mapEl, {
				center: [0, 0],
				zoom: 13,
				attributionControl: false,
				zoomControl: false,
			});
		}
		// show char
		this.updateMap(this.dialogMap, locationLines);
	}

	/**
	 * Init main map actions.
	 * @private
	 */
	initMainMap () {
		// quick and dirty init
		let map = L.map('main-char-map', {
			center: [0, 0],
			zoom: 13,
			attributionControl: false,
			zoomControl: false,
		});
	
		// Map actions
		document
		.querySelectorAll('#passcode-columns .passcode-col-map button')
		.forEach((button)=>{
			button.addEventListener('click', () => {
				let column = parseInt(button.getAttribute('data-col'));
				let newdata = this.locationsColumnData(column);
				this.updateMap(map, newdata);
	
				// mark active (current)
				document.querySelectorAll('.passcode-col-map.active').forEach((prev)=>{
					prev.classList.remove('active');
				})
				button.parentNode.classList.add('active');
			});
		});
	}

	/**
	 * Location view actions.
	 * @private
	 */
	initLocationView () {
		document
		.querySelectorAll('.passcode-col-ll button')
		.forEach((button)=>{
			button.addEventListener('click', () => {
				let column = parseInt(button.getAttribute('data-col'));
				let newdata = this.locationsColumnData(column);
				this.showLocationDialog(column, newdata);
			});
		});
	}

	/**
	 * Init dialog map actions.
	 * @private
	 */
	initDialogMap () {
		document
		.querySelectorAll('button.map-popup')
		.forEach((button)=>{
			button.addEventListener('click', () => {
				let column = parseInt(button.getAttribute('data-col'));
				let newdata = this.locationsColumnData(column);
				this.showMapDialog(column, newdata);
			});
		});
	}
}

export { ColumnsMapViewModel }