<?
	/* @var $pv_controller ModuleController */

	require_once ('./inc/dbConnect.php');
	require_once ('./inc/db/eventHistory.php');
	$dbEventHistory = new dbEventHistory();

	//
	// Przetwarzanie danych
	//
	switch ($pv_controller->action)
	{
		case 'save':
			$uuid = empty($_POST['uuid']) ? '' : $_POST['uuid'];
			$pv_record = array (
				'uuid' => $uuid,
				'history_data' => empty($_POST['data']) ? '' : $_POST['data'],
			);
			if (empty($uuid)) {
				$pv_controller->tpl->setResponseCode(400);
				$pv_controller->tpl->message = 'Brak identyfikatora!';
			} else {
				// check prev.
				$pv_prev = array();
				$dbEventHistory->pf_getRecords($pv_prev, array('uuid' => $uuid), array('uuid'));
				$result = true;
				if (!empty($pv_prev)) {
					$result = $dbEventHistory->pf_setRecords($pv_record, array('uuid' => $uuid));
				} else {
					$result = $dbEventHistory->pf_insRecord($pv_record);
				}
				if ($result) {
					$pv_controller->tpl->message = "OK[$uuid] " . (!empty($pv_prev) ? 'overwrite' : 'insert');
				}
				else {
					$pv_controller->tpl->setResponseCode(500);
					$pv_controller->tpl->message = 'Nie udało się zapisać danych!';
				}
			}
		break;
		case 'list':
			$pv_controller->tpl->file = 'history.tpl.php';
		break;
		case 'list-server':
			$pv_constraints = array();
			// negative
			$not_uuid = empty($_GET['not_uuid']) ? '' : $_GET['not_uuid'];
			if (!empty($not_uuid)) {
				$pv_constraints['uuid'] = array('NOT IN', $not_uuid);
			}
			// positive
			$uuid = empty($_GET['uuid']) ? '' : $_GET['uuid'];
			if (!empty($uuid)) {
				$pv_constraints['uuid'] = $uuid;
			}
			$dbEventHistory->pf_getStats($pv_items, 'last', $pv_constraints);
			$tplData = array();
			if (!empty($pv_items)) {
				$tplData['history'] = $pv_items[0];
			}

			$pv_controller->tpl->file = 'history-server.tpl.php';
			$pv_controller->tpl->data = $tplData;
		break;
		default:
			$pv_controller->tpl->setResponseCode(403);
			$pv_controller->tpl->message = 'Nie używaj bezpośrednio';
		break;
	}

	//
	// Wyświetlanie template
	//
	$pv_controller->tpl->render();
?>