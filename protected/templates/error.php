<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-12">
            <?php if($SITE_ENV === 'development'): ?>
                <h1><?php echo $ERROR['text']; ?></h1>
                <p>Error code: <?php echo $ERROR['code']; ?></p>
                <?php if($ERROR['trace']): ?>
                <pre><?php echo $ERROR['trace']; ?></pre>
                <?php endif; ?>
            <?php else: ?>
                <h2>Error code: <?php echo $ERROR['code']; ?></h2>
            <?php endif; ?>
        </div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>