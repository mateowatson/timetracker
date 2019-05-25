<?php

class AdvancedSearch {
	public function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Advanced Search');

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);

		$req = $f3->get('REQUEST');
		$search_term_project = urldecode($req['stp']);
		$search_term_task = urldecode($req['stt']);
		$search_term_start_date = urldecode($req['stsd']);
		$search_term_end_date = urldecode($req['sted']);
		$search_term_notes = urldecode($req['stn']);
		$page = isset($req['page']) ? (int)urldecode($req['page']) : 0;
		$f3->set('v_no_matches', false);

		$f3->set('v_search_term_project', $search_term_project);
		$f3->set('v_search_term_task', $search_term_task);
		$f3->set('v_search_term_start_date', $search_term_start_date);
		$f3->set('v_search_term_end_date', $search_term_end_date);
		$f3->set('v_search_term_notes', $search_term_notes);
		$f3->set('v_no_matches', false);
		
		$sql_condition = '';
		$sql_offset = 10*($page);
		$continue_search = true;

		if($search_term_project) {
			$project_matches_query = $db->exec('
				SELECT
					id
				FROM projects
				WHERE name LIKE ?
			', array('%'.$search_term_project.'%'));
			if(count($project_matches_query) > 0) {
				$project_matches_array = array();
				foreach ($project_matches_query as $project_match) {
					array_push($project_matches_array, $project_match['id']);
				}
				$project_matches = implode(', ', $project_matches_array);

				$sql_condition .= 'AND project_id IN ('.$project_matches.') ';
			} else {
				$continue_search = false;
			}
		}

		if($search_term_task && $continue_search) {
			$task_matches_query = $db->exec('
				SELECT
					id
				FROM tasks
				WHERE name LIKE ?
			', array('%'.$search_term_task.'%'));
			if(count($task_matches_query) > 0) {
				$task_matches_array = array();
				foreach ($task_matches_query as $task_match) {
					array_push($task_matches_array, $task_match['id']);
				}
				$task_matches = implode(', ', $task_matches_array);

				$sql_condition .= 'AND task_id IN ('.$task_matches.') ';
			} else {
				$continue_search = false;
			}
		}

		if($search_term_start_date && $continue_search) {
			$formatted_date_search_arr = Utils::parse_search_by_date_input($search_term_start_date);

			// VALIDATE DATE FIELDS
			if($formatted_date_search_arr !== FALSE) {
				$start_date_matches_query = $db->exec('
					SELECT
						id
					FROM logs
					WHERE start_time BETWEEN ? AND ?
				', array(
					$formatted_date_search_arr[0],
					$formatted_date_search_arr[1]
				));
				if(count($start_date_matches_query) > 0) {
					$start_date_matches_array = array();
					foreach ($start_date_matches_query as $start_date_match) {
						array_push($start_date_matches_array, $start_date_match['id']);
					}
					$start_date_matches = implode(', ', $start_date_matches_array);

					$sql_condition = 'AND logs.id IN ('.$start_date_matches.') ';
				} else {
					$continue_search = false;
				}
			}
		}

		if($search_term_end_date && $continue_search) {
			$formatted_date_search_arr = Utils::parse_search_by_date_input($search_term_end_date);

			// VALIDATE DATE FIELDS
			if($formatted_date_search_arr !== FALSE) {
				$end_date_matches_query = $db->exec('
					SELECT
						id
					FROM logs
					WHERE end_time BETWEEN ? AND ?
				', array(
					$formatted_date_search_arr[0],
					$formatted_date_search_arr[1]
				));
				if(count($end_date_matches_query) > 0) {
					$end_date_matches_array = array();
					foreach ($end_date_matches_query as $end_date_match) {
						array_push($end_date_matches_array, $end_date_match['id']);
					}
					$end_date_matches = implode(', ', $end_date_matches_array);

					$sql_condition = 'AND logs.id IN ('.$end_date_matches.') ';
				} else {
					$continue_search = false;
				}
			}
		}

		if($search_term_notes && $continue_search) {
			$notes_matches_query = $db->exec('
				SELECT
					id
				FROM logs
				WHERE notes LIKE ?
			', array('%'.$search_term_notes.'%'));
			if(count($notes_matches_query) > 0) {
				$notes_matches_array = array();
				foreach ($notes_matches_query as $notes_match) {
					array_push($notes_matches_array, $notes_match['id']);
				}
				$notes_matches = implode(', ', $notes_matches_array);

				$sql_condition .= 'AND logs.id IN ('.$notes_matches.') ';
			} else {
				$continue_search = false;
			}
		}

		// SET NO MATCHES TO TRUE IF NO MATCHES FOUND
		if(!$sql_condition || !$continue_search) {
			$f3->set('v_no_matches', true);
		} else {

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

			// SET LOGS LIST
			$did_set_v_logs = Utils::set_v_logs(
				$f3,
				$user->id,
				$sql_condition,
				true,
				$sql_offset
			);


			// SET LOGS TOTAL TIME
			$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
				$f3, $user->id, $sql_condition
			);
		}

		// SET NEXT AND PREV LINKS
		if($page === 0) {
			$prev_link = null;
		} else {
			$prev_link = '/advanced-search?stp='.
			urlencode($search_term_project).
			'&stt='.
			urlencode($search_term_task).
			'&stsd='.
			urlencode($search_term_start_date).
			'&sted='.
			urlencode($search_term_end_date).
			'&stn='.
			urlencode($search_term_notes).
			'&page='.(string)($page-1);
		}
		if($page+1 > $logs_count/10) {
			$next_link = null;
		} else {
			$next_link = '/advanced-search?stp='.
			urlencode($search_term_project).
			'&stt='.
			urlencode($search_term_task).
			'&stsd='.
			urlencode($search_term_start_date).
			'&sted='.
			urlencode($search_term_end_date).
			'&stn='.
			urlencode($search_term_notes).
			'&page='.(string)($page+1);
		}
		$f3->set('v_next_link', $next_link);
		$f3->set('v_prev_link', $prev_link);
		$f3->set('v_curr_page', $page+1);
		$f3->set('v_num_pages', ceil($logs_count/10));

		// RENDER
		$view = new \View;
		echo $view->render('advanced-search.php');
	}

	function post_search($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$search_term_project = $req['search_term_project'];
		$search_term_task = $req['search_term_task'];
		$search_term_start_date = $req['search_term_start_date'];
		$search_term_end_date = $req['search_term_end_date'];
		$search_term_notes = $req['search_term_notes'];

		if($search_term_start_date) {
			$formatted_date_search_arr = Utils::parse_search_by_date_input($search_term_start_date);

			if(!$formatted_date_search_arr && $search_term_start_date) {
				$f3->push('v_errors', array(
					'element_id' => 'advanced_search_errors',
					'message' => 'Your date input was not formatted properly. Please use "MM/DD/YYYY" for a single date and "MM/DD/YYYY - MM/DD/YYYY" for a date range.'
				));

				Utils::reroute_with_errors($f3, $args, '/advanced-search?stp='.
					urlencode($search_term_project).
					'&stt='.
					urlencode($search_term_task).
					'&stsd='.
					urlencode($search_term_start_date).
					'&sted='.
					urlencode($search_term_end_date).
					'&stn='.
					urlencode($search_term_notes)
				);
			}
		}

		$f3->reroute('/advanced-search?stp='.
			urlencode($search_term_project).
			'&stt='.
			urlencode($search_term_task).
			'&stsd='.
			urlencode($search_term_start_date).
			'&sted='.
			urlencode($search_term_end_date).
			'&stn='.
			urlencode($search_term_notes));
	}
}