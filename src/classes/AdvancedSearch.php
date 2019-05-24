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
		
		$sql_condition = '';
		$sql_offset = 10*($page);

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