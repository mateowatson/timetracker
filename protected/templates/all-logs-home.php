<?php require_once('partials/header.php'); ?>

<div class="container mb-5">
	<div class="row align-items-baseline">
		<?php require_once('partials/heading.php'); ?>
        <?php require_once('partials/account.php'); ?>
	</div>
</div>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-3">
			<p><a href="<?php echo $SITE_URL; ?>/all-logs?personal">See all personal logs</a></p>
        </div>
        <div class="col-lg-3">
            <p>See all logs of one team.</p>

            <ul>
                <?php foreach($v_teams as $v_team): ?>
                <li>
                    <a href="<?php echo $SITE_URL; ?>/all-logs?team=<?php echo $v_team['team_id']; ?>">
                        <?php echo $v_team['team_name']; ?>
                    </a>
                    <?php if($v_team['creator']): ?>- You created this team.<?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
	</div>
</div>


<?php require_once('partials/footer.php'); ?>