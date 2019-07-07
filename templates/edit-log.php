<?php require_once('partials/header.php'); ?>
<div class="main-layout main-layout--edit-page">
	<?php require_once('partials/heading.php'); ?>

	<?php require_once('partials/account.php'); ?>

	<section class="timer">
		<h1 class="timer__heading">Timer</h1>
		
		<div class="timer__start-time">
			<?php if($v_log_id): ?>
			<form action="/edit-log" method="POST" class="timer__form">
				<input type="text" name="csrf" id="csrf_timer" value="<?php echo $CSRF; ?>" hidden>

				<input type="text" name="edit_log_log_id" id="edit_log_log_id" value="<?php echo $v_log_id; ?>" hidden>

				<label for="edit_log_project" class="timer__label">Project</label>
				<select id="edit_log_project" name="edit_log_project"
					placeholder="Project" class="timer__select">
					<?php foreach($v_projects as $project) : ?>
					<option
						value="<?php echo $project['id']; ?>"
						class="timer__option"
						<?php if($v_log_project_id === (int)$project['id']): ?>selected="selected"<?php endif; ?>>
						<?php echo $project['name']; ?>
					</option>
					<?php endforeach; ?>
				</select>

				<?php if($v_timer_start_new): ?>
				<label for="edit_log_new_project" class="timer__label">
					Start New Project
				</label>
				<input type="text" id="edit_log_new_project"
					name="edit_log_new_project" placeholder="Start New Project..."
					class="timer__text">
				<?php endif; ?>

				<label for="edit_log_task" class="timer__label">Task</label>
				<select id="edit_log_task" name="edit_log_task"
					placeholder="task" class="timer__select">
					<?php foreach($v_tasks as $task) : ?>
					<option
						value="<?php echo $task['id']; ?>"
						class="timer__option"
						<?php if($v_log_task_id === (int)$task['id']): ?>selected="selected"<?php endif; ?>>
						<?php echo $task['name']; ?>
					</option>
					<?php endforeach; ?>
				</select>

				<?php if($v_timer_start_new): ?>
				<label for="edit_log_new_task" class="timer__label">
					Start New Task
				</label>
				<input type="text" id="edit_log_new_task"
					name="edit_log_new_task" placeholder="Start New Task..."
					class="timer__text">
				<?php endif; ?>

				<?php if(!$v_timer_start_new): ?>
				<a href="/edit-log?id=<?php echo $v_log_id; ?>&new" class="timer__start-new-link">
					Start New Project or Task
				</a>
				<?php endif; ?>

				<div class="timer__datetime">
					<h2 class="timer__fieldgroup-heading">Start Time</h2>
					<div class="timer__date">
						<label
							for="edit_log_start_mm"
							class="timer__label">
							Month in two digits
						</label>
						<input
							type="text"
							placeholder="MM"
							value="<?php echo $v_log_start_time['mm'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_start_mm"
							id="edit_log_start_mm">/

						<label
							for="edit_log_start_dd"
							class="timer__label">
							Day in two digits
						</label>
						<input
							type="text"
							placeholder="DD"
							value="<?php echo $v_log_start_time['dd'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_start_dd"
							id="edit_log_start_dd">/

						<label
							for="edit_log_start_yyyy"
							class="timer__label">
							Year in four digits
						</label>
						<input
							type="text"
							placeholder="YYYY"
							value="<?php echo $v_log_start_time['yyyy'] ? : ''; ?>"
							class="timer__datetime-item--four-digits"
							name="edit_log_start_yyyy"
							id="edit_log_start_yyyy">
					</div>
					<div class="timer__time">
						<label
							for="edit_log_start_hour"
							class="timer__label">
							Hour in two digits
						</label>
						<input
							type="text"
							placeholder="hh"
							value="<?php echo $v_log_start_time['hour'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_start_hour"
							id="edit_log_start_hour">:

						<label
							for="edit_log_start_min"
							class="timer__label">
							Minute in two digits
						</label>
						<input
							type="text"
							placeholder="mm"
							value="<?php echo $v_log_start_time['min'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_start_min"
							id="edit_log_start_min">:

						<label
							for="edit_log_start_sec"
							class="timer__label">
							Second in two digits
						</label>
						<input
							type="text"
							placeholder="ss"
							value="<?php echo $v_log_start_time['sec'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_start_sec"
							id="edit_log_start_sec">
					</div>
				</div>

				<div class="timer__datetime">
					<h2 class="timer__fieldgroup-heading">End Time</h2>
					<div class="timer__date">
						<label
							for="edit_log_end_mm"
							class="timer__label">
							Month in two digits
						</label>
						<input
							type="text"
							placeholder="MM"
							value="<?php echo $v_log_end_time['mm'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_end_mm"
							id="edit_log_end_mm">/

						<label
							for="edit_log_end_dd"
							class="timer__label">
							Day in two digits
						</label>
						<input
							type="text"
							placeholder="DD"
							value="<?php echo $v_log_end_time['dd'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_end_dd"
							id="edit_log_end_dd">/

						<label
							for="edit_log_end_yyyy"
							class="timer__label">
							Year in four digits
						</label>
						<input
							type="text"
							placeholder="YYYY"
							value="<?php echo $v_log_end_time['yyyy'] ? : ''; ?>"
							class="timer__datetime-item--four-digits"
							name="edit_log_end_yyyy"
							id="edit_log_end_yyyy">
					</div>
					<div class="timer__time">
						<label
							for="edit_log_end_hour"
							class="timer__label">
							Hour in two digits
						</label>
						<input
							type="text"
							placeholder="hh"
							value="<?php echo $v_log_end_time['hour'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_end_hour"
							id="edit_log_end_hour">:

						<label
							for="edit_log_end_min"
							class="timer__label">
							Minute in two digits
						</label>
						<input
							type="text"
							placeholder="mm"
							value="<?php echo $v_log_end_time['min'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_end_min"
							id="edit_log_end_min">:

						<label
							for="edit_log_end_sec"
							class="timer__label">
							Second in two digits
						</label>
						<input
							type="text"
							placeholder="ss"
							value="<?php echo $v_log_end_time['sec'] ? : ''; ?>"
							class="timer__datetime-item"
							name="edit_log_end_sec"
							id="edit_log_end_sec">
					</div>
				</div>
				

				<label for="edit_log_notes" class="timer__label">Notes</label>
				<textarea id="edit_log_notes" name="edit_log_notes"
				placeholder="Optional notes..." class="timer__textarea"><?php echo $v_log_notes; ?></textarea>

				<input type="submit" value="Save">

				<br><br><br><br>
				<a class="timer__submit--stop" href="/delete-log?log=<?php echo urlencode($v_log_id); ?>">Delete This Log</a>

				<?php if(in_array(
					'edit_log_bottom_confirmations', $v_confirmations_element_ids ? : array()
				)) : ?>
				<div class="confirmations" id="edit_log_bottom_confirmations">
					<?php
					foreach($v_confirmations as $confirmation) :
					if($confirmation->element_id === 'edit_log_bottom_confirmations') :
					?>
					<p><?php echo $confirmation->message; ?></p>
					<?php endif; endforeach; ?>
				</div>
				<?php endif; ?>

				<?php if(in_array(
					'edit_log_bottom_errors', $v_errors_element_ids ? : array()
				)) : ?>
				<div class="errors" id="edit_log_bottom_errors">
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
	</section>
</div>

<?php require_once('partials/footer.php'); ?>