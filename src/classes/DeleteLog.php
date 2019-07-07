<?php

class DeleteLog {
	function show_delete($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$db = $f3->get('DB');

		$session_username = $f3->get('SESSION.session_username');
		$user = new \DB\SQL\Mapper($db, 'users');
		$user->load(array('username = ?', $session_username));
		$f3->set('v_username', $session_username);

		$req = $f3->get('REQUEST');
		$req_log = urldecode($req['log']);
		$log = new \DB\SQL\Mapper($db, 'logs');
		$log->load(array('id = ?', $req_log));
		if($log->user_id !== $user->id) {
			$f3->reroute('/login');
		}

		$f3->set('v_log', $log);

		$f3->set('v_page_title', 'Delete Log');

		// RENDER
		$view = new \View;
		echo $view->render('delete-log.php');
	}
}