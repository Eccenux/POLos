<style>
	<?php include 'validation.css'; ?>
</style>

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
<br clear="all">
<h2>Potwierdzenie</h2>
<p>Po przejściu dalej wykonane zostaną losowania osób do zaproszenia. Czy możemy kontynuować?</p>
<form action="" method="GET">
	<input type="hidden" name="mod" value="draw">
	<input type="hidden" name="a" value="draw">
	<input type="hidden" name="stage" value="2">
	<input type="submit" name="confirm" value="Tak, przejdź do losowania">
</form>
