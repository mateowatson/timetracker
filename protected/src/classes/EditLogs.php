<?php
class EditLogs {
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

        $log_ids = array();
        $log_ids_query_string = '';
        $log_ids_comma_separated = '';
		if(isset($req['log_ids'])) {
            $log_ids = $req['log_ids'];
            $log_ids_query_string = http_build_query(['log_ids' => $log_ids]);
            $log_ids_comma_separated = implode(',',$log_ids);
        }
        

        $f3->set('v_log_ids', $log_ids);
        $f3->set('v_log_ids_query_string', $log_ids_query_string);
        $f3->set('v_log_ids_comma_separated', $log_ids_comma_separated);

        //todo: validate as array of integers

        $extra_conditions = ' AND logs.id IN ('.$log_ids_comma_separated.')';

        $did_set_v_logs = Utils::set_v_logs(
			$f3,
			$user->id,
			$extra_conditions,
			false,
			false,
			null
		);

		$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
			$f3, $user->id, $extra_conditions, false
		);

        // GET PROJECT AND TASK LISTS
		$projects_and_tasks = Utils::get_project_and_task_lists(false, null, $db, $user);
		$projects = $projects_and_tasks['projects'];
		$tasks = $projects_and_tasks['tasks'];
		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);

        if(isset($req['new'])) {
			$f3->set('v_timer_start_new', true);
			if($req['new'] === 'project' || $req['new'] === 'task') {
				$f3->set('v_timer_start_new', $req['new']);
			}
		} else {
			$f3->set('v_timer_start_new', false);
		}

        $f3->set(
            'v_edit_logs_cancel_link',
            $f3->get('SITE_URL') . '/dashboard'
        );

        $f3->set('v_page_title', 'Bulk Edit Logs');

		// RENDER
		$view = new \View;
        echo $view->render('edit-logs.php');
	}

	
}