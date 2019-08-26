<?php

class AllLogs {
    function show($f3, $args) {
        Utils::redirect_logged_out_user($f3, $args);
        Utils::send_csrf($f3, $args);
        
        // GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		$user = $db_users->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);
        $f3->set('v_page_title', 'All Logs');
        $f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);
        
        $req = $f3->get('REQUEST');
		$personal = isset($req['personal']);
        $team_id = isset($req['team']) ? urldecode($req['team']) : false;

        if($personal !== false && $team_id !== false) {
            // send message saying you can't do both
        }

        if($personal) {
            $extra_conditions = '';
            $dashboard_time_filter = '';
            $team_filter = '';

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

            $f3->set('v_page_title', 'All Logs: '.$user->username);
            // RENDER
            $view = new \View;
            echo $view->render('all-logs.php');
            return;
        }

        if($team_id !== false) {
            $is_team = false;
            $team = false;
            $is_user_in_team = false;
            $available_teams = $db->exec('SELECT * FROM users_teams WHERE user_id = ?', $user->id);
            foreach($available_teams as $av_t) {
                if((int)$av_t['user_id'] === (int)$user->id && (int)$av_t['team_id'] === (int)$team_id) {
                    $is_team = true;
                    $is_user_in_team = true;
                    $team = $db->exec('SELECT * FROM teams WHERE id = ?', $av_t['team_id'])[0];
                    break;
                }
            }
            if(!$is_user_in_team) {
                return $f3->reroute('/all-logs');
            }
            $f3->set('v_page_title', $team['name']);
            $team_members = array();
            $team_members_ids = $db->exec(
                'SELECT user_id FROM users_teams WHERE team_id = ?',
                array($team['id'])
            );
            foreach($team_members_ids as $id) {
                array_push(
                    $team_members,
                    $db->exec('SELECT * FROM users WHERE id = ?', array($id['user_id']))[0]
                );
                $f3->set('v_team_members', $team_members);
            }

            $f3->set('v_is_team', $is_team);
    
            // GET LOGS LIST
            $extra_conditions = '';
            $dashboard_time_filter = '';
            /* $dashboard_time_filter = 'AND CAST(logs.start_time AS DATE) =
                    CAST(NOW() AS DATE)'; */
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

            // RENDER
            $view = new \View;
            echo $view->render('all-logs.php');
            return;
        }


        $teams = Utils::get_all_teams_of_logged_in_user($f3);
        $f3->set('v_teams', $teams);
        // RENDER
        $view = new \View;
        echo $view->render('all-logs-home.php');
    }
}