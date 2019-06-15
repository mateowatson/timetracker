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

		$f3->set('v_page_title', 'Dashboard');
		$is_team = false;
		$team = false;
		if(isset($args['team_url_id'])) {
			$is_user_in_team = false;
			$team_name = '';
			$available_teams = $db->exec('SELECT * FROM users_teams WHERE user_id = ?', $user->id);
			foreach($available_teams as $av_t) {
				if((int)$av_t['user_id'] === (int)$user->id && (int)$av_t['team_id'] === (int)$args['team_url_id']) {
					$is_user_in_team = true;
					$is_team = true;
					$team = $db->exec('SELECT * FROM teams WHERE id = ?', $av_t['team_id'])[0];
					break;
				}
			}
			if(!$is_user_in_team) {
				return $f3->reroute('/dashboard');
			}
			$f3->set('v_page_title', 'Team: '.$team['name']);
		}

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
		$extra_conditions = '';
		$is_week = isset($f3->get('REQUEST')['week']) ? true : false;
		$dashboard_time_filter = '';
		if($is_week) {
			$dashboard_time_filter = 'AND (
					YEAR(CAST(logs.start_time AS DATE)) = YEAR(CAST(NOW() AS DATE))
					AND
					WEEK(CAST(logs.start_time AS DATE), 0) = WEEK(CAST(NOW() AS DATE), 0)
				)';
		} else {
			$dashboard_time_filter = 'AND CAST(logs.start_time AS DATE) =
				CAST(NOW() AS DATE)';
		}
		$team_filter = '';
		if($is_team) {
			$team_filter = ' AND logs.team_id = '.$team['id'];
		}
		$extra_conditions .= $dashboard_time_filter .= $team_filter;
		$did_set_v_logs = Utils::set_v_logs(
			$f3,
			$user->id,
			$extra_conditions,
			$team_filter ? : false,
			false,
			null
		);

		// SET TOTAL TIME
		$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
			$f3, $user->id, $extra_conditions, $team_filter ? : false
		);

		if($is_team) {
			$last_log = $db->exec(
				'SELECT * FROM logs WHERE user_id = ? AND team_id = ? ORDER BY start_time DESC LIMIT 1',
				array($user->id, $team['id'])
			);
		} else {
			$last_log = $db->exec(
				'SELECT * FROM logs WHERE user_id = ? ORDER BY start_time DESC LIMIT 1',
				array($user->id)
			);
		}
		foreach($projects as $project_idx => $project) {
			if($project['id'] === $last_log[0]['project_id'] && !isset($req['new'])) {
				$projects[$project_idx]['preselect_in_dropdown'] = true;
			}
		}
		foreach($tasks as $task_idx => $task) {
			if($task['id'] === $last_log[0]['task_id'] && !isset($req['new'])) {
				$tasks[$task_idx]['preselect_in_dropdown'] = true;
			}
		}

		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);
		
		// ADDITIONAL VIEW VARIABLES
		
		// SET SOME TEAM VIEW VARIABLES
		$f3->set('v_is_team', $is_team);
		if($is_team) {
			$f3->set('v_team', $team);
		}

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