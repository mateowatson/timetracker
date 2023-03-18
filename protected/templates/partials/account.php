<div class="col-lg-7 d-flex flex-row flex-wrap flex-md-nowrap justify-content-end align-items-baseline">
	<h2 class="sr-only">Account</h2>

	<a class="btn btn-link" href="<?php echo $SITE_URL; ?>/teams">Teams</a>
	
	<?php if(!$v_user_email_verified && $v_user_email && $EMAIL_ENABLED): ?>
		<a class="btn btn-link" href="<?php echo $SITE_URL; ?>/verify-email">Verify Email</a>
	<?php endif; ?>

	<a href="<?= $SITE_URL ?>/projects" class="btn btn-link">Projects</a>

	<a href="<?= $SITE_URL ?>/tasks" class="btn btn-link">Tasks</a>

	<a href="<?php echo $SITE_URL; ?>/advanced-report" class="btn btn-link">Advanced Report</a>
	
	<a class="btn btn-link" title="<?php echo $v_username; ?>" href="<?php echo $SITE_URL; ?>/account">Account</a>

	<form action="<?php echo $SITE_URL; ?>/logout" method="POST" class="d-flex align-items-baseline">
		<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
		<input class="btn btn-link" type="submit" value="Logout">
	</form>

	<form action="<?php echo $SITE_URL; ?>/theme" method="POST" class="d-flex align-items-baseline">
		<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
		<input type="text" name="global_back_to" id="global_back_to" value="<?php echo $global_back_to; ?>" hidden>
		<input type="text" name="theme" id="theme" value="<?php echo $theme === 'dark' ? 'light' : 'dark'; ?>" hidden>
		<span class="sr-only"><?php echo ($theme === 'dark') ? 'Light Mode' : 'Dark Mode'; ?></span>
		<input class="ml-3 btn <?php echo ($theme === 'dark') ? '' : 'btn-dark'; ?>" type="submit" value="<?php echo ($theme === 'dark') ? '☀️' : '🌙'; ?>">
	</form>
</div>
