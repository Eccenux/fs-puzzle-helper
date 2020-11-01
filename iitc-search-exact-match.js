/*
  IITC exact search
  
  Adds exact matches with "==" in title.
  
  (run once in console)
*/
function exactSearch(query) {
	var term = query.term;
	var teams = ['NEU', 'RES', 'ENL'];
	console.log('exact search', query);

	$.each(portals, function (guid, portal) {
		var data = portal.options.data;
		if (!data.title)
			return;

		if (data.title == term) {
			var team = portal.options.team;
			var color = team == TEAM_NONE ? '#CCC' : COLORS[team];
			query.addResult({
				title: '== ' + data.title,
				description: teams[team] + ', L' + data.level + ', ' + data.health + '%, ' + data.resCount + ' Resonators',
				position: portal.getLatLng(),
				icon: 'data:image/svg+xml;base64,' + btoa('<svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" width="12" height="12" version="1.1">\n	<g style="fill:%COLOR%;stroke:none">\n		<path d="m 6,12 -2,-12  4,0 z" />\n		<path d="m 6,12 -4, -8  8,0 z" />\n		<path d="m 6,12 -6, -4 12,0 z" />\n	</g>\n</svg>\n'.replace(/%COLOR%/g, color)),
				onSelected: function (result, event) {
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
				},
			});
		}
	});
}

//removeHook('search', exactSearch);
addHook('search', exactSearch);