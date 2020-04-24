<?php


define('CONTROLLERS_NAMESPACE', 'App\Controllers\\');
define('AUTH_CONTROLLERS_NAMESPACE', 'App\Controllers\Auth\\');
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION', 'index');
define('ERROR_CONTROLLER', 'Error');
define('ERROR_ACTION', 'error');
define('ACCESS_RESTRICTED', 'Restricted');

define('MODELS_NAMESPACE', 'App\Models\\');

define('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . 
	   $_SERVER['HTTP_HOST'] . '/files/projects/framework/');

define('NAVBAR_BRAND', 'Mion');
define('SITE_TITLE', 'Mion Framework');
define('MAIN_EMAIL_ADDRESS', 'przemekkrogulski94@gmail.com');
define('DEFAULT_LAYOUT', 'default');
define('DEFAULT_EMAIL_LAYOUT', 'default');

define('SESSION_CSRF_NAME', 'BlRFi9t4snPmZNyr');
define('SESSION_USER_ID_NAME', 'wKxpxiDOCpxhWfLW');
define('SESSION_USER_ACL_NAME', '0LIsz0XxwhpGJ2jz');

define('COOKIE_REMEMBER_ME_NAME', 'ZHeLVQK72rwNMS7ODkiq');
define('COOKIE_REMEMBER_ME_EXPIRY', 2592000);

define('RESET_PASSWORD_TOKEN_NAME', 'eP7G8eYq8zjisfpf9gp9');
define('RESET_PASSWORD_TOKEN_EXPIRY', 3600);

define('ACTIVATE_ACCOUNT_TOKEN_NAME', 'G6bhleik7aFps2B5Ry64');

define('PASSWORD_ALGO', PASSWORD_BCRYPT);

define('DEBUG', true);