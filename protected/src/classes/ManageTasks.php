<?php

class ManageTasks {
    public $db = null;
    public $tasks = [];
    public $user = null;

    function init(\Base $f3, array $args) {
        Utils::redirect_logged_out_user($f3, $args);
        Utils::send_csrf($f3, $args);

        // GET DB, SESSION AND USER
        $this->db = $f3->get('DB');
        $session_username = $f3->get('SESSION.session_username');
        $this->user = new \DB\SQL\Mapper($this->db, 'users');
        $this->user->load(array('username=?', $session_username));
        $f3->set('v_username', $session_username);
        $f3->set('v_user_email', $this->user->email);
        $f3->set('v_user_email_verified', $this->user->email_verified);
    }

    function show_all(\Base $f3, array $args) {
        $this->init($f3, $args);

        // set the page title
		$f3->set('v_page_title', 'Manage Tasks');

        // Get all Tasks
        $this->tasks = $this->db->exec('
            SELECT * FROM tasks
            LEFT JOIN users_tasks
                ON users_tasks.task_id = tasks.id
            WHERE users_tasks.user_id = ?
        ', $this->user->id);

        $f3->set('v_tasks', $this->tasks);

        $view = new \View;
        echo $view->render('tasks-list.php');
    }

    function show_task(\Base $f3, array $args) {
        $this->init($f3, $args);

        $task_mapper = new \DB\SQL\Mapper($this->db, 'tasks');
        $task_mapper->load(['id=?', $args['id']]);

        if ($task_mapper->dry()) {
            $f3->error(404);
        }

		$f3->set('v_page_title', 'Edit Task');

        $f3->set('v_task', ['id' => $task_mapper->id, 'name' => $task_mapper->name]);

        $view = new \View;
        echo $view->render('task-single.php');
    }

    function save_task(\Base $f3, array $args) {
        $this->init($f3, $args);

        $req = $f3->get('REQUEST');
        $name = $req['task_name'];

        if (!$name) {
            $f3->push('v_errors', [
                'element_id' => 'save_task_errors',
                'message' => 'Task name cannot be empty.'
            ]);

            Utils::reroute_with_errors($f3, $args, "/tasks/{$args['id']}");
        }

        $task_mapper = new \DB\SQL\Mapper($this->db, 'tasks');
        $task_mapper->load(['id=?', $args['id']]);
        $task_mapper->name = $name;
        $task_mapper->save();
        
        $f3->reroute('/tasks');
    }
}