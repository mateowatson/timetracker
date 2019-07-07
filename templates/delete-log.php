<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--delete-log">
	<?php require_once('partials/heading.php'); ?>
	<?php require_once('partials/account.php'); ?>

	<section class="delete-log">

		<h2 class="delete-log__heading">Confirm</h2>
		<form class="delete-log__form" action="/delete-log" method="POST">
			<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>
			<p>Are you sure you want to delete this log?</p>
			<input type="text" name="log" id="log" value="<?php echo $v_log->id; ?>" hidden>
			<input type="submit" value="Delete This Log">
		</form>

		<?php if(in_array(
			'delete_log_errors', $v_errors_element_ids ? : array()
		)) : ?>
		<div class="errors" id="delete_log_errors">
			<h1 class="errors__heading">The were errors in your request</h1>
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'delete_log_errors') :
			?>
			<p><?php echo $error->message; ?></p>
			<?php endif; endforeach; ?>
		</div>
		<?php endif; ?>
	</section>
</div>
<?php require_once('partials/footer.php'); ?>