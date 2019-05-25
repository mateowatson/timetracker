<section class="search">
	<form action="/search" method="POST" class="search__form">
		<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
		<h1 class="search__heading">Search</h1>

		<label for="search_term" class="search__label search__label--hidden">
			Search
		</label>
		<input type="text" class="search__term" id="search_term"
			name="search_term" placeholder="Search...">

		<label for="search_by" class="search__label">Search by:</label>
		<select id="search_by" name="search_by" class="search__select">
			<option value="project"
				class="search__option"
				<?php echo $v_search_by === 'project' ? 'selected' : ''; ?>>
				Project
			</option>
			<option value="task"
				class="search__option"
				<?php echo $v_search_by === 'task' ? 'selected' : ''; ?>>
				Task
			</option>
			<option value="date"
				class="search__option"
				<?php echo $v_search_by === 'date' ? 'selected' : ''; ?>>
				MM/DD/YYYY or MM/DD/YYYY - MM/DD/YYYY
			</option>
		</select>

		<input type="submit" value="Search" class="search__submit">

		<a href="/advanced-search">Go to Advanced Search</a>
	</form>

	<?php if(in_array(
		'search_errors', $v_errors_element_ids ? : array()
	)) : ?>
	<div class="errors" id="search_errors">
		<h1 class="errors__heading">The were errors in your request</h1>
		<?php
		foreach($v_errors as $error) :
		if($error->element_id === 'search_errors') :
		?>
		<p><?php echo $error->message; ?></p>
		<?php endif; endforeach; ?>
	</div>
	<?php endif; ?>
</section>