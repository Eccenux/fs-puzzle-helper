<section>
<?php
	@include('./img-auto-cut/cut-config.php');
	$halfsStart = -1;
	if (defined('HALFS_COLUMN_START')) {
		$halfsStart = HALFS_COLUMN_START;
	}

	for ($column=$startCol; $column <= $colCount; $column++) {
		$files = $rowFiles[$column];
		$classes = "column";
		$halfs = false;
		if ($halfsStart >= 0 && $column >= $halfsStart) {
			$halfs = true;
			$classes .= " halfs";
		}
		echo "
		<section class='$classes' id='col_$column'>
			<h2>$column</h2>
			<div class='group'>
				<input data-col='$column' name='col_notes' type='text' value=''>
				<button data-col='$column' class='map-popup' tabindex='-1' title='Show map for column'>👁‍</button>
			</div>
		";
		foreach ($files as $file) {
			$id = preg_replace('#\.\w+$#', '', basename($file));
			if (preg_match('#.+?0*([0-9]+).+?0*([0-9]+).*#', $id, $matches)) {
				$col = $matches[1];
				$row = $matches[2];
				$title = "col $col, row $row";
				if (!$halfs) {
					echo "<img src='$file' data-url='$file' id='cell_{$id}' title='{$title}' data-col='$col' data-row='$row' />";
				} else {
					echo "<img src='./img/empty.png'
						style='background-image: url($file);'
						data-url='$file' id='cell_{$id}' title='{$title}' data-col='$col' data-row='$row' />";
				}
			}
		}
		echo '</section>';
	}
?>
</section>
