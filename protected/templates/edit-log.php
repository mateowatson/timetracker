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
			<?php if($v_log_id): ?>
			<form action="<?php echo $SITE_URL; ?>/edit-log" method="POST">
				<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>
				<input type="text" name="edit_log_log_id" id="edit_log_log_id" value="<?php echo $v_log_id; ?>" hidden>
				<input type="text" name="back_to" id="back_to" value="<?php echo $v_edit_log_cancel_link; ?>" hidden>

				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="edit_log_project">Project</label>
							<select class="form-control" id="edit_log_project" name="edit_log_project"
								placeholder="Project">
								<?php foreach($v_projects as $project) : ?>
								<option
									value="<?php echo $project['id']; ?>"
									<?php if($v_log_project_id === (int)$project['id']): ?>selected="selected"<?php endif; ?>>
									<?php echo $project['name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>

						<?php if($v_timer_start_new): ?>
						<div class="form-group">
							<label for="edit_log_new_project">
								Start New Project
							</label>
							<input class="form-control" type="text" id="edit_log_new_project"
								name="edit_log_new_project" placeholder="Start New Project...">
						</div>
						<?php endif; ?>

						<div class="form-group">
							<label for="edit_log_task">Task</label>
							<select class="form-control" id="edit_log_task" name="edit_log_task"
								placeholder="task">
								<?php foreach($v_tasks as $task) : ?>
								<option
									value="<?php echo $task['id']; ?>"
									<?php if($v_log_task_id === (int)$task['id']): ?>selected="selected"<?php endif; ?>>
									<?php echo $task['name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>

						<?php if($v_timer_start_new): ?>
						<div class="form-group">
							<label for="edit_log_new_task">
								Start New Task
							</label>
							<input class="form-control" type="text" id="edit_log_new_task"
								name="edit_log_new_task" placeholder="Start New Task...">
						</div>
						<?php endif; ?>

						<?php if(!$v_timer_start_new): ?>
						<div class="form-group">
							<a href="<?php echo $SITE_URL; ?>/edit-log?id=<?php echo $v_log_id; ?>&new">
								Start New Project or Task
							</a>
						</div>
						<?php endif; ?>
					</div>

					<div class="col-lg-4">
						<fieldset class="form-group">
							<legend class="h6">Start Time</legend>

							<div class="form-row align-items-center mb-2">
								<div class="col-3">
									<label class="sr-only" for="edit_log_start_mm">
										Month in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="MM"
										value="<?php echo $v_log_start_time['mm'] ? : ''; ?>"
										name="edit_log_start_mm"
										id="edit_log_start_mm">
								</div>

								<div class="col-1 text-center">
									/
								</div>

								<div class="col-3">
									<label class="sr-only" for="edit_log_start_dd">
										Day in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="DD"
										value="<?php echo $v_log_start_time['dd'] ? : ''; ?>"
										name="edit_log_start_dd"
										id="edit_log_start_dd">
								</div>

								<div class="col-1 text-center">	
									/
								</div>
									
								<div class="col-4">
									<label class="sr-only" for="edit_log_start_yyyy">
										Year in four digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="YYYY"
										value="<?php echo $v_log_start_time['yyyy'] ? : ''; ?>"
										name="edit_log_start_yyyy"
										id="edit_log_start_yyyy">
								</div>
							</div>

							<div class="form-row align-items-center">
								<div class="col-3">
									<label class="sr-only" for="edit_log_start_hour">
										Hour in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="hh"
										value="<?php echo $v_log_start_time['hour'] ? : ''; ?>"
										name="edit_log_start_hour"
										id="edit_log_start_hour">
								</div>

								<div class="col-1 text-center">
									:
								</div>

								<div class="col-3">
									<label class="sr-only" for="edit_log_start_min">
										Minute in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="mm"
										value="<?php echo $v_log_start_time['min'] ? : ''; ?>"
										name="edit_log_start_min"
										id="edit_log_start_min">
								</div>

								<div class="col-1 text-center">
									:
								</div>

								<div class="col-4">
									<label class="sr-only" for="edit_log_start_sec">
										Second in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="ss"
										value="<?php echo $v_log_start_time['sec'] ? : ''; ?>"
										name="edit_log_start_sec"
										id="edit_log_start_sec">
								</div>
							</div>
						</fieldset>

						<fieldset class="form-group">
							<legend class="h6">End Time</legend>

							<div class="form-row align-items-center mb-2">
								<div class="col-3">
									<label class="sr-only" for="edit_log_end_mm">
										Month in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="MM"
										value="<?php echo $v_log_end_time['mm'] ? : ''; ?>"
										name="edit_log_end_mm"
										id="edit_log_end_mm">
								</div>

								<div class="col-1 text-center">
									/
								</div>

								<div class="col-3">
									<label class="sr-only" for="edit_log_end_dd">
										Day in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="DD"
										value="<?php echo $v_log_end_time['dd'] ? : ''; ?>"
										name="edit_log_end_dd"
										id="edit_log_end_dd">
								</div>

								<div class="col-1 text-center">
									/
								</div>

								<div class="col-3">
									<label class="sr-only" for="edit_log_end_yyyy">
										Year in four digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="YYYY"
										value="<?php echo $v_log_end_time['yyyy'] ? : ''; ?>"
										name="edit_log_end_yyyy"
										id="edit_log_end_yyyy">
								</div>
							</div>

							<div class="form-row align-items-center">
								<div class="col-3">
									<label class="sr-only" for="edit_log_end_hour">
										Hour in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="hh"
										value="<?php echo $v_log_end_time['hour'] ? : ''; ?>"
										name="edit_log_end_hour"
										id="edit_log_end_hour">
								</div>

								<div class="col-1 text-center">
									:
								</div>

								<div class="col-3">
									<label class="sr-only" for="edit_log_end_min">
										Minute in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="mm"
										value="<?php echo $v_log_end_time['min'] ? : ''; ?>"
										name="edit_log_end_min"
										id="edit_log_end_min">
								</div>

								<div class="col-1 text-center">
									:
								</div>

								<div class="col-3">
									<label class="sr-only" for="edit_log_end_sec">
										Second in two digits
									</label>
									<input class="form-control"
										type="text"
										placeholder="ss"
										value="<?php echo $v_log_end_time['sec'] ? : ''; ?>"
										name="edit_log_end_sec"
										id="edit_log_end_sec">
								</div>
							</div>
						</fieldset>
					</div>
					
					<div class="col-lg-4">
						<div class="form-group">
							<label for="edit_log_notes">Notes</label>
							<textarea class="form-control" id="edit_log_notes" name="edit_log_notes"
							placeholder="Optional notes..."><?php echo $v_log_notes; ?></textarea>
						</div>
					</div>
				</div>

				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Save">
					<a href="<?php echo $v_edit_log_cancel_link; ?>" class="btn btn-link">Cancel</a>
				</div>

				<div class="form-group">
					<a class="text-danger" href="<?php echo $SITE_URL; ?>/delete-log?log=<?php echo urlencode($v_log_id); ?>">Delete This Log</a>
				</div>

				<?php if(in_array(
					'edit_log_bottom_confirmations', $v_confirmations_element_ids ? : array()
				)) : ?>
				<div class="alert alert-success" role="alert">
					<?php
					foreach($v_confirmations as $confirmation) :
					if($confirmation->element_id === 'edit_log_bottom_confirmations') :
					?>
					<p><?php echo $confirmation->message; ?></p>
					<p><a href="<?php echo $v_edit_log_cancel_link?>">Go Back</a></p>
					<?php endif; endforeach; ?>
				</div>
				<?php endif; ?>

				<?php if(in_array(
					'edit_log_bottom_errors', $v_errors_element_ids ? : array()
				)) : ?>
				<div class="alert alert-danger" role="alert">
					<?php
					foreach($v_errors as $error) :
					if($error->element_id === 'edit_log_bottom_errors') :
					?>
					<p><?php echo $error->message; ?></p>
					<?php endif; endforeach; ?>
				</div>
				<?php endif; ?>
			</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>