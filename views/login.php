<!doctype html>
<html lang=en>
<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title>Login</title>
<style>
:root              { color-scheme: light dark; }
*                  { color:#615555; line-height:1.6; font-family:lucida grande,segoe ui,roboto,sans-serif }
body               { background:#FEFFFA; margin:0; text-align:center }
input              { color:#444; border:2px solid #989e9a; padding:1.3% 3%; font:inherit }
input:focus        { outline:0; border-color:#333 }
[type=submit]      { background:#f48a5d; color:#865642; border-color:#f48a5d; display:inline-block }
[type=submit]:hover{ cursor:pointer }
[role=notice]      { background:#F48A5D; color:#744955; padding:2.5%; text-align:center; margin:0 } 
[role=notice] a    { color:#C36346; border:0 }
.wrapper           { margin: 7%; }
@media (prefers-color-scheme: dark) {
  *                { color: rgb(210, 207, 211); }
  body             { background: rgb(22, 23, 22); }
}
</style>

<?php partial("alert.php", get_defined_vars()); ?>

<form method=post class=wrapper>
	<?= $csrf_field ?>
	<input placeholder=Username name=user value="<?=$user?>" autofocus>
	<input placeholder=Password name=pass type=password>
	<input type=submit value=Login>
</form>


