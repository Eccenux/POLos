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

	$missing = $total['profile'][0]['profiles'] - count($tplData['profile-persons']);
	if ($missing > 0) {
		$tplData['drawMatchingMessage'] = "Niektóre profile ($missing) nie mają żadnych dopasowań!";
	}

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.summary.tpl.php';
?>