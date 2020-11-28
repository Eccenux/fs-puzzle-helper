<h2>Locations dump flat</h2>
<div>
	<button id="locationslistflat-run">dump all</button> (row order)
	<textarea id="locationslistflat-out" style="width: 100%; height:30vh;"></textarea>
</div>

<script>
	(function() {
		let out = document.querySelector('#locationslistflat-out');
		let dump = document.querySelector('#locationslistflat-run');
		dump.onclick = function() {
			// create `cols` as locations list by column
			let cols = [];
			app.portalsViewModel.state.portals
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
					if (!(p.col in cols)) {
						cols[p.col] = [];
					}
					cols[p.col].push(p.ll);
				})
			;
			console.log(cols);

			// dump
			let locations = '';
			cols
				.forEach((ll, col) => {
					//nel.className = 'text';
					let data = ll.join('\n');
					locations += data + '\n';
				});
			;
			
			// display
			out.value = locations;
		};
	})();
</script>
