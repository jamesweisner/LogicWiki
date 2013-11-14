<?php

require_once 'settings.php'; // Will include settings_local.php, if it exists.
require_once './includes/common.php';
require_once './includes/mysql.php';
require_once './includes/argument.php';
require_once './includes/search.php';
require_once './includes/user.php';

userSession();  // Initialize user session.
mysqlConnect(); // Connect to MySQL database.

// Page navigation.
$args = explode('/', $_GET['q']);
switch($page = array_pop($args))
{
	case '':
	{
		template('search', array(
			'results' => searchResults(),
		));
		exit;
	}
	case 'argument':
	{
		template('argument', array(
			'argument' => argumentView($args),
		));
		exit;
	}
	case 'login':
	{
		template('login', array(
			'error' => userLogin(),
		));
		exit;
	}
	case 'user':
	{
		template('user', array(
			'user' => userView($args),
		));
		exit;
	}
	case 'logout':
	{
		userLogout();
		header('Location: /');
		exit;
	}
	default:
	{
		header('HTTP/1.0 404 Not Found');
		template('error', array(
			'message' => 'Page not found: ' . htmlspecialchars($_GET['q']),
		));
		exit;
	}
}
