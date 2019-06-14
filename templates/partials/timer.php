<section class="timer">
	<h1 class="timer__heading">Timer</h1>
	
	<div class="timer__start-time">
		<?php if(!$v_current_log): ?>
		<form action="/start-time" method="POST" class="timer__form">
			<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>

			<label for="start_time_project" class="timer__label">Project</label>
			<select id="start_time_project" name="start_time_project"
				placeholder="Project" class="timer__select">
				<option value="" selected="selected">Select Existing Project</option>
				<?php foreach($v_projects as $project) : ?>
				<option value="<?php echo $project['id']; ?>"
					class="timer__option"
					<?php echo $project['preselect_in_dropdown'] ? 'selected' : ''; ?>>
					<?php echo $project['name']; ?>
				</option>
				<?php endforeach; ?>
			</select>

			<?php if($v_timer_start_new): ?>
			<label for="start_time_new_project" class="timer__label">
				Start New Project
			</label>
			<input type="text" id="start_time_new_project"
				name="start_time_new_project" placeholder="Start New Project..."
				class="timer__text">
			<?php endif; ?>

			<label for="start_time_task" class="timer__label">Task</label>
			<select id="start_time_task" name="start_time_task"
				placeholder="task" class="timer__select">
				<option value="" selected="selected">Select Existing Task</option>
				<?php foreach($v_tasks as $task) : ?>
				<option value="<?php echo $task['id']; ?>"
					class="timer__option"
					<?php echo $task['preselect_in_dropdown'] ? 'selected' : ''; ?>>
					<?php echo $task['name']; ?>
				</option>
				<?php endforeach; ?>
			</select>

			<?php if($v_timer_start_new): ?>
			<label for="start_time_new_task" class="timer__label">
				Start New Task
			</label>
			<input type="text" id="start_time_new_task"
				name="start_time_new_task" placeholder="Start New Task..."
				class="timer__text">
			<?php endif; ?>

			<?php
			if(!$v_timer_start_new):
				if($v_is_team):
			?>
			<a href="/team/<?php echo $v_team['id']; ?>?new" class="timer__start-new-link">
				Start New Project or Task
			</a>
				<?php else: ?>
			<a href="/dashboard?new" class="timer__start-new-link">
				Start New Project or Task
			</a>
			<?php endif; endif; ?>

			<label for="start_time_notes" class="timer__label">Notes</label>
			<textarea id="start_time_notes" name="start_time_notes"
			placeholder="Optional notes..." class="timer__textarea"></textarea>

			<input type="submit" value="Start" class="timer__submit timer__submit--start"
				<?php echo $v_current_log ? 'disabled' : ' ' ?>>

			<?php if(in_array(
				'start_timer_bottom_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="errors" id="start_timer_bottom_errors">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'start_timer_bottom_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
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
		<form action="/stop-time" method="POST" class="timer__form">
			<input type="text" name="csrf" id="csrf_stop_timer" value="<?php echo $CSRF; ?>" hidden>
			<input type="submit" value="Stop" class="timer__submit timer__submit--stop"
				<?php echo $v_current_log ? ' ' : 'disabled' ?>>

			<?php if(in_array(
				'stop_timer_bottom_errors', $v_errors_element_ids ? : array()
			)) : ?>
			<div class="errors" id="stop_timer_bottom_errors">
				<?php
				foreach($v_errors as $error) :
				if($error->element_id === 'stop_timer_bottom_errors') :
				?>
				<p><?php echo $error->message; ?></p>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</form>
		<?php endif; ?>

		<p>This is your personal timer. <a href="/teams">Select a Team</a></p>
	</div>
</section>