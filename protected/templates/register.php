<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">
			<form action="<?php echo $SITE_URL; ?>/register" method="POST">
				<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
				
				<div class="form-group">
					<label for="username">Username:</label>
					<input class="form-control" type="text" id="username" name="username" placeholder="example11">
				</div>

				<?php if($EMAIL_ENABLED): ?>
				<div class="form-group">
					<label for="email">Email:</label>
					<input class="form-control" type="text" id="email" name="email" placeholder="example@fastmail.com">
				</div>
				<?php endif; ?>
				
				<div class="form-group">
					<label for="password">Password:</label><br>
					<input class="form-control" type="password" id="password" name="password" placeholder="At least 8 characters">
				</div>

				<label for="invoice-number" class="sr-only">
					<input name="invoice-number" type="text">
				</label>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Register">
				</div>
			</form>

			<?php if(in_array(
				'registration_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'registration_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>