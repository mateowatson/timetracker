<?php require_once('partials/header.php'); ?>

<div class="container">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-lg-3">
			<?php require_once('partials/timer.php'); ?>
		</div>
		<?php if($v_is_team): ?>
		<div class="col-lg-3">
			<?php require_once('partials/team-members.php'); ?>
		</div>
		<?php endif; ?>
		<div class="col-lg-3">
			<?php require_once('partials/search.php'); ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h2 class="logs__heading">Recent Logs</h2>
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a
						class="nav-link <?php
							echo isset($REQUEST['week']) ? '' : 'active';
						?>"
						href="<?php echo !$v_is_team ? '/dashboard' : '/team/'.$v_team['id']; ?>">
						Today
					</a>
				</li>
				<li>
					<a
						class="nav-link <?php
							echo isset($REQUEST['week']) ? 'active' : '';
						?>"
						href="<?php echo !$v_is_team ? '/dashboard?week' : '/team/'.$v_team['id'].'?week'; ?>">
						This Week
					</a>
				</li>
			</ul>
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