<p class="text">
	<label>Quick fill suggestions (TSV):</label>
	<input type="text" value="" id="quickFillColumnNotes_tsv" />
	<input type="button" value="fill" onclick="quickFillColumnNotes()"/>
	<script>
	// quick paste
	function quickFillColumnNotes() {
		let tsv = document.querySelector('#quickFillColumnNotes_tsv')?.value;
		if (!tsv) {
			return;
		}
		items = tsv.split('\t')
		Array.from(document.querySelectorAll('[name="col_notes"]')).map((v,index)=>{
			if (!v.value.length) {
				v.value=items[index];
			}
		})
	}
	</script>
</p>
<p class="text">
	<label>No. of portals (per column):</label>
	<input type="text" value="<?=implode("\t", $rowCounts)?>"/>
</p>
<input type="button" value="cut checkup" 
	onclick="document.body.classList.toggle('cut-checkup')"/>
<p>
	&nbsp; <a href="img-auto-cut/all.jpg" target="_blank">all.jpg</a>
</p>
