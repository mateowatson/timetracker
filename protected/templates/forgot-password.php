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
				'forgot_password_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'forgot_password_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
            <form action="<?php echo $SITE_URL; ?>/forgot-password" method="POST">
                <input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
				
				<div class="form-group">
					<label for="username">Enter your username, and we will send a password reset option to
                        the email address associated with it. If you do not remember your username,
                        contact your site administrator.
                    </label>
					<input class="form-control" type="text" id="username" name="username">
                </div>

                <div class="form-group">
					<input class="btn btn-primary" type="submit" value="Login">
				</div>
            </form>

		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>