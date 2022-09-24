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
    public $generated_report;

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
        $this->ar_projects = isset($f3->REQUEST['ar_projects']) ? $f3->REQUEST['ar_projects'] : $this->ar_projects;
        $this->ar_projects = array_map('intval', $this->ar_projects);
        $this->ar_tasks = isset($f3->REQUEST['ar_tasks']) ? $f3->REQUEST['ar_tasks'] : $this->ar_tasks;
        $this->ar_tasks = array_map('intval', $this->ar_tasks);
        $this->ar_notes = isset($f3->REQUEST['ar_notes']) ? $f3->REQUEST['ar_notes'] : $this->ar_notes;
        if(isset($f3->REQUEST['ar_begin_date']))
            $this->ar_begin_date = Utils::validate_date($f3->REQUEST['ar_begin_date']) ? $f3->REQUEST['ar_begin_date'] : null;
        if(isset($f3->REQUEST['ar_end_date']))
            $this->ar_end_date = Utils::validate_date($f3->REQUEST['ar_end_date']) ? $f3->REQUEST['ar_end_date'] : null;
        $this->ar_report_type = isset($f3->REQUEST['ar_report_type']) ? $f3->REQUEST['ar_report_type'] : $this->ar_report_type;

        // GET PROJECTS AND TASKS LISTS
        $this->user_projects_tasks = Utils::get_project_and_task_lists(false, null, $db, $user, true, 'projects.name ASC', 'tasks.name ASC');

        // PREP QUERY VARIABLES WE'LL NEED
        $arprojectids = count($this->ar_projects) && !in_array('ar_all', $this->ar_projects) ? implode('", "',$this->ar_projects) : '';
        $artaskids = count($this->ar_tasks) && !in_array('ar_all', $this->ar_tasks) ? implode('", "',$this->ar_tasks) : '';
        $allprojects = false;
        if(in_array('ar_all', $this->ar_projects))
            $allprojects = true;
        $alltasks = false;
        if(in_array('ar_all', $this->ar_tasks))
            $alltasks = true;

        // PARTS SAME FOR ALL QUERIES
        $timesum = 'ROUND(SUM(IF(logs.end_time != "0000-00-00 00:00:00",
            TIMESTAMPDIFF(SECOND, logs.start_time, logs.end_time)/60/60,
            TIMESTAMPDIFF(SECOND, logs.start_time, NOW())/60/60
        )), 2)';
        $where_clauses = ($arprojectids || !$allprojects ? 'AND project_id IN ("'.$arprojectids.'")' : '').'
            '.($artaskids || !$alltasks ? 'AND task_id IN ("'.$artaskids.'")' : '').'
            '.($this->ar_begin_date ? 'AND DATE(logs.start_time) >= "'.$this->ar_begin_date.'"' : '').'
            '.($this->ar_end_date ? 'AND DATE(logs.end_time) <= "'.$this->ar_end_date.'"' : '').'
            AND (logs.notes LIKE ? OR ?)';
        
        if($this->ar_report_type === 'Total by Week') {
            // GET WEEKLY REPORT
            $this->generated_report = $db->exec('
                SELECT
                    CONCAT(
                        STR_TO_DATE(CONCAT(YEARWEEK(logs.start_time, 0)," Sunday"), "%X%V %W"),
                        " - ",
                        STR_TO_DATE(CONCAT(YEARWEEK(logs.start_time, 0)," Saturday"), "%X%V %W")
                    ) as timeunit,
                    '.$timesum.'
                    as time
                FROM logs
                WHERE user_id = ?
                    '.$where_clauses.'
                GROUP BY YEARWEEK(logs.start_time, 0) WITH ROLLUP
            ', array(
                $user->id,
                '%'.$this->ar_notes.'%',
                !$this->ar_notes
            ));
            $this->generated_report[count($this->generated_report) - 1]['timeunit'] = "Total";
        } else if($this->ar_report_type === 'Total by Day') {
            // GET DAY REPORT
            $this->generated_report = $db->exec('
                SELECT
                    DATE(logs.start_time) as timeunit,
                    '.$timesum.'
                    as time
                FROM logs
                WHERE user_id = ?
                    '.$where_clauses.'
                GROUP BY DAYOFYEAR(DATE(logs.start_time)) WITH ROLLUP
            ', array(
                $user->id,
                '%'.$this->ar_notes.'%',
                !$this->ar_notes
            ));

            // add the day of the week
            foreach($this->generated_report as &$generated_report_item) {
                $item_date = date_create($generated_report_item['timeunit']);
                $generated_report_item['timeunit'] = date_format($item_date, 'Y-m-d, D.');
            }

            $this->generated_report[count($this->generated_report) - 1]['timeunit'] = "Total";
        } else if($this->ar_report_type === 'Total by Year') {
            // GET YEAR REPORT
            $this->generated_report = $db->exec('
                SELECT
                    YEAR(logs.start_time) as timeunit,
                    '.$timesum.'
                    as time
                FROM logs
                WHERE user_id = ?
                    '.$where_clauses.'
                GROUP BY YEAR(DATE(logs.start_time)) WITH ROLLUP
            ', array(
                $user->id,
                '%'.$this->ar_notes.'%',
                !$this->ar_notes
            ));
            $this->generated_report[count($this->generated_report) - 1]['timeunit'] = "Total";
        } else if($this->ar_report_type === 'Individual Logs') {
            // GET INDIVIDUAL LOGS REPORT
            $this->generated_report = $db->exec('
                SELECT
                    CONCAT(
                        DATE_FORMAT(
                            DATE(logs.start_time),
                            "%b %e, %Y"
                        ),
                        " ",
                        TIME_FORMAT(
                            TIME(logs.start_time),
                            "%r"
                        ),
                        " - ",
                        DATE_FORMAT(
                            DATE(logs.end_time),
                            "%b %e, %Y"
                        ),
                        " ",
                        TIME_FORMAT(
                            TIME(logs.end_time),
                            "%r"
                        )
                    ) as timeunit,
                    logs.notes as notes,
                    '.$timesum.'
                    as time,
                    id as logid
                FROM logs
                WHERE user_id = ?
                    '.$where_clauses.'
                GROUP BY logs.id WITH ROLLUP
            ', array(
                $user->id,
                '%'.$this->ar_notes.'%',
                !$this->ar_notes
            ));
            $this->generated_report[count($this->generated_report) - 1]['timeunit'] = "Total";
        }

        // ADDITIONAL VIEW VARIABLES
		$f3->set('v_page_title', 'Advanced Report');
		$f3->set('v_obj', $this);
        $f3->set('v_team', null);
        // RENDER
		$view = new \View;
		echo $view->render('advanced-report.php');
    }
}