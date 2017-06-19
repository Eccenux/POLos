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

	/*
	$dbProfile->pf_getStats($tplData['profile-persons'], 'profile-persons');
	$profilesToDraw = array();
	$profilesToTakeAll = array();
	foreach ($tplData['profile-persons'] as &$row)
	{
		$percentage = $row['persons'] / $row['invites_no'] * 100;
		if ($percentage > 100) {
			$profilesToDraw[] = $row;
		} else if ($row['persons'] > 0) {
			$profilesToTakeAll[] = $row;
		}
	}
	*/

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.summary.tpl.php';
?>