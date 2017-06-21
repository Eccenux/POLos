<?
	/**
	 *	@file Ubber controller :-)
	 *
	 *	@param mod Module name
	 *	@param display Display mode:
	 *		\li (default) - Standard mode (display page with headers)
	 *		\li =raw      - Omit headers (just render content)
	 *	@param a [optional] Module action (receive be $pv_controller->pv_action)
	 *
	 *	@note Each _menu.php is a main menu definition file (example: /modules/_main/_menu.php)
	 *		and receives a $pv_menuItem of type MenuItem
	 *	@note Each controller.php file is a main controller of a module (example: /modules/_main/controller.php)
	 *		and receives a $pv_controller of type ModuleController
	 */
	define('NOT_HACKING_RILI', true);
	date_default_timezone_set('Europe/Paris');

	require_once './inc/configHelper.php';
	require_once './inc/menuItem.php';
	require_once './inc/mainMenu.php';
	require_once './inc/moduleController.php';
	require_once './inc/dirHelper.php';
	require_once './inc/visitLogger.php';

	// setup error handling
	include './inc/fun_error.php';
	init_myErrorHandler();

	// setup performance check
	include './inc/Ticks.php';
	$ticks = new cTicks();

	//
	// Display mode and other params
	//
	$isJustContentMode = false;
	if (!empty($_GET['display']) && $_GET['display']=='raw')
	{
		$isJustContentMode = true;
	}
	$moduleName = !isset($_GET['mod']) ? '' : $_GET['mod'];
	$moduleAction = empty($_GET['a']) ? '' : $_GET['a'];
	// simple HTTP auth
	$userName = empty($_SERVER['PHP_AUTH_USER']) ? 'anon' : $_SERVER['PHP_AUTH_USER'];

	//
	// Main menu and names of modules
	//
	$ticks->pf_insTick('MainMenu');
	$pv_mainMenu = new MainMenu();
	$pv_modules = DirHelper::getSubdirectories("./modules/");
	foreach($pv_modules as $mname)
	{
		$pv_menuItem = new MenuItem($mname);
		@include('./modules/'.$mname.'/_menu.php');
		$pv_mainMenu->addItem($pv_menuItem, $userName);
	}
	$ticks->pf_endTick('MainMenu');

	//
	// Setup current module
	//
	if (!$pv_mainMenu->authCheck($moduleName, $userName))
	{
		$moduleName = '_main';
		$moduleAction = 'auth-fail';
	}
	// require HTTPS
	if ($configHelper->require_secure_connection && !VisitLogger::isSecure()) {
		$moduleName = '_main';
		$moduleAction = 'auth-fail';
	}
	// set current
	if (!in_array($moduleName, $pv_modules))
	{
		$moduleName = '_main';
	}
	$pv_controller = new ModuleController($moduleName, $moduleAction);

	//
	// Render current module
	//
	$ticks->pf_insTick('Controller');
	ob_start();
	if (is_file($pv_controller->controllerPath))
	{
		include($pv_controller->controllerPath);
	}
	$pv_page_content = ob_get_clean();
	$ticks->pf_endTick('Controller');

	//
	// Register visit
	//
	if ($configHelper->register_visits) {
		VisitLogger::register($pv_controller);
	}

	//
	// Raw display mode
	//
	if ($isJustContentMode)
	{
		echo $pv_page_content;
	}
	//
	// Std display mode
	//
	else
	{
		$pv_titles = $pv_mainMenu->getPageTitles($moduleName, $moduleAction);
		$pv_page_title = $pv_titles['title'];
		
		$ticks->pf_insTick('RenderMenu');
		// Prepare&render menu
		$pv_mainMenu->prepare();
		ob_start();
		include("./tpls/_menu.tpl.php");
		$pv_mainMenu = ob_get_clean();
		$ticks->pf_endTick('RenderMenu');

		// Render page
		include('./tpls/_structure.tpl.php');
	}
?>