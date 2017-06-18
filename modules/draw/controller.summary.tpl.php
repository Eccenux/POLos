<h2>Podsumowanie</h2>

<ul>
<li>Liczba profili: <?=$tplData['profile']['total'][0]['profiles']?></li>
<li>Liczba zaproszeń do wylosowania: <?=$tplData['profile']['total'][0]['invites']?></li>
<li>Liczba osób w rejestrze wyborców: <?=$tplData['personal']['total'][0]['people']?></li>
</ul>

<?php if ($tplData['profile']['total'][0]['invites'] > $tplData['personal']['total'][0]['people']) { ?>
<div class="message warning">
Uwaga! Liczba zaproszeń jest większa niż liczba przesłanych danych osobowych.
</div>
<?php } ?>

<h2>Podział na dzielnice</h2>
<div style='float:left;margin-right:1em'>
<h3>Profile zaproszeniowe</h3>
<?php
	ModuleTemplate::printArray($tplData['profile']['region-invites'], array(
		'region' => 'dzielnica',
		'invites' => 'l. zaproszeń',
	));
?>
</div>
<div style='float:left;margin-right:1em'>
<h3>Dane osobowe (wyborcy)</h3>
<?php
	ModuleTemplate::printArray($tplData['personal']['region-counts'], array(
		'region' => 'dzielnica',
		'people' => 'l. osób',
	));
?>
</div>
