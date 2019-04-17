<?php
// auth checks if the user has started a session,
// and if not, redirects to the login page.
// if a $page is supplied, checks if the user has access to the page
function auth($page = null) {
    if (session('user') == null)
        redirect(substr_replace(request_path(), '=', 1,0));

    if ($page) {
        $h = fopen('../passwords', 'r');
        while ($u = fscanf($h, "%s\t%s\t%s")) {
            if ($u[0] == session('user')) {
                if (!$u[2] || in_array(filename($page, ""), explode(",",$u[2])))
                    return;
            }
        }
        halt(404);
    }
}

