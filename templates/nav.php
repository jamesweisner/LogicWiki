<menu>
	<li><a href="/search">Search</a></li>
	<?php if($_SESSION['user_id']): ?>
		<li><a href="/notices">Notices</a></li>
		<li><a href="/friends">Friends</a></li>
		<li><a href="/user">Profile</a></li>
		<li><a href="/logout">Logout</a></li>
	<?php else: ?>
		<li><a href="/register">Register</a></li>
		<li><a href="/login?origin=<?php echo urlencode($_GET['q']); ?>">Login</a></li>
	<?php endif; ?>
</menu>
