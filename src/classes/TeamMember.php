<?php

class TeamMember {
	function add($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

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
		if(!$user_to_add->dry()) {
			//var_dump($user_to_add->username);
			$db_users_teams = new \DB\SQL\Mapper($db, 'users_teams');
			$db_users_teams->user_id = $user_to_add->id;
			$db_users_teams->team_id = $db_team->id;
			$db_users_teams->save();
		}
		$referer_url = $f3->get('HEADERS')['Referer'];
		$referer_url_parts = parse_url($referer_url);
		//$referer_path_parts = explode('/', $referer_url_parts['path']);
		$f3->reroute($referer_url_parts['path']);
	}
}
