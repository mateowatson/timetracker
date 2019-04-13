<section class="account">
	<h1 class="account__heading">Account</h1>
	<p class="account__username">User:
		<?php echo $v_username; ?>
	</p>

	<form action="/logout" method="POST" class="account__logout">
		<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
		<input class="account__button" type="submit" value="Logout">
	</form>
</section>