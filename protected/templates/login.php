<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">
			<?php if(count($v_confirmations)) : ?>
			<div class="alert alert-success" role="alert">
				<?php
				foreach($v_confirmations as $confirmation) :
				?>
				<p><?php echo $confirmation->message; ?></p>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<?php if(in_array(
				'login_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'login_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
			<form action="<?php echo $SITE_URL; ?>/login" method="POST">
				<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
				
				<div class="form-group">
					<label for="username">Username:</label><br>
					<input class="form-control" type="text" id="username" name="username">
				</div>

				<div class="form-group">
					<label for="password">Password:</label><br>
					<input class="form-control" type="password" id="password" name="password">
				</div>

				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Login">
				</div>

				<?php if($EMAIL_ENABLED): ?>
				<a href="<?php echo $SITE_URL; ?>/forgot-password">Forgot Password?</a>
				<?php endif; ?>
			</form>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>