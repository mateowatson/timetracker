<?php

class TeamMember {
	function add($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);

		$req = $f3->get('REQUEST');
		$req_team_member = $req['team_member_name'];
		$req_team_id = $req['team_member_team_id'];
		var_dump($req_team_id);
	}
}