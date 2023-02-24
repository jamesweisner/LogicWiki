<?php

// Suppress PHP notices.
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);

// West coast, baby!
date_default_timezone_set('America/Los_Angeles');

// Override settings.
@include_once('settings_local.php');

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');
define('MYSQL_NAME', 'logic_wiki');

define('SESSION_ID', 'sid');

define('VERSION', 1);
