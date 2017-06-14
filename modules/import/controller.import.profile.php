<?
	/**
		@file Podsumowanie (statystyki)
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	
	require_once ('./inc/db/profile.php');
	$dbProfile = new dbProfile();
	
	// get
	$tplData = array();
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