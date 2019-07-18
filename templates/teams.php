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
				<h2>Select a Team</h2>
				<ul>
					<?php foreach($v_teams as $v_team): ?>
					<li>
						<a href="/team/<?php echo $v_team['team_id']; ?>">
							<?php echo $v_team['team_name']; ?>
						</a>
						<?php if($v_team['creator']): ?>- You created this team.<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="col-lg-6">
				<h2>Create a Team</h2>
				<form action="/teams" method="POST">
					<input type="text" name="csrf" id="csrf_team" value="<?php echo $CSRF; ?>" hidden>

					<div class="form-group">
						<label for="team_name">Team Name:</label>
						<input class="form-control" type="text" name="team_name" id="team_name">
					</div>
					
					<div class="form-group">
						<label for="team_invitees">Enter comma-separated list of usernames you would like to invite.</label>
						<textarea class="form-control" name="team_invitees" id="team_invitees" cols="30" rows="10"></textarea>
					</div>
					
					<div class="form-group">
						<input class="btn btn-primary" type="submit" value="Create Team">
					</div>
				</form>
				<?php if(in_array(
					'create_team_errors', $v_errors_element_ids ? : array()
				)) : ?>
				<div class="errors" id="create_team_errors">
					<h1 class="errors__heading">The were errors in your request</h1>
					<?php
					foreach($v_errors as $error) :
					if($error->element_id === 'create_team_errors') :
					?>
					<p><?php echo $error->message; ?></p>
					<?php endif; endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php require_once('partials/footer.php'); ?>