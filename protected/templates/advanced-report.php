<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
		<?php require_once('partials/team-badge.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-12">
			<h2 class="sr-only">Advanced Report</h2>

            <form action="/advanced-report" method="GET">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <fieldset class="form-group">
                            <legend>Project(s)</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_all" name="ar_project" value="ar_all">
                                <label class="form-check-label" for="ar_project_all">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_project_1" name="ar_project" value="Project 1">
                                <label class="form-check-label" for="ar_project_1">Project 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_project_2" name="ar_project" value="Project 2">
                                <label class="form-check-label" for="ar_project_2">Project 2</label>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <fieldset class="form-group">
                            <legend>Task(s)</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_all" name="ar_task" value="ar_all">
                                <label class="form-check-label" for="ar_task_all">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_task_1" name="ar_task" value="Task 1">
                                <label class="form-check-label" for="ar_project_1">Task 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ar_task_2" name="ar_task" value="Task 2">
                                <label class="form-check-label" for="ar_task_2">Task 2</label>
                            </div>
                        </fieldset>
                    </div>
                    
                    <div class="col-sm-12 col-lg-4">
                        <div class="form-group">
                            <label for="ar_notes">Search Notes</label>
                            <input class="form-control" type="text" name="ar_notes" id="ar_notes">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="ar_begin_date">Begin Date</label>
                            <input class="form-control" type="date" name="ar_begin_date" id="ar_begin_date">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="ar_end_date">End Date (if different from Begin Date)</label>
                            <input class="form-control" type="date" name="ar_end_date" id="ar_end_date">
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="ar_report_type">Report Type</label>
                            <select class="custom-select" name="ar_report_type" id="ar_report_type">
                                <option>Total by Day</option>
                                <option>Total by Week</option>
                                <option>Total by Year</option>
                                <option>Individual Logs</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Generate Report">
                        </div>
                    </div>
            </form>
		</div>
	</div>
</div>

<?php require_once('partials/footer.php'); ?>