<form action="<?php echo $SITE_URL; ?>/report" method="POST" id="report-form">
	<input type="text" name="csrf" id="csrf_report_timer" value="<?php echo $CSRF; ?>" hidden>

    <div class="form-group">
        <label for="rp">
            Project
        </label>
        <select id="rp" class="form-control" name="rp">
            <option value="">All</option>
            <?php foreach($v_projects as $project) : ?>
            <option value="<?php echo $project['id']; ?>"
                <?php echo $project['preselect_in_report_dropdown'] ? 'selected' : ''; ?>>
                <?php echo $project['name']; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="rt">
            Task
        </label>
        <select id="rt" class="form-control" name="rt">
            <option value="">All</option>
            <?php foreach($v_tasks as $task) : ?>
            <option value="<?php echo $task['id']; ?>"
                <?php echo $task['preselect_in_report_dropdown'] ? 'selected' : ''; ?>>
                <?php echo $task['name']; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
		<label for="rd">
			Date or date range<span class="sr-only">, mm/dd/yyyy or mm/dd/yyyy - mm/dd/yyyy</span>
		</label>
		<input type="text" id="rd" class="form-control"
			name="rd" placeholder="mm/dd/yyyy or mm/dd/yyyy - mm/dd/yyyy">
    </div>
    
    

	<div class="form-group">
        <input class="btn btn-primary" type="submit" value="Go">
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