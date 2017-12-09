<?php
class PageFilter {
    public static function mergeList() {
        $lists = func_get_args();
        $lists = array_map('self::makeSearchable', $lists);

        $merged = [];

        while (count($lists) > 0) {
            $merged = self::mergeListInner(array_pop($lists), $merged);
        }

        return self::listApplyMask(self::weightedSort($merged));
    }

    public static function prefix($name) {
        $name = filename($name, '');

        $list = array_filter(self::all(), function($n) use ($name) {
            return strpos($n, $name) === 0;
        });

        $list = array_map(function($n) use ($name) {
            $mask = str_repeat('*', strlen($name)) .
                str_repeat('.', strlen($n) - strlen($name));
            return [
                1,
                pagename($n),
                $mask,
            ];
        }, $list);

        return $name ? $list : [];
    }

    public static function exact($name) {
        $list   = [];
        $filter = $name ? filename($name,'') : ".*";
        $regex  = "|$filter|";
        foreach (self::all() as $entry) {
            $matches = [];
            if (preg_match($regex, $entry, $matches)) {
                if ($name) {
                    $replace = str_repeat('*', strlen($name));
                    $mask = preg_replace($regex, $replace, $entry);
                    $mask = preg_replace('|[^*]|', '.', $mask);
                } else {
                    $mask = $entry;
                }
                $list[] = [
                    1,
                    pagename($entry),
                    $mask
                ];
            }
        }
        return $list;
    }

    // ideas
    // 1. split by character and try to do a search in order
    // 2. split by space and try to do a search out of order
    // 3. try to do a phonetic wordsearch
    // 4. remove stop words and prefixes / suffixes
    // 5. find a way to highlight matching parts
    //
    // all filters should return something like
    // [
    //    [$score, $link, $matched_chars],
    //    ...
    // ]

    private static function all() {
        return array_filter(scandir('../pages'), function($n) {
            return is_dir(filename($n)) ? false : $n;
        });
    }

    private static function makeSearchable($list) {
        $o = [];
        foreach ($list as $l) {
            $o[sha1($l[1])] = $l;
        }
        return $o;
    }

    private static function mergeListInner($l1, $l2) {
        // xxx: also merge the highlight

        $merged = [];

        foreach ($l1 as $sha => $i) {
            if (isset($l2[$sha])) {
                $i[0] += $l2[$sha][0] ?: 0;
                $i[2] = self::mergeMask($i[2], $l2[$sha][2]);
            }
            $merged[$sha] = $i;
        }

        foreach ($l2 as $sha => $i) {
            if (!isset($merged[$sha])) {
                $merged[$sha] = $i;
            }
        }

        return $merged;
    }

    private static function mergeMask($m1, $m2) {
        $m1 = strtr($m1, '.*', '01');
        $m2 = strtr($m2, '.*', '01');
        return strtr($m1 | $m2, '01', '.*');
    }

    private static function listApplyMask($list) {
        foreach ($list as &$i) {
            $i[2] = self::applyMask($i[2], $i[1]);
        }
        return $list;
    }

    private static function applyMask($mask, $str) {
        $positive = false;
        $edges = [];

        foreach (str_split($mask) as $k => $v) {
            if ($positive) {
                if ($v == '.') {
                    $positive = false;
                    $edges[] = $k;
                }
            } else {
                if ($v == '*') {
                    $positive = true;
                    $edges[] = $k;
                }
            }
        }

        if (count($edges) & 1) {
            $edges[] = strlen($str);
        }

        while (count($edges)) {
            $stop  = array_pop($edges);
            $start = array_pop($edges);
            $str = substr($str, 0, $stop) . "</b>" . substr($str, $stop);
            $str = substr($str, 0, $start) . "<b>" . substr($str, $start);
        }

        return $str;
    }

    private static function weightedSort($l) {
        usort($l, function($a, $b) {
            if ($a[0] == $b[0]) {
                return strcmp($a[1], $b[1]);
            }
            return $a[0] < $b[0] ? 1 : -1;
        });
        return $l;
    }
}
