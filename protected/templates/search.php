<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
		<?php require_once('partials/team-badge.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-12">
			<?php require_once('partials/search.php'); ?>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<label for="team">
					Go to Different Team
				</label>
				<select form="search-form" id="team" class="form-control" name="team">
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
				<input name="change-team" form="search-form" class="btn btn-primary" type="submit" value="Go">
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-12" data-controller="fragment-loader" data-fragment-loader-id="search-table">
			<h2>Search Results</h2>
			<?php if(!$v_no_matches): ?>

				<?php require('partials/pagination-links.php'); ?>
				<?php require_once('partials/logs-table.php'); ?>
				<?php require('partials/pagination-links.php'); ?>

			<?php else: ?>
			<p class="logs__no-results-found">
				No results found.
			</p>
			<?php endif; ?>
		</div>
	</div>
</div>


<?php require_once('partials/footer.php'); ?>