<section class="team-members">
	<h2 class="team-members__heading">Team Members</h2>
	<ul class="team-members__list">
		<li class="team-members__list-item">Matt</li>
		<li class="team-members__list-item">Anna</li>
		<li class="team-members__list-item">Blake</li>
	</ul>

	<h2 class="team-members__form-heading">Add Another Member</h2>
	<form class="team-members__form" action="/teams" method="POST">
		<input type="text" name="csrf" id="csrf_team" value="<?php echo $CSRF; ?>" hidden>
		<label class="team-members__form-label" for="team_name">Add another member</label>
		<input class="team-members__form-text-input" type="text" name="team_name" id="team_name">
		<input class="team-members__form-button" type="submit" value="Create Team">
	</form>
</section>