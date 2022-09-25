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
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);
		$SITE_URL = $f3->get('SITE_URL');

		$f3->set('v_page_title', 'Dashboard');
		$is_team = false;
		$team = false;
		if(isset($args['team_url_id'])) {
			$is_user_in_team = false;
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
			//$f3->set('v_page_title', 'Team: '.$team['name']);
		}
		$team_members = array();
		$team_members_ids = array();
		if($is_team) {
			$team_members_ids = $db->exec(
				'SELECT user_id FROM users_teams WHERE team_id = ?',
				array($team['id'])
			);
		}
		foreach($team_members_ids as $id) {
			array_push(
				$team_members,
				$db->exec('SELECT * FROM users WHERE id = ?', array($id['user_id']))[0]
			);
		}
		$f3->set('v_team_members', $team_members);

		// GET PROJECT AND TASK LISTS
		$projects_and_tasks = Utils::get_project_and_task_lists($is_team, $team, $db, $user);
		$projects = $projects_and_tasks['projects'];
		$tasks = $projects_and_tasks['tasks'];

		// GET CURRENTLY RUNNING LOG IF EXISTS
		if(!$is_team) {
			$db_logs = new \DB\SQL\Mapper($db, 'logs');
			$db_logs->load(array(
				'end_time IS NULL AND user_id = ? AND team_id IS NULL',
				$user->id
			));
		} else {
			$db_logs = new \DB\SQL\Mapper($db, 'logs');
			$db_logs->load(array(
				'end_time IS NULL AND user_id = ? AND team_id = ?',
				$user->id, $team['id']
			));
		}
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
		$extra_conditions .= $dashboard_time_filter . $team_filter;
		$did_set_v_logs = Utils::set_v_logs(
			$f3,
			$user->id,
			$extra_conditions,
			$team_filter ? true : false,
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
				'SELECT * FROM logs WHERE user_id = ? AND team_id IS NULL ORDER BY start_time DESC LIMIT 1',
				array($user->id)
			);
		}
		foreach($projects as $project_idx => $project) {
			if($project['id'] === $last_log[0]['project_id'] && !isset($req['new'])) {
				$projects[$project_idx]['preselect_in_dropdown'] = true;
				$projects[$project_idx]['preselect_in_report_dropdown'] = false;
			}
		}
		foreach($tasks as $task_idx => $task) {
			if($task['id'] === $last_log[0]['task_id'] && !isset($req['new'])) {
				$tasks[$task_idx]['preselect_in_dropdown'] = true;
				$tasks[$task_idx]['preselect_in_report_dropdown'] = false;
			}
		}

		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);
		
		// ADDITIONAL VIEW VARIABLES
		
		// SET SOME TEAM VIEW VARIABLES
		$f3->set('v_is_team', $is_team);
		$f3->set('v_team', null);
		$f3->set('v_show_remove_members', null);
		if($is_team) {
			$f3->set('v_team', $team);
			$f3->set('v_show_remove_members', $user->id === (int)$team['creator']);
		}

		// SET SEARCH LINK, REFRESH LINK, AND START NEW LINK
		if($is_team) {
			$f3->set('v_search_link', $SITE_URL.'/search?team='.$team['id']);
			$f3->set('v_refresh_link', $SITE_URL.'/team/'.$team['id']);
			$f3->set('v_start_new_link', sprintf('%s/team/%s?new', $f3->get('SITE_URL'), $team['id']));
		} else {
			$f3->set('v_search_link', '/search');
			$f3->set('v_refresh_link', '/dashboard');
			$f3->set('v_start_new_link', sprintf('%s/dashboard?new', $f3->get('SITE_URL')));
		}

		// SET VARIABLES FOR NEW PROJECT AND NEW TASK LINKS
		$f3->set('v_new_project_link', sprintf('%s=project', $f3->get('v_start_new_link')));
		$f3->set('v_new_task_link', sprintf('%s=task', $f3->get('v_start_new_link')));

		// RENDER
		$view = new \View;
		if(isset($req['new'])) {
			$f3->set('v_timer_start_new', true);
			if($req['new'] === 'project' || $req['new'] === 'task') {
				$f3->set('v_timer_start_new', $req['new']);
			}
		} else {
			$f3->set('v_timer_start_new', false);
		}
		$f3->set('v_report_date', null);
        echo $view->render('dashboard.php');
	}
}