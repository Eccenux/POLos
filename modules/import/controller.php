<?
	/* @var $pv_controller ModuleController */

	require_once ('./inc/dbConnect.php');

	//
	// Crunch data
	//
	switch ($pv_controller->action)
	{
		default:
			include $pv_controller->moduleDir.'/controller.summary.php';
		break;
	}

	//
	// Render prepared template
	//
	$pv_controller->tpl->render();
?>