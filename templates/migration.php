<?php require_once('partials/header.php'); ?>

<div class="main-layout main-layout--login">

	<?php require_once('partials/heading.php'); ?>

	<div class="login">
		<form action="/install" method="POST" id="migration">
			<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>

			<p>Create the initial user.</p>

			<p><label for="username">Username:</label><br>
			<input type="text" id="username" name="username"></p>

			<p><label for="password">Password:</label><br>
			<input type="password" id="password" name="password"></p>
			
			<input class="login__button" type="submit" value="Install">
		</form>

		<?php if(in_array(
			'installation_errors', $v_errors_element_ids ? : array()
		)) : ?>
		<div class="errors">
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'installation_errors') :
			?>
			<p><?php echo $error->message; ?></p>
			<?php endif; endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

</div>

<?php require_once('partials/footer.php'); ?>