<?
	/**
		@file Podsumowanie (statystyki)
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	
	// get
	$tplData = array();
	$tplData['drawPossible'] = $drawPossible;
	$tplData['drawValidationMessage'] = $drawValidationMessage;
	$dbProfile->pf_getStats($tplData['profile-persons'], 'profile-persons');

	foreach ($tplData['profile-persons'] as &$row)
	{
		$percentage = $row['persons'] / $row['invites_no'] * 100;
		if ($percentage < 1) {
			$row['percentage'] = "< 1%";
		} else {
			$row['percentage'] = strtr(sprintf("%.0f", $percentage), '.', ',') . "%";
		}
		if ($percentage > 200) {
			$row['row_class'] = 'draw-fair';
		} else if ($percentage > 100) {
			$row['row_class'] = 'draw-possible';
		} else if ($percentage == 100) {
			$row['row_class'] = 'draw-minimum';
		} else if ($percentage < 100) {
			$row['row_class'] = 'draw-not-possible';
		}
	}

	$missing = $total['profile'][0]['profiles'] - count($tplData['profile-persons']);
	if ($missing > 0) {
		$tplData['drawMatchingMessage'] = "Niektóre profile ($missing) nie mają żadnych dopasowań!";
	}

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.summary.tpl.php';
?>