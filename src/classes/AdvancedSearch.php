<?php

class AdvancedSearch {
	public function show($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Advanced Search');

		// RENDER
		$view = new \View;
		echo $view->render('advanced-search.php');
	}

	function post_search($f3, $args) {
		Utils::redirect_logged_out_user($f3, $args);
		Utils::prevent_csrf($f3, $args);
		Utils::send_csrf($f3, $args);

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