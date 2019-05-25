<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--search-page">
	<?php require_once('partials/heading.php'); ?>

	<?php require_once('partials/account.php'); ?>

	<?php require_once('partials/search.php'); ?>

	<section class="logs">
		<?php if(!$v_no_matches): ?>
		<h1 class="logs__heading logs__heading--hidden">Search Results</h1>

			<?php if($v_prev_link || $v_next_link): ?>
			<div class="pagination-links">
				<?php if($v_prev_link): ?>
				<a class="pagination-links__arrow" href="<?php echo $v_prev_link; ?>">
					&larr;
				</a>
				<?php endif; ?>
				<span class="pagination-links__number">
					<?php echo 'Page ' . $v_curr_page . '/' . $v_num_pages; ?>
				</span>
				<?php if($v_next_link): ?>
				<a class="pagination-links__arrow" href="<?php echo $v_next_link; ?>">
					&rarr;
				</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php require_once('partials/logs-table.php'); ?>

		<?php else: ?>
		<p class="logs__no-results-found">
			No results found.
		</p>
		<p>
			<?php if(!$v_search_term): ?>
			<?php if(!in_array(
				'search_errors', $v_errors_element_ids ? : array()
			)) : ?>
			Enter a date or date range to see logs by date.
			<?php endif; endif; ?>
		</p>
		<?php endif; ?>
	</section>


</div>


<?php require_once('partials/footer.php'); ?>