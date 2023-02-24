<?php

require_once 'settings.php';
require_once 'utils.php';

User::init();

list($api, $action, $page, $args) = Page::parse();

try
{
	switch($page)
	{
		case '':
		case 'search':
		{
			Page::show('search', 'Search', array(
				'results' => Search::results(),
			));
		}
		case 'argument':
		{
			$arg_id = (int) array_shift($args);
			$argument = Argument::get($arg_id);
			if($action) switch($action)
			{
				case 'validity':
					// Any user, besides the author, can endorse the validity of the argument.
					$support = (bool) $_POST['support'];
					$remove  = (bool) $_POST['remove'];
					Page::respond($argument->endorse_validity($support, $remove));
				case 'soundness':
					// Any user, besides the argument's author, can endorse the soundness of individual premises.
					$support = (bool) $_POST['support'];
					$remove  = (bool) $_POST['remove'];
					$prop_id =  (int) $_POST['prop_id'];
					Page::respond($argument->endorse_soundness($prop_id, $support, $remove));
				case 'publish':
					// Publish or unpublish an argument. Re-publishing an argument increments its version.
					$published = (bool) $_POST['published'];
					Page::respond($argument->set_published($published));
				case 'set-premise':
					// Add, update, or remove a premise of an unpublished argument.
					$prop_id  =    (int) $_POST['prop_id'];
					$contents = (string) $_POST['contents'];
					$num      =    (int) $_POST['num'];
					Page::respond($argument->set_premise($prop_id, $contents, $num));
				case 'set-conclusion':
					// Update an unpublished argument's conclusion.
					$prop_id  =    (int) $_POST['prop_id'];
					$contents = (string) $_POST['contents'];
					Page::respond($argument->set_conclusion($prop_id, $contents));
				case 'set-comment':
					// Add, update, or delete a comment. Last updated time is shown publically.
					$comment_id =    (int) $_POST['comment_id'];
					$contents   = (string) $_POST['contents'];
					$delete     =   (bool) $_POST['delete'];
					Page::respond($argument->set_comment($comment_id, $contents, $delete));
			}
			Page::show('argument', $argument->title(), array(
				'argument' => $argument,
			));
		}
		case 'register':
		{
			if($action) switch($action)
			{
				case 'register':
					$email  = (string) $_POST['email'];
					$name   = (string) $_POST['name'];
					$origin = (string) $_GET['origin'];
					if(!($error = User::register($email, $name)))
						Page::redirect($origin);
			}
			Page::show('register', 'Register', array(
				'error' => $error,
			));
		}
		case 'login':
		{
			if($action) switch($action)
			{
				case 'login':
					$email  = (string) $_POST['email'];
					$origin = (string) $_GET['origin'];
					if(!($error = User::login($email)))
						Page::redirect($origin);
			}
			Page::show('login', 'Login', array(
				'error' => $error,
			));
		}
		case 'user':
		{
			$user_id = (int) firstOf(array(array_shift($args), $_SESSION['user_id']));
			$user = User::get($user_id);
			if($action) switch($action)
			{
				case 'set-name':
					// Users may change their own display name.
					$name = (string) $_POST['name'];
					Page::respond($user->set_name($name));
				
				case 'clear-notice':
					// Clear one or all of a user's notices.
					$notice_id = (int) $_POST['notice_id'];
					Page::respond($user->clear_notice($notice_id));
				case 'new-notices':
					// The website periodically checks for new notices.
					$notice_id = (int) $_POST['notice_id'];
					Page::respond($user->new_notices($notice_id));
			}
			Page::show('user', $user->title(), array(
				'user' => $user,
			));
		}
		case 'friends':
		{
			$user = User::get($_SESSION['user_id']);
			if($action) switch($action)
			{
				case 'set-friend':
					// Add, update, or remove a friend.
					$friend_id =  (int) $_POST['friend_id'];
					$keep      = (bool) $_POST['keep'];
					$notify    = (bool) $_POST['notify'];
					Page::respond($user->set_friend($friend_id, $keep, $notify));
			}
			Page::show('friends', 'Friends', array(
				'friends' => $user->friends(),
			));
		}
		case 'logout':
		{
			User::logout();
			header('Location: /');
			exit;
		}
		default:
		{
			if($api)
			{
				echo json_encode(array('error' => 'Method not found.'));
				exit;
			}
			header('HTTP/1.0 404 Not Found');
			Page::show('error', 'Page Not Found', array(
				'message' => 'Page not found: ' . htmlspecialchars($_GET['q']),
			));
		}
	}
}
catch(Exception | Error $e)
{
	if($api)
	{
		echo json_encode(array('error' => $e->getMessage()));
		exit;
	}
	header('HTTP/1.0 500 Internal Server Error');
	Page::show('error', 'Error', array(
		'message' => $e->getMessage(),
		'details' => $e->getTraceAsString(),
	));
}
