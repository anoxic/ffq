<?php
require 'vendor/bento.php';
require 'vendor/Michelf/MarkdownExtra.inc.php';
use \Michelf\MarkdownExtra;

function name_filter($_) {
	return strtolower(preg_replace("/[^a-zA-Z0-9]/", "~", $_));
}

function name($_) {
	return "pages/".name_filter($_).".md";
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
        'notice' => flash('notice'),
    ));
}

function auth() {
	if (session('user') == null) {
		redirect(substr_replace(request_path(), '=', 1,0));
	}
}

function rtime($time) {
	define("SECOND", 1);
	define("MINUTE", 60 * SECOND);
	define("HOUR", 60 * MINUTE);
	define("DAY", 24 * HOUR);
	define("MONTH", 30 * DAY);

	$delta = time() - $time;

	if ($delta < 1 * MINUTE)  return $delta == 1 ? "one second ago" : $delta . " seconds ago";
	if ($delta < 2 * MINUTE)  return "a minute ago";
	if ($delta < 45 * MINUTE) return floor($delta / MINUTE) . " minutes ago";
	if ($delta < 90 * MINUTE) return "an hour ago";
	if ($delta < 24 * HOUR)   return floor($delta / HOUR) . " hours ago";
	if ($delta < 48 * HOUR)   return "yesterday";
	if ($delta < 30 * DAY)    return floor($delta / DAY) . " days ago";

	if ($delta < 12 * MONTH) {
		$months = floor($delta / DAY / 30);
		return $months <= 1 ? "one month ago" : $months . " months ago";
	} else {
		$years = floor($delta / DAY / 365);
		return $years <= 1 ? "one year ago" : $years . " years ago";
	}
}

get('/-', function() { session_start(); $_SESSION = []; session_destroy(); });

get('/~', function() {
	if ($handle = opendir('pages')) {
		echo "<link rel=stylesheet href=src/wiki.css>";
		echo "<hgroup><h1>All Pages</h1><a class=edit href=javascript:window.location='/@'+prompt()>new</a></hgroup>";
		echo "<ul class=list>";

		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				$name = substr($entry, 0, -3);
				echo "<li><a href=\"/~$name\">" . ucwords(str_replace("~", " ", $name)) . "</a></li>";
			}
		}
		closedir($handle);

		echo "</ul>";
	}
});

get('/~<*:page>', function($_) {
	$file = name($_);

	if (file_exists($file)) {
		$parser = new MarkdownExtra;

		$f = file_get_contents($file);
		$f = preg_replace("/(<~([^>]+)>)/", '<a href="/~$2">$2</a>', $f);
		$f = preg_replace("/- +\[ ?\]/", '- <input type=checkbox disabled>', $f);
		$f = preg_replace("/- +\[x\]/", '- <input type=checkbox checked disabled>', $f);
		$f = $parser->transform($f);

		render('view.php', ['file'=>$f, 'name'=>e($_), 'time'=>$time]);
	} else {
		halt(404);
	}
});

form('/=<*:page>', function($_) {
	$users = file("passwords");

	if (request_method('POST')) {
		foreach ($users as $u) {
			if (trim($u) == g('user')." ".g('pass')) {
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
		$file = file_get_contents($name);
		$parser = new MarkdownExtra;

		$md = $file;
		$md = preg_replace("/(<~([^>]+)>)/", '<a href="/~$2">$2</a>', $md);
		$md = preg_replace("/- +\[ ?\]/", '- <input type=checkbox disabled>', $md);
		$md = preg_replace("/- +\[x\]/", '- <input type=checkbox checked disabled>', $md);

		$md = $parser->transform($md);
		$time = rtime(filemtime($name));
	} else {
		$file = "";
		$time = "never";
	}

	render('edit.php', ['csrf_field'=>csrf_field(), 'file'=>$file, 'formatted'=>$md, 'name'=>e($_), 'time'=>$time]);
});

form('/!<*:page>', function($_) {
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
