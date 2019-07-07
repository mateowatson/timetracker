<section class="team-members">
	<h2 class="team-members__heading">Team Members</h2>
	<ul class="team-members__list">
		<?php foreach($v_team_members as $team_member): ?>
			<li class="team-members__list-item">
				<?php echo $team_member['username']; ?>
				<?php if($v_show_remove_members): ?>
				<a href="/remove-member?team=<?php echo $v_team['id']; ?>&user=<?php echo $team_member['id']; ?>" class="team-members__remove-link">remove</a>
				<?php endif; ?>
			</li>
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

	<?php if(in_array(
		'add_member_errors', $v_errors_element_ids ? : array()
	)) : ?>
	<div class="errors" id="add_member_errors">
		<?php
		foreach($v_errors as $error) :
		if($error->element_id === 'add_member_errors') :
		?>
		<p><?php echo $error->message; ?></p>
		<?php endif; endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if(count($v_confirmations)) : ?>
	<div class="confirmations">
		<?php
		foreach($v_confirmations as $confirmation) :
		?>
		<p><?php echo $confirmation->message; ?></p>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</section>
