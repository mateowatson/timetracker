<h2>Search</h2>
<form action="/search" method="POST">
	<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>

	<div class="form-group">
		<label for="search_term">
			Search
		</label>
		<input type="text" id="search_term" class="form-control"
			name="search_term" placeholder="Search...">
	</div>

	<div class="form-group">
		<label for="search_by">Search by:</label>
		<select class="form-control" id="search_by" name="search_by">
			<option value="project"
				<?php echo $v_search_by === 'project' ? 'selected' : ''; ?>>
				Project
			</option>
			<option value="task"
				<?php echo $v_search_by === 'task' ? 'selected' : ''; ?>>
				Task
			</option>
			<option value="date"
				<?php echo $v_search_by === 'date' ? 'selected' : ''; ?>>
				MM/DD/YYYY or MM/DD/YYYY - MM/DD/YYYY
			</option>
		</select>
	</div>

	<div class="form-group">
		<input class="btn btn-primary" type="submit" value="Search">
	</div>

	<div class="form-group">
		<a href="<?php echo $v_advanced_search_link; ?>">Go to Advanced Search</a>
	</div>
</form>

<?php if(in_array(
	'search_errors', $v_errors_element_ids ? : array()
)) : ?>
<div class="alert alert-danger" role="alert">
	<?php
	foreach($v_errors as $error) :
	if($error->element_id === 'search_errors') :
	?>
	<p><?php echo $error->message; ?></p>
	<?php endif; endforeach; ?>
</div>
<?php endif; ?>