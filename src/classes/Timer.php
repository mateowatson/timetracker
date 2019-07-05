<?php

class Timer {
	function start_time($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);

		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$db_projects = new \DB\SQL\Mapper($db, 'projects');
		$db_users_projects = new \DB\SQL\Mapper($db, 'users_projects');
		$db_tasks = new \DB\SQL\Mapper($db, 'tasks');
		$db_users_tasks = new \DB\SQL\Mapper($db, 'users_tasks');
		$db_logs = new \DB\SQL\Mapper($db, 'logs');

		$is_team = false;
		$team = false;
		$referer_url = $f3->get('HEADERS')['Referer'];
		$referer_url_parts = parse_url($referer_url);
		$referer_path_parts = explode('/', $referer_url_parts['path']);
		$referer_team_id = null;
		if(count($referer_path_parts) > 2 && $referer_path_parts[1] === 'team') {
			$referer_team_id = $referer_path_parts[2];
			$available_teams = $db->exec('SELECT * FROM users_teams WHERE user_id = ?', $user->id);
			$is_user_in_team = false;
			foreach($available_teams as $av_t) {
				if((int)$av_t['user_id'] === (int)$user->id && (int)$av_t['team_id'] === (int)$referer_team_id) {
					$is_user_in_team = true;
					$is_team = true;
					$team = $db->exec('SELECT * FROM teams WHERE id = ?', $av_t['team_id'])[0];
					break;
				}
			}
			if(!$is_user_in_team) {
				return $f3->reroute('/dashboard');
			}
		}

