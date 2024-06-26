<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-sm-6 col-md-5">
            <h2><?= $v_project['name'] ?></h2>

            <?php if($v_project['archived']): ?>
                <p class="badge badge-secondary mb-3">Archived</p>
            <?php endif; ?>

            <?php if (in_array('save_project_errors', $v_errors_element_ids ?: [])): ?>

            <div class="alert alert-danger" role="alert">
                <p>The following errors occurred:</p>

                <ul>
                    <?php foreach ($v_errors as $error): ?>
                        <?php if ($error->element_id === 'save_project_errors'): ?>
                            <li><?php echo $error->message; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php endif; ?>

            <form action="/projects/<?= $v_project['id']; ?>" method="post">
                <input type="hidden" name="csrf" id="csrf_project" value="<?php echo $CSRF; ?>" />

                <div class="form-group">
                    <label for="project_name">Name</label>
                    <input id="project_name" type="text" class="form-control" name="project_name" value="<?= $v_project['name'] ?>" />
                </div>

                <button class="btn btn-primary mr-3" type="submit">Save</button>

                <?php if(!$v_project['archived']): ?>

                    <button class="btn btn-danger mr-3" type="submit" name="archive">Archive</button>

                <?php else: ?>

                    <button class="btn btn-danger mr-3" type="submit" name="unarchive">Unarchive</button>

                <?php endif; ?>

                <a href="/projects">Back to Projects</a>
            </form>
        </div>
    </div>
</div>