<?
	/**
		@file Wyniki
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	/* @var $dbProfile dbProfile */
	/* @var $dbPersonal dbPersonal */
	
	// get
	$tplData = array();

	$dbProfile->pf_getStats($tplData['profiles-results'], 'profiles-results');
	$pv_controller->tpl->file = 'controller.results.tpl.php';

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
?>