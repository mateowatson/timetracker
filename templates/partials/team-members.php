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

<h3 class="sr-only">Add Another Member</h3>
<form class="team-members__form" action="/team-member" method="POST">
	<input type="text" name="csrf" id="csrf_new_team_member" value="<?php echo $CSRF; ?>" hidden>
	<input type="text" name="team_member_team_id" id="team_member_team_id" value="<?php echo $v_team['id']; ?>" hidden>
	
	<div class="form-group">
		<label for="team_member_name">Add another member</label>
		<input type="text" name="team_member_name" class="form-control"
			id="team_member_name" placeholder="Username...">
	</div>

	<div class="form-group">
		<input class="btn btn-primary" type="submit" value="Add Member">
	</div>
</form>

<?php if(in_array(
	'add_member_errors', $v_errors_element_ids ? : array()
)) : ?>
<div class="alert alert-danger" role="alert">
	<?php
	foreach($v_errors as $error) :
	if($error->element_id === 'add_member_errors') :
	?>
	<p><?php echo $error->message; ?></p>
	<?php endif; endforeach; ?>
</div>
<?php endif; ?>

<?php if(count($v_confirmations)) : ?>
<div class="alert alert-success" role="alert">
	<?php
	foreach($v_confirmations as $confirmation) :
	?>
	<p><?php echo $confirmation->message; ?></p>
	<?php endforeach; ?>
</div>
<?php endif; ?>