		if($is_team) {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, $referer_url_parts['path']);
		} else {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, '/dashboard');
		}

		

		$req = $f3->get('REQUEST');
		$req_new_proj = $req['start_time_new_project'];
		$req_proj = $req['start_time_project'];
		$req_new_task = $req['start_time_new_task'];
		$req_task = $req['start_time_task'];
		$req_notes = $req['start_time_notes'];

		// CHECK ERRORS
		// Validate the required fields were filled in
		if((!$req_new_proj && !$req_proj)||(!$req_new_task && !$req_task)) {
			$f3->push('v_errors', array(
				'element_id' => 'start_timer_bottom_errors',
				'message' => 'Project and Task fields are required'
			));
		}

		// Only proceed if the previous log entry has end_time
		$nonfinished_logs = $db->exec('
			SELECT end_time from logs
			WHERE end_time IS NULL
			AND user_id = ?
		', array($user->id));
		if(count($nonfinished_logs) > 0) {
			$f3->push('v_errors', array(
				'element_id' => 'start_timer_bottom_errors',
				'message' => 'You must the stop previous log before starting a new one.'
			));
		}

		// Finally redirect to page if need be, along with errors
		if($is_team) {
			Utils::reroute_with_errors($f3, $args, $referer_url_parts['path']);
		} else {
			Utils::reroute_with_errors($f3, $args, '/dashboard');
		}

		// DECLARE VARIABLES FOR THE LOG RECORD LATER ON
		$running_timer_project = null;
		$running_timer_task = null;
		$running_timer_notes = null;

		// HANDLE PROJECT
		if($req_new_proj) {
			$db_projects->load(array('name=?', $req_new_proj));
			$proj_already_exists = !$db_projects->dry();
			if($proj_already_exists) {
				$userProjectsQuery = $db->exec('
					SELECT * FROM projects LEFT JOIN users_projects ON
					(users_projects.user_id = ? AND
					users_projects.project_id = projects.id) WHERE
					users_projects.user_id IS NOT NULL
				', array($user->id));
				$userProjects = array();
				foreach($userProjectsQuery as $query) {
					array_push($userProjects, $query['project_id']);
				}
				if(!in_array($db_projects->id, $userProjects)) {
					$db_projects->reset();
					$db_projects->name = $req_new_proj;
					$db_projects->save();
					$db_users_projects->user_id = $user->id;
					$db_users_projects->project_id = $db_projects->id;
					$db_users_projects->save();
				}
			} else if(!$proj_already_exists) {
				$db_projects->name = $req_new_proj;
				$db_projects->save();
				$db_users_projects->user_id = $user->id;
				$db_users_projects->project_id = $db_projects->id;
				$db_users_projects->save();
			}
			$running_timer_project = $db_projects->id;
		} else if($req_proj) {
			$db_projects->load(array('id=?', $req_proj));
			// Just in case it is not in db somehow
			if($db_projects->dry()) {
				$db_projects->name = $req_proj;
				$db_projects->save();
				$db_users_projects->user_id = $user->id;
				$db_users_projects->project_id = $db_projects->id;
				$db_users_projects->save();
			}
			$running_timer_project = $db_projects->id;
		}

		// HANDLE TASK
		if($req_new_task) {
			$db_tasks->load(array('name=?', $req_new_task));
			$task_already_exists = !$db_tasks->dry();
			if($task_already_exists) {
				$userTasksQuery = $db->exec('
					SELECT * FROM tasks LEFT JOIN users_tasks ON
					(users_tasks.user_id = ? AND
					users_tasks.task_id = tasks.id) WHERE
					users_tasks.user_id IS NOT NULL
				', array($user->id));
				$userTasks = array();
				foreach($userTasksQuery as $query) {
					array_push($userTasks, $query['task_id']);
				}
				if(!in_array($db_tasks->id, $userTasks)) {
					$db_tasks->reset();
					$db_tasks->name = $req_new_task;
					$db_tasks->save();
					$db_users_tasks->user_id = $user->id;
					$db_users_tasks->task_id = $db_tasks->id;
					$db_users_tasks->save();
				}
			} else if(!$task_already_exists) {
				$db_tasks->name = $req_new_task;
				$db_tasks->save();
				$db_users_tasks->user_id = $user->id;
				$db_users_tasks->task_id = $db_tasks->id;
				$db_users_tasks->save();
			}
			$running_timer_task = $db_tasks->id;
		} else if($req_task) {
			$db_tasks->load(array('id=?', $req_task));
			// Just in case it is new somehow
			if($db_tasks->dry()) {
				$db_tasks->name = $req_task;
				$db_tasks->save();
				$db_users_tasks->user_id = $user->id;
				$db_users_tasks->task_id = $db_tasks->id;
				$db_users_tasks->save();
			}
			$running_timer_task = $db_tasks->id;
		}

		// HANDLE NOTES
		if($req_notes) {
			$running_timer_notes = $req_notes;
		}

		// INSERT NEW LOG TO DB
		$db_logs->user_id = $user->id;
		$db_logs->project_id = $running_timer_project;
		$db_logs->task_id = $running_timer_task;
		$db_logs->start_time = date("Y-m-d H:i:s");
		$db_logs->end_time = null;
		$db_logs->notes = $running_timer_notes;
		if($is_team) {
			$db_logs->team_id = $team['id'];
		}
		$db_logs->save();
		
		if($is_team) {
			$f3->reroute($referer_url_parts['path']);
		} else {
			$f3->reroute('/dashboard');
		}
	}

	function stop_time($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);

		$db = $f3->get('DB');

		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));

		$is_team = false;
		$team = false;
		$referer_url = $f3->get('HEADERS')['Referer'];
		$referer_url_parts = parse_url($referer_url);
		$referer_path_parts = explode('/', $referer_url_parts['path']);
		$referer_team_id = null;
		if(count($referer_path_parts) > 2 && $referer_path_parts[1] === 'team') {
			$referer_team_id = $referer_path_parts[2];
			$available_teams = $db->exec('SELECT * FROM users_teams WHERE user_id = ?', $user->id);
			$is_user_in_team = false;
			foreach($available_teams as $av_t) {
				if((int)$av_t['user_id'] === (int)$user->id && (int)$av_t['team_id'] === (int)$referer_team_id) {
					$is_user_in_team = true;
					$is_team = true;
					$team = $db->exec('SELECT * FROM teams WHERE id = ?', $av_t['team_id'])[0];
					break;
				}
			}
			if(!$is_user_in_team) {
				return $f3->reroute('/dashboard');
			}
		}

		if($is_team) {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, $referer_url_parts['path']);
		} else {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, '/dashboard');
		}

		$db_logs = new \DB\SQL\Mapper($db, 'logs');

		$db_logs->load(array(
			'end_time="0000-00-00 00:00:00" AND user_id = ?',
			$user->id
		));
		
		if($db_logs->dry()) {
			$f3->push('v_errors', array(
				'element_id' => 'stop_timer_bottom_errors',
				'message' => 'All logs are already stopped.'
			));
		}
		if($is_team) {
			Utils::reroute_with_errors($f3, $args, $referer_url_parts['path']);
		} else {
			Utils::reroute_with_errors($f3, $args, '/dashboard');
		}

		$db_logs->end_time = date("Y-m-d H:i:s");
		$db_logs->save();

		if($is_team) {
			$f3->reroute($referer_url_parts['path']);
		} else {
			$f3->reroute('/dashboard');
		}
	}
}
