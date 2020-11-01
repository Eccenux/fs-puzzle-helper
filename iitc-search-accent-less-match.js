/*
  IITC accent-less search (diacritic insensitive).
  
  Adds insenstive matches.
  
  (run once in console)
*/
IitcAccentLessSearch = class {
	constructor() {
		this.svg = `<svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" width="12" height="12" version="1.1">
			<g style="fill:white;stroke:none">
				<path d="m 6,12 -2,-12  4,0 z" />
				<path d="m 6,12 -4, -8  8,0 z" />
				<path d="m 6,12 -6, -4 12,0 z" />
			</g>
		</svg>`;
		this.icon = 'data:image/svg+xml;base64,' + btoa(this.svg);
	}

	find(query) {
		var term = query.term;
		console.log('accent-less search', query);

		$.each(portals, (guid, portal) => {
			var data = portal.options.data;
			if (!data.title)
				return;
	
			if (data.title == term) {
				this.addResult(query, guid, portal);
			}
		});
	}

	addResult(query, guid, portal) {
		var data = portal.options.data;
		query.addResult({
			title: '~= ' + data.title,
			description: 'No accent match',
			position: portal.getLatLng(),
			icon: this.icon,
			onSelected: this.onSelect(guid, portal),
		});
	}

	onSelect(guid, portal) {
		return function (result, event) {
			if (event.type == 'dblclick') {
				zoomToAndShowPortal(guid, portal.getLatLng());
			} else if (window.portals[guid]) {
				if (!map.getBounds().contains(result.position))
					map.setView(result.position);
				renderPortalDetails(guid);
			} else {
				window.selectPortalByLatLng(portal.getLatLng());
			}
			return true;
		};
	}
}

// remove previous version
if (typeof accentLessSearch === 'function') {
	removeHook('search', accentLessSearch);
}

// add new eaxact search
var iitcAccentLessSearch = new IitcAccentLessSearch();
function accentLessSearch(query) {
	iitcAccentLessSearch.find(query);
}
addHook('search', accentLessSearch);