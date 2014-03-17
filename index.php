<?php
require 'vendor/bento.php';
require 'vendor/Michelf/MarkdownExtra.inc.php';

if (!file_exists("pages")) mkdir("pages");
if (!file_exists("pages/v")) mkdir("pages/v");


function g($prop) {
    if (!$prop)
        return array_merge($_GET, $_POST);
    if (isset($_POST[$prop]))
        return $_POST[$prop];
    if (isset($_GET[$prop]))
        return $_POST[$prop];
}

function session($prop, $val = false) {
	if (!session_id()) session_start();

	if ($val)
		return $_SESSION[$prop] = $val;
	else
		if (isset($_SESSION[$prop])) return $_SESSION[$prop];

	session_write_close();
}

function render($file, $data = []) {
	display_template(__DIR__ . "/views/$file", $data + [
		'error'  => flash('error'),
		'alert'  => flash('alert'),
		'notice' => flash('notice'),
	]);
}

function auth() {
	if (session('user') == null)
		redirect(substr_replace(request_path(), '=', 1,0));
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

function filename($n = "", $prefix = "pages/") {
    if ($n < 0) $prefix = "";
    if (empty($n) || $n < 0) $n = substr(request_path(), 1);

    $n = preg_replace("; +;", " ", $n);
    $n = preg_replace(";/;", ".", $n);
    $n = preg_replace(";[^a-z.];", "-", strtolower($n));

    return $prefix . $n;
}

function page_fetch($_ = null, $v = null) {
    if ($v != null)
        return file_get_contents(filename($_, "pages/v/")."~".$v);
    if (is_link(filename($_)))
        return file_get_contents(readlink(filename($_)));

    return false;
}

function page_store($name, $contents) {
    $link = filename($name);

    if (file_exists($link)) unlink($link);
    
    if (is_link($link)) {
        $last = readlink($link);
        preg_match("/~\d+/", $last, $next);
        $next = filename($name, "pages/v/")
              . "~" . (substr(reset($next), 1) + 1);
        unlink($link);
    }

    if (empty($next)) 
        $next = filename($name, "pages/v/") . "~0";

    if (@file_put_contents($next, $contents))
        return symlink($next, $link);

    return false;
}

function markdown($_) {
		$parser = new \Michelf\MarkdownExtra;

        $_ = preg_replace(
            "/(<~([^>]+)>)/", '<a href="/~$2">$2</a>', $_);
        $_ = preg_replace(
            "/- +\[ ?\]/", '- <input type=checkbox disabled>', $_);
        $_ = preg_replace(
            "/- +\[x\]/", '- <input type=checkbox checked disabled>', $_);

		return $parser->transform($_);
}

get('/-', function() { session_start(); $_SESSION = []; session_destroy(); });

form('/@<*:page>', function($_) {
	auth();

	if (request_method('POST')) {
        if (page_store($_, g("content")))
            flash("alert", "Nice update!");
        else {
            flash("alert", "Something went wrong here... :-(");
            flash("text", g("content"));
        }

        redirect();
	}

    if  ($file = g("text"));
    else $file = page_fetch($_);

	if ($file) {
        $md = markdown($file);
		$time = rtime(filemtime(readlink(filename($_))));
	} else {
        $md = "";
        $time = "never";
    }

    render('edit.php', 
        ['csrf_field'=>csrf_field(), 'file'=>$file,
        'formatted'=>$md, 'name'=>e($_), 'time'=>$time]);
});

form('/!<*:page>', function($_) {
	auth();

	$file = name($_);

	if ( !file_exists($file))
		halt(404);

	if (request_method('POST')) {
		unlink($file);
		redirect();
	}

	render('delete.php', ['csrf_field'=>csrf_field(), 'file'=>$file]);
});

form('/=<*:page>', function($_) {
	if (request_method('POST')) {
		foreach (file("passwords") as $u) {
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

get('/<*:page>~<#:version>', function($_, $v) {
	if ($f = page_fetch($_, $v)) 
        render('view.php', 
            ['file'=>markdown($f), 'name'=>e($_), 'time'=>$time]);
	else
		halt(404);
});

get('/<*:page>', function($_) {
	if ($f = page_fetch($_)) {
        render('view.php', 
            ['file'=>markdown($f), 'name'=>e($_), 'time'=>$time]);
    }
	else
		halt(404);
});

get('/', function() {
	if ($handle = opendir('pages')) {
		echo "<link rel=stylesheet href=src/wiki.css>";
        echo "<hgroup>"
           . "<h1>All Pages</h1>"
           . "<a class=edit href=javascript:window.location='/@'+prompt()>"
           . "new</a>"
           . "</hgroup>";
		echo "<ul class=list>";

		while (false !== ($entry = readdir($handle))) {
			if (!is_dir("pages/$entry")) {
				$name = ucwords(str_replace("~", " ", $entry));
				echo "<li><a href=\"/$name\">" . $name . "</a></li>";
			}
		}
		closedir($handle);

		echo "</ul>";
	}
});


return run(__FILE__);

