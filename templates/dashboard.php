<?php require_once('partials/header.php'); ?>
<div class="main-layout">
	<?php require_once('partials/heading.php'); ?>

	<?php require_once('partials/account.php'); ?>

	<?php require_once('partials/timer.php'); ?>

	<?php require_once('partials/search.php'); ?>

	<section class="logs">
		<h1 class="logs__heading">Recent Logs</h1>
		<ul class="logs__filter-ul">
			<li class="logs__filter-li">
				<a
					class="logs__filter-li-a <?php
						echo isset($REQUEST['week']) ? '' : 'logs__filter-li-a--current';
					?>"
					href="<?php echo !$v_is_team ? '/dashboard' : '/team/'.$v_team['id']; ?>">
					Today
				</a>
			</li>
			<li>
				<a
					class="logs__filter-li-a <?php
						echo isset($REQUEST['week']) ? 'logs__filter-li-a--current' : '';
					?>"
					href="<?php echo !$v_is_team ? '/dashboard?week' : '/team/'.$v_team['id'].'?week'; ?>">
					This Week
				</a>
			</li>
		</ul>
		<?php if($v_logs): ?>
		<?php require_once('partials/logs-table.php'); ?>
		<?php else: ?>
		<?php require_once('partials/logs-no-logs.php'); ?>
		<?php endif; ?>
	</section>

</div>

<?php require_once('partials/footer.php'); ?>