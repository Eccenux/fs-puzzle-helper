<h2>Latest portals dump</h2>
<div>
	<button id="portallist-run">dump to list</button> (reverse order)
	<div id="portallist-out"></div>
</div>
<script>
	(function() {
		let out = document.querySelector('#portallist-out');
		let dump = document.querySelector('#portallist-run');
		dump.onclick = function() {
			let container = document.createElement('table');
			container.className = 'dump-table';
			container.innerHTML = `<thead>
				<tr>
					<th style="width:3em">Cell</th>
					<th style="width:25em">FS puzzle</th>
					<th>Notes</th>
				</tr>
			</thead><tbody></tbody>`;
			let containerBody = container.querySelector('tbody');
			if (out.firstChild) {
				out.removeChild(out.firstChild)
			}
			app.portalsViewModel.state.portals
				.map(p=>{
					return {
						col: p.col,
						row: p.row,
						notes: p.notes,
						data: p.puzzleData(),
					}
				})
				.reverse()
				.forEach(p => {
					let nel = document.createElement('tr');
					//nel.className = 'text';
					nel.innerHTML = `
						<tr>
							<td>${p.col}, ${p.row}</td>
							<td><input value="${p.data}" onclick="this.select();" style="width:100%"></td>
							<td>${p.notes}</td>
						</tr>
					`;
					containerBody.appendChild(nel);
				});
			;
			out.appendChild(container);
		};
	})();
</script>
