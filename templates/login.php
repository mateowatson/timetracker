<?php require_once('partials/header.php'); ?>

<div class="main-layout main-layout--login">

	<?php require_once('partials/heading.php'); ?>

	<div class="login">
		<form action="/login" method="POST">
			<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
			
			<p><label for="username">Username:</label><br>
			<input type="text" id="username" name="username"></p>

			<p><label for="password">Password:</label><br>
			<input type="password" id="password" name="password"></p>

			<input class="login__button" type="submit" value="Login">
		</form>
	</div>

</div>

<?php require_once('partials/footer.php'); ?>