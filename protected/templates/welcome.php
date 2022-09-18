<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">

			<p>
				<a class="login__button login__button--link" href="<?php echo $SITE_URL; ?>/login">Login</a>
				<?php if($v_open_registration): ?>
				<span class="login__or-separator">or</span>
				<a class="login__button login__button--link" href="<?php echo $SITE_URL; ?>/register">Register</a>
				<?php endif; ?>
			</p>

		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>