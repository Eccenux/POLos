<?
	/**
		@file Zapis
	*/
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	/* @var $dbProfile dbProfile */
	/* @var $dbPersonal dbPersonal */
	
	// data
	$profile_id = empty($_POST['profile']) ? '' : $_POST['profile'];
	$person_ids = empty($_POST['persons']) ? '' : $_POST['persons'];
	$verification = empty($_POST['verification']) ? '' : $_POST['verification'];
	// validate
	if (empty($profile_id) || empty($person_ids) || empty($verification))
	{
		$pv_controller->tpl->setResponseCode(400);
		$pv_controller->tpl->message = 'Brak wymaganych danych!';
	}
	else
	{
		if ($dbPersonal->pf_setRecords(array('profile_id' => $profile_id), array('id' => array('IN', $person_ids))))
		{
			$pv_controller->tpl->message = "OK ($profile_id, $person_ids)";
		}
		else
		{
			$pv_controller->tpl->setResponseCode(500);
			$pv_controller->tpl->message = "Nie udało się zapisać danych! ($profile_id)";
		}
	}
?>