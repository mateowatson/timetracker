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
				'verify_email_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'verify_email_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>

            <?php if(!$v_user_email_verified && $v_user_email): ?>
			<form action="<?php echo $SITE_URL; ?>/verify-email" method="POST">
				<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
                
				<div class="form-group">
					<label for="email_verification_code">
						Enter in the email verification code you received at
						<strong><?php echo $v_user_email; ?></strong>.
					</label>
					<input class="form-control" type="text"
						name="email_verification_code" id="email_verification_code"
						autocomplete="off">
                </div>
                
                <div class="form-group">
					<input type="submit" value="Submit" class="btn btn-primary">
				</div>
            </form>
			<?php endif; ?>
			
			<?php if($v_user_email_verified && $v_user_email): ?>
			<p>Your verified email address is <strong><?php echo $v_user_email; ?></strong>.</p>
			<?php endif; ?>

			<?php if(!$v_user_email): ?>
			<p>You have not submitted an email address yet. <a href="<?php echo $SITE_URL; ?>/account">Edit your profile</a> to add an address.</p>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php require_once('partials/footer.php'); ?>