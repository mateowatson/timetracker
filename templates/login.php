<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<?php if(count($v_confirmations)) : ?>
<div class="confirmations">
	<?php
	foreach($v_confirmations as $confirmation) :
	?>
	<p><?php echo $confirmation->message; ?></p>
	<?php endforeach; ?>
</div>
<?php endif; ?>
<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">
			<form action="/login" method="POST">
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
			</form>

			<?php if(in_array(
				'login_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="p-3 mb-3 bg-danger text-white" id="start_timer_bottom_errors">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'login_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>