<?php
// redlinks checks if internal links in a text are broken,
// and if so adds the class "redlink" to them
function redlinks($_) {
    return preg_replace_callback("/<a href=\"?\/([^'\">]+)\"?>/", function($matches){
        if (is_link(filename($matches[1])))
            return $matches[0];
        else
            return preg_replace("/<a/", "<a class=redlink", $matches[0]);
    }, $_);
}

