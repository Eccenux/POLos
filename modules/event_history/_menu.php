<?
	/* @var $pv_menuItem MenuItem */
	$pv_menuItem->title = 'Historia';
	$pv_menuItem->order = 5;
	$pv_menuItem->users = AUTH_GROUP_OPS;
	
	$pv_menuItem->addSubItem('list','historia działań');

	// Note! This is just a visibility thing, NOT actual authorization!
	$users = explode(',', AUTH_GROUP_ADMIN);
	if (in_array($userName, $users))
	{
		$pv_menuItem->addSubItem('list-server','zapisana historia');
	}
?>