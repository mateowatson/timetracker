<div class="col-sm text-right">
	<h2 class="sr-only">Account</h2>
	<p>User:
		<?php echo $v_username; ?>
	</p>

	<form action="/logout" method="POST">
		<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
		<input class="btn btn-link" type="submit" value="Logout">
	</form>
</div>
