<?php
/**
 * Menu item (entry) for the module.
 *
 * Allows incjectiong either a single menu item or an item with submenu (sub-entries).
 * So this is basically li or li > ul.
 *
 * @author Maciej Nux Jaros
 */
class MenuItem
{
	/**
	 * Module name as set by __construct().
	 * @readonly
	 * @var string
	 */
	public $moduleName;
	/**
	 * Title of the module shown to the user in menu.
	 * @var string
	 */
	public $title;
	/**
	 * [optional] A number used to order main menu items (if not given then sorting by module name).
	 * @var int|null
	 */
	public $order = null;
	/**
	 * [optional] Custom URL (i.e. not linking to a module).
	 * @var string|null
	 */
	public $url = null;
	/**
	 * [optional] Users allowed to use this module/action
	 *
	 * Use 'anon' or keep `null` to allow anonymous access.
	 * To be more exact `null` means any authorized user OR anon can access module.
	 * Setting to 'anon' would mean only anonymous access is allowed.
	 *
	 * @var string
	 */
	public $users = null;

	/**
	 * Submenu items.
	 * @readonly Use addSubItem instead.
	 * @var array
	 */
	public $submenu = array();

	/**
	 * @param type $moduleName Keep it set autmatically as a dirname of the module; unless you have a realy good reason not too ;-)
	 */
	public function __construct($moduleName)
	{
		$this->moduleName = $moduleName;
	}

	/**
	 * Add (append) submenu item.
	 * @param type $action Action name.
	 * @param type $title Title (if empty then the same as action).
	 */
	public function addSubItem($action, $title='')
	{
		if (empty($title))
		{
			$title = $action;
		}
		$this->submenu[] = array (
			'action' => $action,
			'title' => $title,
		);
	}

	/**
	 * Check if given user name is authorized to view the menu/module.
	 *
	 * @note This does NOT check if user is logged in or anything like that. This just checks settings.
	 */
	public function authCheck($userName)
	{
		if (is_null($this->users))
		{
			return true;
		}
		$users = explode(',', $this->users);
		if (in_array($userName, $users))
		{
			return true;
		}
		return false;
	}
}
