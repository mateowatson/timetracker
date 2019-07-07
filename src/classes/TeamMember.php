<?php

class TeamMember {
	function add($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$referer_url = $f3->get('HEADERS')['Referer'];
		$referer_url_parts = parse_url($referer_url);

		$req = $f3->get('REQUEST');
		$req_team_member = $req['team_member_name'];
		$req_team_id = $req['team_member_team_id'];
		//var_dump($req_team_member);
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$user = new \DB\SQL\Mapper($db, 'users');
		$user->load(array('username=?', $session_username));
		$user_to_add = new \DB\SQL\Mapper($db, 'users');
		$user_to_add->load(array('username=?', $req_team_member));
		$db_team = new \DB\SQL\Mapper($db, 'teams');
		$db_team->load(array('id = ?', $req_team_id));

		// Check for team membership
		$db_users_teams = new \DB\SQL\Mapper($db, 'users_teams');
		$db_users_teams->load(array('user_id = ?', $user->id));
		if($db_users_teams->dry()) {
			$f3->reroute('/login');
		}

		if(!$user_to_add->dry()) {
			$db_users_teams = new \DB\SQL\Mapper($db, 'users_teams');
			$db_users_teams->user_id = $user_to_add->id;
			$db_users_teams->team_id = $db_team->id;
			$db_users_teams->save();

			$f3->push('v_confirmations', array(
				'element_id' => 'team_members_confirmations',
				'message' => $user_to_add->username . ' has been added!'
			));
			Utils::reroute_with_confirmations($f3, $args, $referer_url_parts['path']);
		}

		$f3->push('v_errors', array(
			'element_id' => 'add_member_errors',
			'message' => 'We couldn\'t add that user. Make sure the username is spelled correctly.'
		));

		Utils::reroute_with_errors($f3, $args, $referer_url_parts['path']);
	}

	function show_remove($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$req_team = urldecode($req['team']);
		$req_user = urldecode($req['user']);

		$db = $f3->get('DB');
		$user_to_remove = new \DB\SQL\Mapper($db, 'users');
		$user_to_remove->load(array('id = ?', $req_user));
		$team = new \DB\SQL\Mapper($db, 'teams');
		$team->load(array('id = ?', $req_team));

		$session_username = $f3->get('SESSION.session_username');
		$user = new \DB\SQL\Mapper($db, 'users');
		$user->load(array('username = ?', $session_username));

		// Only team creators can delete members
		if($team->creator !== $user->id) {
			$f3->reroute('/login');
		}
		$f3->set('v_team', $team);
		$f3->set('v_user_to_remove', $user_to_remove);

		$referer_url = $f3->get('HEADERS')['Referer'];
		$referer_url_parts = parse_url($referer_url);
		$f3->set('v_cancel_url', $referer_url_parts['path']);

		$f3->set('v_page_title', 'Remove Member');

		// RENDER
		$view = new \View;
		echo $view->render('remove-member.php');
	}

	function remove($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$req_team = $req['team_id'];
		$req_user = $req['user_id'];
		//error_log('XXXXXXXX: '.print_r( $req, true));

		$db = $f3->get('DB');
		$user_to_remove = new \DB\SQL\Mapper($db, 'users');
		$user_to_remove->load(array('id = ?', $req_user));
		//error_log('XXXXXXXX: '.print_r( $user_to_remove, true));
		$team = new \DB\SQL\Mapper($db, 'teams');
		$team->load(array('id = ?', $req_team));

		$session_username = $f3->get('SESSION.session_username');
		$user = new \DB\SQL\Mapper($db, 'users');
		$user->load(array('username = ?', $session_username));

		// Only team creators can delete members
		if($team->creator !== $user->id) {
			$f3->reroute('/login');
		}

		// Reroute to screen that says they are already removed if necessary
		$db_users_teams = new \DB\SQL\Mapper($db, 'users_teams');
		$db_users_teams->load(array('user_id = ?', $user_to_remove->id));
		if($db_users_teams->dry()) {
			// Change below to team dashboard with errors
			$f3->reroute('/login');
		}

		if(!$user_to_remove->dry()) {
			$db_users_teams->erase();
			$f3->push('v_confirmations', array(
				'element_id' => 'team_members_confirmations',
				'message' => $user_to_remove->username . ' has been removed.'
			));
			Utils::reroute_with_confirmations($f3, $args, '/team/'.$team->id);
		}
	}
}
