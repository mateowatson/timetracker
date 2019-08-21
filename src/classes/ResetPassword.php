<?php

class ResetPassword {
    function show($f3, $args) {
        Utils::redirect_logged_in_user($f3, $args);
        Utils::send_csrf($f3, $args);

		$f3->set('v_page_title', 'Reset Password');
		$view=new \View;
        echo $view->render('reset-password.php');
    }

    function finish_password_reset($f3, $args) {
        $db = $f3->get('DB');
        Utils::prevent_csrf_from_tab_conflict($f3, $args, '/forgot-password');
        
        
    }
}