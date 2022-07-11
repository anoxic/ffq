<?php
$u = $uf = $pf = $error = '';

if ($method == 'POST') {
    if (login($post['u'], $post['p'])) {
        $redirect = "/" . substr($uri, 2);
        $id = random_int(PHP_INT_MIN, PHP_INT_MAX);
        $uname = filename($post['u'], '');
        $headers['Set-Cookie']  = "wiki_user=$uname; path=/";
        $headers['Set-Cookie '] = "wiki_session=$id; path=/";
        wiki_session_storage($id, 'wiki_user', $uname);
        return;
    }
    $error = "Try again!";
    $code = 401;
}

// refill 'u' and autofocus elem
$u = isset($post['u']) ? htmlspecialchars($post['u'], ENT_QUOTES) : '';
if ($u) {
    $pf = 'autofocus';
} else {
    $uf = 'autofocus';
}
?>
<!doctype html>
<html lang=en>
<head>
    <?=require('src/meta.php')?>
    <title>Login</title>
    <style>
    *             { text-align: center; margin: 0; padding: 0; outline: none; font: inherit; background: inherit }
    body          { background: #872e4e; font: bold 120%/1.5 times, serif }
    input         { background: #141414; color: #f2efea; border: 0; border-radius: 5px; padding: .425em 0; caret-color: #872e4e }
    ::placeholder { color: #872e4e }
    [type=submit] { visibility: hidden }
    .login        { width: 100%; display: flex; align-content: stretch; gap: .3em }
    .login >*     { flex-grow: 1 }
    @supports(padding: max(0px)) {
        body {
            padding-top: max(.6em, env(safe-area-inset-top));
            padding-right: max(.6em, env(safe-area-inset-right));
            padding-bottom: max(.6em, env(safe-area-inset-bottom));
            padding-left: max(.6em, env(safe-area-inset-left));
        }
    }
    </style>
<head>
<body>
    <?=$error?>

    <form method=post>
        <div class=login>
            <input name=u <?=$uf?> value="<?=$u?>" autocomplete=username placeholder=Username tabindex=1>
            <input name=p <?=$pf?> type=password autocomplete=current-password placeholder=Password tabindex=2>
        </div>
        <input type=submit>
    </form>
</body>
</html>
