<?php $this->setSiteTitle('Reset Password'); ?>

<?= $this->start('head'); ?>
<?= $this->end(); ?>

<?= $this->start('body'); ?>

<h3>Click the link below to reset your password.</h3>
<p>If you didn't tried to reset your password, just ignore this message.</p>

<a href="<?= BASE_URL; ?>verification/reset-password?token=<?= $this->data->token; ?>" class="btn btn-lg btn-block btn-primary">
	Reset Password
</a>

<?= $this->end(); ?>