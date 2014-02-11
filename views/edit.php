<!doctype html>
<html>
<head>
	<title><?=$name?> * Edit</title>
	<style>
	html, body {
		height: 100%;
		min-height: 100%;
		margin: 0;
	}
	hgroup {
		border-bottom: 2px solid #A7B7CC;
		padding: 3px 1em 0;
		height: 3.9em;
	}
	h1 {
		margin: 0;
		font-weight: normal;
		color: #8CA6B3;
	}
	h2 {
		margin: 0 0 .3em;
		font-family: sans-serif;
		font-size: 88%;
		color: #8CA6B3;
	}
	a {
		text-decoration: none;
		color: #274A69;
		margin: 0 .1em;
	}
	a:hover {
		border-bottom: 1px dotted;
	}
	textarea {
		background: #F7F9FA;
		border: none;
		position: absolute;
		top: 5em;
		bottom: 0;
		margin: 0;
		padding: 1em;
		width: 100%;
		outline: 0;
	}
	button {
		font-size: 1.5em;
		height: 2em;
		line-height: 1.6em;
		padding: 0 1em;
		position: absolute;
		top: 0.3em;
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
</head>

<body>
<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>

<hgroup>
	<h1>Now editing <a href=/~<?=$name?>><?=$name?></a></h1>
	<h2>Last modified <?=$time?></h2>
</hgroup>

<form method=post>
	<?= csrf_field() ?>
	<textarea name=content><?=$file?></textarea>
	<button>Update</button>
</form>

</body>
</html>
