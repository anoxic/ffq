<?php
ini_set('display_errors', 1);


/**
 * You can call all of these "requires" a table of contents
 */

require '../vendor/bento.php';
require '../vendor/Michelf/MarkdownExtra.inc.php';

require '../src/getfirst.php';          // get first line of a file

require '../src/http/g.php';            // fetch get/post variables
require '../src/http/session.php';      // get and set session variables

require '../src/page/page.php';         // ::store, ::fetch, and ::listall wiki pages
require '../src/page/filename.php';     // generate the relative path of a wiki page
require '../src/page/pagename.php';     // format a pretty page name
require '../src/page/pagefilter.php';   // format a pretty page name

require '../src/template/asset.php';    // load an asset
require '../src/template/markdown.php'; // compile an extended markdown to html
require '../src/template/partial.php';  // load a partial
require '../src/template/redlinks.php'; // highlight broken links
require '../src/template/render.php';   // render a php template
require '../src/template/rtime.php';    // filter unix time into a relative format

require '../src/user/user.php';         // ::create, ::store, ::fetch, and ::listall wiki pages
require '../src/user/auth.php';         // verify a user is logged in, or prompt login


/**
 * General logic
 */

define('RECENT_VISITS', 10);

if (file_exists("../sitename"))
    define("SITE_NAME", getfirst("../sitename"));
else
    define("SITE_NAME", "Zicki");

if (!file_exists("../pages"))   mkdir("../pages");
if (!file_exists("../pages/v")) mkdir("../pages/v");
if (!file_exists("../users"))   mkdir("../users");

if (file_exists('../private') && !in_array(substr(request_path(),1,1), ['=','-']))
    auth();


/**
 * Routes
 */

get('/', function() {
    render('list.php',
        ['name'=>"All Pages",'list'=>Page::listall('/', session('user')),'all'=>true]);
});

get('/-', function() {
    session_start();
    $_SESSION = [];
    session_destroy();
});

function login_page($_ = "") {
    if (request_method('POST')) {
        $hash = password_hash(g('pass'), PASSWORD_BCRYPT, ['salt' => sha1(g('user'))]);
        $test = trim(g('user'))."\t".$hash;
        foreach (file("../passwords") as $u) {
            if (substr(trim($u), 0, strlen($test)) == $test) {
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

get('/filter', function() {
    auth("filter");

    $list = Page::filter(g("q"));

    if (strpos($_SERVER['HTTP_ACCEPT'],'json') !== false) {
        echo json_encode($list);
    } else {
        render('filter.php', compact('list'));
    }
});

form('/register', function() {
    if (request_method('POST')) {
        if (g('pw') != g('pw_'))
            flash_redirect(g()+['error'=>'Passwords must match!']);

        $fields = ['uname'=>'Username','mail'=>'Email Address','bio'=>'Bio'];

        foreach ($fields as $u=>$n) {
            if (empty($_POST[$u]))
                flash_redirect(g()+['error'=>"$n is required"]);
        }

        $user = User::create([
            'uname' => g('uname'), // probably we need to impose limits on usernames
            'email' => g('email'),
            'pw'    => g('pw'),
        ]);

        if (User::store($user)) {
            Page::store('~'.g('uname'), g("bio"));
            flash_redirect(['alert'=>'User added!'],'/registered');
        } else

        flash('error', 'Something went wrong');
        redirect();
    }

    render('register.php');
});

form('/:<*:page>', function($_) {
    auth($_);

    if (request_method('POST')) {
        if (Page::store(g("slug"), g("content"),
           ['summary'=>g('summary'), 'title'=>g('title'), 'author'=>session('user')])) 
        {
            if (g("slug") != $_)
                unlink(filename($_));

            flash("alert", "Nice update!");
            redirect("/".g("slug"));
        } else {
            flash("alert", "Something went wrong here... :-(");
            flash("text", g("content"));
            redirect();
        }
    }

    $page = Page::fetch($_);
    $text = g("text") ? g("text") : ($page) ? $page->text : "";
    $title = g("title") ? g("title") : isset($page->header['title'])
        ? $page->header['title'] : e($_);

    render(
        'edit.php', 
        [
            'csrf_field'=>csrf_field(),
            'text'=>$text,
            'page'=>$page,
            'slug'=>e($_),
            'title'=>$title,
        ]
    );
});

form('/!<*:page>', function($_) {
    auth($_);

    $file = filename($_);

    if (!file_exists($file))
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
    auth($_);

    if ($f = Page::fetch($_, $v)) {
        $title = g("title") ? g("title") : isset($f->header['title'])
            ? $f->header['title'] : e($_);
        render('view.php', 
            ['file'=>$f, 'slug'=>e($_), 'title'=>$title, 'newer'=>false, 'versions'=>Page::versions($_)]);
    } else {
        halt(404);
    }
});

get('/\\*<*:page>', function($_) {
    auth($_);

    if ($f = Page::fetch($_)) {
        $v = $f->version;
        $list = [];
        do {
            $f = Page::fetch($_, $v);
            $list[$v] = [
                "author" => $f->header['author'],
                "time" => $f->time,
                "summary" => $f->header['summary'],
                "size" => $s = strlen($f->text),
                "delta" => $s - (isset($list[$v+1]) ? $list[$v+1]['size'] : 0),
            ];
        } while ($v--);
        render('revisions.php', ['list'=>$list, 'name'=>e($_)]);
    } else {
        halt(404);
    }
});

get('/<*:page>', function($_) {
    auth($_);

    if (substr($_, -1) == "/" && $list = Page::listall($_)) {
        render('list.php', ['name'=>$_,'list'=>$list,'all'=>$_=='/']);
    } elseif ($f = Page::fetch($_)) {
        $title = g("title") ? g("title") : isset($f->header['title'])
            ? $f->header['title'] : e($_);
        render('view.php', 
            ['file'=>$f, 'slug'=>e($_), 'title'=>$title, 'newer'=>false, 'versions'=>Page::versions($_)]);
    } else {
        halt(404);
    }
});

return run(__FILE__);

