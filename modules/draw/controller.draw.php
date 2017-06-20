<?
	/**
		@file Podsumowanie (statystyki)
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	/* @var $dbProfile dbProfile */
	/* @var $dbPersonal dbPersonal */
	
	// get
	$tplData = array();

	$stage = empty($_GET['stage']) ? 0 : intval($_GET['stage']);

	switch ($stage) {
		// confirm
		case 0:
			$tplData['profile-total'] = $total['profile'];
			$tplData['personal-total'] = $total['personal'];
			$pv_controller->tpl->file = 'controller.draw0_confirm.tpl.php';
		break;
		// reset and match persons to profiles
		case 1:
			// reset
			$dbPersonal->pf_setRecords(array('profile_id' => null, 'draw_id' => null));
			// match
			$profiles = array();
			$dbProfile->pf_getRecords($profiles, array('row_state' => 0));
			foreach ($profiles as &$profile) {
				$dbPersonal->setProfile($profile);
			}
			// get stats
			$dbProfile->pf_getStats($tplData['profile-persons'], 'profile-persons-matched');
			DrawHelper::prepareProfilePerson($tplData['profile-persons']);
			// tpl
			$pv_controller->tpl->file = 'controller.draw1_match.tpl.php';
		break;
		// prepare draw lists
		case 2:
			$pv_controller->tpl->file = 'controller.draw2_draw.tpl.php';
		break;
	}

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
?>