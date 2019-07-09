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

		<h2>Go to...</h2>

		<p><a href="/dashboard">Pearsonal Dashboard</a></p>

		<p><a href="/teams">Teams</a></p>

		<?php if(in_array(
			'select_dashboard_errors', $v_errors_element_ids ? : array()
		)) : ?>
		<div class="errors" id="select_dashboard_errors">
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'select_dashboard_errors') :
			?>
			<p><?php echo $error->message; ?></p>
			<?php endif; endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

</div>

<?php require_once('partials/footer.php'); ?>