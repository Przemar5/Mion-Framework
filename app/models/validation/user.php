<?php

$rules = [
	"username" => [
		"required" => [
			"args" => [],
			"msg" => "Username is required."
		],
		"min" => [
			"args" => [6],
			"msg" => "Username must be equal or longer than 6 characters."
		],
		"max" => [
			"args" => [50],
			"msg" => "Username must be equal or shorter than 50 characters."
		],
		"pattern-match" => [
			"args" => ["/^[0-9a-zA-Z \~\`\!\@\#|$\%\^\*\(\)\-\_\=\+\[\]\{\}\|\:\;\,\.\<\>\/\?]+$/"],
			"msg" => "Username contains illegal characters."
		],
		"unique" => [
			"args" => ["user", "username"],
			"msg" => "Given username already exists in our database. Please choose another one."
		]
	],
	"email" => [
		"required" => [
			"args" => [],
			"msg" => "Email address is required."
		],
		"min" => [
			"args" => [6],
			"msg" => "Email address must be equal or longer than 6 characters."
		],
		"max" => [
			"args" => [255],
			"msg" => "Email address must be equal or shorter than 255 characters."
		],
		"pattern-match" => [
			"args" => ['/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD'],
			"msg" => "Given value isn't valid email address."
		],
		"unique" => [
			"args" => ["user", "email"],
			"msg" => "Given email already exists in our database. Please choose another one."
		]
	],
	"first_name" => [
		"required" => [
			"args" => [],
			"msg" => "First name is required."
		],
		"min" => [
			"args" => [2],
			"msg" => "First name must be equal or longer than 2 characters."
		],
		"max" => [
			"args" => [50],
			"msg" => "First name must be equal or shorter than 50 characters."
		],
		"pattern-match" => [
			"args" => ["/^[a-zA-Z]*$/"],
			"msg" => "First name may contains only letters."
		]
	],
	"last_name" => [
		"required" => [
			"args" => [],
			"msg" => "Last name is required."
		],
		"min" => [
			"args" => [2],
			"msg" => "Last name must be equal or longer than 2 characters."
		],
		"max" => [
			"args" => [100],
			"msg" => "Last name must be equal or shorter than 100 characters."
		],
		"pattern-match" => [
			"args" => ["/^[a-zA-Z\-]+$/"],
			"msg" => "Last name may contains only letters and hyphens."
		]
	],
	"password" => [
		"required" => [
			"args" => [],
			"msg" => "You must enter a password."
		],
		"min" => [
			"args" => [6],
			"msg" => "Your password must be equal or longer than 6 characters."
		],
		"max" => [
			"args" => [50],
			"msg" => "Your password must be equal or shorter than 50 characters."
		],
		"pattern-match" => [
			"args" => ["/^[0-9a-zA-Z _\-\=\+\[\]\{\}\;\:\/\?\,\.\<\>\|\!\@\#\$\%\^\&\*\(\)]+$/"],
			"msg" => "Your password contains invalid characters."
		]
	]
];