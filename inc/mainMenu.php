<?php
/**
 * Main menu helper.
 *
 * @author Maciej Nux Jaros
 */
class MainMenu implements Iterator
{
	private $items = array();
    public function __construct() {
    }

	// <editor-fold defaultstate="collapsed" desc="Iterator methods">
    function rewind() {
        reset($this->items);
    }
    function current() {
        return current($this->items);
    }
    function key() {
        return key($this->items);
    }
    function next() {
        next($this->items);
    }
    function valid() {
		$key = key($this->items);
		return ($key !== NULL && $key !== FALSE);
	}
	// </editor-fold>

	/**
	 * Add (append) menu item.
	 * @param MenuItem $menuItem Menu item.
	 */
	public function addItem($menuItem)
	{
		$this->items[$menuItem->moduleName] = $menuItem;
	}

	/**
	 * Sort items based on order options.
	 */
	private function sort()
	{
		// sort menu
		$menuOrder = array();
		$menuOrderNames = array();
		foreach ($this->items as $key => $row)
		{
			$menuOrder[$key]  = !is_null($row->order) ? $row->order : 9999;
			$menuOrderNames[$key]  = $key;
		}
		array_multisort($menuOrder, SORT_ASC, $menuOrderNames, SORT_ASC, $this->items);
	}

	/**
	 * Get html encoded module URL.
	 * @param string $moduleName
	 * @param string $moduleAction
	 * @param array $extraParams
	 * @return string
	 */
	public static function getModuleUrl($moduleName, $moduleAction='', $extraParams=array())
	{
		return htmlspecialchars(self::getRawModuleUrl($moduleName, $moduleAction, $extraParams));
	}

	/**
	 * Get raw (non-html encoded) module URL.
	 * 
	 * @note values will be URL-encoded.
	 *
	 * @param string $moduleName
	 * @param string $moduleAction
	 * @param array $extraParams
	 * @return string
	 */
	public static function getRawModuleUrl($moduleName, $moduleAction='', $extraParams=array())
	{
		$url = "?mod={$moduleName}";
		if (!empty($moduleAction))
		{
			$url .= "&a=". rawurlencode($moduleAction);
		}
		foreach($extraParams as $k=>$v)
		{
			$url .= "&". rawurlencode($k) ."=". rawurlencode($v);
		}
		return $url;
	}

	/**
	 * Prepare items before rendenring.
	 */
	public function prepare()
	{
		$this->sort();

		// prepare urls
		foreach ($this->items as $mpoz)
		{
			if (empty($mpoz->url))
			{
				$mpoz->url = $this->getModuleUrl($mpoz->moduleName);
			}
			// one-level submenu
			if (!empty($mpoz->submenu))
			{
				foreach ($mpoz->submenu as &$subpoz)
				{
					if (empty($subpoz['url']))
					{
						$subpoz['url'] = $this->getModuleUrl($mpoz->moduleName, $subpoz['action']);
					}
				}
			}
		}
	}

	/**
	 * Check if given user name is authorized to view the module.
	 *
	 * @note This does NOT check if user is logged in or anything like that. This just checks settings.
	 */
	public function authCheck($moduleName, $userName)
	{
		if (!isset($this->items[$moduleName]))
		{
			return true;
		}
		$menuItem = $this->items[$moduleName];
		return $menuItem->authCheck($userName);
	}
}
