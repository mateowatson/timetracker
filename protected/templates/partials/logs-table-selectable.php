<form class="table-responsive" method="GET" action="<?php echo $SITE_URL; ?>/edit-logs">
	<input type="submit" value="Edit Selected" class="btn btn-link pl-0">
	<table class="table table-bordered table-sm" style="min-width: 960px;">
		<thead class="thead-light">
			<tr>
				<th colspan="2"></th>
				<th>User</th>
				<th>Project</th>
				<th>Task</th>
				<th>Notes</th>
				<th>Time Started</th>
				<th>Time Ended</th>
				<th>Time Logged</th>
			</tr>
			<tr>
				<th colspan="8" class="text-right">Total</th>
				<th><?php echo Utils::html($v_logs_total_time); ?></th>
			</tr>
		</thead>
		<?php if($v_logs): ?>
		<tbody>
			<?php foreach ($v_logs as $idx => $log): ?>
			<tr>
				<td><label class="sr-only" for="log_<?php echo $idx ?>">Select</label><input type="checkbox" name="log_ids[]" id="log_<?php echo $idx ?>" value="<?php echo urlencode($log['id']); ?>"></td>
				<td><a href="/edit-log?id=<?php echo urlencode($log['id']); ?><?php echo !empty($_SERVER['REQUEST_URI']) ? '&back_to='.urlencode($_SERVER['REQUEST_URI']) : '' ?>">Edit</a></td>
				<td><?php echo $log['username']; ?></td>
				<td><?php echo $log['project_name']; ?></td>
				<td><?php echo $log['task_name']; ?></td>
				<td style="max-width: 300px;"><?php echo $log['notes']; ?></td>
				<td><?php echo Utils::html($log['start_time_formatted']); ?></td>
				<td><?php echo Utils::html($log['end_time_formatted']); ?></td>
				<td><?php echo Utils::html($log['time_sum']); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<?php endif; ?>
	</table>
</form>