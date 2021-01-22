<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php echo $SITE_NAME; ?> - <?php echo $v_page_title; ?></title>
	<link rel="stylesheet" href="<?php echo $SITE_URL; ?>/dist/app.css">
	<link rel="icon" sizes="16x16" href="<?php echo $SITE_URL; ?>/favicon.ico">
	<link rel="icon" href="<?php echo $SITE_URL; ?>/assets/images/favicon.svg" type="image/svg+xml">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $SITE_URL; ?>/apple-touch-icon.png">
	<link rel="manifest" href="<?php echo $SITE_URL; ?>/site.webmanifest">
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
