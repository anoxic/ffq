<?php
// functions for storing, fetching, and listing pages

class Page {
    public static function listall($dir = "/") {
        $dir  = "|^".filename($dir,'')."*|";
        $list = [];

        foreach (scandir('../pages') as $entry) {
            if (!is_dir("../pages/$entry") && preg_match($dir, $entry))
                $list[] = pagename($entry);
        }

        return count($list)>0 ? $list : [];
    }

    public static function filter($filter = "/") {
        $list = PageFilter::mergeList(
            PageFilter::prefix($filter),
            PageFilter::exact($filter)
        );
        return $list;
    }

    public static function versions($name) {
        $versions = [];

        foreach (scandir('../pages/v') as $i) {
            $n1 = filename($name,'')."~";
            $n2 = substr($i,0,strlen($n1));

            if ($n1 == $n2) {
                preg_match("/\d+$/", $i, $match);
                $versions[] = reset($match);
            }
        }
        natcasesort($versions);

        return $versions;
    }

    public static function latest($name) {
        return end(self::versions($name));
    }

    public static function fetch($_, $v = null) {
        if     ($v !== null)           $v = filename($_, "../pages/v/")."~".$v;
        elseif (file_exists(filename($_))) $v = trim("../".file_get_contents(filename($_)));

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
        $next = self::get_next($name);

        $contents = "\0:". http_build_query($header) ."\n". $contents;

        if (file_put_contents("../" . $next, $contents)) {
            if (file_exists($link)) {
                unlink($link);
            }
            return file_put_contents($link, $next);
        }
    }

    public static function get_next($name)
    {
        $link = filename($name);

        if (file_exists($link) && $last = trim(file_get_contents($link))) {
            preg_match("/~\d+/", $last, $next);
            $next = filename($name, "pages/v/")
                  ."~". (substr(reset($next), 1) + 1);
        }

        if (empty($next)) {
            $next = filename($name, "pages/v/") ."~0";
        }

        return $next;
    }
}

