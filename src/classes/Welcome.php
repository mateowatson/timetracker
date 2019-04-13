<?php

class Welcome {
	function show($f3, $args) {
		Utils::redirect_logged_in_user($f3, $args);

		$view=new \View;
        echo $view->render('welcome.php');
	}
}