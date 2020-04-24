<?php 

use Core\Classes\HTML;
use Core\Classes\Form;

$this->setSiteTitle('Retrieve Password'); 

?>


<?= $this->start('head'); ?>
<?= $this->end(); ?>

<?= $this->start('body'); ?>

<div class="row h-100">
	<div class="col-sm-6 offset-sm-3 my-auto">
		<div class="card">
			<div class="card-header">
				<h2>Retrieve Password</h2>
			</div>

			<div class="card-body">
				<?= Form::errors($this->error); ?>

				<form action="<?= BASE_URL; ?>forgot-password/verify" method="post">
					<?= Form::csrf(); ?>
					<?= HTML::inputBlock(['type' => 'email', 'id' => 'email', 'name' => 'email', 
										  'class' => 'form-control', 'value' => $this->user->email ?? '',
										  'placeholder' => 'Enter your email address...'],
										   ['text' => 'Email address', 'class' => 'form-group']); ?>
					
					<?= HTML::submit(['value' => 'Send Email', 'class' => 'btn btn-block btn-success']); ?>
					<?= HTML::link(['href' => BASE_URL . 'login', 'text' => 'Go back']); ?>
				</form>
			</div>
		</div>
	</div>
</div>

<?= $this->end(); ?>