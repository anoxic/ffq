<?php
$headers["Set-Cookie"]  = "wiki_user=; Max-Age=0;";
$headers["Set-Cookie "] = "wiki_session=; Max-Age=0;"; // trailing space tricks swoole into sending multiple headers
$redirect = '/';
echo 'You have been logged out.';

