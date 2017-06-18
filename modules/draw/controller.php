<?
	/* @var $pv_controller ModuleController */

	require_once ('./inc/dbConnect.php');

	//
	// Crunch data
	//
	switch ($pv_controller->action)
	{
		case 'profile':
			include $pv_controller->moduleDir.'/controller.import.profile.php';
		break;
		case 'personal':
			include $pv_controller->moduleDir.'/controller.import.personal.php';
		break;
		default:
			include $pv_controller->moduleDir.'/controller.summary.php';
		break;
	}

	//
	// Render prepared template
	//
	$pv_controller->tpl->render();
?>