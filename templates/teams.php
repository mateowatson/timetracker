<?php require_once('partials/header.php'); ?>
<div class="main-layout">
	<?php require_once('partials/heading.php'); ?>
	<?php require_once('partials/account.php'); ?>
</div>
<h2>Select a Team</h2>
<ul>
	<li><a href="#">Team Name</a></li>
	<li><a href="#">Team Name</a></li>
	<li><a href="#">Team Name</a></li>
	<li><a href="#">Team Name</a></li>
	<li><a href="#">Team Name</a></li>
	<li><a href="#">Team Name</a></li>
</ul>

<h2>Create a Team</h2>
<form action="/teams" method="POST">
	<input type="text" name="csrf" id="csrf_team" value="<?php echo $CSRF; ?>" hidden>
	<label for="team_name">Team Name:</label><br>
	<input type="text" name="team_name" id="team_name"><br>
	<label for="team_invitees">Enter comma-separated list of usernames you would like to invite.</label><br>
	<textarea name="team_invitees" id="team_invitees" cols="30" rows="10"></textarea><br>
	<input type="submit" value="Create Team">
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
<?php require_once('partials/footer.php'); ?>