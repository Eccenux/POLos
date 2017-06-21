<?
	/* @var $pv_menuItem MenuItem */
	$pv_menuItem->title = 'Eksport danych';
	$pv_menuItem->order = 3;
	$pv_menuItem->users = AUTH_GROUP_OPS;
	
	$pv_menuItem->addSubItem('invitations','Zaproszenia');
	$pv_menuItem->addSubItem('survey','Ankiety');
?>