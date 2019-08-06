<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<?php if($v_is_team): ?>
		<div class="col-lg-4">
			<?php require_once('partials/team-members.php'); ?>
		</div>
		<?php endif; ?>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h2>Logs</h2>
		</div>
		<div class="col-lg-12">
			<?php if($v_logs): ?>
			<?php require_once('partials/logs-table.php'); ?>
			<?php else: ?>
			<?php require_once('partials/logs-no-logs.php'); ?>
			<?php endif; ?>
		</div>
</section>


<?php require_once('partials/footer.php'); ?>