<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">

        <div class="col-lg-12">
			<?php if(!empty($v_log_ids)): ?>
			<form action="<?php echo $SITE_URL; ?>/edit-logs" method="POST">
				<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>
				<input type="text" name="edit_logs_log_ids" id="edit_logs_log_id" value="<?php echo implode($v_log_ids); ?>" hidden>

				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label for="edit_logs_project">Set Project</label>
							<select class="form-control" id="edit_logs_project" name="edit_logs_project"
								placeholder="Project">
								<?php foreach($v_projects as $project) : ?>
								<option
									value="<?php echo $project['id']; ?>">
									<?php echo $project['name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>

						<?php if($v_timer_start_new): ?>
						<div class="form-group">
							<label for="edit_logs_new_project">
								Create New Project
							</label>
							<input class="form-control" type="text" id="edit_logs_new_project"
								name="edit_logs_new_project" placeholder="Create New Project...">
						</div>
						<?php endif; ?>

						<div class="form-group">
							<label for="edit_logs_task">Set Task</label>
							<select class="form-control" id="edit_logs_task" name="edit_logs_task"
								placeholder="task">
								<?php foreach($v_tasks as $task) : ?>
								<option
									value="<?php echo $task['id']; ?>">
									<?php echo $task['name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>

						<?php if($v_timer_start_new): ?>
						<div class="form-group">
							<label for="edit_logs_new_task">
								Create New Task
							</label>
							<input class="form-control" type="text" id="edit_logs_new_task"
								name="edit_logs_new_task" placeholder="Create New Task...">
						</div>
						<?php endif; ?>

						<?php if(!$v_timer_start_new): ?>
						<div class="form-group">
							<a href="<?php echo $SITE_URL; ?>/edit-logs?<?php echo $v_log_ids_query_string; ?>&new">
								Create New Project or Task
							</a>
						</div>
						<?php endif; ?>
					</div>
					
					<div class="col-lg-6">
						<div class="form-group">
							<label for="edit_logs_notes">Notes</label>
							<textarea class="form-control" id="edit_logs_notes" name="edit_logs_notes"
							placeholder="Optional notes..."></textarea>
						</div>
					</div>
				</div>


                <div class="row my-3">
                    <div class="col-lg-12">
                        <p>The following logs will be updated with the changes above.</p>
                        <?php if($v_logs): ?>
                        <?php require_once('partials/logs-table-not-editable.php'); ?>
                        <?php else: ?>
                        <?php require_once('partials/logs-no-logs.php'); ?>
                        <?php endif; ?>
                    </div>
                </div>
                

				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Update">
					<a href="<?php echo $v_edit_logs_cancel_link; ?>" class="btn btn-link">Cancel</a>
				</div>

				<div class="form-group my-5">
					<a class="text-danger" href="<?php echo $SITE_URL; ?>/delete-logs?log_ids=<?php echo $v_log_ids_query_string; ?>">Delete These Logs</a>
				</div>
			</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>