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
				'account_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'account_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>

			<form action="/account" method="POST">
				<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
				
				<?php if($v_is_user_admin): ?>
				<h2>Registration availability</h2>
				<p>Currently, registration is <?php echo $v_open_registration ? 'open' : 'closed'; ?>.</p>
				<div class="form-group">
					<label for="account_registration">Registration of new users</label>
					<select name="account_registration" id="account_registration" class="form-control">
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

				<h2>Add a user</h2>
				<div class="form-group">
					<label for="account_add_username">Username</label>
					<input type="text" placeholder="username" id="account_add_username"
						name="account_add_username" class="form-control">
				</div>

				<div class="form-group">
					<label for="account_add_password">Password</label>
					<input type="password" placeholder="password" id="account_add_password"
						name="account_add_password" class="form-control">
				</div>
				<?php endif; ?>

				<h2>Change profile info</h2>
				<div class="form-group">
					<label for="account_username">Username (currently <strong><?php echo $v_username; ?>)</strong></label>
					<input type="text" placeholder="example11" id="account_username"
						name="account_username" class="form-control">
				</div>

				<div class="form-group">
					<label for="account_email">Email (currently <strong><?php echo $v_user_email; ?></strong>)</label>
					<input type="text" placeholder="example@fastmail.com" id="account_email"
						name="account_email" class="form-control">
				</div>

				<div class="form-group">
					<label for="account_password">Password</label>
					<input type="password" placeholder="At least 8 characters" id="account_password"
						name="account_password" class="form-control">
				</div>

				<div class="form-group">
					<input type="submit" value="Save" class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>
<?php require_once('partials/footer.php'); ?>