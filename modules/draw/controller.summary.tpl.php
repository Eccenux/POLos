<h2>Podstawowa weryfikacja</h2>

<style>
	<?php include 'validation.css'; ?>
</style>

<?php if (!empty($tplData['drawValidationMessage'])) { ?>
	<div class="message <?=($tplData['drawPossible'] ? 'warning' : 'error')?>">
	<?=$tplData['drawValidationMessage']?>
	</div>
<?php } else { ?>
	<ul>
		<li>Liczba profili: <?=ModuleTemplate::formatNumber($tplData['profile-total'][0]['profiles'])?></li>
		<li>Liczba zaproszeń: <?=ModuleTemplate::formatNumber($tplData['profile-total'][0]['invites'])?></li>
		<li>Liczba osób: <?=ModuleTemplate::formatNumber($tplData['personal-total'][0]['people'])?></li>
	</ul>
<?php } ?>

<h2>Dopasowanie profili</h2>
<?php if (!empty($tplData['drawMatchingMessage'])) { ?>
<div class="message warning">
<?=$tplData['drawMatchingMessage']?>
</div>
<?php } ?>
<div style="float:left">
<?php
	ModuleTemplate::printArray($tplData['profile-persons'], array(
		'group_name' => 'grupa',
		'sex' => 'płeć',
		'age_min' => 'wiek od',
		'age_max' => 'wiek do',
		'region' => 'dzielnica',
		'invites_no' => 'l. zaproszeń',
		'persons' => 'l. osób',
		'percentage' => 'os./zap.',
	));
?>
</div>
<div style="float:left; padding-left: 1em;">
	<table>
		<tr><th>Legenda</th></tr>
		<tr class="draw-fair">			<td>Losowanie sprawiedliwe</td></tr>
		<tr class="draw-possible">		<td>Losowanie możliwe</td></tr>
		<tr class="draw-minimum">		<td>Na styk</td></tr>
		<tr class="draw-not-possible">	<td>Za mało osób</td></tr>
	</table>
</div>
