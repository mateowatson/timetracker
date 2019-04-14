<?php

class Search {
	function post_search($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$search_term = $req['search_term'];
		$search_by = $req['search_by'];

		$f3->reroute('/search?search_term='.
			urlencode($search_term).
			'&search_by='.
			urlencode($search_by));
	}

	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);

		$req = $f3->get('REQUEST');
		$search_term = urldecode($req['search_term']);
		$search_by = urldecode($req['search_by']);
		$page = isset($req['page']) ? (int)urldecode($req['page']) : 0;
		$f3->set('v_no_matches', false);
		
		$sql_condition = '';
		$sql_offset = 10*($page);

		if($search_by === 'project') {
			$project_matches_query = $db->exec('
				SELECT
					id
				FROM projects
				WHERE name LIKE ?
			', array('%'.$search_term.'%'));
			if(count($project_matches_query) > 0) {
				$project_matches_array = array();
				foreach ($project_matches_query as $project_match) {
					array_push($project_matches_array, $project_match['id']);
				}
				$project_matches = implode(', ', $project_matches_array);

				$sql_condition = 'AND project_id IN ('.$project_matches.')';
			}
		} else if($search_by === 'task') {
			$task_matches_query = $db->exec('
				SELECT
					id
				FROM tasks
				WHERE name LIKE ?
			', array('%'.$search_term.'%'));
			if(count($task_matches_query) > 0) {
				$task_matches_array = array();
				foreach ($task_matches_query as $task_match) {
					array_push($task_matches_array, $task_match['id']);
				}
				$task_matches = implode(', ', $task_matches_array);

				$sql_condition = 'AND task_id IN ('.$task_matches.')';
			}
		}

		// SET NO MATCHES TO TRUE IF NO MATCHES FOUND
		if(!$sql_condition) {
			$f3->set('v_no_matches', true);
		}

		// GET LOGS COUNT
		$logs_count_query = $db->exec(
			'
				SELECT COUNT(*) as logs_count
				FROM logs WHERE user_id = ? '.$sql_condition.'
			',
			array(
				$user->id
			)
		);
		$logs_count = $logs_count_query[0]['logs_count'];
		$f3->set('v_logs_count', $logs_count);
		
		$did_set_v_logs = Utils::set_v_logs(
			$f3,
			$user->id,
			$sql_condition,
			true,
			$sql_offset
		);


		// GET LOGS TOTAL TIME
		$logs_total_time = $db->exec('
			SELECT
				SUM(
					IF(logs.end_time != "0000-00-00 00:00:00",
						TIMESTAMPDIFF(SECOND, logs.start_time, logs.end_time),
						TIMESTAMPDIFF(SECOND, logs.start_time, NOW())
					)
				) as total_time
			FROM logs
			WHERE user_id = ? '.$sql_condition.'
			ORDER BY start_time DESC
		', array($user->id));
		
		$f3->set(
			'v_logs_total_time',
			Utils::timediff_from_seconds($logs_total_time[0]['total_time'])
		);

		// SET NEXT AND PREV LINKS
		if($page === 0) {
			$prev_link = null;
		} else {
			$prev_link = "/search?search_term=".$req['search_term']."&search_by=".
				$req['search_by']."&page=".(string)($page-1);
		}
		if($page+1 > $logs_count/10) {
			$next_link = null;
		} else {
			$next_link = "/search?search_term=".$req['search_term']."&search_by=".
				$req['search_by']."&page=".(string)($page+1);
		}
		$f3->set('v_next_link', $next_link);
		$f3->set('v_prev_link', $prev_link);
		$f3->set('v_curr_page', $page+1);
		$f3->set('v_num_pages', ceil($logs_count/10));

		
		// ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Search');

		// RENDER
		$view = new \View;
		echo $view->render('search.php');
	}
}