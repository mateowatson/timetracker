<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-6">
			<?php require_once('partials/search.php'); ?>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-12">
			<h2>Search Results</h2>
			<?php if(!$v_no_matches): ?>

				<?php if($v_prev_link || $v_next_link): ?>
				<div class="mb-3">
					<?php if($v_prev_link): ?>
					<a class="mr-2" href="<?php echo $v_prev_link; ?>">
						&larr;
					</a>
					<?php endif; ?>
					<span>
						<?php echo 'Page ' . $v_curr_page . '/' . $v_num_pages; ?>
					</span>
					<?php if($v_next_link): ?>
					<a class="ml-2" href="<?php echo $v_next_link; ?>">
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
			<?php endif; ?>
		</div>
	</div>
</div>


<?php require_once('partials/footer.php'); ?>