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
		default:
			$pv_controller->tpl->file = 'controller.summary.tpl.php';
		break;
	}

	//
	// Wyświetlanie template
	//
	$pv_controller->tpl->render();
?>