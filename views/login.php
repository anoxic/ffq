<style>
body {
    margin: 7em;
    text-align: center;
}
button {
	height: 2em;
	line-height: 1.6em;
	padding: 0 1em;
}
.msg {
	text-align: center;
	width: 100%;
	margin: 0;
	line-height: 3.6em;
    position: absolute;
    top: 0;
    left: 0;
}
</style>

<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>

<form method=post>
	<?= $csrf_field ?>
	<input class=u placeholder=Username autofocus name=user value="<?=$user?>">
	<input class=g placeholder=Password name=pass type=password>
	<button>Login</button>
</form>

