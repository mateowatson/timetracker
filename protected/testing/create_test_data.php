<?php

require '../vendor/autoload.php';

$f3 = \Base::instance();

$f3->config('../setup.cfg');

$f3->set('DB', new \DB\SQL(
	'mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock'.
		';port='.$f3->get('DB_PORT').
		';dbname='.$f3->get('DB_NAME'),
	$f3->get('DB_USER'),
	$f3->get('DB_PASSWORD')
));
$db = $f3->get('DB');

/* -- Words and names --------------------------------------- */

$usernames = [ 'jill', 'edward', 'jeremy', 'mia', 'sam' ];

$wordlist = file_get_contents( 'wordlist.csv' );
$wordlist = explode( ',', $wordlist );

// generate a bank of project and task names
$num_of_names = 200;
$names = [];

for( $i=0; $i < $num_of_names; $i++ ) { 
    $label_length = rand( 1, 3 );
    $label = '';

    for( $j=0; $j<$label_length; $j++) {
        $index = rand( 0, count($wordlist) - 1);
        $label .= ' ' . ucfirst( $wordlist[$index] );
    }

    $names[] = trim( $label );
}

//error_log( print_r( $names, true ) );


/* -- Dates and times --------------------------------------- */

$years = [ '2015', '2016', '2017', '2018', '2019' ];
$date_ranges = [];
$num_of_date_ranges = 200;

for( $i=0; $i<$num_of_date_ranges; $i++ ) {
    $year = $years[rand(0, count($years) - 1)];
    $month = str_pad( strval(rand(1, 12)), '0', STR_PAD_LEFT );
    $day = str_pad( strval(rand(1, 28)), '0', STR_PAD_LEFT );
    $hour = str_pad( strval(rand(0, 23)), '0', STR_PAD_LEFT );
    $min = str_pad( strval(rand(0, 59)), '0', STR_PAD_LEFT );
    $sec = str_pad( strval(rand(0, 59)), '0', STR_PAD_LEFT );

    $start_date = date_create( sprintf(
        '%s-%s-%s %s:%s:%s',
        $year, $month, $day, $hour, $min, $sec
    ) );

    $end_date = date_create( sprintf(
        '%s-%s-%s %s:%s:%s',
        $year, $month, $day, $hour, $min, $sec
    ) );;

    $rand_seconds = rand(300, 10800);
    $duration = date_interval_create_from_date_string( "$rand_seconds seconds" );

    date_add( $end_date, $duration );

    $date_ranges[] = [
        'start' => date_format($start_date, "Y/m/d H:i:s"),
        'end' => date_format($end_date, "Y/m/d H:i:s")
    ];
    //error_log(date_format($start_date, "Y/m/d H:i:s") . ' - ' . date_format($end_date, "Y/m/d H:i:s"));
}


/* -- Users and data ---------------------------------------- */

$users = [];

// build up users
foreach( $usernames as $name ) {
    $users[] = [
        'id' => null,
        'username' => $name,
        'email' => "$name@example.test",
        'password' => "$name#12345"
    ];
}

// build up projects and tasks per user
foreach( $users as &$user ) {
    $projects = [];
    $tasks = [];

    $user['projects'] = get_items( 10 );
    $user['tasks'] = get_items( 10 );
}


/* -- DB Insertion ------------------------------------------ */

// insert users, projects, tasks, and into the database
foreach( $users as &$user ) {
    // insert user
    $user['id'] = insert_user( $user );

    // insert projects and users_projects
    foreach( $user['projects'] as &$project ) {
        $project['id'] = insert_project( $project );
        insert_users_projects( $project, $user );
    }

    // insert tasks
    foreach( $user['tasks'] as &$task ) {
        $task['id'] = insert_task( $task );
        // insert users_tasks
        insert_users_tasks( $task, $user );
    }

    // make sure each project has at least one associated log
    foreach( $user['projects'] as $project ) {
        $random_task = $user['tasks'][rand(0, 9)];
        insert_log( $user, $project, $random_task );
    }

    // make sure each task has at least one associated log
    foreach( $user['tasks'] as $task ) {
        $random_project = $user['projects'][rand(0, 9)];
        insert_log( $user, $random_project, $task );
    }

    // now insert some random logs
    for( $i=0; $i<100; $i++ ) {
        $random_project = $user['projects'][rand(0, 9)];
        $random_task = $user['tasks'][rand(0, 9)];
        insert_log( $user, $random_project, $random_task );
    }
}

// error_log( print_r( $users, true ) );


/* -- Teams ------------------------------------------------- */

$teams = [
    [
        'id' => null,
        'name' => 'Team A',
        'members' => [ $users[0], $users[1], $users[2] ],
        'projects' => [],
        'tasks' => []
    ],
    [
        'id' => null,
        'name' => 'Team B',
        'members' => [ $users[3], $users[4] ],
        'projects' => [],
        'tasks' => []
    ]
];

