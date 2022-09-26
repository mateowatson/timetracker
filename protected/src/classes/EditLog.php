<?php
class EditLog {
	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$req = $f3->get('REQUEST');

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);

		// MISC EDIT LOG VIEW VARS
		if(isset($req['id'])) {
			$f3->set('v_log_id', $req['id']);
		} else {
			$f3->set('v_log_id', false);
		}
		$f3->set('v_log_task_id', false);
		$f3->set('v_log_project_id', false);
		if($f3->get('v_log_id')) {
			$db_logs = new \DB\SQL\Mapper($db, 'logs');
			$log = $db_logs->load(array('id = ?', $f3->get('v_log_id')));
			$log_task_id = $log->get('task_id');
			$log_project_id = $log->get('project_id');
			$log_notes = $log->get('notes');
			$f3->set('v_log_task_id', $log_task_id);
			$f3->set('v_log_project_id', $log_project_id);
			$f3->set('v_log_notes', $log_notes);

			// Parse start and end time
			$log_start_time = Utils::chunk_datetime($log->start_time);
			$log_end_time = Utils::chunk_datetime($log->end_time);
			$f3->set('v_log_start_time', $log_start_time);
			$f3->set('v_log_end_time', $log_end_time);
		} else {
			$f3->reroute('/dashboard');
		}

		if(isset($req['new'])) {
			$f3->set('v_timer_start_new', true);
		} else {
			$f3->set('v_timer_start_new', false);
		}
		
		$f3->set('v_page_title', 'Edit Log');


		// DETERMINE CANCEL LINK
		$is_team = false;
		$team = false;
		$referer_url = $f3->get('HEADERS')['Referer'];
		$referer_url_parts = parse_url($referer_url);
		$referer_path_parts = explode('/', $referer_url_parts['path']);
		parse_str(parse_url($referer_url, PHP_URL_QUERY), $referer_query_parts);
		$referer_team_id = null;
		if(
			(count($referer_path_parts) > 2 && $referer_path_parts[1] === 'team') ||
			(isset($referer_query_parts['team']))
		) {
			$referer_team_id = $referer_path_parts[2] ? : $referer_query_parts['team'];
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
			$f3->set(
				'v_edit_log_cancel_link',
				$f3->get('SITE_URL') . '/team/' . $team['id']
			);
		} else {
			$f3->set(
				'v_edit_log_cancel_link',
				$f3->get('SITE_URL') . '/dashboard'
			);
		}

		// DEFINE BACK BUTTON, EDIT CANCEL BUTTON TOO MAYBE
		if(!empty($req['back_to'])) {
			$f3->set(
				'v_edit_log_cancel_link',
				$req['back_to']
			);
		}

		// GET PROJECT AND TASK LISTS
		$projects_and_tasks = Utils::get_project_and_task_lists($is_team, $team, $db, $user);
		$projects = $projects_and_tasks['projects'];
		$tasks = $projects_and_tasks['tasks'];
		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);

		// RENDER
		$view = new \View;
        echo $view->render('edit-log.php');
	}

	function submit_edit($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$req_new_proj = !empty($req['edit_log_new_project']) ? $req['edit_log_new_project'] : null;
		$req_proj = $req['edit_log_project'];
		$req_new_task = !empty($req['edit_log_new_task']) ? $req['edit_log_new_task'] : null;
		$req_task = $req['edit_log_task'];
		$req_notes = $req['edit_log_notes'];
		$req_log_id = $req['edit_log_log_id'];
		$req_log_start_time = Utils::datetime_chunks_to_sql(array(
			$req['edit_log_start_yyyy'],
			$req['edit_log_start_mm'],
			$req['edit_log_start_dd'],
			$req['edit_log_start_hour'],
			$req['edit_log_start_min'],
			$req['edit_log_start_sec'],
		));
		$req_log_end_time = Utils::datetime_chunks_to_sql(array(
			$req['edit_log_end_yyyy'],
			$req['edit_log_end_mm'],
			$req['edit_log_end_dd'],
			$req['edit_log_end_hour'],
			$req['edit_log_end_min'],
			$req['edit_log_end_sec'],
		));

		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$db_projects = new \DB\SQL\Mapper($db, 'projects');
		$db_users_projects = new \DB\SQL\Mapper($db, 'users_projects');
		$db_tasks = new \DB\SQL\Mapper($db, 'tasks');
		$db_users_tasks = new \DB\SQL\Mapper($db, 'users_tasks');
		$db_logs = new \DB\SQL\Mapper($db, 'logs');
		$db_log = $db_logs->load(array('id=?', $req_log_id));

		// CHECK ERRORS
		// Validate the required fields were filled in
		if((!$req_new_proj && !$req_proj)||(!$req_new_task && !$req_task)) {
			$f3->push('v_errors', array(
				'element_id' => 'edit_log_bottom_errors',
				'message' => 'Project and Task fields are required.'
			));
		}
		// Validate the log already exists
		if($db_log->dry()) {
			$f3->push('v_errors', array(
				'element_id' => 'edit_log_bottom_errors',
				'message' => 'Could not find log to update.'
			));
		}
		// Validate the user owns the log
		if($db_log->user_id !== $user->id) {
			$f3->push('v_errors', array(
				'element_id' => 'edit_log_bottom_errors',
				'message' => 'You do not have permissions to update this log.'
			));
		}
		// Validate datetimes submitted
		if($req_log_start_time === false) {
			$f3->push('v_errors', array(
				'element_id' => 'edit_log_bottom_errors',
				'message' => 'Invalid date and times submitted.'
			));
		}

		// Redirect to page if need be, along with errors
		Utils::reroute_with_errors($f3, $args, '/edit-log?id=' . $db_log->id);
		
		// DECLARE VARIABLES FOR THE LOG RECORD LATER ON
		$return_edit_log_project = null;
		$return_edit_log_task = null;
		$return_edit_log_notes = null;

		// HANDLE PROJECT
		// Add new project to db
		if($req_new_proj) {
			$db_projects->load(array('name=?', $req_new_proj));
			// Make sure is new project before adding to projects
			if($db_projects->dry()) {
				$db_projects->name = $req_new_proj;
				$db_projects->save();
			}
			// Add the new record to users_projects
			$db_users_projects->user_id = $user->id;
			$db_users_projects->project_id = $db_projects->id;
			$db_users_projects->save();
			// Assign id to variable to keep track of this for making the log
			$return_edit_log_project = $db_projects->id;
		// OR find existing project in db
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
			// Assign id to variable to keep track of this for making the log
			$return_edit_log_project = $db_projects->id;
		}

		// HANDLE TASK
		// Add new task to db
		if($req_new_task) {
			$db_tasks->load(array('name=?', $req_new_task));
			// Make sure is new task before adding to tasks
			if($db_tasks->dry()) {
				$db_tasks->name = $req_new_task;
				$db_tasks->save();
			}
			// Add the new record to users_tasks
			$db_users_tasks->user_id = $user->id;
			$db_users_tasks->task_id = $db_tasks->id;
			$db_users_tasks->save();
			// Assign id to variable to keep track of this for making the log
			$return_edit_log_task = $db_tasks->id;
		// OR find existing task in db
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
			// Assign id to variable to keep track of this for making the log
			$return_edit_log_task = $db_tasks->id;
		}

		// HANDLE NOTES
		if($req_notes) {
			$return_edit_log_notes = $req_notes;
		}

		// EDIT LOG
		$db_log->project_id = $return_edit_log_project;
		$db_log->task_id = $return_edit_log_task;
		$db_log->notes = $return_edit_log_notes;
		$db_log->start_time = $req_log_start_time;
		$db_log->end_time = $req_log_end_time ? : null;
		$db_log->save();

		$f3->push('v_confirmations', array(
			'element_id' => 'edit_log_bottom_confirmations',
			'message' => 'Log updated!'
		));

		if(!empty($req['back_to'])) {
			Utils::reroute_with_confirmations($f3, $args, '/edit-log?id=' . $db_log->id . '&back_to=' . urlencode($req['back_to']));
		}
		Utils::reroute_with_confirmations($f3, $args, '/edit-log?id=' . $db_log->id);
	}
}