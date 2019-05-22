<?php require_once('partials/header.php'); ?>

<div class="main-layout main-layout--login">

	<?php require_once('partials/heading.php'); ?>

	<div class="login">
		<?php if(count($v_confirmations)) : ?>
		<div class="confirmations">
			<?php
			foreach($v_confirmations as $confirmation) :
			?>
			<p><?php echo $confirmation->message; ?></p>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<form action="/login" method="POST">
			<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
			
			<p><label for="username">Username:</label><br>
			<input type="text" id="username" name="username"></p>

			<p><label for="password">Password:</label><br>
			<input type="password" id="password" name="password"></p>

			<input class="login__button" type="submit" value="Login">
		</form>

		<?php if(in_array(
			'login_errors', $v_errors_element_ids ? : array()
		)) : ?>
		<div class="errors" id="start_timer_bottom_errors">
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

<?php require_once('partials/footer.php'); ?>