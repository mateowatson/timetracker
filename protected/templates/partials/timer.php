<?php if ($v_current_log): ?>
	<h2>Timer Running</h2>
<?php else: ?>
	<h2>Timer</h2>
<?php endif; ?>

<div>
	<?php if (!$v_current_log): ?>
	<?php if (
   in_array("start_timer_bottom_errors", $v_errors_element_ids ?: [])
 ): ?>
	<div class="alert alert-danger" role="alert">
		<p>The following errors occurred:</p>
		<ul>
			<?php foreach ($v_errors as $error):
     if ($error->element_id === "start_timer_bottom_errors"): ?>
			<li><?php echo $error->message; ?></li>
			<?php endif;
   endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	
	<form action="<?php echo $SITE_URL; ?>/start-time" method="POST" data-timer-form>
		<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>

		<?php if ($v_timer_start_new !== "project" && count($v_projects)): ?>
		<div class="form-group">
			<label for="start_time_project">Project</label>
			<select class="form-control" id="start_time_project" name="start_time_project">
				<option value="">Select Existing Project</option>
				<?php foreach ($v_projects as $project): ?>
				<option value="<?php echo $project["id"]; ?>"
					<?php echo $project["preselect_in_dropdown"] ? "selected" : ""; ?>>
					<?php echo $project["name"]; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<?php if (
    ($v_timer_start_new || count($v_projects) === 0) &&
    $v_timer_start_new !== "task"
  ): ?>
		<div class="form-group">
			<label for="start_time_new_project">
				Start New Project
			</label>
			<input class="form-control" type="text" id="start_time_new_project"
				name="start_time_new_project" placeholder="Start New Project...">
		</div>
		<?php endif; ?>

		<?php if (!$v_timer_start_new && count($v_projects)): ?>
		<div class="form-group">
			<p><a href="<?php echo $v_new_project_link; ?>" data-ajax-link>New Project</a></p>
		</div>
		<?php endif; ?>

		<?php if ($v_timer_start_new !== "task" && count($v_tasks)): ?>
		<div class="form-group">
			<label for="start_time_task">Task</label>
			<select class="form-control" id="start_time_task" name="start_time_task">
				<option value="">Select Existing Task</option>
				<?php foreach ($v_tasks as $task): ?>
				<option value="<?php echo $task["id"]; ?>"
					<?php echo $task["preselect_in_dropdown"] ? "selected" : ""; ?>>
					<?php echo $task["name"]; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<?php if ($v_timer_start_new || count($v_tasks) === 0): ?>
		<div class="form-group">
			<label for="start_time_new_task">
				Start New Task
			</label>
			<input class="form-control" type="text" id="start_time_new_task"
				name="start_time_new_task" placeholder="Start New Task...">
		</div>
		<?php endif; ?>

		<?php if (!$v_timer_start_new && count($v_tasks)): ?>
		<div class="form-group">
			<p><a href="<?php echo $v_new_task_link; ?>" data-ajax-link>New Task</a></p>
		</div>
		<?php endif; ?>

		<div class="form-group">
			<label for="start_time_notes">Notes</label>
			<textarea class="form-control" id="start_time_notes" name="start_time_notes"
			placeholder="Optional notes..."></textarea>
		</div>

		<div class="form-group">
			<button
				class="btn btn-success btn-spinner" type="submit" data-timer-submit
				<?php echo $v_current_log ? "disabled" : " "; ?>
			>
				<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" data-timer-submit-spinner></span>
				Start
			</button>
			<?php if ($v_timer_start_new): ?>
			<a href="<?php echo $v_refresh_link; ?>" class="btn btn-link" data-ajax-link>Cancel</a>
			<?php endif; ?>
		</div>
	</form>
	<?php endif; ?>

	<?php if ($v_current_log): ?>
	<div>
		<p data-timer-elapsed="<?php echo $v_current_log_diff; ?>">
			<span>
				<?php echo $v_current_log_diff; ?>
			</span>
			<a href="<?php echo $v_refresh_link; ?>">Refresh</a>
		</p>
		<p>
			Project: <?php echo $v_current_log_project; ?>
		</p>
		<p>Task:
			<?php echo $v_current_log_task; ?>
		</p>
		<?php if ($v_current_log["notes"]): ?>
		<h3>Notes</h3>
		<p><?php echo $v_current_log["notes"]; ?></p>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if ($v_current_log): ?>
	<?php if (in_array("stop_timer_bottom_errors", $v_errors_element_ids ?: [])): ?>
	<div class="alert alert-danger" role="alert">
		<p>The following errors occurred:</p>
		<ul>
			<?php foreach ($v_errors as $error):
     if ($error->element_id === "stop_timer_bottom_errors"): ?>
			<li><?php echo $error->message; ?></li>
			<?php endif;
   endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<form action="/stop-time" method="POST" data-timer-form>
		<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
		<div class="form-group">
			<button 
				type="submit"
				class="btn btn-danger"
	 			data-timer-submit
				<?php echo $v_current_log ? " " : "disabled"; ?>
			>
			<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" data-timer-submit-spinner></span>
				Stop
			</button>
		</div>
	</form>
	<?php endif; ?>
</div>
