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
</table>
<div class="map">
	<div id="main-char-map" class="char-map"></div>
</div>
