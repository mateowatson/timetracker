<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Migration</title>
</head>
<body>

<h1>Install</h1>

<div id="run-migration-page">
	<!-- <form method="POST" action="" id="run-migration" name="run-migration">
		<input type="submit" value="Run Migration" id="run-migration-submit">
	</form> -->

	<form action="/migration" method="POST" id="migration">
		<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>

		<p>Create the initial user.</p>

		<label for="username">Username:</label><br>
		<input type="text" id="username" name="username"><br><br>

		<label for="password">Password:</label><br>
		<input type="password" id="password" name="password"><br>
		
		<input type="submit" value="Install">
	</form>

	<?php if(in_array(
		'installation_errors', $v_errors_element_ids ? : array()
	)) : ?>
	<div class="errors" id="start_timer_bottom_errors">
		<?php
		foreach($v_errors as $error) :
		if($error->element_id === 'installation_errors') :
		?>
		<p><?php echo $error->message; ?></p>
		<?php endif; endforeach; ?>
	</div>
	<?php endif; ?>
</div>


<?php require_once('partials/footer.php'); ?>