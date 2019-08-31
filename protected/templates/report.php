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
			<?php require_once('partials/report.php'); ?>
		</div>
		<div class="col-lg-4">
			<?php if($v_report_show_teams_dropdown): ?>
			<div class="form-group">
				<label for="team">
					Go to Different Team
				</label>
				<select form="report-form" id="team" class="form-control" name="team">
					<option value="noteam">None (personal logs)</option>
					<?php foreach($v_teams as $team) : ?>
					<option value="<?php echo $team['team_id']; ?>"
						<?php echo ((int)$team['team_id'] === (int)$v_team['id'] ? 'selected' : ''); ?>>
						<?php echo $team['team_name']; ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="form-group">
				<input name="change-team" form="report-form" class="btn btn-primary" type="submit" value="Go">
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-12">
			<?php if(!$v_no_matches): ?>

				<?php if($v_prev_link || $v_next_link): ?>
				<div class="mb-3">
					<?php if($v_prev_link): ?>
					<a class="mr-2" href="<?php echo $SITE_URL . $v_prev_link; ?>">
						&larr;
					</a>
					<?php endif; ?>
					<span>
						<?php echo 'Page ' . $v_curr_page . '/' . $v_num_pages; ?>
					</span>
					<?php if($v_next_link): ?>
					<a class="ml-2" href="<?php echo $SITE_URL . $v_next_link; ?>">
						&rarr;
					</a>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php require_once('partials/logs-table.php'); ?>

			<?php else: ?>
			<p>
				No logs found.
			</p>
			<?php endif; ?>
		</div>
		<div class="col-lg-12">
			<p><a href="<?php echo $SITE_URL; ?>/all-logs">All Logs View</a></p>
		</div>
	</div>
</div>


<?php require_once('partials/footer.php'); ?>