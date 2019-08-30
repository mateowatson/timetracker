<?php

class Report {
    function show($f3, $args) {
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

		$req = $f3->get('REQUEST');
		$report_project = urldecode($req['rp']);
		$report_task = urldecode($req['rt']);
		$team_id = urldecode($req['team']);
		$page = isset($req['page']) ? (int)urldecode($req['page']) : 0;
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
			if($project['id'] === $report_project) {
				$projects[$project_idx]['preselect_in_dropdown'] = true;
			}
		}
		foreach($tasks as $task_idx => $task) {
			if($task['id'] === $report_task) {
				$tasks[$task_idx]['preselect_in_dropdown'] = true;
			}
		}
		$f3->set('v_projects', $projects);
		$f3->set('v_tasks', $tasks);
		
		$sql_condition = '';
		$sql_offset = 10*($page);

		if($report_project) {
            $sql_condition = 'AND project_id = '.$report_project;
        }
        
        if($report_task) {
			$sql_condition = 'AND task_id = '.$report_task;
		}

		if($is_team && $sql_condition) {
			$sql_condition .= ' AND logs.team_id = ' . $team_id;
		}

		// SET NO MATCHES TO TRUE IF NO MATCHES FOUND
		if(!$sql_condition) {
			$f3->set('v_no_matches', true);
		}

		// GET LOGS COUNT
		if($is_team && $sql_condition) {
			$logs_count_query = $db->exec(
				'
					SELECT COUNT(*) as logs_count, team_id
					FROM logs WHERE true '.$sql_condition.'
				'
			);
			$logs_count = $logs_count_query[0]['logs_count'];
		} else if($sql_condition) {
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
		if($sql_condition) {
			$f3->set('v_logs_count', $logs_count);
		} else {
			$f3->set('v_logs_count', 0);
		}
		
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
				$report_task."&page=".(string)($page-1);
		}

		if($page+1 > $logs_count/10 || $logs_count <= 10) {
			$next_link = null;
		} else {
			$next_link = "/report?rp=".$report_project."&rt=".
				$report_task."&page=".(string)($page+1);
		}
		$f3->set('v_next_link', $next_link);
		$f3->set('v_prev_link', $prev_link);
		$f3->set('v_curr_page', $page+1);
		$f3->set('v_num_pages', ceil($logs_count/10));

		
		// ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Report');

		// RENDER
		$view = new \View;
		echo $view->render('report.php');
    }

    function generate($f3, $args) {
        Utils::redirect_logged_out_user($f3, $args);
        Utils::prevent_csrf($f3, $args);
        
        $req = $f3->get('REQUEST');
		$report_project = $req['rp'];
		$report_task = $req['rt'];

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
        
        $f3->reroute('/report?rp='.
			urlencode($report_project).
			'&rt='.
			urlencode($report_task).
			($is_team ? '&team='.$team['id'] : ''));
    }
}