<?php
require 'vendor/bento.php';

function name($_) {
	return "pages/".preg_replace("/[^a-zA-Z0-9]/", "~", $_).".md";
}

function g($prop) {
	if (isset($_POST[$prop])) return $_POST[$prop];
	if (isset($_GET[$prop])) return $_GET[$prop];
}

function s($prop, $val = false) {
	session_start();

	if ($val != false)           $_SESSION[$prop] = $val;
	if (isset($_SESSION[$prop])) return $_SESSION[$prop];

	session_write_close();
}

function render($data, $template) {

}

function auth() {
	if (s('user') == null) {
		redirect(substr_replace(request_path(), '=', 1,0));
	}
}

get('/-', function() { session_start(); $_SESSION = []; session_destroy(); });

get('/~<*:page>', function($_) {
	$file = name($_);

	if (file_exists($file)) {
		header("Content-type: text/plain");
		echo file_get_contents($file);
	} else {
		halt(404);
	}
});

form('/=<*:page>', function($_) {
	$users = ["brian test", "jim pass"];

	if (request_method('POST')) {
		foreach ($users as $u) {
			if ($u == g('user')." ".g('pass')) {
				s('user', g('user'));
				redirect($_);
			}
		}

		flash('error', 'Username or password does not match :(');
		flash('user', g('user'));
		redirect();
	}

	echo flash('error')."<form method=post>".csrf_field()
	    ."<input autofocus name=user value=\"".flash('user')."\"><input name=pass type=password>"
	    ."<button>&gt;</button></form>";
});

form('/@<*:page>', function($_) {
	auth();
	$name = name($_);

	if (request_method('POST')) {
		if ( !file_exists("pages/")) {
			mkdir("pages/");
		}

		if (file_put_contents($name, $_POST['content'])) {
			flash('notice', 'Post successfully saved. Yay!');
		} else {
			flash('error', 'Something went horribly wrong :(');
		}
		redirect();
	}

	if (file_exists($name)) {
		$file = e(file_get_contents($name));
	} else {
		$file = "";
	}

	echo "<form method=post><textarea name=content>$file</textarea>"
            .csrf_field()."<button>&gt;</button></form>";
});

form('/%<*:page>', function($_) {
	$file = name($_);

	if ( !file_exists($file)) {
		halt(404);
	}

	auth();

	if (request_method('POST')) {
		unlink($file);
		redirect();
	}

	echo "<form method=post>".csrf_field()
            ."Are you sure you want to delete this page?<button>Yes</button></form>";
});

return run(__FILE__);
