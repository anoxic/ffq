<style>
body {
    margin: 0;
    text-align: center;
}
input, button {
    border: 1px solid;
    padding: 1.5% 2%;
}
.msg {
    background: #F8D6C3;
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

