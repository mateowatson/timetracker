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
			<h2>Team Members</h2>
			<ul>
				<?php foreach($v_team_members as $team_member): ?>
				<li>
					<?php echo $team_member['username']; ?>
					<?php if($v_show_remove_members): ?>
					<a href="/remove-member?team=<?php echo $v_team['id']; ?>&user=<?php echo $team_member['id']; ?>"
						class="px-3">
						remove
					</a>
					<?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h2>Logs</h2>
			<?php if($v_logs): ?>
			<?php require_once('partials/logs-table.php'); ?>
			<?php else: ?>
			<?php require_once('partials/logs-no-logs.php'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>


<?php require_once('partials/footer.php'); ?>