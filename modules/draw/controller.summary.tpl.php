<h2>Podstawowa weryfikacja</h2>

<?php if (!empty($tplData['drawValidationMessage'])) { ?>
<div class="message <?=($tplData['drawPossible'] ? 'warning' : 'error')?>">
<?=$tplData['drawValidationMessage']?>
</div>
<?php } ?>

<h2>Dopasowanie profili</h2>
<div>
<?php
	ModuleTemplate::printArray($tplData['profile-persons'], array(
		'group_name' => 'grupa',
		'sex' => 'płeć',
		'age_min' => 'wiek od',
		'age_max' => 'wiek do',
		'region' => 'dzielnica',
		'invites_no' => 'l. zaproszeń',
		'persons' => 'l. osób',
	));
?>
</div>