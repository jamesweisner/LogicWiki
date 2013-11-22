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
	case 'search':
	{
		showPage('search', 'Search', array(
			'results' => searchResults(),
		));
		exit;
	}
	case 'argument':
	{
		$argument = argumentView($args);
		showPage('argument', $argument['title'], array(
			'argument' => $argument,
		));
		exit;
	}
	case 'login':
	{
		showPage('login', 'Login', array(
			'error' => userLogin(),
		));
		exit;
	}
	case 'user':
	{
		$user = userView($args);
		showPage('user', $user['title'], array(
			'user' => $user,
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
		showPage('error', 'Page Not Found', array(
			'message' => 'Page not found: ' . htmlspecialchars($_GET['q']),
		));
		exit;
	}
}
