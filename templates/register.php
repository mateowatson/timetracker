<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Register</title>
</head>
<body>
<h1>Register</h1>
<form action="/register" method="POST" id="login">
	<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
	<label for="username">Username:</label><br>
	<input type="text" id="username" name="username"><br><br>

	<label for="password">Password:</label><br>
	<input type="password" id="password" name="password"><br>
	<input type="submit" value="Register">
</form>
<?php if(in_array(
	'registration_errors', $v_errors_element_ids ? : array()
)) : ?>
<div class="errors" id="start_timer_bottom_errors">
	<?php
	foreach($v_errors as $error) :
	if($error->element_id === 'registration_errors') :
	?>
	<p><?php echo $error->message; ?></p>
	<?php endif; endforeach; ?>
</div>
<?php endif; ?>

<?php require_once('partials/footer.php'); ?>