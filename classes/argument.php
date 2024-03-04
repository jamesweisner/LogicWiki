<?php

class Argument
{
	public $arg_id, $premises, $data;

	public function __construct($arg_id)
	{
		$this->arg_id = $arg_id;
		$this->premises = array();
		$this->data = DB::row("SELECT * FROM argument WHERE arg_id = %d", array($arg_id));
		if(!$this->data)
			throw new Exception('Argument not found.');
		foreach(DB::all("SELECT * FROM premise WHERE arg_id = %d", array($arg_id)) as $p)
			$this->premises[$p['num']] = $p['prop_id'];
	}

	public function new($conclusion, $premises)
	{
		$this->arg_id = DB::insert('argument', array(
			'conclusion'   => $conclusion,
			'author'       => $_SESSION['user_id'],
			'version'      => 0,
			'published'    => 0,
			'popularity'   => 0,
			'endorsements' => 0,
		));
		$this->premises = $;
	}

	public function endorse_validity($support, $remove)
	{
		// Any user, besides the author, can endorse the validity of the argument.
		if(!$_SESSION['user_id'])
			throw new Exception('Please log in.');
		if($this->user_id == $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($remove)
		{
			// Remove previous endorsement.
			return DB::query("
				DELETE FROM endorsement
				WHERE user_id = %d
				AND arg_id = %d
			", array($_SESSION['user_id'], $this->arg_id));
		}
		else
		{
			// Add or update endorsement.
			return DB::replace('validity', array(
				'user_id' => $_SESSION['user_id'],
				'arg_id'  => $arg_id,
				'support' => $support,
			));
		}
	}

	public function endorse_soundness($prop_id, $support, $remove)
	{
		// Any user, besides the argument's author, can endorse the soundness of individual premise.
		if(!$_SESSION['user_id'])
			throw new Exception('Please log in.');
		if($this->user_id == $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if(!in_array($prop_id, array_values($this->premises)))
			throw new Exception('Proposition is not a premise of this argument.');
		if($remove)
		{
			// Remove previous endorsement.
			return DB::query("
				DELETE FROM endorsement
				WHERE user_id = %d
				AND prop_id = %d
			", array($_SESSION['user_id'], $prop_id));
		}
		else
		{
			// Set or update endorsement.
			return DB::replace('soundness', array(
				'user_id' => $_SESSION['user_id'],
				'prop_id' => $prop_id,
				'support' => $support,
			));
		}
	}

	public function set_published($published)
	{
		// Publish or unpublish an argument. Re-publishing an argument increments its version.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($published)
		{
			// Publish this argument.
			if($this->data['published'])
				throw new Exception('Already published.');
			return DB::query("
				UPDATE argument
				SET published = NOW(), version = version + 1
				WHERE arg_id = %d
			", array($this->arg_id));
		}
		else
		{
			// Unpublish this argument.
			if(!$this->data['published'])
				throw new Exception('Not currently published.');
			$count = DB::result("SELECT COUNT(*) FROM validity WHERE arg_id = %d", array($this->arg_id));
			if($count > 100)
				throw new Exception('Cannot unpublish an argument with 100+ endorsements.');
			if($count)
				DB::query("DELETE FROM validity WHERE arg_id = %d", array($this->arg_id));
			return DB::query('UPDATE argument SET published = NULL WHERE arg_id = %d', array($this->arg_id));
		}
	}

	private function new_proposition($contents)
	{
		return DB::insert('proposition', array(
			'contents' => $contents,
		));
	}

	public function set_premise($prop_id, $contents, $num)
	{
		// Add, update, or remove a premise of an unpublished argument.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($this->data['published'])
			throw new Exception('Cannot change published argument.');
		if(!$prop_id) $prop_id = $this->new_proposition($contents);
		return DB::replace('premise', array(
			'arg_id'  => $this->arg_id,
			'prop_id' => $prop_id,
			'num'     => $num,
		));
	}

	public function set_conclusion($prop_id, $contents)
	{
		// Update an unpublished argument's conclusion.
		if($this->user_id != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($this->data['published'])
			throw new Exception('Cannot change published argument.');
		if(!$prop_id) $prop_id = $this->new_proposition($contents);
		return DB::query("
			UPDATE argument
			SET conclusion = %d
			WHERE arg_id = %d
		", array($prop_id, $this->arg_id));
	}

	public function set_comment($comment_id, $contents, $delete)
	{
		// Add, update, or delete a comment. Last updated time is shown publically.
		if($comment_id)
		{
			return DB::insert('comment', array(
				'arg_id'   => $this->arg_id,
				'user_id'  => $_SESSION['user_id'],
				'contents' => $contents,
			));
		}
		$author = DB::result("
			SELECT user_id
			FROM comment
			WHERE comment_id = %d
		", array($comment_id));
		if($author != $_SESSION['user_id'])
			throw new Exception('Permission denied.');
		if($delete)
		{
			return DB::query("
				DELETE FROM comment
				WHERE comment_id = %d
			", array($comment_id));
		}
		else
		{
			return DB::query("
				UPDATE comment
				SET edited = NOW(), contents = '%s'
				WHERE comment_id = %d
			", array($comment_id, $contents));
		}
	}
}
