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
	<?php include 'form.css'; ?>
</style>

<form id="import-form" method="post" action="">

<section>
	<p><label for="import-csv">Plik CSV:</label> <input type="file" id="import-csv" /></p>
</section>

<section>
	<p><label>Format pliku (kolejność kolumn):</label></p>
	<input type="hidden" id="import-order" />
	<script>
		<?php include 'import.columns.js'; ?>
	</script>
	<div style="float:left">
		<ul id="import-sortable-columns" class="sortable-columns">
			<?php foreach ($tplData['columns'] as $number => $column) { ?>
				<?php if (empty($column)) { ?>
					<li class="ui-state-highlight ignore-column"><em>ignoruj</em></li>
				<?php } else { ?>
					<li class="ui-state-default"
						data-column="<?=$column['column']?>"
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
</section>

<section class="buttons">
	<input type="submit" name="save" value="Importuj" />
</section>

</form>
<script>
	<?php include 'import.form.js'; ?>
</script>
