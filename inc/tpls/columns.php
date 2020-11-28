<section>
<?php
	for ($column=$startCol; $column <= $colCount; $column++) {
		$files = $rowFiles[$column];
		echo "
		<section class='column' id='col_$column'>
			<h2>$column</h2>
			<input data-col='$column' name='col_notes' type='text' value=''>
		";
		foreach ($files as $file) {
			$id = preg_replace('#\.\w+$#', '', basename($file));
			if (preg_match('#.+?0*([0-9]+).+?0*([0-9]+).*#', $id, $matches)) {
				$col = $matches[1];
				$row = $matches[2];
				$title = "col $col, row $row";
				echo "<img src='$file' id='cell_{$id}' title='{$title}' data-col='$col' data-row='$row' />";
			}
		}
		echo '</section>';
	}
?>
</section>
