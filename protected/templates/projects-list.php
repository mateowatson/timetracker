<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
		<?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <?php if(isset($v_confirmations) && count($v_confirmations)) : ?>
			<div class="alert alert-success" role="alert">
				<?php
				foreach($v_confirmations as $confirmation) :
				?>
				<p><?php echo $confirmation->message; ?></p>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

            <div class="mb-3">
            <?php if(!$v_show_archived) : ?>
                <a href="/projects?archived=1" class="btn btn-secondary">Show archived projects</a>
            <?php else: ?>
                <a href="/projects" class="btn btn-secondary">Hide archived projects</a>
            <?php endif; ?>
            </div>

            <?php if (count($v_projects)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($v_projects as $project): ?>
                            <tr>
                                <td><?= $project['id'] ?></td>
                                <td><a href="/projects/<?= $project['id'] ?>"><?= $project['name'] ?></a><?php if($project['archived']):?> <span class="badge badge-secondary">archived</span><?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>No existing projects.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once('partials/footer.php'); ?>