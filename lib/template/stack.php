<?php
// get a stack of recent visits

function stack($_) {
    if (! $stack = session('view_stack')) $stack = [];
    if (! $pos = g('pos'))                $pos = 0;

    if (reset($stack) != $_) {
        array_unshift($stack, $_);
        $stack = array_slice($stack, 0,RECENT_VISITS);
        session('view_stack', $stack);
    }

    return [$pos, $stack];
}
