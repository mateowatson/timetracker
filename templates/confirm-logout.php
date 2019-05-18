<?php require_once('partials/header.php'); ?>

<div class="main-layout main-layout--login">

	<?php require_once('partials/heading.php'); ?>

	<div class="login">
		<p>It looks like you may have multiple windows or tabs logged in as
			<strong><?php echo $v_username; ?></strong>.
		Are you sure you want to log out? If so, be sure to close all other tabs or windows
		after clicking YES below.</p>
		<form action="/logout" method="POST">
			<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
			<input class="login__button login__button--all-caps" type="submit" value="Yes">
			<span class="login__or-separator"> </span>
			<a href="/" class="login__button login__button--gray login__button--link login__button--all-caps">No</a>
		</form>
	</div>

</div>

<?php require_once('partials/footer.php'); ?>