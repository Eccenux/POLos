<h2>Ostatnie wyniki losowań</h2>
<?php
	ModuleTemplate::printArray($tplData['profiles-results'], array(
		'group_name' => 'grupa',
		'sex' => 'płeć',
		'age_min' => 'wiek od',
		'age_max' => 'wiek do',
		'region' => 'dzielnica',
		'invites_no' => 'l. zaproszeń',
		'draw_time' => 'czas zapisu losowania',
		'verification' => 'dane losowania',
	), array(
		'verification' => function($value){
			return "<a data-RandomApi-result='".$value."' href='#'>szczegóły</a>";
		}
	));
?>

<script>
	$('#content a[data-RandomApi-result]').click(function(){
		var $dialog = $('#randomApi-verify-dialog');
		var result = JSON.parse(this.getAttribute('data-RandomApi-result'));
		$('[data-id="serialNumber"]', $dialog).text(result.random.serialNumber);
		$('[data-id="min"]', $dialog).text(result.random.min);
		$('[data-id="max"]', $dialog).text(result.random.max);
		$('[data-id="result"]', $dialog).text(result.random.data.join(', '));
		$('[name="random"]', $dialog).val(JSON.stringify(result.random));
		$('[name="signature"]', $dialog).val(result.signature);
		$dialog.dialog({
			modal: true
		});
	});
</script>