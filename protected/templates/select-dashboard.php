<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-6">
			<?php if(count($v_confirmations)) : ?>
			<div class="alert alert-success" role="alert">
				<?php
				foreach($v_confirmations as $confirmation) :
				?>
				<p><?php echo $confirmation->message; ?></p>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<h2>Go to...</h2>

			<ul class="list-unstyled">
				<li><a href="<?php echo $SITE_URL; ?>/dashboard">Pearsonal Dashboard</a></li>
				<li><a href="<?php echo $SITE_URL; ?>/teams">Teams</a></li>
			</ul>
			

			<?php if(in_array(
				'select_dashboard_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="alert alert-danger" role="alert">
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
</div>

<?php require_once('partials/footer.php'); ?>