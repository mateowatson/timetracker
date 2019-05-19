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
			<option value="project" class="search__option">Project</option>
			<option value="task" class="search__option">Task</option>
			<option value="date" class="search__option">Date Started - MM/DD/YYYY</option>
		</select>

		<input type="submit" value="Search" class="search__submit">
	</form>
</section>