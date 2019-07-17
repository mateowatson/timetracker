<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--admin">
	<?php require_once('partials/heading.php'); ?>
	<?php require_once('partials/account.php'); ?>

	<section class="admin">

		<h2 class="admin__heading">Admin Stuff</h2>

		<form action="/search" method="POST" class="admin__form">
			<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>

			<label for="admin_registration" class="admin__label">Registration of new users</label>
			<select name="admin_registration" id="admin_registration" class="admin__select">
				<option value="open" class="admin__option">Open</option>
				<option value="closed" class="admin__option">Closed</option>
			</select>

			<label for="admin_username" class="admin__label">New user username</label>
			<input type="text" placeholder="username" id="admin_username" name="admin_username" class="admin__input">

			<label for="admin_password" class="admin__label">New user password</label>
			<input type="password" placeholder="password" id="admin_password" name="admin_password" class="admin__input">

			<input type="submit" value="Save" class="admin__submit">
		</form>

		<?php if(in_array(
			'admin_errors', $v_errors_element_ids ? : array()
		)) : ?>
		<div class="errors" id="admin_errors">
			<h1 class="errors__heading">The were errors in your request</h1>
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'admin_errors') :
			?>
			<p><?php echo $error->message; ?></p>
			<?php endif; endforeach; ?>
		</div>
		<?php endif; ?>
	</section>
</div>
<?php require_once('partials/footer.php'); ?>