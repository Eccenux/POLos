<h2>Podstawowa weryfikacja</h2>

<?php if (!empty($tplData['drawValidationMessage'])) { ?>
<div class="message <?=($tplData['drawPossible'] ? 'warning' : 'error')?>">
<?=$tplData['drawValidationMessage']?>
</div>
<?php } ?>

<h2>Dopasowanie profili</h2>
<div>
<?php
	/*
	ModuleTemplate::printArray($tplData['profile']['region-invites'], array(
		'region' => 'dzielnica',
		'invites' => 'l. zaproszeń',
	));
	*/
?>
</div>