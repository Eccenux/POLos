<style>
	<?php include 'form.css'; ?>
</style>

<section>
	<p><label for="import-csv">Plik CSV:</label> <input type="file" id="import-csv" name="csv" /></p>
</section>

<section>
	<p><label>Format pliku (kolejność kolumn):</label></p>
	<input type="hidden" id="import-order" name="order" />
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

<script>
	<?php include 'import.form.js'; ?>
</script>
