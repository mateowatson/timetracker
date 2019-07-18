<div class="table-responsive">
	<table class="table table-bordered table-sm" style="min-width: 960px;">
		<thead class="thead-light">
			<tr>
				<th></th>
				<th>User</th>
				<th>Project</th>
				<th>Task</th>
				<th>Notes</th>
				<th>Time Started</th>
				<th>Time Ended</th>
				<th>Time Logged</th>
			</tr>
			<tr>
				<th colspan="7">Total</th>
				<th><?php echo $v_logs_total_time; ?></th>
			</tr>
		</thead>
		<?php if($v_logs): ?>
		<tbody>
			<?php foreach ($v_logs as $log): ?>
			<tr>
				<td><a href="/edit-log?id=<?php echo urlencode($log['id']); ?>">Edit</a></td>
				<td><?php echo $log['username']; ?></td>
				<td><?php echo $log['project_name']; ?></td>
				<td><?php echo $log['task_name']; ?></td>
				<td style="max-width: 300px;"><?php echo $log['notes']; ?></td>
				<td><?php echo $log['start_time_formatted']; ?></td>
				<td>
					<?php echo $log['end_time_formatted']; ?>
				</td>
				<td>
					<?php echo $log['time_sum']; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<?php endif; ?>
	</table>
</div>