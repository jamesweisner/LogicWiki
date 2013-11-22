<?php

// Suppress notices.
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// Override settings.
if(file_exists('settings_local.php'))
	require_once('settings_local.php');

define('MYSQL_HOSTNAME', 'localhost');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_VERSION',  0);
define('MYSQL_DATABASE', 'logic_wiki');
define('SESSION_NAME',   'sid');
