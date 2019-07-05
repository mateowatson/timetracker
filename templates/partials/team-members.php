<section class="team-members">
	<h2 class="team-members__heading">Team Members</h2>
	<ul class="team-members__list">
		<?php foreach($v_team_members as $team_member): ?>
			<li class="team-members__list-item"><?php echo $team_member['username']; ?></li>
		<?php endforeach; ?>
	</ul>

	<h2 class="team-members__form-heading">Add Another Member</h2>
	<form class="team-members__form" action="/team-member" method="POST">
		<input type="text" name="csrf" id="csrf_new_team_member" value="<?php echo $CSRF; ?>" hidden>
		<input type="text" name="team_member_team_id" id="team_member_team_id" value="<?php echo $v_team['id']; ?>" hidden>
		<label class="team-members__form-label" for="team_member_name">Add another member</label>
		<input class="team-members__form-text-input" type="text" name="team_member_name"
			id="team_member_name" placeholder="Username...">
		<input class="team-members__form-button" type="submit" value="Add Member">
	</form>
</section>
