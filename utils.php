<?php

function echox($s)
{
	echo htmlspecialchars((string) $s);
}

function firstOf($args)
{
	foreach($args as $arg)
		if(!empty($arg))
			return $arg;
}

function classloader($class)
{
	// Custom class loader.
	include strtolower("classes/$class.php");
}

spl_autoload_register('classloader');
