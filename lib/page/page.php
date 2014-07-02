<?php
// functions for storing, fetching, and listing pages
class Page {
    public static function listall($dir = "/") {
        $dir  = "|^".filename($dir,'')."*|";
        $list = [];

        foreach (scandir('pages') as $entry) {
            if (!is_dir("pages/$entry") && preg_match($dir, $entry))
                $list[] = pagename($entry);
        }

        return count($list)>0 ? $list : null;
    }

    public static function fetch($_, $v = null) {
        if     ($v !== null)           $v = filename($_, "pages/v/")."~".$v;
        elseif (is_link(filename($_))) $v = readlink(filename($_));

        if (file_exists($v)) {
            $page = new self;
            $page->version = explode("~", $v)[1];
            $page->text = file_get_contents($v);
            $page->time = filemtime($v);

            if (substr($page->text, 0, 2) == "\0:") { # get the header, if it exists
                $t = explode("\n", $page->text);
                parse_str(substr(array_shift($t), 2), $page->header);
                $page->text = join("\n", $t);
            }
            return $page;
        }
    }

    public static function store($name, $contents, $header) {
        $link = filename($name);

        if (file_exists($link)) unlink($link);
        
        if (is_link($link) && $last = readlink($link)) {
            preg_match("/~\d+/", $last, $next);
            $next = filename($name, "pages/v/")
                  ."~". (substr(reset($next), 1) + 1);
            unlink($link);
        }

        if (empty($next)) 
            $next = filename($name, "pages/v/") ."~0";

        $contents = "\0:". http_build_query($header) ."\n". $contents;

        if (@file_put_contents($next, $contents))
            return symlink($next, $link);
    }
}
