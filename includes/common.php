<?php

function template($file, $args = array())
{
	$game = getGame();
	extract($args);
	unset($args);
	if(!file_exists("./templates/$game/$file.html"))
		$game = 'default';
	include "./templates/$game/$file.html";
}

function handleError($error, $args = null)
{
	$args = is_array($args) ? json_encode($args) : '{}';
	error_log("$error $args");
}

function firstOf($args)
{
	foreach($args as $arg)
		if(!empty($arg))
			return $arg;
}

function tail($file, $count)
{
	$fp = fopen($file, 'r');
	$position = filesize($file);
	fseek($fp, $position - 1);
	$chunklen = 4096;
	while($position > 0)
	{
		$position -= $chunklen;
		if($position < 0)
		{
			$chunklen += $position;
			$position = 0;
		}
		fseek($fp, $position);
		$data = fread($fp, $chunklen) . $data;
		if(substr_count($data, "\n") >= $count + 1)
		{
			preg_match('/(.*?\n){' . ($count - 1) . '}$/', $data, $match);
			$data = $match[0];
			break;
		}
	}
	fclose($fp);
	return $data;
}
