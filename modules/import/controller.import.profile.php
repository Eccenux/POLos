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
		$helper->save(function($record, $rowState) use ($dbProfile) {
			ImportHelper::insRecord($dbProfile, $record, $rowState);
		});
		$tplData['parserInfo'] = $helper->infoBuild();
	}
	
	// get
	$tplData['profile'] = array();
	$dbProfile->pf_getStatsMany($tplData['profile'], array('total', 'region-invites'));

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