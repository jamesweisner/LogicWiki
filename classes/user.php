<?php

class User
{
	public $user_id, $data;

	public function __construct($user_id)
	{
		$this->user_id = $user_id;
		$this->data = DB::row("SELECT * FROM user WHERE user_id = %d", array($user_id));
		if(!$this->data)
			throw new Exception('User not found.');
	}

	public static function init()
	{
		session_name(SESSION_ID);
		session_start();
		session_write_close();
	}

	public static function register($email, $name)
	{
		if(empty($email))
			return 'Please specify an email address';
		if(empty($name))
			return 'Please specify a name.';
		if(DB::result("SELECT COUNT(*) FROM user WHERE email = '%s'", array($email)))
			return 'An account with that email address already exists.';
		DB::insert('user', array('email' => $email, 'name' => $name));
		User::login($email);
		return null;
	}

	public static function login($email)
	{
		if(!($user_id = DB::result("SELECT user_id FROM user WHERE email = '%s'", array($email))))
			return 'Unrecognized email address.';
		session_start();
		$_SESSION['user_id'] = $user_id;
		session_write_close();
		return null;
	}

	public static function logout()
	{
		session_destroy();
		session_unset();
	}

	public function set_name($name)
	{
		// Users may change their own display name.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		return DB::query("UPDATE user SET name = '%s' WHERE user_id = %d", array($name, $this->user_id));
	}

	public function set_friend($friend_id, $keep, $notify)
	{
		// Add, update, or remove a friend.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($keep)
		{
			return DB::replace('friend', array(
				'user_id'   => $this->user_id,
				'friend_id' => $friend_id,
				'notify'    => $notify,
			));
		}
		else
		{
			return DB::query("
				DELETE FROM friend
				WHERE user_id = %d
				AND friend_id = %d
			", array($this->user_id, $friend_id));
		}
	}

	public function clear_notice($notice_id)
	{
		// Clear one or all of a user's notices.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($notice_id)
		{
			return DB::query("
				DELETE FROM notice
				WHERE user_id = %d
				AND notice_id = %d
			", array($this->user_id, $notice_id));
		}
		else
		{
			return DB::query("
				DELETE FROM notice
				WHERE user_id = %d
			", array($this->user_id));
		}
	}

	public function new_notices($notice_id)
	{
		// The website periodically checks for new notices.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		return DB::all("
			SELECT *
			FROM notice USE INDEX (check_new)
			WHERE user_id = %d
			AND notice_id > %d
			ORDER BY user_id, notice_id DESC
			LIMIT 100
		", array($this->user_id, $notice_id));
	}
}
