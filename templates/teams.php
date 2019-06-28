<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--manage-teams">
	<?php require_once('partials/heading.php'); ?>
	<?php require_once('partials/account.php'); ?>

	<section class="manage-teams">

		<h2 class="manage-teams__heading">Select a Team</h2>
		<ul class="manage-teams__team-list">
			<?php foreach($v_teams as $v_team): ?>
			<li class="manage-teams__team-list-item">
				<a class="manage-teams__team-list-item-link"
					href="/team/<?php echo $v_team['team_id']; ?>">
					<?php echo $v_team['team_name']; ?>
				</a>
				<?php if($v_team['creator']): ?>- You created this team.<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<h2 class="manage-teams__heading">Create a Team</h2>
		<form class="manage-teams__form" action="/teams" method="POST">
			<input type="text" name="csrf" id="csrf_team" value="<?php echo $CSRF; ?>" hidden>
			<label class="manage-teams__form-label" for="team_name">Team Name:</label>
			<input class="manage-teams__form-text-input" type="text" name="team_name" id="team_name">
			<label class="manage-teams__form-label" for="team_invitees">Enter comma-separated list of usernames you would like to invite.</label>
			<textarea class="manage-teams__form-textarea" name="team_invitees" id="team_invitees" cols="30" rows="10"></textarea>
			<input class="manage-teams__form-button" type="submit" value="Create Team">
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
	</section>
</div>
<?php require_once('partials/footer.php'); ?>