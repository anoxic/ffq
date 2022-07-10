<?php
// filename creates the pathname of a wiki page
function filename(string $n, $prefix = "wiki/"): string
{
    $n = trim($n, "/");
    $n = strtolower($n);
    $n = preg_replace(";[^a-z0-9/];", "_", $n);
    $n = preg_replace(";_+;", "_", $n);
    $n = preg_replace(";/+;", "/", $n);
    $n = $prefix . $n;
    return $n;
}

// gets first line of file, optionally removeing a prefix
function firstln(string $file, string $prefix = "title "): string
{
    $f = fopen($file, 'r');
    $line = fgets($f);
    fclose($f);
    if (substr($line, 0, strlen($prefix)) == $prefix) {
        $line = substr($line, strlen($prefix));
    }
    return trim($line);
}

// attempt to find the wiki file
function tryfind(string $uri, ?array &$children)
{
    $found = 0;
    $path  = filename($uri);
    $file  = null;

    if (file_exists($i = filename($uri, "aka/") . ".txt")) {
        $found++;
        $path = filename(firstln($i));
    }

    if (file_exists($i = "$path.txt")) {
        $found++;
        $file = $i;
    }

    if (is_dir($path)) {
        $found++;

        if (is_array($children)) {
            $children = globl("$path/*");
        }

        if (file_exists($i = "$path/index.txt")) {
            $file = $i;
        }
    }

    return [$found, $file, $path];
}

// make current items bolded in a listing
function breadcrumb(array $listing, string $current = ""): array
{
    foreach ($listing as &$l) {
        $display = str_replace('.txt', '', basename($l));
        $href = str_replace(['wiki/', '//', '.txt'], ['/', '', ''], $l);
        if (str_replace('.txt', '', $l) == $current) {
            $l = "<a href=\"$href\"><b>$display</b></a>";
        } else {
            $l = "<a href=\"$href\">$display</a>";
        }
    }
    return array_reverse($listing);
}

// remove "index.txt" from listing
function globl(string $pattern)
{
    $files = glob($pattern);
    foreach ($files as $k => $c) {
        if (basename($c) == 'index.txt') {
            unset($files[$k]);
        }
    }
    return $files;
}

// parse / render gemtext-ish files
function render(string $path): array
{
    $h    = fopen($path, 'r');
    $meta = [];
    $htm  = '';
    $mode = 'meta'; // values = meta | text | pre
    $pre  = false;  // whether we've written "<pre>" or not

    $link = function($l) {
        $href = strtok($l, " \t");
        $text = trim(substr($l, strlen($href) + 1));
        return "<li><a href=\"$href\">$text</a>";
    };

    while ($l = fgets($h)) {
        if ($mode == 'meta') {
            $l = trim($l);
            $key = strtok($l, " \t");

            if ($key) {
                $meta[$key] = trim(substr($l, strlen($key) + 1));
            } else {
                $mode = 'text';
                continue;
            }
        }

        if ($mode == 'text') {
            $l = trim($l);
            $sigil = strtok($l, " \t");
            $ln = trim(substr($l, strlen($sigil)));

            if ($sigil == '```') {
                $mode = 'pre';
                continue;
            }

            $htm .= match($sigil) {
                '=>'    => $link($ln),
                '#'     => "<h2>$ln</h2>",
                '##'    => "<h3>$ln</h3>",
                '###'   => "<h4>$ln</h4>",
                '>'     => "<blockquote>$ln</blockquote>",
                '*'     => "<li>$ln</li>",
                '['     => "<li><input type=checkbox> " . substr($ln, strpos($ln, ']') + 1),
                '[x]'   => "<li><input type=checkbox checked> $ln",
                default => "<p>$l</p>",
            };

            $htm .= "\n";
        }

        if ($mode == 'pre') {
            $sigil = strtok(trim($l), " \t");

            if (!$pre) {
                $htm .= "<pre>";
                $pre = true;
            }

            if ($sigil == '```') {
                $mode = 'text';
                $htm .= "</pre>";
                $pre = false;
                continue;
            }

            $htm .= htmlentities($l);
        }
    }
    return [$meta, $htm];
}
