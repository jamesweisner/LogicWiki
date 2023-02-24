<form method="post">
	<label>Email</label>
	<input type="text" name="email" value="<?php echox($_POST['email']); ?>" />
	<input type="text" name="name"  value="<?php echox($_POST['name']);  ?>" />
	<input type="submit" value="Register" />
</form>
