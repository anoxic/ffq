<?php
require 'vendor/bento.php';

function name($_) {
	return "pages/".preg_replace("/[^a-zA-Z0-9]/", "~", $_).".md";
}

function g($prop) {
	if (isset($_POST[$prop])) return $_POST[$prop];
	if (isset($_GET[$prop])) return $_GET[$prop];
}

function session($prop, $val = false) {
	session_start();

	if ($val != false)           $_SESSION[$prop] = $val;
	if (isset($_SESSION[$prop])) return $_SESSION[$prop];

	session_write_close();
}

function render($file, $data = array()) {
    display_template(__DIR__ . "/views/$file", $data + array(
        'error' => flash('error'),
        'alert' => flash('alert'),
    ));
}

function auth() {
	if (session('user') == null) {
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
				session('user', g('user'));
				redirect($_);
			}
		}

		flash('error', 'Username or password does not match :(');
		flash('user', g('user'));
		redirect();
	}

	render('login.php', ['csrf_field'=>csrf_field(), 'user'=>flash('user')]);
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

	render('edit.php', ['csrf_field'=>csrf_field(), 'file'=>$file]);
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

	render('delete.php', ['csrf_field'=>csrf_field(), 'file'=>$file]);
});

return run(__FILE__);
