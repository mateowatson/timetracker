<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Login</title>
</head>
<body>

<h1>Login</h1>
<form action="/login" method="POST" id="login">
	<input type="text" name="csrf" value="<?php echo $CSRF; ?>" hidden>
	<label for="username">Username:</label><br>
	<input type="text" id="username" name="username"><br><br>

	<label for="password">Password:</label><br>
	<input type="password" id="password" name="password"><br>
	<input type="submit" value="Login">
</form>

<?php require_once('partials/footer.php'); ?>