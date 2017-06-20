<?php
// auth checks if the user has started a session,
// and if not, redirects to the login page
function auth() {
    if (session('user') == null)
        redirect(substr_replace(request_path(), '=', 1,0));
}

