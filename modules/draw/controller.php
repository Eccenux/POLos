<?
	/* @var $pv_controller ModuleController */

	require_once ('./inc/dbConnect.php');

	require_once ('./inc/db/profile.php');
	$dbProfile = new dbProfile();
	require_once ('./inc/db/personal.php');
	$dbPersonal = new dbPersonal();

	// get
	$total = array();
	$total['profile'] = array();
	$dbProfile->pf_getStats($total['profile'], 'total', array(
		'row_state' => 0
	));
	$total['personal'] = array();
	$dbPersonal->pf_getStats($total['personal'], 'total', array(
		'row_state' => 0
	));

	// validate draw
	$drawPossible = true;
	$drawValidationMessage = "";
	if ($total['profile'][0]['profiles'] < 1) {
		$drawPossible = false;
		$drawValidationMessage = "Nie można wykonać losowania. Musisz najpierw przesłać profile zaproszeniowe.";
	} else if ($total['personal'][0]['people'] < 1) {
		$drawPossible = false;
		$drawValidationMessage = "Nie można wykonać losowania. Musisz najpierw przesłać dane osobowe.";
	} else if ($total['profile'][0]['invites'] > $total['personal'][0]['people']) {
		$drawPossible = false;
		$drawValidationMessage = "Nie można wykonać losowania. Liczba zaproszeń jest większa niż liczba osób.";
	}

	//
	// Crunch data
	//
	switch ($pv_controller->action)
	{
		default:
			include $pv_controller->moduleDir.'/controller.summary.php';
		break;
		case 'draw':
			if ($drawPossible) {
				include $pv_controller->moduleDir.'/controller.draw.php';
			} else {
				$pv_controller->tpl->message = $drawValidationMessage;
			}
		break;
	}

	//
	// Render prepared template
	//
	$pv_controller->tpl->render();
?>