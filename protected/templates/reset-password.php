<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
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
				'reset_password_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'reset_password_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
            <form action="<?php echo $SITE_URL; ?>/reset-password" method="POST">
                <input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
                <p>Enter your current username, the password reset code we sent you, and your new desired password.</p>

                <div class="form-group">
					<label for="username">
                        Current Username
                    </label>
					<input class="form-control" type="text" id="username" name="username">
                </div>

				<div class="form-group">
					<label for="password_reset_code">
                        Password Reset Code
                    </label>
					<input class="form-control" type="text" id="password_reset_code"
						name="password_reset_code" autocomplete="off">
                </div>


                <div class="form-group">
					<label for="password">
                        New Password
                    </label>
					<input class="form-control" type="password" id="password" name="password">
                </div>

                <div class="form-group">
					<input class="btn btn-primary" type="submit" value="Submit">
				</div>
            </form>

		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>