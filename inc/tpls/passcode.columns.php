<table>
	<tr>
		<th>Column</th>
		<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
			<th class="passcode-col-no"><?=$column?></td>
		<?php } ?>
	</tr>
	<?php if (empty($gsId)) { ?>
	<tr>
		<th>Char</th>
		<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
			<td class="passcode-col-char">
				<input data-col="<?=$column?>" name="char" type="text" value="">
			</td>
		<?php } ?>
	</tr>
	<?php } ?>
	<tr>
		<th>Map</th>
		<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
			<td class="passcode-col-map">
				<button data-col="<?=$column?>" title="Show map for column">👁‍</button>
			</td>
		<?php } ?>
	</tr>
	<tr>
		<th>Locations</th>
		<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
			<td class="passcode-col-ll">
				<button data-col="<?=$column?>" title="View locations list">📝</button>
			</td>
		<?php } ?>
	</tr>
</table>
<div class="map">
	<div id="main-char-map" class="char-map"></div>
</div>

<div id="locations-dialog" title="Locations list">
	<textarea></textarea>
</div>

<style>
#locations-dialog {
	display: none;
}
#locations-dialog textarea {
	width: 100%;
	height: 9.5em;
}
</style>