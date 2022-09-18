<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-6">
			<form action="<?php echo $SITE_URL; ?>/install" method="POST" id="migration">
				<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>

				<p>Create the initial user.</p>

				<div class="form-group">
					<label for="username">Username</label>
					<input class="form-control" type="text" id="username" name="username">
				</div>

				<?php if($EMAIL_ENABLED): ?>
				<div class="form-group">
					<label for="email">Email (optional, required if you forget your password)</label>
					<input class="form-control" type="text" id="email" name="email">
				</div>
				<?php endif; ?>

				<div class="form-group">
					<label for="password">Password</label><br>
					<input class="form-control" type="password" id="password" name="password">
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Install">
				</div>
			</form>

			<?php if(in_array(
				'installation_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'installation_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>