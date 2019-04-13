<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--search-page">
	<?php require_once('partials/heading.php'); ?>

	<?php require_once('partials/account.php'); ?>

	<?php require_once('partials/search.php'); ?>

	<section class="logs">
		<?php if(!$v_no_matches): ?>
			<h1 class="logs__heading logs__heading--hidden">Search Results</h1>
			<?php require_once('partials/logs-table.php'); ?>

			<div class="search-pagination">
			<?php if($v_prev_link): ?>
				<a href="<?php echo $v_prev_link; ?>">&larr;</a>
			<?php endif; ?>
			<?php echo $v_curr_page . '/' . $v_num_pages; ?>
			<?php if($v_next_link): ?>
				<a href="<?php echo $v_next_link; ?>">&rarr;</a>
			<?php endif; ?>
			</div>
			<?php else: ?>
			<p class="logs__no-results-found">No results found.</p>
		<?php endif; ?>
	</section>


</div>


<?php require_once('partials/footer.php'); ?>