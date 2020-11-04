<!DOCTYPE html>
<html lang="en">
<head>
    <title>Portal cells</title>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="copyright" content="Maciej Jaros">

	<link rel="stylesheet" type="text/css" href="puzzle.css">
	<link rel="icon" href="img/icon.svg" sizes="any" type="image/svg+xml">

	<script src="js/main.js" type="module"></script>
</head>
<body>
	<main>
		<section id="zoomer">
			<img src="img-auto-cut/col_1.jpg" />
		</section>
		<section id="columns">
			<!-- keyword layout, 19 cols (June 2020) -->
			<!-- 
			<section>
				<section class="column left  " id="col_1"><h2>1</h2><img src="img-auto-cut/col_1.jpg"    /></section>
				<section class="column left  " id="col_2"><h2>2</h2><img src="img-auto-cut/col_2.jpg"    /></section>
				<section class="column left  " id="col_3"><h2>3</h2><img src="img-auto-cut/col_3.jpg"    /></section>
				<section class="column left  " id="col_4"><h2>4</h2><img src="img-auto-cut/col_4.jpg"    /></section>
				<section class="column left  " id="col_5"><h2>5</h2><img src="img-auto-cut/col_5.jpg"    /></section>
			</section>
			<section>
				<section class="column right " id="col_15"><h2>15</h2><img src="img-auto-cut/col_15.jpg"  /></section>
				<section class="column right " id="col_16"><h2>16</h2><img src="img-auto-cut/col_16.jpg"  /></section>
				<section class="column right " id="col_17"><h2>17</h2><img src="img-auto-cut/col_17.jpg"  /></section>
				<section class="column right " id="col_18"><h2>18</h2><img src="img-auto-cut/col_18.jpg"  /></section>
				<section class="column right " id="col_19"><h2>19</h2><img src="img-auto-cut/col_19.jpg"  /></section>
			</section>
			<section>
				<section class="column keyword" id="col_6"><h2>6</h2><img src="img-auto-cut/col_6.jpg"   /></section>
				<section class="column keyword" id="col_7"><h2>7</h2><img src="img-auto-cut/col_7.jpg"   /></section>
				<section class="column keyword" id="col_8"><h2>8</h2><img src="img-auto-cut/col_8.jpg"   /></section>
				<section class="column keyword" id="col_9"><h2>9</h2><img src="img-auto-cut/col_9.jpg"   /></section>
				<section class="column keyword" id="col_10"><h2>10</h2><img src="img-auto-cut/col_10.jpg" /></section>
				<section class="column keyword" id="col_11"><h2>11</h2><img src="img-auto-cut/col_11.jpg" /></section>
				<section class="column keyword" id="col_12"><h2>12</h2><img src="img-auto-cut/col_12.jpg" /></section>
				<section class="column keyword" id="col_13"><h2>13</h2><img src="img-auto-cut/col_13.jpg" /></section>
				<section class="column keyword" id="col_14"><h2>14</h2><img src="img-auto-cut/col_14.jpg" /></section>
			</section>
			-->
			<section>
			<?php
				$base = "./img-auto-cut/cells/";
				for ($column=1; $column < 30; $column++) {
					$files = glob($base . sprintf("/col_%03d_*.jpg", $column));
					if (empty($files)) {
						break;
					}
					echo '<section class="column" id="col_'.$column.'"><h2>'.$column.'</h2>';
					foreach ($files as $file) {
						echo "<img src='$file' />";
					}
					echo '</section>';
				}
			?>
			</section>
		</section>
		<section id="controls">
			<button id="reset-all">reset all</button>
		</section>
	</main>

	<script>
		// css: var(--scrollbar-width)
		//document.documentElement.style.setProperty('--scrollbar-width', (window.innerWidth - document.documentElement.clientWidth) + "px");		
	</script>
</body>
</html>