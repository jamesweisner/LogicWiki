<?php

class Page
{
	private static $resources = array(
		'jquery.js',
		'argument.js',
		'user.js',
		'main.css',
	);

	public static function parse()
	{
		$api = false;
		$action = $_POST ? strtolower($_POST['action']) : null;
		$args = explode('/', firstOf(array($_GET['q'], '/')));
		$page = (string) array_shift($args);
		if($page == 'api')
		{
			$api = true;
			$page = (string) array_shift($args);
		}
		return array($api, $action, $page, $args);
	}

	public static function show($page, $title, $data)
	{
		Page::template('page', array(
			'page'  => $page,
			'title' => $title,
			'data'  => $data,
		));
		exit;
	}

	public static function template($file, $args = array())
	{
		extract($args);
		unset($args);
		try
		{
			ob_start();
			if(!file_exists("templates/$file.php"))
				throw new Exception("Missing template: $file");
			include "templates/$file.php";
			ob_end_flush();
		}
		catch(Exception $e)
		{
			ob_end_clean();
			$message = $e->getMessage();
			$details = $e->getTraceAsString();
			include 'templates/error.php';
			error_log("Template error ($file): " . $e->getMessage());
		}
	}

	public static function bust_cache($file)
	{
		$version = VERSION;
		switch(end(explode('.', $file)))
		{
			case 'css':
				return "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/$file?$version\" />\n";
			case 'js':
				return "<script type=\"text/javascript\" src=\"/js/$file?$version\"></script>\n";
		}
		throw new Exception('Unknown media file type: ' . $file);
	}

	public static function link($page, $identifier, $name)
	{
		$identifier = urlencode($identifier);
		$name = htmlspecialchars($name);
		return "<a href=\"/$page/$identifier\">$name</a>";
	}

	public static function respond($result)
	{
		echo json_encode(array('result' => $result));
		exit;
	}
	public static function redirect($uri)
	{
		header("Location: /$uri");
		exit;
	}
}
