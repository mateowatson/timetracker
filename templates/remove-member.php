<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-12">

			<h2>Confirm</h2>
			<form action="/remove-member" method="POST">
				<input type="text" name="csrf" id="csrf_team" value="<?php echo $CSRF; ?>" hidden>
				<input type="text" name="user_id" id="user_id" value="<?php echo $v_user_to_remove->id; ?>" hidden>
				<input type="text" name="team_id" id="team_id" value="<?php echo $v_team->id; ?>" hidden>
				<p>Are you sure you want to remove
				<strong><?php echo $v_user_to_remove->username; ?></strong> from
				<strong><?php echo $v_team->name; ?></strong>?</p>
				<input class="btn btn-primary" type="submit" value="Remove">
				<a class="btn btn-link" href="<?php echo $v_cancel_url; ?>">Cancel</a>
			</form>

			<?php if(in_array(
				'remove_member_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'remove_member_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php require_once('partials/footer.php'); ?>