<?
	/* @var $pv_controller ModuleController */

	require_once ('./inc/dbConnect.php');

	//
	// Crunch data
	//
	switch ($pv_controller->action)
	{
		default:
		case 'invitations':
			include $pv_controller->moduleDir.'/controller.invitations.php';
		break;
		case 'survey':
			include $pv_controller->moduleDir.'/controller.survey.php';
		break;
	}

	//
	// Render prepared template
	//
	$pv_controller->tpl->render();
?>