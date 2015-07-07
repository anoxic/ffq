<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title>Login</title>
<style>
body {
    margin: 0;
    text-align: center;
}
input {
    border: 1px solid #419CB9;
    padding: 1.5% 3%;
    outline: 0;
}
input[type=submit] {
    background: #419CB9;
    color: #fff;
}
.msg {
    background: #E2E2E2;
    font-style: italic;
    padding: 2.5%;
}
.wrapper {
     margin: 7%;
}
</style>

<?php if ($error || $alert || $notice): ?>
<p class=msg> <?= $error ?> <?= $alert ?> <?= $notice ?> </p>
<? endif; ?>

<form method=post class=wrapper>
	<?= $csrf_field ?>
	<input class=u placeholder=Username autofocus name=user value="<?=$user?>">
	<input class=g placeholder=Password name=pass type=password>
	<input type=submit value=Login>
</form>

