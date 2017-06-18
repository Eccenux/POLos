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

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.summary.tpl.php';
?>