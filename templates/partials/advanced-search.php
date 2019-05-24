<section class="search">
	<form action="/advanced-search" method="POST" class="search__form">
		<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
		<h1 class="search__heading">Search</h1>

		<label for="search_term_project" class="search__label search__label--hidden">
			Project
		</label>
		<input type="text" class="search__term" id="search_term_project"
			name="search_term_project" placeholder="Project">
		</select>

		<label for="search_term_task" class="search__label search__label--hidden">
			Task
		</label>
		<input type="text" class="search__term" id="search_term_task"
			name="search_term_task" placeholder="Task">
		</select>

		<label for="search_term_start_date" class="search__label search__label--hidden">
			Start date (YYYY-MM-DD)
		</label>
		<input type="text" class="search__term" id="search_term_start_date"
			name="search_term_start_date" placeholder="Start date (YYYY-MM-DD)">
		</select>

		<label for="search_term_end_date" class="search__label search__label--hidden">
			End date (YYYY-MM-DD)
		</label>
		<input type="text" class="search__term" id="search_term_end_date"
			name="search_term_end_date" placeholder="End date (YYYY-MM-DD)">
		</select>

		<label for="search_term_notes" class="search__label search__label--hidden">
			Notes
		</label>
		<input type="text" class="search__term" id="search_term_notes"
			name="search_term_notes" placeholder="Notes">
		</select>

		<input type="submit" value="Search" class="search__submit">
	</form>

	<?php if(in_array(
		'advanced_search_errors', $v_errors_element_ids ? : array()
	)) : ?>
	<div class="errors" id="advanced_search_errors">
		<h1 class="errors__heading">The were errors in your request</h1>
		<?php
		foreach($v_errors as $error) :
		if($error->element_id === 'advanced_search_errors') :
		?>
		<p><?php echo $error->message; ?></p>
		<?php endif; endforeach; ?>
	</div>
	<?php endif; ?>
</section>