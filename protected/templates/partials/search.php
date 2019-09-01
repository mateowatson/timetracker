<h2>Search</h2>
<form action="<?php echo $SITE_URL; ?>/search" method="POST" id="search-form">
	<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>

	<div class="form-row">

		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_project">
					Project
				</label>
				<select id="search_project" class="form-control"
					name="search_project" placeholder="Search..."
					value="<?php echo $v_search_term_project ? : ''; ?>">
					<option value="">Select Project</option>
					<?php foreach($v_projects as $project) : ?>
					<option value="<?php echo $project['id']; ?>"
						<?php echo $project['preselect_in_search_dropdown'] ? 'selected' : ''; ?>>
						<?php echo $project['name']; ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_task">
					Task
				</label>
				<select id="search_task" class="form-control"
					name="search_task" placeholder="Search..."
					value="<?php echo $v_search_term_project ? : ''; ?>">
					<option value="">Select Task</option>
					<?php foreach($v_tasks as $task) : ?>
					<option value="<?php echo $task['id']; ?>"
						<?php echo $task['preselect_in_dropdown'] ? 'selected' : ''; ?>>
						<?php echo $task['name']; ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_project">
					Search project name
				</label>
				<input type="text" id="search_term_project" class="form-control"
					name="search_term_project" placeholder="Search..."
					value="<?php echo $v_search_term_project ? : ''; ?>">
			</div>
		</div>
		
		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_task">
					Search task name
				</label>
				<input type="text" id="search_term_task" class="form-control"
					name="search_term_task" placeholder="Search..."
					value="<?php echo $v_search_term_task ? : ''; ?>">
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_start_date">
					Start date (MM/DD/YYYY):
				</label>
				<input type="text" id="search_term_start_date" class="form-control"
					name="search_term_start_date" placeholder="Search..."
					value="<?php echo $v_search_term_start_date ? : ''; ?>">
				</select>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_end_date">
					End date (MM/DD/YYYY):
				</label>
				<input type="text" id="search_term_end_date" class="form-control"
					name="search_term_end_date" placeholder="Search..."
					value="<?php echo $v_search_term_end_date ? : ''; ?>">
				</select>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_notes">
					Notes:
				</label>
				<input type="text" id="search_term_notes" class="form-control"
					name="search_term_notes" placeholder="Search..."
					value="<?php echo $v_search_term_notes ? : ''; ?>">
				</select>
			</div>
		</div>

		<div class="col-lg-12">
			<div class="form-group">
				<input type="submit" value="Search" class="btn btn-primary">
			</div>
		</div>
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