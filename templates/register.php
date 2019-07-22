<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">
			<form action="/register" method="POST">
				<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
				
				<div class="form-group">
					<label for="username">Username:</label>
					<input class="form-control" type="text" id="username" name="username">
				</div>
				
				<div class="form-group">
					<label for="password">Password:</label><br>
					<input class="form-control" type="password" id="password" name="password">
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Register">
				</div>
			</form>

			<?php if(in_array(
				'registration_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="p-3 mb-3 bg-danger text-white" id="start_timer_bottom_errors">
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