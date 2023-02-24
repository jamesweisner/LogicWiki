<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Logic Wiki - <?php echox($title); ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico" />
		<?php foreach(Page::$resources as $resource): ?>
			<?php echo Page::bust_cache($resource); ?>
		<?php endforeach; ?>
	<body>
		<header>
			<h1>Logic Wiki - <?php echox($title); ?></h1>
			<nav><?php Page::template('nav'); ?></nav>
		</header>
		<main><?php Page::template($page, $data); ?></main>
		<footer>
			<menu>
				<li><a href="/tos">Terms of Service</a></li>
				<li><a href="/faq">About</a></li>
				<li><a href="/contact">Contact</a></li>
				<li>&copy; <?php echox(date('Y')); ?></li>
			</menu>
		</footer>
		<?php foreach(array('notices', 'register', 'login') as $id): ?>
			<dialog id="<?php echox($id); ?>">
				<?php Page::template($id); ?>
			</dialog>
		<?php endforeach; ?>
	</body>
</html>
