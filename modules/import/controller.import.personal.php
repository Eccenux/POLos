<?
	/**
		@file Import profili zaproszeniowych.
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	
	require_once ('./inc/db/personal.php');
	$dbPersonal = new dbPersonal();

	$tplData = array();

	// disable time limit
	set_time_limit(0);

	// save
	if (!empty($_POST['save']) && !empty($_FILES['csv']) && !empty($_FILES['csv']["tmp_name"])) {
		//echo "<pre>".var_export($_POST, true)."</pre>";
		//echo "<pre>".var_export($_FILES, true)."</pre>";

		require_once ('CsvParser.php');
		require_once ('CsvParser.personal.php');
		require_once ('ImportHelper.php');

		// parse and save file
		$helper = new ImportHelper($columnParsers, 'personal');
		$helper->parse($_FILES['csv'], $_POST['order']);
		$saveStatus = $helper->save(function($record, $rowState, $fileId) use ($dbPersonal) {
			ImportHelper::insRecord($dbPersonal, $record, $rowState, $fileId);
		});
		if ($saveStatus) {
			if (!empty($_POST['overwrite']) && $_POST['overwrite'] === 'y') {
				$dbPersonal->pf_delRecords(array('csv_file' => array('!=', $helper->fileId)));
			}
		}
		$tplData['parserInfo'] = $helper->infoBuild();
	}
	
	// get
	$tplData['personal'] = array();
	$dbPersonal->pf_getStatsMany($tplData['personal'], array('total', 'region-counts'), array(
		'row_state' => 0
	));

	// define CSV columns
	$tplData['columns'] = array(
		array('column' => 'region',		'title' => 'Dzielnica'),
		array('column' => 'pesel',		'title' => 'PESEL'),
		array('column' => 'name',		'title' => 'Imię'),
		array('column' => 'surname',	'title' => 'Nazwisko'),
		array('column' => 'city',		'title' => 'Miasto'),
		array('column' => 'street',		'title' => 'Ulica'),
		array('column' => 'building_no','title' => 'Nr budynku'),
		array('column' => 'flat_no',	'title' => 'Nr lokalu'),
		array('column' => 'zip_code',	'title' => 'Kod pocztowy'),
	);

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.import.personal.tpl.php';
?>