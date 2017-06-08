<?php
require_once './inc/moduleTemplate.php';

/**
 * Controller helper.
 *
 * @author Maciej Nux Jaros
 */
class ModuleController
{
	/**
	 * Action within module received from user.
	 *
	 * If empty then default sub-controller for the module should be used.
	 * 
	 * @readonly
	 * @var string
	 */
	public $action;
	/**
	 * @readonly
	 * @var string
	 */
	public $moduleDir;
	/**
	 * Path to public directory of the module.
	 * @readonly
	 * @var string
	 */
	public $modulePublicDir;
	/**
	 * Main controller file path.
	 * @readonly
	 * @var string
	 */
	public $controllerPath;
	/**
	 * @readonly
	 * @var string
	 */
	public $moduleName;

	/**
	 * @var ModuleTemplate
	 */
	public $tpl;

	/**
	 * Get URL for this module for given action.
	 * @param string $action
	 * @param array $extraParams
	 */
	public function getActionUrl($action, $extraParams=array())
	{
		return MainMenu::getModuleUrl($this->moduleName, $action, $extraParams);
	}

	/**
	 * Get raw (non-html encoded) URL for this module for given action.
	 * @param string $action
	 * @param array $extraParams
	 */
	public function getRawActionUrl($action, $extraParams=array())
	{
		return MainMenu::getRawModuleUrl($this->moduleName, $action, $extraParams);
	}

	/**
	 * @param string $moduleName Module name.
	 * @param string $action Action within module.
	 */
	public function __construct($moduleName, $action)
	{
		$this->moduleName = $moduleName;
		$this->moduleDir = './modules/'.$moduleName.'/';
		$this->controllerPath = './modules/'.$moduleName.'/controller.php';
		$this->modulePublicDir = './modules_public/'.$moduleName.'/';
		$this->action = $action;
		$this->tpl = new ModuleTemplate($this->moduleDir);
	}
}
