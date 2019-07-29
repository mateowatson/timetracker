<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>


<div class="container mb-5">
	<div class="row">
		<div class="col-lg-6">

			<form action="/search" method="POST">
				<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>

				<div class="form-group">
					<label for="admin_registration">Registration of new users</label>
					<select name="admin_registration" id="admin_registration" class="form-control">
						<option value="open">Open</option>
						<option value="closed">Closed</option>
					</select>
				</div>

				<div class="form-group">
					<label for="admin_username">New user username</label>
					<input type="text" placeholder="username" id="admin_username"
						name="admin_username" class="form-control">
				</div>

				<div class="form-group">
					<label for="admin_password">New user password</label>
					<input type="password" placeholder="password" id="admin_password"
						name="admin_password" class="form-control">
				</div>

				<div class="form-group">
					<input type="submit" value="Save" class="btn btn-primary">
				</div>
			</form>

			<?php if(in_array(
				'admin_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="errors" id="admin_errors">
				<h1 class="errors__heading">The were errors in your request</h1>
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'admin_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php require_once('partials/footer.php'); ?>