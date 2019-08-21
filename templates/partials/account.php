<div class="col-lg-4 d-flex flex-row justify-content-end align-items-center">
	<h2 class="sr-only">Account</h2>
	
	<?php if(!$v_user_email_verified && $v_user_email): ?>
		<a class="btn btn-link" href="/verify-email">Verify Email</a>
	<?php endif; ?>
	
	<a class="btn btn-link" href="/account"><?php echo $v_username; ?></a>

	<form class="ml-3" action="/logout" method="POST">
		<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
		<input class="btn btn-link pr-0" type="submit" value="Logout">
	</form>
</div>
