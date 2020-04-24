<?php 

use Core\Classes\HTML;
use Core\Classes\Form;

$this->setSiteTitle('Register'); 

?>


<?= $this->start('head'); ?>
<?= $this->end(); ?>

<?= $this->start('body'); ?>

<div class="row h-100">
	<div class="col-sm-6 offset-sm-3 my-auto">
		<div class="card">
			<div class="card-header">
				<h2>Register</h2>
			</div>

			<?= HTML::errors($this->errors); ?>
		
			<form action="<?= BASE_URL; ?>register/verify" method="post" class="card-body">
				<?= Form::csrf(); ?>
				
				<?= HTML::inputBlock(['type' => 'text', 'id' => 'username', 'name' => 'username', 
									  'value' => $this->user->username ?? '', 'autocomplete' => 'off', 
									  'class' => 'form-control', 'placeholder' => 'Enter your username...'],
									   ['text' => 'Username', 'class' => 'form-group']); ?>
				<?= HTML::inputBlock(['type' => 'email', 'id' => 'email', 'name' => 'email', 
									  'value' => $this->user->email ?? '', 'class' => 'form-control', 
									  'placeholder' => 'Enter your email here...'],
									   ['text' => 'Email Address', 'class' => 'form-group']); ?>
				<?= HTML::inputBlock(['type' => 'text', 'id' => 'firstName', 'name' => 'first_name',
									  'value' => $this->user->first_name ?? '', 'class' => 'form-control', 
									  'placeholder' => 'Enter your first name'],
									   ['text' => 'First Name', 'class' => 'form-group']); ?>
				<?= HTML::inputBlock(['type' => 'text', 'id' => 'lastName', 'name' => 'last_name',
									  'value' => $this->user->last_name ?? '', 'class' => 'form-control', 
									  'placeholder' => 'Enter your last name...'],
									   ['text' => 'Last Name', 'class' => 'form-group']); ?>
				<?= HTML::inputBlock(['type' => 'password', 'id' => 'password', 'name' => 'password', 
									  'autocomplete' => 'new-password', 'class' => 'form-control', 
									  'placeholder' => 'Enter your password...'],
									   ['text' => 'Password', 'class' => 'form-group']); ?>
				<?= HTML::inputBlock(['type' => 'password', 'id' => 'rePassword', 'name' => 're_password', 
									  'class' => 'form-control', 'placeholder' => 'Repeat your password...'],
									   ['text' => 'Repeat Password', 'class' => 'form-group']); ?>
				<?= HTML::link(['href' => BASE_URL . 'login', 'text' => "Go back"]); ?>
				<?= HTML::submit(['value' => 'Register', 'class' => 'btn btn-block btn-success']); ?>
			</form>
		</div>
	</div>
</div>

<?= $this->end(); ?>