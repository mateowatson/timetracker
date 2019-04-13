<div class="logs__table-wrapper">
	<table class="logs__table">
		<thead class="logs__thead">
			<tr class="logs__thead-row">
				<th class="logs__th"></th>
				<th class="logs__th">User</th>
				<th class="logs__th">Project</th>
				<th class="logs__th">Task</th>
				<th class="logs__th">Notes</th>
				<th class="logs__th logs__th--number">Time Started</th>
				<th class="logs__th logs__th--number">Time Ended</th>
				<th class="logs__th logs__th--number">Time Logged</th>
			</tr>
			<tr class="logs__thead-row">
				<th colspan="7" class="logs__th  logs__th--total-time-label">Total</th>
				<th class="logs__th logs__th--number"><?php echo $v_logs_total_time; ?></th>
			</tr>
		</thead>
		<?php if($v_logs): ?>
		<tbody class="logs__tbody">
			<?php foreach ($v_logs as $log): ?>
			<tr class="logs__tbody-row">
				<td class="logs__td"><a href="/edit-log?id=<?php echo urlencode($log['id']); ?>">Edit</a></td>
				<td class="logs__td"><?php echo $log['username']; ?></td>
				<td class="logs__td"><?php echo $log['project_name']; ?></td>
				<td class="logs__td"><?php echo $log['task_name']; ?></td>
				<td class="logs__td"><?php echo $log['notes']; ?></td>
				<td class="logs__td logs__td--number"><?php echo $log['start_time_formatted']; ?></td>
				<td class="logs__td logs__td--number">
					<?php echo $log['end_time_formatted']; ?>
				</td>
				<td class="logs__td logs__td--number">
					<?php echo $log['time_sum']; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<?php endif; ?>
	</table>
</div>