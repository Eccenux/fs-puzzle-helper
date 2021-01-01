<?php if (!empty($gsId)) { ?>
	<section class="frame">
		<iframe width='100%' height='200' frameborder='0' src='https://docs.google.com/spreadsheets/<?=$gsId?>/edit?rm=minimal#gid=<?=$gsSheetId?>'></iframe>
	</section>
	<section id="sheet-size-controls">
		<button class="resize" data-class="gs-hide">Hide GS</button>
		<button class="resize" data-class="gs-default">Default GS</button>
		<button class="resize" data-class="gs-large">Large GS</button>
	</section>
<?php } else { ?>
	<section id="sheet-form">
		<form action="">
			<label title="Sheet URL">Google Sheet URL:</label>
			<input name="gsurl" type="url" value="https://docs.google.com/spreadsheets/d/111gE09r7AqnhXfsuNouOssruunuRt3rTXw7Nt42zpVU/edit#gid=1662443983">
			<input type="submit" value="submit">
		</form>
	</section>
<?php } ?>
