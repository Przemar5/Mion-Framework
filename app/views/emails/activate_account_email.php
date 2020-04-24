<?php $this->setSiteTitle('Activate Account'); ?>

<?= $this->start('head'); ?>
<?= $this->end(); ?>

<?= $this->start('body'); ?>

<h3>Click the link below to activate your account.</h3>
<p>If you didn't tried to register at <?= SITE_TITLE; ?>, just ignore this message.</p>

<a href="<?= BASE_URL; ?>verification/activate-account?token=<?= $this->data->token; ?>" class="btn btn-lg btn-block btn-primary">
	Activate Account
</a>

<?= $this->end(); ?>