<h2>Latest portals dump</h2>
<div>
	<button id="portallist-run-fs">dump to FS list</button>
	<button id="portallist-run-names">dump to names list</button>
	<div id="portallist-out"></div>
</div>
<script>
	(function() {
		let out = document.querySelector('#portallist-out');
		document.querySelector('#portallist-run-fs').onclick = function() {
			dumpReverse(false);
		}
		document.querySelector('#portallist-run-names').onclick = function() {
			dumpReverse(true);
		}
		function dumpReverse(useNames) {
			let container = document.createElement('table');
			container.className = 'dump-table';
			let mainColName = useNames ? 'Portal' : 'FS puzzle';
			container.innerHTML = `<thead>
				<tr>
					<th style="width:3em">Cell</th>
					<th style="width:25em">${mainColName}</th>
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
						title: p.title,
						data: p.puzzleData(),
					}
				})
				.reverse()
				.forEach(p => {
					let nel = document.createElement('tr');
					//nel.className = 'text';
					let mainData = useNames ? p.title : p.data;
					nel.innerHTML = `
						<tr>
							<td>${p.col}, ${p.row}</td>
							<td><input value="${mainData}" onclick="this.select();" style="width:100%"></td>
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
