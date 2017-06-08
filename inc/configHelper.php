<?php
require_once './inc/config.php';

class ConfigHelper
{
	function __construct()
	{
	}

	public function __get($key)
	{
		switch ($key)
		{
			case 'panel_stage':
				return (!defined('PANEL_STAGE')) ? 'draw' : constant('PANEL_STAGE');
			case 'require_secure_connection':
				return defined('REQUIRE_SECURE_CONNECTION') && constant('REQUIRE_SECURE_CONNECTION');
			case 'register_visits':
				return defined('REGISTER_VISITS') && constant('REGISTER_VISITS');
		}
		return null;
	}
}

$configHelper = new ConfigHelper();