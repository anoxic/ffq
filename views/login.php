<title>Login</title>
<style>
body {
    margin: 0;
    text-align: center;
}
input, button {
    border: 1px solid #419CB9;
    padding: 1.5% 3%;
    outline: 0;
}
button {
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
	<button type=submit>Login</button>
</form>

