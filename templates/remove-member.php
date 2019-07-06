<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--remove-member">
	<?php require_once('partials/heading.php'); ?>
	<?php require_once('partials/account.php'); ?>

	<section class="remove-member">

		<h2 class="remove-member__heading">Confirm</h2>
		<form class="remove-member__form" action="/remove-member" method="POST">
			<input type="text" name="csrf" id="csrf_team" value="<?php echo $CSRF; ?>" hidden>
			<input type="text" name="user_id" id="user_id" value="<?php echo $v_user_to_remove->id; ?>" hidden>
			<input type="text" name="team_id" id="team_id" value="<?php echo $v_team->id; ?>" hidden>
			<p>Are you sure you want to remove
			<strong><?php echo $v_user_to_remove->username; ?></strong> from
			<strong><?php echo $v_team->name; ?></strong>?</p>
			<input class="remove-member__form-button" type="submit" value="Remove">
			<a class="remove-member__form-cancel" href="<?php echo $v_cancel_url; ?>">Cancel</a>
		</form>
		<?php if(in_array(
			'remove_member_errors', $v_errors_element_ids ? : array()
		)) : ?>
		<div class="errors" id="remove_member_errors">
			<h1 class="errors__heading">The were errors in your request</h1>
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'remove_member_errors') :
			?>
			<p><?php echo $error->message; ?></p>
			<?php endif; endforeach; ?>
		</div>
		<?php endif; ?>
	</section>
</div>
<?php require_once('partials/footer.php'); ?>