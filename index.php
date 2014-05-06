<?php
require 'vendor/bento.php';
require 'vendor/Michelf/MarkdownExtra.inc.php';

if (!file_exists("pages")) mkdir("pages");
if (!file_exists("pages/v")) mkdir("pages/v");

define('RECENT_VISITS', 10);

/**
 *           *.*. TABLE of CONTENTS .*.*
 *
 * http ~*
 *   g - fetch get/post variables
 *   session - get and set session variables
 *
 * templates ~*
 *   markdown - compile an extended markdown to html
 *   render - render a php template
 *   rtime - filter unix time into a relative format
 *
 * objects ~*
 *   filename - generate the relative path of a wiki page
 *   list_pages - list all pages in the wiki
 *   Page - store and fetch wiki pages
 *
 * route helpers ~*
 *   auth - verify a user is logged in, or log them in
 *
 * routes ~*
 *   index   /
 *   login   /=
 *   logout  /-
 *   edit    /:page
 *   delete  /!page
 *   page v. /page~x
 *   page    /page
 */

function g($prop) {
    if (!$prop)
        return array_merge($_GET, $_POST);
    if (isset($_POST[$prop]))
        return $_POST[$prop];
    if (isset($_GET[$prop]))
        return $_GET[$prop];
}

function session($prop, $val = false) {
	if (!session_id()) session_start();

	if ($val)
		return $_SESSION[$prop] = $val;
	else
		if (isset($_SESSION[$prop])) return $_SESSION[$prop];
}

function markdown($_) {
		$parser = new \Michelf\MarkdownExtra;

        $_ = preg_replace(
            "/(<~([^>]+)>)/", '<a href="/$2">$2</a>', $_);
        $_ = preg_replace(
            "/- +\[ ?\]/", '- <input type=checkbox disabled>', $_);
        $_ = preg_replace(
            "/- +\[x\]/", '- <input type=checkbox checked disabled>', $_);

        # Redlinks
        #
        preg_match_all("/<a href=\"\/[^'\">]+\">/", $_, $m);
        $replacements = [];

        foreach ($m[0] as $mat) {
            if (!isset($replacements[$mat])) {
                preg_match("/(href=\"\/)([^\"]+)(\")/", $mat, $e);

                if (!is_link(filename($e[2])))
                    $replacements[$mat] = preg_replace("/<a/", "<a class=redlink", $mat);
            }
        }

        foreach ($replacements as $a=>$b) {
            $_ = preg_replace("|".$a."|", $b, $_);
        }

		return $parser->transform($_);
}

