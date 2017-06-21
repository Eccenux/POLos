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

	// disable time limit
	set_time_limit(0);
	
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
			// mark take-all as draw_id=0
			$profiles = array();
			$dbProfile->pf_getStats($profiles, 'profile-persons-matched');
			$tplData['profile-counts']['total'] = count($profiles);
			$tplData['profile-counts']['take-all'] = 0;
			$tplData['profile-counts']['to-draw'] = 0;
			$tplData['profile-persons-counts']['total'] = 0;
			$tplData['profile-persons-counts']['take-all'] = 0;
			$tplData['profile-persons-counts']['to-draw'] = 0;
			$tplData['profiles']['to-draw'] = array();
			foreach ($profiles as &$profile)
			{
				$tplData['profile-persons-counts']['total'] += $profile['persons'];
				if ($profile['persons'] <= $profile['invites_no']) {
					$tplData['profile-counts']['take-all']++;
					$tplData['profile-persons-counts']['take-all'] += $profile['persons'];
					$dbPersonal->pf_setRecords(
						array(
							'draw_id' => 0
						),
						array(
							'profile_id' => $profile['id']
						)
					);
				} else {
					$tplData['profile-counts']['to-draw']++;
					$tplData['profile-persons-counts']['to-draw'] += $profile['persons'];
					$tplData['profiles']['to-draw'][$profile['id']] = $profile;
				}
			}
			// get list for draws
			$tplData['persons-to-draw'] = array();
			$dbPersonal->pf_getRecords($tplData['persons-to-draw'],
				array(
					'draw_id' => array('IS', 'NULL'),
					'profile_id' => array('IS NOT', 'NULL')
				),
				array('id', 'pesel', 'profile_id')
			);

			$pv_controller->tpl->file = 'controller.draw2_draw.tpl.php';
		break;
	}

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
?>