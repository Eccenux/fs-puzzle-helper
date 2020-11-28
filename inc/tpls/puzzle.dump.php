<div class="dump-accordion">
	<aside class="dump">
		<?php include './inc/tpls/puzzle.dump.last-portals.php'; ?>
	</aside>

	<aside class="dump">
		<?php include './inc/tpls/puzzle.dump.portal-columns.php'; ?>
	</aside>

	<aside class="dump">
		<?php include './inc/tpls/puzzle.dump.ll-columns.php'; ?>
	</aside>

	<aside class="dump">
		<?php include './inc/tpls/puzzle.dump.ll-blob.php'; ?>
	</aside>
</div>

<script>
	$(".dump-accordion")
	.accordion({
		header: "> aside > h2",
		collapsible: true,
		active: false, // collapsed
		heightStyle: "content",
	})
	.sortable({
		axis: "y",
		handle: "h2",
		stop: function( event, ui ) {
			// IE doesn't register the blur when sorting
			// so trigger focusout handlers to remove .ui-state-focus
			ui.item.children( "h2" ).triggerHandler( "focusout" );
	
			// Refresh accordion to handle new order
			$( this ).accordion( "refresh" );
		}
	});
</script>
