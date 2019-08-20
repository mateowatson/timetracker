<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">

            <form action="/forgot-password" method="POST">
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