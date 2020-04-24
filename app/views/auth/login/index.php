<?php 

use Core\Classes\HTML;
use Core\Classes\Form;

$this->setSiteTitle('Sign In'); 

?>


<?= $this->start('head'); ?>
<?= $this->end(); ?>

<?= $this->start('body'); ?>

<div class="row h-100">
	<div class="col-sm-6 offset-sm-3 my-auto">
		<div class="card">
			<div class="card-header">
				<h2>Login</h2>
			</div>

			<div class="card-body">
				<?= Form::errors($this->error); ?>

				<form action="<?= BASE_URL; ?>login/verify" method="post">
					<?= Form::csrf(); ?>
					<?= HTML::inputBlock(['type' => 'text', 'id' => 'username', 'name' => 'username', 
										  'class' => 'form-control', 'value' => $this->user->username ?? '',
										  'placeholder' => 'Enter your username...'],
										   ['text' => 'Username', 'class' => 'form-group']); ?>
					<?= HTML::inputBlock(['type' => 'password', 'id' => 'password', 'name' => 'password', 
										  'class' => 'form-control', 'value' => '',
										  'placeholder' => 'Enter your password...'],
										   ['text' => 'Password', 'class' => 'form-group']); ?>
					<?= HTML::checkboxBlock(['name' => 'remember_me', 'value' => '1', $this->user->remember_me => '',
											 'class' => 'form-check-input', 'id' => 'remember_me'], 
										 	['text' => 'Remember Me', 'class' => 'form-check-label'],
										   	['class' => 'form-check']); ?>
					
					<?= HTML::link(['href' => BASE_URL . 'forgot-password', 'text' => "Don't remember password?"]); ?>
					<?= HTML::submit(['value' => 'Login', 'class' => 'btn btn-block btn-success']); ?>
					<?= HTML::link(['href' => BASE_URL . 'register', 'text' => 'Register', 
									'class' => 'btn btn-block btn-primary']); ?>
				</form>
			</div>
		</div>
	</div>
</div>

<?= $this->end(); ?>