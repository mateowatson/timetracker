<div class="col-lg-4 d-flex flex-row justify-content-end align-items-center">
	<h2 class="sr-only">Account</h2>
	
	<?php if(!$v_user_email_verified && $v_user_email && $EMAIL_ENABLED): ?>
		<a class="btn btn-link" href="<?php echo $SITE_URL; ?>/verify-email">Verify Email</a>
	<?php endif; ?>

	<?php if($v_search_link): ?>
		<a class="btn btn-link" href="<?php echo $v_search_link; ?>">Search</a>
	<?php else: ?>
		<a class="btn btn-link" href="<?php echo $SITE_URL; ?>/search">Search</a>
	<?php endif; ?>
	
	<a class="btn btn-link" title="<?php echo $v_username; ?>" href="<?php echo $SITE_URL; ?>/account">Account</a>

	<form action="<?php echo $SITE_URL; ?>/logout" method="POST">
		<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
		<input class="btn btn-link pr-0" type="submit" value="Logout">
	</form>
</div>
