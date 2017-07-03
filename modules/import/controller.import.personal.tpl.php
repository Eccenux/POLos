<h2>Import danych osób</h2>

<h3>Zaimportowane dane</h3>
<?php if (empty($tplData['personal']['total'][0]['people'])) { ?>
<p>Brak</p>
<?php } else { ?>
<ul>
<li>Liczba osób: <?=$tplData['personal']['total'][0]['people']?></li>
</ul>
<?php } ?>

<h3>Nowe dane</h3>

<?php if (!empty($tplData['parserInfo'])) { ?>
	<?=$tplData['parserInfo']?>
	<input type="button" onclick="history.go(-1)" value="Wróć" />
<?php } else { ?>
	<form id="import-form" method="post" action="" enctype="multipart/form-data">
		<p>
			<label for="import-age-base">Data do obliczania wieku</label>
			<input type="text" id="import-age-base" name="age-base" value="<?=date('Y-m-d')?>" />
		</p>
		<p>
			Uwaga! Jeśli dana osoba ma urodziny w podanym dniu, to przyjmuje się, że osiągnęła już nowy wiek.
			<br>To znaczy osoba urodzona 2000-01-01 ma 18 lat już dla daty 2018-01-01, a nie dzień później.
		</p>
		<script>
		$("#import-age-base").datepicker();
		</script>
		<?php include 'import.form.tpl.php'; ?>
		<section class="buttons">
			<input type="submit" name="save" value="Importuj" />
		</section>
	</form>
<?php } ?>
