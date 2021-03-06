<?php

class Teams {
	function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);
		$f3->set('v_page_title', 'Teams');
		
		$teams = Utils::get_all_teams_of_logged_in_user($f3);
		$f3->set('v_teams', $teams);
		// RENDER
		$view = new \View;
		echo $view->render('teams.php');
	}

	function create($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$req_team_name = $req['team_name'];
		$req_team_invitees = $req['team_invitees'];
		$req_team_invitees = explode(',', $req_team_invitees);

		// VALIDATE TEAM NAME
		if(!$req_team_name) {
			$f3->push('v_errors', array(
				'element_id' => 'create_team_errors',
				'message' => 'Team name required.'
			));
			Utils::reroute_with_errors($f3, $args, '/teams');
		}

		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$db_users = new \DB\SQL\Mapper($db, 'users');
		// GO AHEAD AND VALIDATE INVITEE LIST
		foreach($req_team_invitees as $invitee) {
			$db_users->reset();
			$invited_user = $db_users->load(array('username=?', trim($invitee)));
			if(!$invited_user) {
				$f3->push('v_errors', array(
					'element_id' => 'create_team_errors',
					'message' => 'At least one of the people on your invite list is not a user. No team created. Try again with different invitees.'
				));
				Utils::reroute_with_errors($f3, $args, '/teams');
			}
		}
		$db_users->reset();
		$user = $db_users->load(array('username=?', $session_username));

		$db_teams = new \DB\SQL\Mapper($db, 'teams');
		$db_teams->name = $req_team_name;
		$db_teams->creator = $user->id;
		$db_teams->save();

		$db_users_teams = new \DB\SQL\Mapper($db, 'users_teams');
		$db_users_teams->user_id = $user->id;
		$db_users_teams->team_id = $db_teams->id;
		$db_users_teams->save();

		foreach($req_team_invitees as $invitee) {
			$db_users->reset();
			$invited_user = $db_users->load(array('username=?', trim($invitee)));
			if($invited_user) {
				$db_users_teams->reset();
				$db_users_teams->user_id = $invited_user->id;
				$db_users_teams->team_id = $db_teams->id;
				$db_users_teams->save();
			}
		}

		$f3->reroute('/teams');

	}
}