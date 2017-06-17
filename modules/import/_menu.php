<?
	/* @var $pv_menuItem MenuItem */
	$pv_menuItem->title = 'Import danych';
	$pv_menuItem->order = 1;
	$pv_menuItem->users = AUTH_GROUP_OPS;
	
	$pv_menuItem->addSubItem('profile','Profile');
	$pv_menuItem->addSubItem('personal','Osoby');
	$pv_menuItem->addSubItem('summary','Podsumowanie');
?>