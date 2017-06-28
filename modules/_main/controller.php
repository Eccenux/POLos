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
		case 'INSTALL':
			$pv_controller->tpl->file = 'INSTALL.html';
		break;
		case 'LICENSE':
			$pv_controller->tpl->file = 'LICENSE.html';
		break;
		default:
			$pv_controller->tpl->file = 'README.html';
		break;
	}

	//
	// Wyświetlanie template
	//
	$pv_controller->tpl->render();
?>