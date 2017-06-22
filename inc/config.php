<?php
	define ('AUTH_GROUP_OPS', 'admin,maciej.j,marcin.g');
	define ('AUTH_GROUP_ADMIN', 'admin,maciej.j');

	if ($_SERVER['HTTP_HOST'] != 'localhost') {
		define ('REQUIRE_SECURE_CONNECTION', true);
		define ('REGISTER_VISITS', true);
	}

	// fake user while testing
	if ($_SERVER['HTTP_HOST'] == 'localhost') {
		$_SERVER['PHP_AUTH_USER'] = 'admin';
	}

