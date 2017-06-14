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

<form id="import-form" method="post" action="">
	<?php include 'import.form.tpl.php'; ?>
	<section class="buttons">
		<input type="submit" name="save" value="Importuj" />
	</section>
</form>