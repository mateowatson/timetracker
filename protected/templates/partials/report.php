<h2>Report</h2>
<form action="<?php echo $SITE_URL; ?>/report" method="POST">
	<input type="text" name="csrf" id="csrf_report_timer" value="<?php echo $CSRF; ?>" hidden>

    <div class="form-group">
        <label for="rp">
            Project
        </label>
        <select id="rp" class="form-control"
            name="rp" value="<?php echo $v_search_term_project ? : ''; ?>">
            <option value="">Select Project</option>
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
        <select id="rt" class="form-control"
            name="rt" value="<?php echo $v_search_term_project ? : ''; ?>">
            <option value="">Select Task</option>
            <?php foreach($v_tasks as $task) : ?>
            <option value="<?php echo $task['id']; ?>"
                <?php echo $task['preselect_in_report_dropdown'] ? 'selected' : ''; ?>>
                <?php echo $task['name']; ?>
            </option>
            <?php endforeach; ?>
        </select>
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