function render($file, $data = []) {
	display_template(__DIR__ . "/views/$file", $data + [
		'error'  => flash('error'),
		'alert'  => flash('alert'),
		'notice' => flash('notice'),
	]);
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

function list_pages($dir = "/") {
    $list = [];

    if ($handle = opendir('pages')) {
        $regex = "|^".filename($dir,'')."*|";

        while (false !== ($entry = readdir($handle))) {
            if (!is_dir("pages/$entry") && preg_match($regex, $entry)) {
                $name = ucwords(str_replace("-", " ",
                    str_replace(".", "/", $entry)));
                $list[] = $name;
            }
        }
        closedir($handle);
    }

    if (count($list) < 1)
        return false;

    natcasesort($list);

    foreach ($list as $k=>$v) {
        if (substr($v, 0,1) == ' ')
            $list[$k] = substr_replace($v, '~', 0,1);
    }

    return $list;
}

class Page {
    public $version;
    public $text;
    public $time;
    public $header;
    
    public function fetch($_ = null, $v = null) {
        $page = new self;

        if ($v != null)
            $v = filename($_, "pages/v/")."~".$v;
        elseif (is_link(filename($_)))
            $v = readlink(filename($_));

        if (file_exists($v)) {
            $page->version = explode("~", $v)[1];
            $page->text = file_get_contents($v);
            $page->time = filemtime($v);

            # get the header
            if (substr($page->text, 0, 2) == "\0:") {
                $t = explode("\n", $page->text);
                parse_str(substr(array_shift($t), 2), $page->header);
                $page->text = join("\n", $t);
            }

            return $page;
        }

        return false;
    }

    public function store($name, $contents, $header) {
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

        $contents = "\0:" . http_build_query($header) . "\n" . $contents;

        if (@file_put_contents($next, $contents))
            return symlink($next, $link);

        return false;
    }
}

function auth() {
    if (session('user') == null)
        redirect(substr_replace(request_path(), '=', 1,0));
}


/*. *.*.*.* *.*.*.* *.*.*.* *.*.*.* .*/

if (file_exists('private') && !in_array(substr(request_path(),1,1), ['=','-']))
    auth();


get('/', function() {
    render('list.php',
        ['name'=>"All Pages",'list'=>list_pages(),'all'=>true]);
});

get('/-', function() {
    session_start();
    $_SESSION = [];
    session_destroy();
});

function login_page($_) {
	if (request_method('POST')) {
		foreach (file("passwords") as $u) {
			if (trim($u) == g('user')." ".g('pass')) {
				session('user', g('user'));
				redirect("/".$_);
			}
		}

		flash('error', 'Access denied! Please try again.');
		flash('user', g('user'));
		redirect();
	}

    render('login.php',
        ['csrf_field'=>csrf_field(), 'user'=>flash('user')]);
}
form('/=', 'login_page');
form('/=<*:page>', 'login_page');


form('/:<*:page>', function($_) {
	auth();

	if (request_method('POST')) {
        if (Page::store($_, g("content"), ['summary'=>g('summary'), 'author'=>session('user')])) {
            flash("alert", "Nice update!");
            redirect("/".$_);
        } else {
            flash("alert", "Something went wrong here... :-(");
            flash("text", g("content"));
            redirect();
        }
	}

    if  ($file = g("text"));
    else $file = Page::fetch($_)->text;

	if ($file) {
        $md = markdown($file);
	} else {
        $md = "";
    }

    render('edit.php', 
        ['csrf_field'=>csrf_field(), 'file'=>$file,
         'formatted'=>$md, 'name'=>e($_)]);
});

form('/!<*:page>', function($_) {
	auth();

	$file = filename($_);

	if ( !is_link($file))
		halt(404);

	if (request_method('POST')) {
		if (unlink($file))
            flash("notice", "Successfully deleted $_");
        else
            flash("error", "Could not delete $_");

        redirect("/");
	}

	render('delete.php', ['csrf_field'=>csrf_field(), 'file'=>$file]);
});

get('/<*:page>~<#:version>', function($_, $v) {
	if ($f = Page::fetch($_, $v)) 
        render('view.php', 
            ['file'=>markdown($f->text), 'name'=>e($_), 'time'=>$f->time,
             'version'=>$v, 'fname'=>filename($_,''), 'newer'=>true]);
	else
		halt(404);
});

get('/<*:page>', function($_) {
    if (substr($_, -1) == "/") {
        if ($list = list_pages($_))
            render('list.php', ['name'=>$_,'list'=>$list]);
    }
    elseif ($f = Page::fetch($_)) {
        if ($pos = g('pos'))
            $stack = session('view_stack');
        else {
            $pos = 0;

            if (! $stack = session('view_stack'))
                $stack = [];

            if ($stack[0] != $_) {
                array_unshift($stack, $_);
                $stack = array_slice($stack, 0,RECENT_VISITS);
                session('view_stack', $stack);
            }
        }

        render('view.php', 
            ['file'=>markdown($f->text), 'name'=>e($_),
             'pos'=>$pos, 'fname'=>filename($_,''), 'time'=>$f->time,
             'version'=>$f->version, 'head'=>$f->header, 'stack'=>$stack]);
    }
	else
		halt(404);
});

return run(__FILE__);