foreach( $teams as &$team ) {
    // insert team in db
    $team['id'] = insert_team( $team, $team['members'][0] );

    // insert user/team relationship
    foreach( $team['members'] as $member ) {
        insert_users_teams( $team, $member );
    }

    // create 10 team projects and tasks
    $team['projects'] = get_items( 10 );
    $team['tasks'] = get_items( 10 );

    // insert projects and tasks
    for( $i=0; $i<10; $i++ ) {
        $team['projects'][$i]['id'] = insert_project( $team['projects'][$i] );
        $team['tasks'][$i]['id'] = insert_task( $team['tasks'][$i] );
    }

    // create team logs
    $users_projects_table = new \DB\SQL\Mapper( $db, 'users_projects' );
    $users_tasks_table = new \DB\SQL\Mapper( $db, 'users_tasks' );

    foreach( $team['members'] as $member ) {
        for( $i=0; $i<20; $i++ ) {
            // get a random team project and task
            $random_project = $team['projects'][rand(0, 9)];
            $random_task = $team['tasks'][rand(0, 9)];

            // if the user/project relationship isn't established, create it
            $users_projects_table->reset();
            $users_projects_table->load( [ 'user_id = ? and project_id = ?', $member['id'], $random_project['id'] ] );
            if( $users_projects_table->dry() ) insert_users_projects( $random_project, $member );

            // if the user/project relationship isn't established, create it
            $users_tasks_table->reset();
            $users_tasks_table->load( [ 'user_id = ? and task_id = ?', $member['id'], $random_task['id'] ] );
            if( $users_tasks_table->dry() ) insert_users_tasks( $random_task, $member );

            insert_log(
                $member,
                $team['projects'][rand(0, 9)],
                $team['tasks'][rand(0, 9)],
                $team
            );
        }
    }
}


/* -- Functions --------------------------------------------- */

function get_name() {
    global $names;
    global $num_of_names;
    return $names[ rand(0, $num_of_names - 1) ];
}

function get_sentence() {
    global $wordlist;
    $num_of_words = rand(3, 15);
    $sentence = [];

    for( $i=0; $i<$num_of_words; $i++ ) {
        $sentence[] = $wordlist[ rand(0, count($wordlist) - 1) ];
    }

    $sentence = implode( ' ', $sentence ) . '.';
    return ucfirst( $sentence );
}

function get_items( $num ) {
    $items = [];
    for( $i=0; $i<$num; $i++ ) {
        $items[] = [
            'id' => null,
            'name' => get_name()
        ];
    }
    return $items;
}

function get_date_range() {
    global $date_ranges;
    global $num_of_date_ranges;
    return $date_ranges[ rand(0, $num_of_date_ranges - 1) ];
}

function insert_user( $user ) {
    global $db;
    $user_table = new \DB\SQL\Mapper( $db, 'users' );

    $user_table->load( ['username=?', $user['username']] );
    $user_table->reset();
    $user_table->username = $user['username'];
    $user_table->email = $user['email'];
    $user_table->password = password_hash( $user['password'], PASSWORD_DEFAULT );
    $user_table->admin = 0;
    $user_table->email_verified = 0;
    $user_table->insert();
    return $user_table->id;
}

function insert_project( $project ) {
    global $db;
    $projects_table = new \DB\SQL\Mapper( $db, 'projects' );

    $projects_table->reset();
    $projects_table->name = $project['name'];
    $projects_table->insert();
    return $projects_table->id;
}

function insert_users_projects( $project, $user ) {
    global $db;
    $users_projects_table = new \DB\SQL\Mapper( $db, 'users_projects' );

    $users_projects_table->reset();
    $users_projects_table->user_id = $user['id'];
    $users_projects_table->project_id = $project['id'];
    $users_projects_table->insert();
}

function insert_task( $task ) {
    global $db;
    $tasks_table = new \DB\SQL\Mapper( $db, 'tasks' );

    $tasks_table->reset();
    $tasks_table->name = $task['name'];
    $tasks_table->insert();
    return $tasks_table->id;
}

function insert_users_tasks( $task, $user ) {
    global $db;
    $users_tasks_table = new \DB\SQL\Mapper( $db, 'users_tasks' );

    $users_tasks_table->reset();
    $users_tasks_table->user_id = $user['id'];
    $users_tasks_table->task_id = $task['id'];
    $users_tasks_table->insert();
}

function insert_log( $user, $project, $task, $team = null ) {
    global $db;
    $logs_table = new \DB\SQL\Mapper( $db, 'logs' );

    $logs_table->reset();
    $logs_table->user_id = $user['id'];
    $logs_table->project_id = $project['id'];
    $logs_table->task_id = $task['id'];
    $range = get_date_range();
    $logs_table->start_time = $range['start'];
    $logs_table->end_time = $range['end'];
    $logs_table->notes = rand(0, 1) ? get_sentence() : null;

    if( $team ) {
        $logs_table->team_id = $team['id'];
    }

    $logs_table->insert();
}

function insert_team( $team, $creator ) {
    global $db;
    $teams_table = new \DB\SQL\Mapper( $db, 'teams' );

    $teams_table->reset();
    $teams_table->name = $team['name'];
    $teams_table->creator = $creator['id'];
    $teams_table->insert();
    return $teams_table->id;
}

function insert_users_teams( $team, $user ) {
    global $db;
    $users_teams_table = new \DB\SQL\Mapper( $db, 'users_teams' );

    $users_teams_table->reset();
    $users_teams_table->user_id = $user['id'];
    $users_teams_table->team_id = $team['id'];
    $users_teams_table->insert();
}