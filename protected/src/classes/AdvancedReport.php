<?php

class AdvancedReport {
    function show(\Base $f3, array $args) {
        Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

        // ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Advanced Report');

        // RENDER
		$view = new \View;
		echo $view->render('advanced-report.php');
    }
}