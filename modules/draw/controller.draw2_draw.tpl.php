<h2>Podsumowanie</h2>
<ul>
<li>Liczba wszystkich profili: 
	<?=$tplData['profile-counts']['total']?> (<?=$tplData['profile-persons-counts']['total']?> osób)
</li>
<li>Liczba profili przyjętych bez losowania: 
	<?=$tplData['profile-counts']['take-all']?> (<?=$tplData['profile-persons-counts']['take-all']?> osób)
</li>
<li>Liczba profili przeznaczonych do losowania: 
	<?=$tplData['profile-counts']['to-draw']?> (<?=$tplData['profile-persons-counts']['to-draw']?> osób)
</li>
</ul>

<style>
	<?php include 'draw.css'; ?>
</style>

<h2>Listy do losowania</h2>
<p>Listy zawierają numer PESEL z ukrytym początkiem i końcem.</p>

<div id="draw-test-mode" class="message warning">
	Tryb testowy! Ograniczona liczba losowań.
</div>

<input type="button" id="draw-starter" value="Losuj">
<script>
	<?php include 'draw.js'; ?>
</script>

<section id="draw-lists">
<?php $prevProfileId = -1; ?>
<?php foreach ($tplData['persons-to-draw'] as $person) { ?>
	<?php $profile = $tplData['profiles']['to-draw'][$person['profile_id']] ?>
	<?php if ($prevProfileId != $person['profile_id']) { ?>
		<?php if ($prevProfileId > 0) { ?>
			</ol>
			</section>
		<?php } ?>
		<section
			data-draw-profile="<?=$person['profile_id']?>"
			data-draw-invites="<?=$profile['invites_no']?>"
			data-draw-persons="<?=$profile['persons']?>"
		>
		<h3>Losowanie <?=$profile['invites_no']?> osób</h3>
		<ol>
		<?php $prevProfileId = $person['profile_id']; ?>
	<?php } ?>
	<li data-person="<?=$person['id']?>">
		<?=DrawHelper::safePesel($person['pesel'])?>
		<tt>(<?=$person['id']?>)</tt>
	</li>
<?php } ?>
</ol>
</section>