<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
		<?php require_once('partials/team-badge.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-4">
			<div class="card">
				<div class="card-body">
					<?php require_once('partials/timer.php'); ?>
				</div>
			</div>
		</div>
		<?php if($v_is_team): ?>
		<div class="col-lg-4">
			<div class="card bg-transparent border-0">
				<div class="card-body px-0">
					<?php require_once('partials/team-members.php'); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="col-lg-4">
			<div class="card bg-transparent border-0">
				<div class="card-body px-0">
					<h2>Report</h2>
					<?php require_once('partials/report.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h2>Recent Logs</h2>
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a
						class="nav-link <?php
							echo isset($REQUEST['week']) ? '' : 'active';
						?>"
						href="<?php echo $SITE_URL; ?><?php echo !$v_is_team ? '/dashboard' : '/team/'.$v_team['id']; ?>">
						Today
					</a>
				</li>
				<li>
					<a
						class="nav-link <?php
							echo isset($REQUEST['week']) ? 'active' : '';
						?>"
						href="<?php echo $SITE_URL; ?><?php echo !$v_is_team ? '/dashboard?week' : '/team/'.$v_team['id'].'?week'; ?>">
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
		<div class="col-lg-12">
			<p><a href="<?php echo $SITE_URL; ?>/all-logs">All Logs View</a></p>
		</div>
	</div>
</div>


<?php require_once('partials/footer.php'); ?>