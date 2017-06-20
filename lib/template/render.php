<?php
// render gets some "flash" session data + any data supplied
// and renders a PHP template
function render($file, $data = []) {
    display_template("../views/$file", $data + [
        'error'  => flash('error'),
        'alert'  => flash('alert'),
        'notice' => flash('notice'),
    ]);
}

