<style>
body {
	margin: 0;
}
input {
	position: absolute;
	top: 0;
	bottom: 0;
	padding: 0;
	margin: 0;
	border: 0;
	outline: 0;
	width: 50%;
	text-align: center;
}
.u {
	left: 0;
}
.g {
	right: 0;
	border-left: 2px solid #EAEFF1;
}
button {
	height: 2em;
	line-height: 1.6em;
	padding: 0 1em;
	position: absolute;
	bottom: 0.3em;
	right: 0.5em;
}
.msg {
	position: absolute;
	text-align: center;
	width: 100%;
	margin: 0;
	line-height: 3.6em;
}
</style>

<form method=post>
	<?= $csrf_field ?>
	<input class=u placeholder=Username autofocus name=user value="<?=$user?>">
	<input class=g placeholder=Password name=pass type=password>
	<button>Login</button>
</form>

<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>

