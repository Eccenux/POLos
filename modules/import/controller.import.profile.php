<?
	/**
		@file Import profili zaproszeniowych.
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	
	require_once ('./inc/db/profile.php');
	$dbProfile = new dbProfile();

	$tplData = array();

	// save
	if (!empty($_POST['save']) && !empty($_FILES['csv']) && !empty($_FILES['csv']["tmp_name"])) {
		//echo "<pre>".var_export($_POST, true)."</pre>";
		//echo "<pre>".var_export($_FILES, true)."</pre>";

		require_once ('CsvParser.php');
		require_once ('CsvParser.profile.php');
		require_once ('ImportHelper.php');

		// parse and save file
		$helper = new ImportHelper($columnParsers, 'profile');
		$helper->parse($_FILES['csv'], $_POST['order']);
		$saveStatus = $helper->save(function($record, $rowState, $fileId) use ($dbProfile) {
			ImportHelper::insRecord($dbProfile, $record, $rowState, $fileId);
		});
		if ($saveStatus) {
			if (!empty($_POST['overwrite']) && $_POST['overwrite'] === 'y') {
				$dbProfile->pf_delRecords(array('csv_file' => array('!=', $helper->fileId)));
			}
		}
		$tplData['parserInfo'] = $helper->infoBuild();

		// merge profile duplicates
		$duplicates = array();
		$dbProfile->pf_getStats($duplicates, 'profile-duplicates', array(
			'row_state' => 0
		));
		if (!empty($duplicates)) {
			$tplData['parserInfo'] .= "<div class='message note'>"
				. "Wykryto duplikaty profili. Profile zostały połączone wg kryteriów: Płeć, Wiek, Dzielnica. Dla scalonego profilu liczba zaproszeń została zsumowana."
				. "</div>"
			;
			foreach($duplicates as $duplicate) {
				$ids = explode(",", $duplicate['ids']);
				$merge_id = array_pop($ids);
				// set invites for merged row
				$dbProfile->pf_setRecords(
					array(
						'invites_no' => $duplicate['invites_no']
					),
					array('id' => $merge_id)
				);
				// set other as invalid
				$dbProfile->pf_setRecords(
					array(
						'row_state' => CsvRowState::INVALID
					),
					array('id' => array('IN', $ids))
				);
			}
		}
	}
	
	// get
	$tplData['profile'] = array();
	$dbProfile->pf_getStatsMany($tplData['profile'], array('total', 'region-invites'), array(
		'row_state' => 0
	));

	// define CSV columns
	$tplData['columns'] = array(
		null,	// skip
		array(
			'column' => 'group_name',
			'title' => 'Grupa',
		),
		array(
			'column' => 'sex',
			'title' => 'Płeć',
		),
		array(
			'column' => 'age_min_max',
			'title' => 'Wiek (zakres)',
		),
		array(
			'column' => 'region',
			'title' => 'Dzielnica',
		),
		null,
		array(
			'column' => 'invites_no',
			'title' => 'Liczba zaproszeń',
		),
	);

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.import.profile.tpl.php';
?>