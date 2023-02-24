<?php

class DB
{
	private static function connect()
	{
		static $db;
		if(!isset($db))
		{
			if(!extension_loaded('mysqli'))
				throw new Exception('Failed to load extension: mysqli');
			$db = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_NAME);
			if($db->connect_error)
			{
				throw new Exception('Database connection failed: ' . implode(' ', array(
					$db->connect_errno,
					$db->connect_error,
					MYSQL_HOST,
					MYSQL_USER,
					MYSQL_NAME,
				)));
				return $db = false;
			}
			$db->set_charset('utf8');
		}
		return $db;
	}

	public static function query($query, $args = array())
	{
		if(!($db = self::connect()))
			return false;
		foreach($args as &$arg)
			$arg = $db->real_escape_string($arg);
		array_unshift($args, $query);
		$query = call_user_func_array('sprintf', $args);
		if(!($result = $db->query($query)))
			throw new Exception('Database query failed: ' . $query);
		return $result;
	}

	public static function insert($table, $row, $replace = false)
	{
		if(!($db = self::connect()))
			return false;
		$fields = array();
		$values = array();
		foreach($row as $field => $value)
		{
			$fields[] = "`$field`";
			if($value === null)
				$values[] = 'NULL';
			else
				$values[] = "'" . str_replace('%', '%%', $db->real_escape_string($value)) . "'";
		}
		$fields = implode(', ', $fields);
		$values = implode(', ', $values);
		$action = $replace ? 'REPLACE' : 'INSERT';
		$sql = "$action INTO `$table` ($fields) VALUES ($values)";
		if(!self::query($sql))
			return false;
		return $db->insert_id;
	}

	public static function replace($table, $row)
	{
		self::insert($table, $row, true);
	}

	public static function result($query, $args = array())
	{
		if(!($result = self::query($query, $args)))
			return false;
		$row = $result->fetch_row();
		if(!is_array($row))
			return null; // No restuls returned.
		return reset($row);
	}

	public static function row($query, $args = array())
	{
		if(!($result = self::query($query, $args)))
			return false;
		return $result->fetch_assoc();
	}

	public static function all($query, $args = array(), $key = null)
	{
		if(!($result = self::query($query, $args)))
			return false;
		$rows = array();
		while($row = $result->fetch_assoc())
			if($key)
				$rows[$row[$key]] = $row;
			else
				$rows[] = $row;
		return $rows;
	}

	public static function update($query, $args = array())
	{
		if(!($db = self::connect()))
			return false;
		if(!self::query($query, $args))
			return false;
		return $db->affected_rows > 0;
	}

	public function now()
	{
		// Trust PHP time zone.
		return date('Y-m-d H:i:s');
	}
}
