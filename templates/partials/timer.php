<h2>Timer</h2>

<div>
	<?php if(!$v_current_log): ?>
	<?php if(in_array(
		'start_timer_bottom_errors', $v_errors_element_ids ? : array()
	)) : ?>
	<div class="p-3 mb-3 bg-danger text-white" id="start_timer_bottom_errors">
		<h3>Sorry, there were one or more errors :-(.</h3>
		<p>The following errors occurred:</p>
		<ul>
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'start_timer_bottom_errors') :
			?>
			<li><?php echo $error->message; ?></li>
			<?php endif; endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	
	<form action="/start-time" method="POST">
		<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>

		<div class="form-group">
			<label for="start_time_project">Project</label>
			<select class="form-control" id="start_time_project" name="start_time_project"
				placeholder="Project">
				<option value="" selected="selected">Select Existing Project</option>
				<?php foreach($v_projects as $project) : ?>
				<option value="<?php echo $project['id']; ?>"
					<?php echo $project['preselect_in_dropdown'] ? 'selected' : ''; ?>>
					<?php echo $project['name']; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php if($v_timer_start_new): ?>
		<div class="form-group">
			<label for="start_time_new_project">
				Start New Project
			</label>
			<input class="form-control" type="text" id="start_time_new_project"
				name="start_time_new_project" placeholder="Start New Project...">
		</div>
		<?php endif; ?>

		<div class="form-group">
			<label for="start_time_task">Task</label>
			<select class="form-control" id="start_time_task" name="start_time_task"
				placeholder="task">
				<option value="" selected="selected">Select Existing Task</option>
				<?php foreach($v_tasks as $task) : ?>
				<option value="<?php echo $task['id']; ?>"
					<?php echo $task['preselect_in_dropdown'] ? 'selected' : ''; ?>>
					<?php echo $task['name']; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php if($v_timer_start_new): ?>
		<div class="form-group">
			<label for="start_time_new_task">
				Start New Task
			</label>
			<input class="form-control" type="text" id="start_time_new_task"
				name="start_time_new_task" placeholder="Start New Task...">
		</div>
		<?php endif; ?>

		<?php
		if(!$v_timer_start_new):
			if($v_is_team):
		?>
		<div class="form-group text-center">
			<p><a href="/team/<?php echo $v_team['id']; ?>?new">
				Start New Project or Task
			</a></p>
		</div>
		
			<?php else: ?>
		<div class="form-group text-center">
			<p><a href="/dashboard?new">
				Start New Project or Task
			</a></p>
		</div>
		<?php endif; endif; ?>

		<div class="form-group">
			<label for="start_time_notes">Notes</label>
			<textarea class="form-control" id="start_time_notes" name="start_time_notes"
			placeholder="Optional notes..."></textarea>
		</div>

		<div class="form-group">
			<input class="btn btn-success" type="submit" value="Start" <?php echo $v_current_log ? 'disabled' : ' ' ?>>
		</div>
	</form>
	<?php endif; ?>

	<?php if($v_current_log): ?>
	<div class="timer__counter">
		<h2 class="timer__counter-heading">Timer Running</h2>
		<p class="timer__counter-paragraph">
			<span class="timer__running-total">
				<?php echo $v_current_log_diff; ?>
			</span>
			<a class="timer__refresh" href="/">Refresh</a>
		</p>
		<p class="timer__counter-paragraph">
			Project: <?php echo $v_current_log_project; ?>
		</p>
		<p class="timer__counter-paragraph">Task:
			<?php echo $v_current_log_task; ?>
		</p>
		<?php if($v_current_log['notes']): ?>
		<h3 class="timer__counter-subheading">Notes</h3>
		<p class="timer__counter-paragraph"><?php echo $v_current_log['notes']; ?></p>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if($v_current_log): ?>
	<?php if(in_array(
		'stop_timer_bottom_errors', $v_errors_element_ids ? : array()
	)) : ?>
	<div class="p-3 mb-3 bg-danger text-white" id="stop_timer_bottom_errors">
		<h3>Sorry, there were one or more errors :-(.</h3>
		<p>The following errors occurred:</p>
		<ul>
			<?php
			foreach($v_errors as $error) :
			if($error->element_id === 'stop_timer_bottom_errors') :
			?>
			<li><?php echo $error->message; ?></li>
			<?php endif; endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<form action="/stop-time" method="POST">
		<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
		<div class="form-group">
			<input type="submit" value="Stop" class="btn btn-danger"
			<?php echo $v_current_log ? ' ' : 'disabled' ?>>
		</div>
	</form>
	<?php endif; ?>

	<?php if(!$v_is_team): ?>
	<p>This is your personal timer. <a href="/teams">Select a Team</a></p>
	<?php else: ?>
	<p><a href="/dashboard">Go back to your personal dashboard.</a></p>
	<p><a href="/teams">Select another team</a></p>
	<?php endif; ?>
</div>
