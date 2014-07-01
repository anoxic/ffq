<style>
body {
    margin: 0;
    text-align: center;
}
button {
	height: 2em;
	line-height: 1.6em;
	padding: 0 1em;
}
.wrapper {
     margin: 7%;
}
.msg {
    line-height: 3.6em;
    background: #F8D6C3;
}
</style>

<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>

<form method=post class=wrapper>
	<?= $csrf_field ?>
	<input class=u placeholder=Username autofocus name=user value="<?=$user?>">
	<input class=g placeholder=Password name=pass type=password>
	<button type=submit>Login</button>
</form>

