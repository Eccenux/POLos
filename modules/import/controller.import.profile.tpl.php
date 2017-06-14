<h2>Import profili</h2>

<h3>Zaimportowane dane</h3>
<?php if (empty($tplData['profile']['total'][0]['profiles'])) { ?>
<p>Brak</p>
<?php } else { ?>
<ul>
<li>Liczba profili: <?=$tplData['profile']['total'][0]['profiles']?></li>
<li>Liczba zaproszeń do wylosowania: <?=$tplData['profile']['total'][0]['invites']?></li>
</ul>
<?php } ?>

<h3>Nowe dane</h3>

<style>
	label { font-weight: bold; }
</style>

<p><label for="import-csv">Plik CSV:</label> <input type="file" id="import-csv" /></p>

<p><label>Format pliku (kolejność kolumn):</label></p>
<script>
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
</script>
<style>
	ul.sortable-columns { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
	ul.sortable-columns li { margin: 5px; padding: 5px; width: 150px; }

	ul#import-sortable-columns {
		list-style-type: upper-latin;
		margin-left: 1em;
	}
</style>

<div style="float:left">
	<ul id="import-sortable-columns" class="sortable-columns">
		<?php foreach ($tplData['columns'] as $number => $column) { ?>
			<?php if (empty($column)) { ?>
				<li class="ui-state-highlight ignore-column"><em>ignoruj</em></li>
			<?php } else { ?>
				<li class="ui-state-default"
					id="import-column-<?=$column['column']?>"
				><?=$column['title']?></li>
			<?php } ?>
		<?php } ?>
	</ul>
</div>
<div style="float:left">
	<ul class="sortable-columns">
		<li id="import-draggable-column" class="ui-state-highlight ignore-column"><em>ignoruj</em></li>
	</ul>
</div>

<br clear="all"/>