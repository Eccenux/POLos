$( function() {
	$( "#import-sortable-columns" ).sortable({
		revert: true
	});
	// connect to columns
	$( "#import-draggable-column" ).draggable({
		connectToSortable: "#import-sortable-columns",
		helper: "clone",
		revert: "invalid"
	});
	// allow dropping columns back
	// (but only with `ignore-column` class)
	$('#import-draggable-column').droppable({
		drop: function(event, ui) {
			if (ui.draggable.hasClass('ignore-column')) {
				ui.draggable.remove();
			}
		}
	});
	$(".sortable-columns").disableSelection();
} );
