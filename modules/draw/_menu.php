<?
	/* @var $pv_menuItem MenuItem */
	$pv_menuItem->title = 'Losowanie';
	$pv_menuItem->order = 2;
	$pv_menuItem->users = AUTH_GROUP_OPS;
	
	$pv_menuItem->addSubItem('summary','Weryfikacja');
	$pv_menuItem->addSubItem('draw','Losowanie');
	$pv_menuItem->addSubItem('results','Wyniki');
?>