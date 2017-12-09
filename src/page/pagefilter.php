<?php
class PageFilter {
    public static function mergeList() {
        $lists = func_get_args();
        $lists = array_map('self::makeSearchable', $lists);

        $merged = [];

        while (count($lists) > 0) {
            $merged = self::mergeListInner(array_pop($lists), $merged);
        }

        return self::weightedSort($merged);
    }

    public static function prefix($name) {
        $name = filename($name, '');

        $list = array_filter(self::all(), function($n) use ($name) {
            return strpos($n, $name) === 0;
        });

        $list = array_map(function($n) use ($name) {
            return [
                1,
                pagename($n),
                pagename(
                    '<b>' .
                    substr($n, 0, strlen($name)) .
                    "</b>" .
                    substr($n, strlen($name))
                ),
            ];
        }, $list);

        return $name ? $list : [];
    }

    public static function exact($name) {
        $list   = [];
        $filter = $name ? filename($name,'') : ".*";
        $regex  = "|(.*)($filter)(.*)|";
        foreach (self::all() as $entry) {
            $matches = [];
            if (preg_match($regex, $entry, $matches)) {
                $replace = $name ? preg_replace($regex, '$1<b>$2</b>$3', $entry) : $entry;
                $list[] = [
                    1,
                    pagename($entry),
                    pagename($replace)
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
