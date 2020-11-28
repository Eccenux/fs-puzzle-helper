<h2>Locations dump per column</h2>
<div>
	<button id="locationslist-run">dump columns</button> (row order)
	<div id="locationslist-out"></div>
</div>

<script>
	(function() {
		let out = document.querySelector('#locationslist-out');
		let dump = document.querySelector('#locationslist-run');
		dump.onclick = function() {
			// prepare container / header
			let container = document.createElement('table');
			container.className = 'dump-table';
			container.innerHTML = `<thead>
				<tr>
					<th style="width:3em">Column</th>
					<th>Locations</th>
				</tr>
			</thead><tbody></tbody>`;
			let containerBody = container.querySelector('tbody');
			if (out.firstChild) {
				out.removeChild(out.firstChild)
			}

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

			// create rows
			cols
				.forEach((ll, col) => {
					let nel = document.createElement('tr');
					//nel.className = 'text';
					let data = ll.join('\n');
					nel.innerHTML = `
						<tr>
							<td>${col}</td>
							<td><textarea rows="5" onclick="this.select();" style="width:100%">${data}</textarea></td>
						</tr>
					`;
					containerBody.appendChild(nel);
				});
			;
			
			// display
			out.appendChild(container);
		};
	})();
</script>
