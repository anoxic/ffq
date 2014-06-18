<?php
// redlinks checks if internal links in a text are broken,
// and if so adds the class "redlink" to them
function redlinks($_) {
    preg_match_all("/<a href=\"\/[^'\">]+\">/", $_, $links);

    foreach ($links[0] as $link) {
        preg_match("/(?<=href=\"\/)[^\"]+(?=\")/", $link, $file);

        if (!is_link(filename($file[0])))
            $reps[$link] = preg_replace("/<a/", "<a class=redlink", $link);
    }

    if (isset($reps)) foreach ($reps as $a=>$b)
        $_ = preg_replace("|".$a."|", $b, $_);

    return $_;
}

