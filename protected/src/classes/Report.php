<?php

class Report {
    function show($f3, $args) {
        Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);
		$SITE_URL = $f3->get('SITE_URL');

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);

		$req = $f3->get('REQUEST');
		$report_project = urldecode(isset($req['rp']) ? $req['rp'] : '');
		$report_task = urldecode(isset($req['rt']) ? $req['rt'] : '');
		$report_date = urldecode(isset($req['rd']) ? $req['rd'] : '');
		// Set variable so we can autopopulate in the view
		$f3->set('v_report_date', $report_date);
		$report_team_member = urldecode(isset($req['rtm']) ? $req['rtm'] : '');
		$team_id = urldecode(isset($req['team']) ? $req['team'] : '');
		$page = isset($req['page']) ? (int)urldecode($req['page']) : 0;
		$f3->set('v_no_matches', false);

		$is_team = false;
		$team = false;
		$v_team = null;
		if($team_id) {
			$available_teams = $db->exec('SELECT * FROM users_teams WHERE user_id = ?', $user->id);
			$is_user_in_team = false;
			foreach($available_teams as $av_t) {
				if((int)$av_t['user_id'] === (int)$user->id && (int)$av_t['team_id'] === (int)$team_id) {
					$is_user_in_team = true;
                    $is_team = true;
					$team = $av_t;
					$v_team = $db->exec('SELECT * FROM teams WHERE id = ?', $av_t['team_id'])[0];
					$f3->set('v_search_team_id', $team_id);
					break;
				}
			}
			if(!$is_user_in_team) {
				return $f3->reroute('/dashboard');
			}
		}
		
		// Set team members variable
		$team_members = array();
		$team_members_ids = array();
		if(isset($v_team['id'])) {
			$team_members_ids = $db->exec(
				'SELECT user_id FROM users_teams WHERE team_id = ?',
				array($v_team['id'])
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
		$projects_and_tasks = Utils::get_project_and_task_lists($is_team, $v_team, $db, $user);
		$projects = $projects_and_tasks['projects'];
		$tasks = $projects_and_tasks['tasks'];
		foreach($projects as $project_idx => $project) {
			if((int)$project['id'] === (int)$report_project) {
				$projects[$project_idx]['preselect_in_report_dropdown'] = true;
			}
		}
		foreach($tasks as $task_idx => $task) {
			if((int)$task['id'] === (int)$report_task) {
				$tasks[$task_idx]['preselect_in_report_dropdown'] = true;
			}
		}
		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);
		
		$sql_condition = '';
		$sql_offset = 10*($page);

		if($report_project) {
            $sql_condition = ' AND project_id = '.(int)$report_project;
        }
        
        if($report_task) {
			$sql_condition .= ' AND task_id = '.(int)$report_task;
		}

		if($report_date) {
			$formatted_date_search_arr = Utils::parse_search_by_date_input($report_date);

			if(!$formatted_date_search_arr && $report_date) {
				$f3->push('v_errors', array(
					'element_id' => 'report_errors',
					'message' => 'Your date input was not formatted properly. Please use "mm/dd/yyy" for a single date and "mm/dd/yyy - mm/dd/yyy" for a date range.'
				));

				//Utils::reroute_with_errors($f3, $args, '/search?search_by=date');

				if($is_team) {
					Utils::reroute_with_errors($f3, $args, '/report?&team='.$team_id);
				} else {
					Utils::reroute_with_errors($f3, $args, '/report');
				}
			}
			// VALIDATE DATE FIELDS
			if($formatted_date_search_arr !== FALSE) {
				$logs_matches_query = $db->exec('
					SELECT
						id
					FROM logs
					WHERE start_time BETWEEN ? AND ?
				', array(
					$formatted_date_search_arr[0],
					$formatted_date_search_arr[1]
				));
				if(count($logs_matches_query) > 0) {
					$logs_matches_array = array();
					foreach ($logs_matches_query as $logs_match) {
						array_push($logs_matches_array, $logs_match['id']);
					}
					$logs_matches = implode(', ', $logs_matches_array);

					$sql_condition .= (($sql_condition ? ' ' : '').'AND logs.id IN ('.$logs_matches.')');
				} else {
					$f3->set('v_no_matches', true);
				}
			}
		}

		if($is_team) {
			$sql_condition .= ' AND logs.team_id = ' . (int)$team_id;
		}

		if($is_team && $report_team_member) {
			$sql_condition .= ' AND logs.user_id = ' . (int)$report_team_member;
			$f3->set('v_team_member_id', $report_team_member);
		}

		// SET NO MATCHES TO TRUE IF NO MATCHES FOUND
		/* if(!$sql_condition) {
			$f3->set('v_no_matches', true);
		} */

		// GET LOGS COUNT
		if($is_team) {
			$logs_count_query = $db->exec(
				'
					SELECT COUNT(*) as logs_count, team_id
					FROM logs WHERE true '.$sql_condition.'
				'
			);
			$logs_count = $logs_count_query[0]['logs_count'];
		} else {
			$logs_count_query = $db->exec(
				'
					SELECT COUNT(*) as logs_count, team_id
					FROM logs WHERE user_id = ? '.$sql_condition.'
				',
				array(
					$user->id
				)
			);
			$logs_count = $logs_count_query[0]['logs_count'];
		}
		$f3->set('v_logs_count', $logs_count);
		
		if(!$logs_count) {
			$f3->set('v_no_matches', true);
		}

		// SET LOGS LIST
		if($logs_count) {
			$did_set_v_logs = Utils::set_v_logs(
				$f3,
				$user->id,
				$sql_condition,
				$is_team,
				true,
				$sql_offset
			);
		}

		// SET LOGS TOTAL TIME
		if($logs_count) {
			$did_set_v_logs_total_time = Utils::set_v_logs_total_time(
				$f3, $user->id, $sql_condition, $is_team
			);
		}

		// SET NEXT AND PREV LINKS
		if($page === 0) {
			$prev_link = null;
		} else {
			$prev_link = "/report?rp=".$report_project."&rt=".
				$report_task."&rd=".$report_date."&page=".(string)($page-1);
			if($is_team) {
				$prev_link .= '&team='.$team_id;
			}
		}

		if(($page+1)*10 >= $logs_count || $logs_count <= 10) {
			$next_link = null;
		} else {
			$next_link = "/report?rp=".$report_project."&rt=".
				$report_task."&rd=".$report_date."&page=".(string)($page+1);
			if($is_team) {
				$next_link .= '&team='.$team_id;
			}
		}
		$f3->set('v_next_link', $next_link);
		$f3->set('v_prev_link', $prev_link);
		$f3->set('v_curr_page', $page+1);
		$f3->set('v_num_pages', ceil($logs_count/10));

		// SET SEARCH LINK
		if($is_team) {
			$f3->set('v_search_link', $SITE_URL.'/search?team='.$v_team['id']);
		} else {
			$f3->set('v_search_link', '/search');
		}

		
		// ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Report');

		$teams = Utils::get_all_teams_of_logged_in_user($f3);
		$f3->set('v_teams', $teams);
		$f3->set('v_team', $v_team);
		$f3->set('v_is_team', $is_team);
		$f3->set('v_report_show_teams_dropdown', true);

		// RENDER
		$view = new \View;
		echo $view->render('report.php');
    }

    function generate($f3, $args) {
        Utils::redirect_logged_out_user($f3, $args);
        Utils::prevent_csrf($f3, $args);
        
        $req = $f3->get('REQUEST');
		$report_project = isset($req['rp']) ? $req['rp'] : null;
		$report_task = isset($req['rt']) ? $req['rt'] : null;
		$report_date = isset($req['rd']) ? $req['rd'] : null;
		$report_team = isset($req['team']) ? $req['team'] : null;
		$report_team_member = isset($req['rtm']) ? $req['rtm'] : null;
		$report_noteam = $report_team === 'noteam';
		$report_changeteam = isset($req['change-team']) ? $req['change-team'] : null;

		// GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
        $f3->set('v_username', $session_username);
        
        $is_team = false;
		$team = false;
		if($report_team && !$report_noteam && $report_changeteam) {
			$is_team = true;
			$team = $db->exec('SELECT * FROM teams WHERE id = ?', (int)$report_team)[0];
		} else if(!$report_noteam) {
			$referer_url = $f3->get('HEADERS')['Referer'];
			$referer_url_parts = parse_url($referer_url);
			$referer_path_parts = explode('/', $referer_url_parts['path']);
			parse_str(parse_url($referer_url, PHP_URL_QUERY) ?: '', $referer_query_parts);
			$referer_team_id = null;
			if(
				(count($referer_path_parts) > 2 && $referer_path_parts[1] === 'team') ||
				(isset($referer_query_parts['team']))
			) {
				$referer_team_id = !empty($referer_path_parts[2]) ? $referer_path_parts[2] : $referer_query_parts['team'];
				$is_team = true;
				$team = $db->exec('SELECT * FROM teams WHERE id = ?', (int)$referer_team_id)[0];
			}
		}

		if(!$report_changeteam) {
			$referer_url = $f3->get('HEADERS')['Referer'];
			$referer_url_parts = parse_url($referer_url);
			if(isset($referer_url_parts['query']))
				parse_str($referer_url_parts['query'], $referer_query);
			if(isset($referer_query['team'])) {
				$referer_team_id = $referer_query['team'];
				$is_team = true;
				$team = $db->exec('SELECT * FROM teams WHERE id = ?', (int)$referer_team_id)[0];
			}
		} else {
			// If we ARE changing teams, reset other report parameters to nothing
			$report_project = '';
			$report_task = '';
			$report_date = '';
			$report_team_member = '';
		}
        
        if($is_team && !$report_changeteam) {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, $referer_url_parts['path']);
		} else {
			Utils::prevent_csrf_from_tab_conflict($f3, $args, '/report');
		}

        $f3->reroute(
			'/report?rp='.
			urlencode($report_project).
			'&rt='.
			urlencode($report_task).
			'&rd='.
			urlencode($report_date).
			($is_team ? '&team='.urlencode($team['id']) : '').
			($report_team_member ? '&rtm='.urlencode($report_team_member) : '')
		);
    }
}