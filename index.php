<?php
ini_set('display_errors', 1);


/**
 * You can call all of these "requires" a table of contents
 */

require 'vendor/bento.php';
require 'vendor/Michelf/MarkdownExtra.inc.php';

require 'lib/getfirst.php';          // get first line of a file

require 'lib/http/g.php';            // fetch get/post variables
require 'lib/http/session.php';      // get and set session variables

require 'lib/page/filename.php';     // generate the relative path of a wiki page
require 'lib/page/pagename.php';     // format a pretty page name
require 'lib/page/page.php';         // ::store, ::fetch, and ::listall wiki pages

require 'lib/template/redlinks.php'; // highlight broken links
require 'lib/template/markdown.php'; // compile an extended markdown to html
require 'lib/template/render.php';   // render a php template
require 'lib/template/rtime.php';    // filter unix time into a relative format
require 'lib/template/stack.php';    // list visited pages using session and request

require 'lib/user/user.php';         // ::create, ::store, ::fetch, and ::listall wiki pages
require 'lib/user/auth.php';         // verify a user is logged in, or prompt login


/**
 * General logic
 */

define('RECENT_VISITS', 10);

if (file_exists("sitename"))
    define("SITE_NAME", getfirst("sitename"));
else
    define("SITE_NAME", "Zicki");

if (!file_exists("pages")) mkdir("pages");
if (!file_exists("pages/v")) mkdir("pages/v");
if (!file_exists("users")) mkdir("users");

if (file_exists('private') && !in_array(substr(request_path(),1,1), ['=','-']))
    auth();


/**
 * Routes
 */

get('/', function() {
    render('list.php',
        ['name'=>"All Pages",'list'=>Page::listall(),'all'=>true]);
});

get('/-', function() {
    session_start();
    $_SESSION = [];
    session_destroy();
});

function login_page($_ = "") {
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
    auth();

    if (request_method('POST')) {
        if (Page::store(g("title"), g("content"),
           ['summary'=>g('summary'), 'author'=>session('user')])) 
        {
            if (g("title") != $_)
                unlink(filename($_));

            flash("alert", "Nice update!");
            redirect("/".g("title"));
        } else {
            flash("alert", "Something went wrong here... :-(");
            flash("text", g("content"));
            redirect();
        }
    }

    $page = Page::fetch($_);
    $text = g("text")? g("text") : ($page)? $page->text : "";

    render('edit.php', 
        ['csrf_field'=>csrf_field(), 'text'=>$text, 'page'=>$page, 'name'=>e($_)]);
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
    if ($f = Page::fetch($_, $v)) {
        list($pos, $stack) = stack($_);

        render('view.php', 
            ['file'=>$f, 'name'=>e($_), 'pos'=>$pos, 'stack'=>$stack, 'newer'=>false, 'versions'=>Page::versions($_)]);
    } else
        halt(404);
});

get('/<*:page>', function($_) {
    if (substr($_, -1) == "/" && $list = Page::listall($_))
        render('list.php', ['name'=>$_,'list'=>$list,'all'=>$_=='/']);

    elseif ($f = Page::fetch($_)) {
        list($pos, $stack) = stack($_);

        render('view.php', 
            ['file'=>$f, 'name'=>e($_), 'pos'=>$pos, 'stack'=>$stack, 'newer'=>false, 'versions'=>Page::versions($_)]);
    } else
        halt(404);
});

return run(__FILE__);

