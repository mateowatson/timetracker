<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php echo $SITE_NAME; ?> - <?php echo $v_page_title; ?></title>
	<link rel="stylesheet" href="/dist/app.css">



	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#1b335f">
	<meta name="theme-color" content="#e4e4e4">
</head>
<body class="my-5">

<?php if(in_array(
	'csrf_error', $v_errors_element_ids ? : array()
)) : ?>
<div class="container">
	<div class="row">
		<div class="col-lg-6">
			<div class="alert alert-danger" role="alert">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'csrf_error') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
		</div>
	</div>
</div>

<?php endif; ?>
