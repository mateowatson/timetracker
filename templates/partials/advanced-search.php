<h2>Search</h2>
<form action="/advanced-search" method="POST">
	<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>

	<div class="form-row">
		
		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_project">
					Project:
				</label>
				<input type="text" id="search_term_project" class="form-control"
					name="search_term_project" placeholder="Search..."
					value="<?php echo $v_search_term_project ? : ''; ?>">
				</select>
			</div>
		</div>
		
		<div class="col-lg-6">
			<div class="form-group">
				<label for="search_term_task">
					Task:
				</label>
				<input type="text" id="search_term_task" class="form-control"
					name="search_term_task" placeholder="Search..."
					value="<?php echo $v_search_term_task ? : ''; ?>">
				</select>
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
	'advanced_search_errors', $v_errors_element_ids ? : array()
)) : ?>
<div class="alert alert-danger" id="advanced_search_errors">
	<?php
	foreach($v_errors as $error) :
	if($error->element_id === 'advanced_search_errors') :
	?>
	<p><?php echo $error->message; ?></p>
	<?php endif; endforeach; ?>
</div>
<?php endif; ?>