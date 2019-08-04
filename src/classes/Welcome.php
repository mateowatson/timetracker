<?php

class Welcome {
	function show($f3, $args) {
		Utils::redirect_logged_in_user($f3, $args);

		$db = $f3->get('DB');
		$site_options = new \DB\SQL\Mapper($db, 'site_options');
		$open_registration = $site_options->load(array('option_key = \'open_registration\''));
		if($open_registration->option_value === 'false') {
			$f3->set('v_open_registration', false);
		} else {
			$f3->set('v_open_registration', true);
		}

		$f3->set('v_page_title', 'Welcome');
		$view=new \View;
        echo $view->render('welcome.php');
	}
}