<?php 

use Core\Classes\HTML;
use Core\Classes\Form;

$this->setSiteTitle('Reset Password'); 

?>


<?= $this->start('head'); ?>
<?= $this->end(); ?>

<?= $this->start('body'); ?>

<div class="row h-100">
	<div class="col-sm-6 offset-sm-3 my-auto">
		<div class="card">
			<div class="card-header">
				<h2>Reset Password</h2>
			</div>

			<div class="card-body">
				<?= Form::errors($this->error); ?>

				<form action="<?= BASE_URL; ?>reset-password/verify" method="post">
					<?= Form::csrf(); ?>
					<?= HTML::inputBlock(['type' => 'password', 'id' => 'password', 'name' => 'password', 
										  'class' => 'form-control', 'value' => '',
										  'placeholder' => 'Enter new password...'],
										   ['text' => 'New Password', 'class' => 'form-group']); ?>
					<?= HTML::inputBlock(['type' => 'password', 'id' => 'rePassword', 'name' => 're_password', 
										  'class' => 'form-control', 'value' => '',
										  'placeholder' => 'Repeat new password...'],
										   ['text' => 'Repeat password', 'class' => 'form-group']); ?>
					
					<?= HTML::submit(['value' => 'Save Password', 'class' => 'btn btn-block btn-success']); ?>
				</form>
			</div>
		</div>
	</div>
</div>

<?= $this->end(); ?>