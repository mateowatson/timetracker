<?php

class AdvancedReport {
    public $ar_projects = [];
    public $ar_tasks = [];
    public $ar_notes;
    public $ar_begin_date;
    public $ar_end_date;
    public $ar_report_type;
    public $user_projects;
    public $user_tasks;
    public $weekly_report;

    function show(\Base $f3, array $args) {
        Utils::redirect_logged_out_user($f3, $args);
		Utils::send_csrf($f3, $args);

        // GET DB, SESSION AND USER
		$db = $f3->get('DB');
		$session_username = $f3->get('SESSION.session_username');
		$user = new \DB\SQL\Mapper($db, 'users');
		$user->load(array('username=?', $session_username));
		$f3->set('v_username', $session_username);
		$f3->set('v_user_email', $user->email);
		$f3->set('v_user_email_verified', $user->email_verified);
		$SITE_URL = $f3->get('SITE_URL');

        // GET SEARCH PARAMETERS
        $this->ar_projects = $f3->REQUEST['ar_projects'] ? : $this->ar_projects;
        $this->ar_projects = array_map('intval', $this->ar_projects);
        $this->ar_tasks = $f3->REQUEST['ar_tasks'] ? : $this->ar_tasks;
        $this->ar_tasks = array_map('intval', $this->ar_tasks);
        $this->ar_notes = $f3->REQUEST['ar_notes'] ? : $this->ar_notes;
        $this->ar_begin_date = $f3->REQUEST['ar_begin_date'] ? : $this->ar_begin_date;
        $this->ar_end_date = $f3->REQUEST['ar_end_date'] ? : $this->ar_end_date;
        $this->ar_report_type = $f3->REQUEST['ar_report_type'] ? : $this->ar_report_type;

        // GET PROJECTS AND TASKS LISTS
        $this->user_projects_tasks = Utils::get_project_and_task_lists(false, null, $db, $user, true);

        // GET WEEKLY REPORT
        $arprojectids = count($this->ar_projects) && !in_array('ar_all', $this->ar_projects) ? implode('", "',$this->ar_projects) : '';
        $artaskids = count($this->ar_tasks) && !in_array('ar_all', $this->ar_tasks) ? implode('", "',$this->ar_tasks) : '';
        $allprojects = false;
        if(in_array('ar_all', $this->ar_projects))
            $allprojects = true;
        $alltasks = false;
        if(in_array('ar_all', $this->ar_tasks))
            $alltasks = true;
        $this->weekly_report = $db->exec('
			SELECT
                CONCAT(
                    STR_TO_DATE(CONCAT(YEARWEEK(logs.start_time, 0)," Sunday"), "%X%V %W"),
                    " - ",
                    STR_TO_DATE(CONCAT(YEARWEEK(logs.start_time, 0)," Saturday"), "%X%V %W")
                ) as week,
                SUM(IF(end_time != "0000-00-00 00:00:00",
                    TIMESTAMPDIFF(SECOND, start_time, end_time)/60/60,
                    TIMESTAMPDIFF(SECOND, start_time, NOW())/60/60
                ))
                as time
			FROM logs
			WHERE user_id = ?
                '.($arprojectids || !$allprojects ? 'AND project_id IN ("'.$arprojectids.'")' : '').'
                '.($artaskids || !$alltasks ? 'AND task_id IN ("'.$artaskids.'")' : '').'
            GROUP BY YEARWEEK(logs.start_time, 0) WITH ROLLUP
		', array(
            $user->id
        ));
        $this->weekly_report[count($this->weekly_report) - 1]['week'] = "Total";

        // ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Advanced Report');
		$f3->set('v_obj', $this);

        // RENDER
		$view = new \View;
		echo $view->render('advanced-report.php');
    }
}