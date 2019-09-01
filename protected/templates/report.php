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
		<div class="col-12" data-controller="fragment-loader" data-fragment-loader-id="report-table">
			<?php if(!$v_no_matches): ?>

				<?php require('partials/pagination-links.php'); ?>
				<?php require_once('partials/logs-table.php'); ?>
				<?php require('partials/pagination-links.php'); ?>

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