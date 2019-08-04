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
				'admin_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'admin_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>

			<form action="/admin" method="POST">
				<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
				
				<?php if($v_is_user_admin): ?>
				<div class="form-group">
					<label for="admin_registration">Registration of new users</label>
					<select name="admin_registration" id="admin_registration" class="form-control">
						
						<option value="open"
							<?php echo $v_open_registration ? 'selected' : ''; ?>>
							Open
						</option>
						<option value="closed"
							<?php echo !$v_open_registration ? 'selected' : ''; ?>>
							Closed
						</option>
					</select>
				</div>
				<?php endif; ?>

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
		</div>
	</div>
</div>
<?php require_once('partials/footer.php'); ?>