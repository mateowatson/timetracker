<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Time Tracker - <?php echo $v_page_title; ?></title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i" rel="stylesheet">
	<link rel="stylesheet" href="/dist/app.css">
</head>
<body class="body">

<?php if(in_array(
	'csrf_error', $v_errors_element_ids ? : array()
)) : ?>
<div class="in-header-errors" id="csrf_error">
	<?php
	foreach($v_errors as $error) :
	if($error->element_id === 'csrf_error') :
	?>
	<p><?php echo $error->message; ?></p>
	<?php endif; endforeach; ?>
</div>
<?php endif; ?>
