<?php

class Timer {
	function start_time($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$db_projects = new \DB\SQL\Mapper($db, 'projects');
		$db_users_projects = new \DB\SQL\Mapper($db, 'users_projects');
		$db_tasks = new \DB\SQL\Mapper($db, 'tasks');
		$db_users_tasks = new \DB\SQL\Mapper($db, 'users_tasks');
		$db_logs = new \DB\SQL\Mapper($db, 'logs');

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
		Utils::reroute_with_errors($f3, $args, '/dashboard');

		// DECLARE VARIABLES FOR THE LOG RECORD LATER ON
		$running_timer_project = null;
		$running_timer_task = null;
		$running_timer_notes = null;

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
			$running_timer_project = $db_projects->id;
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
			$running_timer_project = $db_projects->id;
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
			$running_timer_task = $db_tasks->id;
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
		$db_logs->save();

		$f3->reroute('/dashboard');
	}

	function stop_time($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$db = $f3->get('DB');

		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));

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
		Utils::reroute_with_errors($f3, $args, '/dashboard');

		$db_logs->end_time = date("Y-m-d H:i:s");
		$db_logs->save();

		$f3->reroute('/dashboard');
	}
}