<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-6">
			<p>It looks like you may have multiple windows or tabs logged in as
				<strong><?php echo $v_username; ?></strong>.
			Are you sure you want to log out? If so, be sure to close all other tabs or windows
			after clicking YES below.</p>

			<form action="/logout" method="POST">
				<input type="text" name="csrf" id="csrf" value="<?php echo $CSRF; ?>" hidden>
				<input class="btn btn-primary" type="submit" value="Yes">
				<a href="/" class="btn">No</a>
			</form>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>