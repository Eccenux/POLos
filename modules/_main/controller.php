<?
	/**
		@file Strona główna
	*/
	/* @var $pv_controller ModuleController */

	//
	// Przetwarzanie danych
	//
	switch ($pv_controller->action)
	{
		case 'auth-fail':
			$pv_controller->tpl->file = 'auth-fail.tpl.php';
		break;
		case 'README':
		case 'INSTALL':
		case 'UPDATE':
		case 'HELP':
		case 'LICENSE':
			$pv_controller->tpl->file = $pv_controller->action.'.html';
		break;
		default:
		break;
	}

	//
	// Wyświetlanie template
	//
	$pv_controller->tpl->render();
?>