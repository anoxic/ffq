<?php
class PageFilter {
    public static function exact($name) {
        $list   = [];
        $filter = $name ? filename($name,'') : ".*";
        $regex  = "|(.*)($filter)(.*)|";
        foreach (scandir('../pages') as $entry) {
            $matches = [];
            if (!is_dir("../pages/$entry") && preg_match($regex, $entry, $matches)) {
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
}
