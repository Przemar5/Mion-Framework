<?php

use Core\Classes\Router;
use Core\Classes\URL;

$menu = Router::getMenu('menu_acl');
//dd($menu);
//echo URL::currentUrl();

?>

<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
	<a class="navbar-brand" href="<?php echo ROOT; ?>">
  		<?php echo NAVBAR_BRAND; ?>
  	</a>
  	
  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_menu" aria-controls="main_menu" aria-expanded="false" aria-label="Toggle navigation">
  		<span class="navbar-toggler-icon"></span>
  	</button>

	<div class="collapse navbar-collapse" id="main_menu">
		<ul class="navbar-nav">
			<?php foreach ($menu as $key => $value): ?>
				<?php if (is_array($value)): ?>
					<li class="nav-item">
						<a class="nav-link dropdown" href="<?= $value; ?>" id="nambarDropdown" role="button">
							<?= $key; ?>
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<?php foreach ($value as $k => $v): 
								$active = (URL::currentUrl() === $v) ? 'active' : ''; ?>
								<?php if ($k == 'separator'): ?>
									<div class="dropdown-divider"></div>
								<?php else: ?>
									<a href="#" class="dropdown-item <?= $active; ?>">
										<?= $k; ?>
									</a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</li>
				<?php else: 
					$active = (URL::currentUrl() === $value) ? 'active' : ''; ?>
					<li class="nav-item">
						<a href="<?= $value; ?>" class="nav-link <?= $active; ?>">
							<?= $key; ?>
						</a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>