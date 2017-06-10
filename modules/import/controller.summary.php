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
	require_once ('./inc/db/personal.php');
	$dbPersonal = new dbPersonal();
	
	// get
	$tplData = array();
	$tplData['profile'] = array();
	$dbProfile->pf_getStatsMany($tplData['profile'], array('total', 'region-invites'));
	$tplData['personal'] = array();
	$dbPersonal->pf_getStatsMany($tplData['personal'], array('total', 'region-counts'));

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.summary.tpl.php';
?>