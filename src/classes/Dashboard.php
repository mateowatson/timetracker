<?php
class Dashboard {
	function show($f3, $args) {
		$req = $f3->get('REQUEST');
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);

		// GET PROJECT AND TASK LISTS
		$projectsQuery = $db->exec('
			SELECT * FROM projects LEFT JOIN users_projects ON
			(users_projects.user_id = ? AND
			users_projects.project_id = projects.id) WHERE
			users_projects.user_id IS NOT NULL
		', array($user->id));
		$tasksQuery = $db->exec('
			SELECT * FROM tasks LEFT JOIN users_tasks ON
			(users_tasks.user_id = ? AND
			users_tasks.task_id = tasks.id) WHERE
			users_tasks.user_id IS NOT NULL
		', array($user->id));
		$projects = array();
		$tasks = array();
		foreach($projectsQuery as $query) {
			array_push($projects, array(
				'id' => $query['project_id'],
				'name' => $query['name'],
				'preselect_in_dropdown' => false
			));
		}
		foreach($tasksQuery as $query) {
			array_push($tasks, array(
				'id' => $query['task_id'],
				'name' => $query['name'],
				'preselect_in_dropdown' => false
			));
		}

		// GET CURRENTLY RUNNING LOG IF EXISTS
		$db_logs = new \DB\SQL\Mapper($db, 'logs');
		$db_logs->load(array(
			'end_time="0000-00-00 00:00:00" AND user_id = ?',
			$user->id
		));
		$f3->set('v_current_log', null);
		$f3->set('v_current_log_diff', null);
		$f3->set('v_current_log_project', null);
		$f3->set('v_current_log_task', null);
		if(!$db_logs->dry()) {
			$db_logs->copyto('v_current_log');
			$diff_start = date_create($f3->get('v_current_log')['start_time']);
			$diff_now = date_create();
			$diff = date_diff($diff_start, $diff_now, false)->format('%H:%I:%S');
			$f3->set('v_current_log_diff', $diff);
			foreach ($projects as $project) {
				$proj_id = intval($project['id']);
				$curr_log_proj_id = intval($f3->get('v_current_log')['project_id']);
				if($proj_id === $curr_log_proj_id) {
					$f3->set('v_current_log_project', $project['name']);
				}
			}
			foreach ($tasks as $task) {
				$task_id = intval($task['id']);
				$curr_log_task_id = intval($f3->get('v_current_log')['task_id']);
				if($task_id === $curr_log_task_id) {
					$f3->set('v_current_log_task', $task['name']);
				}
			}
		}

		// GET LOGS LIST
		$is_week = isset($f3->get('REQUEST')['week']) ? true : false;
		$dashboard_time_filter = 'null';
		if($is_week) {
			$dashboard_time_filter = '
				AND (
					YEAR(CAST(logs.start_time AS DATE)) = YEAR(CAST(NOW() AS DATE))
					AND
					WEEK(CAST(logs.start_time AS DATE), 0) = WEEK(CAST(NOW() AS DATE), 0)
				)
			';
		} else {
			$dashboard_time_filter = '
				AND CAST(logs.start_time AS DATE) =
				CAST(NOW() AS DATE)
			';
		}
		$did_set_v_logs = Utils::set_v_logs(
			$f3,
			$user->id,
			$dashboard_time_filter,
			false,
			null
		);

		// SET TOTAL TIME
		$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
			$f3, $user->id, $dashboard_time_filter
		);

		$logs = $f3->get('v_logs');
		foreach($projects as $project_idx => $project) {
			if($project['name'] === $logs[0]['project_name'] && !isset($req['new'])) {
				$projects[$project_idx]['preselect_in_dropdown'] = true;
			}
		}
		foreach($tasks as $task_idx => $task) {
			if($task['name'] === $logs[0]['task_name'] && !isset($req['new'])) {
				$tasks[$task_idx]['preselect_in_dropdown'] = true;
			}
		}

		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);
		
		// ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Dashboard');

		// RENDER
		$view = new \View;
		if(isset($req['new'])) {
			$f3->set('v_timer_start_new', true);
		} else {
			$f3->set('v_timer_start_new', false);
		}
        echo $view->render('dashboard.php');
	}
}