<?php

function mysqlConnect()
{
	static $link;
	if($link) return $link;
	if(!($link = mysql_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD)))
	{
		handleError('MySQL connection failure.');
		return false;
	}
	if(!mysql_select_db(MYSQL_DATABASE))
	{
		handleError('MySQL select database failure.');
		return false;
	}
	$result = mysqlQuery("SELECT value FROM Settings WHERE name = 'version'");
	$version = (int) mysql_result($result, 0);
	if($version < MYSQL_VERSION)
	{
		for($i = $version; $i < MYSQL_VERSION; $i++)
		{
			$file = 'includes/install-' . $i . '.sql';
			if(file_exists($file))
				mysqlQuery(file_get_contents($file));
			else
				handleError('MySQL install file not found.', array(
					'file' => $file,
				));
		}
		$version = intVal(MYSQL_VERSION);
		mysqlQuery("UPDATE Settings SET value = $version AND name = 'version'");
	}
	return $link;
}

function mysqlQuery($query, $type = false)
{
	static $result;
	if($result) mysql_free_result($result);
	if(!($result = mysql_query($query)))
	{
		handleError('MySQL error.', array(
			'query' => $query,
			'errno' => mysql_errno(),
			'error' => mysql_error(),
		));
	}
	switch($type)
	{
		case 'INSERT':
		case 'REPLACE':
			return mysql_insert_id();
		case 'UPDATE':
			return mysql_affected_rows();
	}
	return $result;
}

function mysqlFetchAll($result, $key = '', $keyIsUnique = true)
{
	$data = array();
	while($row = mysql_fetch_assoc($result))
	{
		if($key)
		{
			if($keyIsUnique) $data[$row[$key]]   = $row;
			else             $data[$row[$key]][] = $row;
		}
		else
		{
			$data[] = $row;
		}
	}
	return $data;
}

function mysqlInsert($table, $fields, $replace = false, $ignore = false)
{
	if(!is_array($fields) || !count($fields))
	{
		handleError('MySQL insert field list error.', array(
			'table'  => $table,
			'fields' => serialize($fields),
		));
		return false;
	}
	foreach($fields as $field => $value)
	{
		$columns[] = "`$field`";
		$values[]  = "'" . mysql_real_escape_string($value) . "'";
	}
	$action = $replace ? 'REPLACE' : 'INSERT';
	if($ignore) $action .= ' IGNORE';
	$query = "$action INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
	return mysqlQuery($query);
}
