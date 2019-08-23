<?php

class AccessDenied {
	function show($f3, $args) {
		Utils::redirect_logged_in_user($f3, $args);

		$f3->set('v_page_title', 'Access Denied');
		$view=new \View;
        echo $view->render('access-denied.php');
	}
}