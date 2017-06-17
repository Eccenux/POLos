<h2>Import profili</h2>

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
		<?php include 'import.form.tpl.php'; ?>
		<section class="buttons">
			<input type="submit" name="save" value="Importuj" />
		</section>
	</form>
<?php } ?>
