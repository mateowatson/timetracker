<?php

class Search {
	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Search');

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);

		$req = $f3->get('REQUEST');
		$search_project = urldecode($req['sp']);
		$search_task = urldecode($req['st']);
		$search_term_project = urldecode($req['stp']);
		$search_term_task = urldecode($req['stt']);
		$search_term_start_date = urldecode($req['stsd']);
		$search_term_end_date = urldecode($req['sted']);
		$search_term_notes = urldecode($req['stn']);
		$team_id = urldecode($req['team']);
		$page = isset($req['page']) ? (int)urldecode($req['page']) : 0;
		$f3->set('v_no_matches', false);

		$f3->set('v_search_project', $search_project);
		$f3->set('v_search_task', $search_task);
		$f3->set('v_search_term_project', $search_term_project);
		$f3->set('v_search_term_task', $search_term_task);
		$f3->set('v_search_term_start_date', $search_term_start_date);
		$f3->set('v_search_term_end_date', $search_term_end_date);
		$f3->set('v_search_term_notes', $search_term_notes);
		$f3->set('v_no_matches', false);

		$is_team = false;
		$team = false;
		if($team_id) {
			$available_teams = $db->exec('SELECT * FROM users_teams WHERE user_id = ?', $user->id);
			$is_user_in_team = false;
			foreach($available_teams as $av_t) {
				if((int)$av_t['user_id'] === (int)$user->id && (int)$av_t['team_id'] === (int)$team_id) {
					$is_user_in_team = true;
					$is_team = true;
					$team = $av_t;
					$f3->set('v_search_team_id', $team_id);
					break;
				}
			}
			if(!$is_user_in_team) {
				return $f3->reroute('/dashboard');
			}
		}

		// GET PROJECT AND TASK LISTS
		$projects_and_tasks = Utils::get_project_and_task_lists($is_team, $team, $db, $user);
		$projects = $projects_and_tasks['projects'];
		$tasks = $projects_and_tasks['tasks'];
		foreach($projects as $project_idx => $project) {
			if($project['id'] === $search_project) {
				$projects[$project_idx]['preselect_in_dropdown'] = true;
			}
		}
		foreach($tasks as $task_idx => $task) {
			if($task['id'] === $search_task) {
				$tasks[$task_idx]['preselect_in_dropdown'] = true;
			}
		}
		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);
		
		$sql_condition = '';
		$sql_offset = 10*($page);
		$continue_search = true;

		if($is_team) {
			$sql_condition .= ' AND logs.team_id = ' . $team_id;
		}
		
		if(
			!$search_project &&
			!$search_task &&
			!$search_term_project &&
			!$search_term_task &&
			!$search_term_start_date &&
			!$search_term_end_date &&
			!$search_term_notes
		) {
			$sql_condition = '';
		}

		if($search_project) {
            $sql_condition .= ' AND project_id = '.$search_project;
        }
        
        if($search_task) {
			$sql_condition .= ' AND task_id = '.$search_task;
		}

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

				$sql_condition .= ' AND project_id IN ('.$project_matches.') ';
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

				$sql_condition .= ' AND task_id IN ('.$task_matches.') ';
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

					$sql_condition .= ' AND logs.id IN ('.$start_date_matches.') ';
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

					$sql_condition .= ' AND logs.id IN ('.$end_date_matches.') ';
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
		}
		
		if($is_team) {
			// GET LOGS COUNT
			$logs_count_query = $db->exec(
				'
					SELECT COUNT(*) as logs_count, team_id
					FROM logs WHERE true '.$sql_condition.'
				'
			);
			$logs_count = $logs_count_query[0]['logs_count'];
			$f3->set('v_logs_count', $logs_count);

			// SET LOGS LIST
			$did_set_v_logs = Utils::set_v_logs(
				$f3,
				$user->id,
				$sql_condition,
				$is_team,
				true,
				$sql_offset
			);


			// SET LOGS TOTAL TIME
			$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
				$f3, $user->id, $sql_condition, $is_team
			);
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
				false,
				true,
				$sql_offset
			);


			// SET LOGS TOTAL TIME
			$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
				$f3, $user->id, $sql_condition, false
			);
		}
		
		if( !count($f3->get('v_logs')) ) {
			$f3->set('v_no_matches', true);
		}
		

		// SET NEXT AND PREV LINKS
		if($page === 0) {
			$prev_link = null;
		} else {
			$prev_link = '/search?stp='.
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
		if($page+1 > $logs_count/10 || $logs_count <= 10) {
			$next_link = null;
		} else {
			$next_link = '/search?stp='.
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
		echo $view->render('search.php');
	}

	function post_search($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$search_project = $req['search_project'];
		$search_task = $req['search_task'];
		$search_term_project = $req['search_term_project'];
		$search_term_task = $req['search_term_task'];
		$search_term_start_date = $req['search_term_start_date'];
		$search_term_end_date = $req['search_term_end_date'];
		$search_term_notes = $req['search_term_notes'];

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);

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
			Utils::prevent_csrf_from_tab_conflict($f3, $args, $referer_url_parts['path']);
		} else {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, '/dashboard');
		}

		if(
			!$search_project &&
			!$search_task &&
			!$search_term_project &&
			!$search_term_task &&
			!$search_term_start_date &&
			!$search_term_end_date &&
			!$search_term_notes
		) {
			$f3->reroute('/search'.($is_team ? '?team='.$team['id'] : ''));
		}

		if($search_term_start_date) {
			$formatted_date_search_arr = Utils::parse_search_by_date_input($search_term_start_date);

			if(!$formatted_date_search_arr && $search_term_start_date) {
				$f3->push('v_errors', array(
					'element_id' => 'search_errors',
					'message' => 'Your date input was not formatted properly. Please use "MM/DD/YYYY" for a single date and "MM/DD/YYYY - MM/DD/YYYY" for a date range.'
				));

				Utils::reroute_with_errors($f3, $args, '/search?stp='.
					urlencode($search_term_project).
					'&stt='.
					urlencode($search_term_task).
					'&st='.
					urlencode($search_task).
					'&sp='.
					urlencode($search_project).
					'&stsd='.
					urlencode($search_term_start_date).
					'&sted='.
					urlencode($search_term_end_date).
					'&stn='.
					urlencode($search_term_notes).
					($is_team ? '&team='.$team['id'] : '')
				);
			}
		}

		$f3->reroute('/search?stp='.
			urlencode($search_term_project).
			'&stt='.
			urlencode($search_term_task).
			'&st='.
			urlencode($search_task).
			'&sp='.
			urlencode($search_project).
			'&stsd='.
			urlencode($search_term_start_date).
			'&sted='.
			urlencode($search_term_end_date).
			'&stn='.
			urlencode($search_term_notes).
			($is_team ? '&team='.$team['id'] : '')
		);
	}
}